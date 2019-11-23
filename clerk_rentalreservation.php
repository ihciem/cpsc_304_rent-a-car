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

        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())
        $rentIDresGen = 0; //generated rentID for the rental table

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

        function printResult($result) { //prints results from a select statement
            $header = false;

            echo "<br>Generating receipt<br>";
            echo "<table>";
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                $numKeys = array_filter(array_keys($row), function($numKey) {return is_int($numKey);});
                $assocKeys = array_filter(array_keys($row), function($assocKey) {return is_string($assocKey);});

                // output header/column/attribute names
                if (!$header) {
                    echo "<thead><tr>";
                    foreach ($assocKeys as $key) {
                        echo '<th>' . ($key !== null ? htmlentities($key, ENT_QUOTES) : '') . str_repeat("&nbsp;", 5) . '</th>';
                    }
                    echo "</tr></thead>";
                    $header = true;
                }

                // output all the data rows.
                echo '<tr>';
                foreach ($numKeys as $index) {
                    echo "<td>" . $row[$index] . str_repeat("&nbsp;", 5) . "</td>";
                }
                echo '</tr>';
            }
            echo "</table>";
        }

        function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

        function handleUpdateRequest() {
            global $db_conn;

            $old_name = $_POST['oldName'];
            $new_name = $_POST['newName'];

            // you need the wrap the old name and new name values with single quotations
            executePlainSQL("UPDATE demoTable SET name='" . $new_name . "' WHERE name='" . $old_name . "'");
            OCICommit($db_conn);
        }

        // HANDLER FOR INSERT
        function handleInsertRequest() {
            global $db_conn;
            global $rentIDresGen;

            // Finding vehicle from reservation confno

            $confno = $_POST['confno'];

            $vlicense = executePlainSQL("SELECT v.vlicense FROM reservation r, vehicle v WHERE r.confno = '" . $confno . "' AND r.vtname = v.vtname AND ROWNUM <= 1");
            if ($row = OCI_Fetch_Array($vlicense, OCI_BOTH)) {
                echo "<br> Grabbing vlicense:" . $row[0] . "<br>";
            }
            $odometer = executePlainSQL("SELECT odometer FROM vehicle WHERE vlicense = '" . $vlicense . "'");
            if ($row = OCI_Fetch_Array($odometer, OCI_BOTH)) {
                echo "<br> Grabbing odometer:" . $row[0] . "<br>";
            }
            executePlainSQL("UPDATE vehicle SET status = 'rented' WHERE vlicense ='" . $vlicense . "'");
            echo "<br> Updating vehicle status <br>";

            $fromdt = executePlainSQL("SELECT fromdt FROM reservation WHERE confno = '" . $confno . "'");
            echo "<br> Grabbing fromtdt <br>";
            $todt = executePlainSQL("SELECT todt FROM reservation WHERE confno = '" . $confno . "'");
            echo "<br> Grabbing todt <br>";
            $dlicense = executePlainSQL("SELECT dlicense FROM reservation WHERE confno = '" . $confno . "'");
            echo "<br> Grabbing dlicense <br>";

            //Getting the values from user and insert data into the table
            $rentIDresGen = $rentIDresGen++;
            $rentIDString = strval($rentIDresGen);

            $tuple = array (
                ":bind1" => $rentIDString,
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


            executeBoundSQL("INSERT INTO rental VALUES (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6, :bind7, :bind8)", $alltuples);
            OCICommit($db_conn);
        }

        function handleCountRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM demoTable");

            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
            }
        }

        // HANDLER FOR PRINTING
        function handleShowTableRequest() {
            global $db_conn;
            global $rentIDresGen;

            $result = executePlainSQL("SELECT R.rentid, R.fromdt, R.todt, v.vtname FROM rental R, vehicle v WHERE R.vlicense = v.vlicense AND R.rentid = '" . $rentIDresGen . "'");

            printResult($result);

            $count = executePlainSQL("SELECT Count(*) FROM reservation");

            if (($row = oci_fetch_row($count)) != false) {
                echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
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

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('countTuples', $_GET)) {
                    handleCountRequest();
                } else if (array_key_exists('showTable', $_GET)) {
                    handleShowTableRequest();
                }

                disconnectFromDB();
            }
        }

        ?>
        
        <h2>Rental With a Prior Reservation</h2>
        <hr />

        <form method="POST" action="clerk_rentalreservation.php">
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            Reservation Confirmation Number: 
                <?php
                    
                    connectToDB();
                    $reservations = executePlainSQL("SELECT reservation.confno FROM reservation");
                    echo  '<select name="confno"  multiple="no">';

                    while ($row = OCI_Fetch_Array($reservations, OCI_RETURN_NULLS+OCI_ASSOC))
                    {
                        echo "<option value=\"". $row['CONFNO'] . "\">" . $row['CONFNO'] . "</option>";
                    }
                    echo '</select>';
                  ?>
                <br><br/>
            <!--Card Number insert-->
            Card Number: <input type="text" name="cardno"> <br /><br />
            <input type="submit" value="Submit" name="insertSubmit"></p>
        </form>

        <hr />

        <form method="GET" action="clerk_rentalreservation.php"> <!--Generate Receipt-->
            <input type="hidden" id="showTableRequest" name="showTableRequest">
            <input type="submit" value="Generate Receipt" name="showTable"></p>
        </form>
        
        <?php
        if (isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest']) || isset($_GET['showTableRequest'])) {
            handleGETRequest();
        }
         ?>
	</body>

</html>