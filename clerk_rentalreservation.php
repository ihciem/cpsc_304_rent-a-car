<html>
    <head>
      <!-- Required meta tags -->
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">

      <!-- Bootstrap CSS -->
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    </head>

    <body>

      <?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP ------------------------- END OF HTML ----------------------------
        include 'connectToDB.php';
        include 'printResult.php';

        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())


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
            $continueProcessing = true;

            // Generate new unused rentid (from max previous rentid)
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

            // Get confirmation number for reservation
            $confno = $_POST['confno'];

            // Finding vehicle license plate from confirmation number
            $vlicenseSQL = executePlainSQL("SELECT v.vlicense FROM reservation r, vehicle v WHERE r.confno = '" . $confno . "' AND r.vtname = v.vtname AND v.status = 'available' AND ROWNUM <= 1");
            if ($row = OCI_Fetch_Array($vlicenseSQL, OCI_BOTH)) {
                $vlicense = $row[0];
                // echo "<br> Grabbing vlicense: " . $row[0] . "<br>";
            } else {
                $continueProcessing = false;
                echo "<br> There are no vehicles of this type available. Please upgrade the customer to another vehicle type. <br>";
            }

            if ($continueProcessing) {
              $rentalMade = executePlainSQL("SELECT r.confno FROM rental r WHERE r.confno='" . $confno . "'");
              if ($row = OCI_Fetch_Array($rentalMade, OCI_BOTH)) {
                echo "This reservation has already been processed into a rental. Please refresh the page to see the list of reservations not yet processed.";
                $continueProcessing = false;
              }
            }

            if ($continueProcessing)  {
              // Finding vehicle odometer from vehicle license
              $odometerSQL = executePlainSQL("SELECT odometer FROM vehicle WHERE vlicense = '" . $vlicense . "'");
              if ($row = OCI_Fetch_Array($odometerSQL, OCI_BOTH)) {
                  $odometer = $row[0];
                  // echo "<br> Grabbing odometer: " . $row[0] . "<br>";
              }

              // echo "<br> Updating vehicle status <br>";
              executePlainSQL("UPDATE vehicle SET status = 'rented' WHERE vlicense ='" . $vlicense . "'");

              // Finding start date time of rental from reservation
              $fromdtSQL = executePlainSQL("SELECT fromdt FROM reservation WHERE confno = '" . $confno . "'");
              if ($row = OCI_Fetch_Array($fromdtSQL, OCI_BOTH)) {
                  $fromdt = $row[0];
                  // echo "<br> Grabbing fromdt: " . $row[0] . "<br>";
              }

              // Finding end date time of rental from reservation
              $todtSQL = executePlainSQL("SELECT todt FROM reservation WHERE confno = '" . $confno . "'");
              if ($row = OCI_Fetch_Array($todtSQL, OCI_BOTH)) {
                  $todt = $row[0];
                  // echo "<br> Grabbing todt: " . $row[0] . "<br>";
              }

              // Finding driver's license from reservation
              $dlicenseSQL = executePlainSQL("SELECT dlicense FROM reservation WHERE confno = '" . $confno . "'");
              if ($row = OCI_Fetch_Array($dlicenseSQL, OCI_BOTH)) {
                  $dlicense = $row[0];
                  // echo "<br> Grabbing dlicense: " . $row[0] . "<br>";
              }

              $tuple = array (
                  ":bind1" => $rentid,
                  ":bind2" => $_POST['cardno'],
                  ":bind3" => $odometer,
                  ":bind4" => $vlicense,
                  ":bind5" => $fromdt,
                  ":bind6" => $todt,
                  ":bind7" => $dlicense,
                  ":bind8" => $_POST['confno']
              );

              $alltuples = array (
                  $tuple
              );

              // executePlainSQL("DELETE FROM rental WHERE rentid = '" . $rentid . "'");
              // echo "<br> Deleting tuples with the same rentid <br>";

              executeBoundSQL("INSERT INTO rental VALUES (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6, :bind7, :bind8)", $alltuples);
              OCICommit($db_conn);

              echo "<br><b> Receipt: </b><br>";

              echo "<br> Details of assigned (rented) vehicle: <br>";
              $vehicleDetails = executePlainSQL("SELECT v.vid, v.vlicense, v.make, v.model, v.color, v.odometer, v.vtname FROM rental R, vehicle v WHERE R.vlicense = v.vlicense AND R.rentid = '" . $rentid . "'");
              printResult($vehicleDetails);

              echo "<br> Details of rental: <br>";
              $rentalDetails = executePlainSQL("SELECT R.rentid, R.confno, R.dlicense, R.vlicense, v.vtname, R.fromdt, R.todt FROM rental R, vehicle v WHERE R.vlicense = v.vlicense AND R.rentid = '" . $rentid . "'");
              printResult($rentalDetails);

              // Rental time interval
              $interval = $startDate->diff($returnDate);
              $difference = $interval->format("%a");
              echo "<br> Rental period: " . $difference . "<br>";
            }
        }

        // HANDLER FOR PRINTING
        function handleShowTableRequest() {
            global $db_conn;
            global $rentid;
            //$rentIDString = strval($$_SESSION["rentid"]);
            echo "<br> Grabbing rentid: " . $rentid . "<br>";

            $result = executePlainSQL("SELECT R.rentid, R.fromdt, R.todt, v.vtname FROM rental R, vehicle v WHERE R.vlicense = v.vlicense AND R.rentid = '" . $rentid . "'");

            printResult($result);


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

        // HANDLE ALL GET ROUTES
	       // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('countTuples', $_GET)) {
                    handleShowAvailableVehiclesRequest();
                } else if (array_key_exists('showTable', $_GET)) {
                    handleShowTableRequest();
                }

                disconnectFromDB();
            }
        }

        ?>
        <hr />

        <h2>Rental With a Prior Reservation</h2>
        <hr />

        <form method="POST" action="clerk_rentalreservation.php">
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            Reservation Confirmation Number:
                <?php
                    connectToDB();
                    // List of reservations that have yet to be processed into a rental
                    $reservations = executePlainSQL("SELECT r.confno FROM reservation r WHERE NOT EXISTS (SELECT rent.confno FROM rental rent WHERE rent.confno = r.confno)");
                    echo  '<select name="confno"  multiple="no" required>';
                    while ($row = OCI_Fetch_Array($reservations, OCI_RETURN_NULLS+OCI_ASSOC)) {
                        echo "<option value=\"". $row['CONFNO'] . "\">" . $row['CONFNO'] . "</option>";
                    }
                    echo '</select>';
                ?>
                <br><br/>
            <!--Card Number insert-->
            Card Number: <input type="text" name="cardno" oninvalid="this.setCustomValidity('Please enter a valid credit card number.')" onchange="try{setCustomValidity('')}catch(e){}" oninput="setCustomValidity(' ')" required pattern="[0-9]{16}"> <br /><br />
            <input type="submit" value="Submit" name="insertSubmit"></p>
        </form>
        <hr />

        <form action="clerk_rentalnoreservation.php">

              <p><input type="submit" value="NO RESERVATION"></p>
        </form>

        <hr />

        <?php
        if (isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['showTableRequest'])) {
            handleGETRequest();
        }
        ?>
	</body>

</html>
