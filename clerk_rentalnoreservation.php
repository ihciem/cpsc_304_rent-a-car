<!--Test Oracle file for UBC CPSC304 2018 Winter Term 1
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22)
  This file shows the very basics of how to execute PHP commands
  on Oracle.
  Specifically, it will drop a table, create a table, insert values
  update values, and then query for values

  IF YOU HAVE A TABLE CALLED "demoTable" IT WILL BE DESTROYED

  The script assumes you already have a server set up
  All OCI commands are commands to the Oracle libraries
  To get the file to work, you must place it somewhere where your
  Apache server can run it, and you must rename it to have a ".php"
  extension.  You must also change the username and password on the
  OCILogon below to be your ORACLE username and password -->

  <html>
    <head>
      <!-- Required meta tags -->
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">

      <!-- Bootstrap CSS -->
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    </head>

    <body>


        <hr />

        <!--Generate Receipt
        <form method="GET" action="clerk_rentalnoreservation.php">
            <input type="hidden" id="showTableRequest" name="showTableRequest">
            <input type="submit" value="Generate Receipt" name="showTable"></p>
        </form>
        -->
        <?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP ------------------------- END OF HTML ----------------------------
        include 'connectToDB.php';
        include 'printResult.php';

        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        date_default_timezone_set('America/Los_Angeles');
        $date = date('Y-m-d h.i.s A');
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())
        //generated rentID for the rental table

        function debugAlertMessage($message) {
            global $show_debug_alert_messages;

            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
            }
        }

        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            //echo "<br>running ".$cmdstr."<br>";
            global $db_conn, $success;

            $statement = OCIParse($db_conn, $cmdstr);
            //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                echo htmlentities($e['message']);
                $success = False;
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                $success = False;
            }

			return $statement;
		}

        function executeBoundSQL($cmdstr, $list) {
            /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

			global $db_conn, $success;
			$statement = OCIParse($db_conn, $cmdstr);

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn);
                echo htmlentities($e['message']);
                $success = False;
            }

            foreach ($list as $tuple) {
                foreach ($tuple as $bind => $val) {
                    //echo $val;
                    //echo "<br>".$bind."<br>";
                    OCIBindByName($statement, $bind, $val);
                    unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
				}

                $r = OCIExecute($statement, OCI_DEFAULT);
                if (!$r) {
                    echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                    $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                    echo htmlentities($e['message']);
                    echo "<br>";
                    $success = False;
                }
            }
        }

        // HANDLER FOR INSERT
        function handleInsertRequest() {
            global $db_conn;
            // Generate new unused rentid
            $rentidResult = executePlainSQL("SELECT rentid FROM rental WHERE rentid = (SELECT MAX(rentid) FROM rental)");
            $row = oci_fetch_array($rentidResult);
            $prevRentid = $row[0];
            $prevrentidSplit = preg_split('/[^A-Z0-9]+|(?<=[A-Z])(?=[0-9])|(?<=[0-9])(?=[A-Z])/', $prevRentid, 0, PREG_SPLIT_NO_EMPTY);
            $paddedZeroes = strlen($prevrentidSplit[1]); // originl length of ID, needed in str_pad
            $rentidNum = (int) preg_replace('/[^0-9]*/', '', $prevrentidSplit[1]);
            $rentidNum++;
            $rentidNum = str_pad($rentidNum, $paddedZeroes, '0', STR_PAD_LEFT);
            $rentid = $prevrentidSplit[0] . $rentidNum;
            // echo "prevrentid: " . $prevRentid  . '<br>';
            // echo "rentid: " . $rentid . '<br>';

            // Find an available car
            $vt_name = $_POST['insvtname'];
            $continueProcessing = true;
            if ($_POST['insvtname']==null) {
              $continueProcessing = false;
              echo "Please select a vehicle type.";
            }

            if ($continueProcessing) {
              $vlicenseSQL = executePlainSQL("SELECT vlicense From vehicle WHERE vtname = '" . $vt_name . "' AND status = 'available' AND ROWNUM <= 1");
              if ($row = OCI_Fetch_Array($vlicenseSQL, OCI_BOTH)) {
                  $vlicense = $row[0];
                  echo "<br> Grabbing vlicense: " . $row[0] . "<br>";
              } else {
                $continueProcessing = false;
                echo "<br> There are no vehicles of this type available. Please upgrade the customer to another vehicle type. <br>";
              }
              $startDate = new DateTime($_POST['insfromdt']);
              $start = date_format($startDate, 'Y-m-d h.i.s A');
              // echo "<br> Grabbing format startDate: " . $start . "<br>";

              $returnDate = new DateTime($_POST['instodt']);
              $return = date_format($returnDate, 'Y-m-d h.i.s A');
              // echo "<br> Grabbing format returnDate: " . $return . "<br>";

              $validDateTime = true;

              if ($startDate >= $returnDate || $startDate < $date) {
                $validDateTime = false;
                echo "<br> Invalid start or return date. <br>";
              }
            }

            if ($continueProcessing && $validDateTime) {

              $odometerSQL = executePlainSQL("SELECT odometer FROM vehicle WHERE vlicense = '" . $vlicense . "'");
              if ($row = OCI_Fetch_Array($odometerSQL, OCI_BOTH)) {
                  $odometer = $row[0];
                  echo "<br> Grabbing odometer: " . $row[0] . "<br>";
              }
              executePlainSQL("UPDATE vehicle SET status = 'rented' WHERE vlicense ='" . $vlicense . "'");
              // echo "<br> Updating vehicle status <br>";
              //
              // echo $_POST['insfromdt'] . '<br>';
              // echo $_POST['instodt'] . '<br>';

              if ($validDateTime) {
                $startDate = date_format($startDate, 'd-M-Y h.i.s A');
                $returnDate = date_format($returnDate, 'd-M-Y h.i.s A');
                //Getting the values from user and insert data into the table

                $tuple = array (
                    ":bind1" => $rentid,
                    ":bind2" => $_POST['inscardno'],
                    ":bind3" => $odometer,
                    ":bind4" => $vlicense,
                    ":bind5" => $startDate,
                    ":bind6" => $returnDate,
                    ":bind7" => $_POST['insdlicense'],
                    ":bind8" => ""
                );

                $alltuples = array (
                    $tuple
                );


                executeBoundSQL("INSERT INTO rental VALUES (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6, :bind7, :bind8)", $alltuples);
                OCICommit($db_conn);

                echo "<br><b> Receipt: </b><br>";

                echo "<br> Details of assigned (rented) vehicle: <br>";
                $vehicleDetails = executePlainSQL("SELECT v.vid, v.vlicense, v.make, v.model, v.color, v.odometer, v.vtname FROM rental R, vehicle v WHERE R.vlicense = v.vlicense AND R.rentid = '" . $rentid . "'");
                printResult($vehicleDetails);

                echo "<br> Details of rental: <br>";
                $rentalDetails = executePlainSQL("SELECT R.rentid, R.confno, R.dlicense, R.vlicense, v.vtname, R.fromdt, R.todt FROM rental R, vehicle v WHERE R.vlicense = v.vlicense AND R.rentid = '" . $rentid . "'");
                printResult($rentalDetails);
              }
            }
        }


        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('insertQueryRequest', $_POST)) {
                    handleInsertRequest();
                }

                disconnectFromDB();
            }
        }

        ?>


        <h2>New Rental</h2>
        <hr />
        <form method="POST" action="clerk_rentalnoreservation.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            Vehicle Type:
            <?php

                connectToDB();
                $vehicles = executePlainSQL("SELECT vtname from vehicleType ORDER BY vtname");
                echo  '<select name="insvtname"  multiple="no">';

                while ($row = OCI_Fetch_Array($vehicles, OCI_RETURN_NULLS+OCI_ASSOC))
                {
                    echo "<option value=\"". $row['VTNAME'] . "\">" . $row['VTNAME'] . "</option>";
                }
                echo '</select>';
              ?>
            <br /><br />
            Card Number: <input type="text" name="inscardno" oninvalid="this.setCustomValidity('Please enter a valid credit card number.')" onchange="try{setCustomValidity('')}catch(e){}" oninput="setCustomValidity(' ')" required pattern="[0-9]{16}"> <br /><br />
            Starting Date: <input type="datetime-local" name="insfromdt" max="9999-12-31"> <br /><br />
            Returning Date: <input type="datetime-local" name="instodt" max="9999-12-31"> <br /><br />
            Drivers License:
                <?php

                    connectToDB();
                    $dlicenses = executePlainSQL("SELECT dlicense FROM customer");
                    echo  '<select name="insdlicense"  multiple="no">';

                    while ($row = OCI_Fetch_Array($dlicenses, OCI_RETURN_NULLS+OCI_ASSOC))
                    {
                        echo "<option value=\"". $row['DLICENSE'] . "\">" . $row['DLICENSE'] . "</option>";
                    }
                    echo '</select>';
                  ?>
                <br><br/>

            <input type="submit" value="Submit" name="insertSubmit"></p>
        </form>
        <hr />

        <form action="clerk_rentalreservation.php">
              <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->

              <p><input type="submit" value="HAS RESERVATION"></p>
        </form>
        <hr />

        <?php
        if (isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        }
         ?>
	</body>
</html>
