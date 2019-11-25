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

        function handleUpdateRequest() {
            global $db_conn;

            $old_name = $_POST['oldName'];
            $new_name = $_POST['newName'];

            // you need the wrap the old name and new name values with single quotations
            executePlainSQL("UPDATE demoTable SET name='" . $new_name . "' WHERE name='" . $old_name . "'");
            OCICommit($db_conn);
        }

        function handleResetRequest() {
            global $db_conn;
            // Drop old table
            executePlainSQL("DROP TABLE demoTable");

            // Create new table
            executePlainSQL("CREATE TABLE demoTable (id int PRIMARY KEY, name char(30))");
            OCICommit($db_conn);
        }

        function handleInsertRequest() {
            global $db_conn;
            $continueProcessing = true;

            $rent_id = $_POST['rental'];

            $odometer = $_POST['odometer'];
            $odometerSQL = executePlainSQL("SELECT v.odometer FROM rental r, vehicle v WHERE r.rentid='" . $rent_id . "'");
            if ($row = OCI_Fetch_Array($odometerSQL, OCI_BOTH)) {
              if ($odometer <= $row[0]) {
                echo "Please enter the correct number for the odometer (should be greater than before it was rented).";
                $continueProcessing = false;
              }
            }

            if ($continueProcessing) {
              $alreadyReturned = executePlainSQL("SELECT return.rentid FROM return WHERE return.rentid='" . $rent_id . "'");
              if ($row = OCI_Fetch_Array($alreadyReturned, OCI_BOTH)) {
                echo "This return has already been processed. Please refresh the page to see the list of rentals not yet returned.";
                $continueProcessing = false;
              }
            }

            if ($continueProcessing) {
              $vlicense = executePlainSQL("SELECT rental.vlicense FROM rental WHERE rental.rentid='" . $rent_id . "'");
              if ($row = OCI_Fetch_Array($vlicense, OCI_BOTH)) {
                  $vlicense = $row[0];
                  executePlainSQL("UPDATE vehicle SET status = 'rented' WHERE vlicense ='" . $vlicense . "'");
              } else {
                echo "There is a database problem with the vehicle being returned. Please update the vehicle first.";
                $continueProcessing = false;
              }
            }

            if ($continueProcessing) {

              echo "<br> RentID: " . $rent_id;

              $confnoSQL = executePlainSQL("SELECT rental.confno FROM rental WHERE rental.rentid='" . $rent_id . "'");
              if ($row = OCI_Fetch_Array($confnoSQL, OCI_BOTH)) {
                  $confno = $row[0];
              }

              echo "<br> Reservation confirmation number: " . $confno . "<br>";

              $from_dtSQL = executePlainSQL("SELECT fromdt FROM rental WHERE rentid='" . $rent_id . "'");
              if ($row = OCI_Fetch_Array($from_dtSQL, OCI_BOTH)) {
                  $from_dt = $row[0];
                  echo "<br> Starting Date and Time: " . $row[0];
              }
              $to_dtSQL = executePlainSQL("SELECT todt FROM rental WHERE rentid='" . $rent_id . "'");
              if ($row = OCI_Fetch_Array($to_dtSQL, OCI_BOTH)) {
                  $to_dt = $row[0];
                  echo "<br> Return Date and Time: " . $row[0];
              }

              // converting timestamp to DateTime objects
              $from_dtDT = new DateTime();
              $from_dtDT->setTimestamp(strtotime($from_dt));
              $to_dtDT = new DateTime($to_dt);
              $to_dtDT->setTimestamp(strtotime($to_dt));

              // finding difference between DateTime objects
              $interval = $from_dtDT->diff($to_dtDT);
              $difference = $interval->format('%a days %H hours %I minutes');
              echo "<br> <b>Total Rental Time:</b> " . $difference . "<br>";

              echo "<br> <b>RATES </b>";
              //Finding specific rates for the returned vehicle
              $w_rateSQL = executePlainSQL("SELECT vt.wrate FROM vehicleType vt, rental r, vehicle v WHERE r.rentid ='" . $rent_id . "' AND r.vlicense = v.vlicense AND v.vtname = vt.vtname");
              if ($row = OCI_Fetch_Array($w_rateSQL, OCI_BOTH)) {
                  $w_rate = $row[0];
                  echo "<br> Weekly Rate: " . $row[0];
              }
              $d_rateSQL = executePlainSQL("SELECT vt.drate FROM vehicleType vt, rental r, vehicle v WHERE r.rentid ='" . $rent_id . "' AND r.vlicense = v.vlicense AND v.vtname = vt.vtname");
              if ($row = OCI_Fetch_Array($d_rateSQL, OCI_BOTH)) {
                  $d_rate = $row[0];
                  echo "<br> Daily Rate: " . $row[0];
              }
              $wi_rateSQL = executePlainSQL("SELECT vt.wirate FROM vehicleType vt, rental r, vehicle v WHERE r.rentid ='" . $rent_id . "' AND r.vlicense = v.vlicense AND v.vtname = vt.vtname");
              if ($row = OCI_Fetch_Array($wi_rateSQL, OCI_BOTH)) {
                  $wi_rate = $row[0];
                  echo "<br> Weekly Insurance Rate: " . $row[0];
              }
              $di_rateSQL = executePlainSQL("SELECT vt.dirate FROM vehicleType vt, rental r, vehicle v WHERE r.rentid ='" . $rent_id . "' AND r.vlicense = v.vlicense AND v.vtname = vt.vtname");
              if ($row = OCI_Fetch_Array($di_rateSQL, OCI_BOTH)) {
                  $di_rate = $row[0];
                  echo "<br> Daily Insurance Rate: " . $row[0] . "<br>";
              }

              //Calculating cost in weeks and days
              $weeks = floor($interval->format('%a') / 7);
              echo "<br> Rental time in weeks: " . $weeks;
              $days = $interval->format('%a') - ($weeks*7);
              echo "<br> Rental time in days: " . $days . "<br>";
              $days = $interval->format('%a') - ($weeks*7);

              echo "<br> <b>COST CALCULATIONS </b>";
              $cost = ($weeks*$w_rate + $weeks*$wi_rate) + ($days*$d_rate + $days*$di_rate);
              echo "<br> Cost = ". $weeks . "*$" . $w_rate . "+" . $days . "$*" . $d_rate. "+" . $weeks . "$*" . $wi_rate . "+" . $days . "$*" . $di_rate;
              $driverate = ($weeks*$w_rate + $days*$d_rate);
              echo "<br> Driving Costs: $" . $driverate;
              $insurrate = ($weeks*$wi_rate + $days*$di_rate);
              echo "<br> Insurance Costs: $" . $insurrate;
              echo "<br> <u><b>TOTAL COST:</b></u> $" . $cost . "<br>". "<br>";

              $tuple = array (
                  ":bind1" => $_POST['rental'],
                  ":bind2" => $to_dt,
                  ":bind3" => $_POST['insodometer'],
                  ":bind4" => strtoupper($_POST['insfulltank']),
                  ":bind5" => $cost
              );

              $alltuples = array (
                  $tuple
              );

              executeBoundSQL("insert into return values (:bind1, :bind2, :bind3, :bind4, :bind5)", $alltuples);
              OCICommit($db_conn);

              // Show cost breakdown
              if ($row = OCI_Fetch_Array($cost, OCI_BOTH)) {
                  echo "<br>Total Cost Breakdown: ". $row[0]. "<br>";
              }
              if ($row = OCI_Fetch_Array($driverate, OCI_BOTH)) {
                  echo "<br> Driving Costs: " . $row[0] . "<br>";
              }
              if ($row = OCI_Fetch_Array($insurrate, OCI_BOTH)) {
                  echo "<br> Insurance Costs: " . $row[0] . "<br>";
              }

              $result = executePlainSQL("SELECT rentid, returndt, value FROM return WHERE rentid = '" . $rent_id . "'");

              printResult($result);
            }
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

            // $result = executePlainSQL("SELECT rentid, returndt, value FROM return WHERE rentid = '" . $rent_id . "'");

            //printResult($result);
        }

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    handleCreateCustomerRequest();
                } else if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleUpdateRequest();
                } else if (array_key_exists('insertQueryRequest', $_POST)) {
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

        <h2>Returns</h2>
        <hr />

        <form method="POST" action="clerk_return.php">
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            Rental ID:
                <?php

                    connectToDB();
                    $rentals = executePlainSQL("SELECT rent.rentid FROM rental rent WHERE NOT EXISTS (SELECT r.rentid FROM return r WHERE r.rentid = rent.rentid)");
                    echo  '<select name="rental"  multiple="no" required>';

                    while ($row = OCI_Fetch_Array($rentals, OCI_RETURN_NULLS+OCI_ASSOC))
                    {
                        echo "<option value=\"". $row['RENTID'] . "\">" . $row['RENTID'] . "</option>";
                    }
                    echo '</select>';
                  ?>
                <br><br/>

            Odometer: <input type="text" name="insodometer" required> <br /><br />
            Full Tank: <input type="radio" name="insfulltank" value="YES" required> Yes
                       <input type="radio" name="insfulltank" value="NO" required> No<br><br />
            <input type="submit" value="Submit" name="insertSubmit"></p>
        </form>

        <hr />

        <?php
        if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest']) || isset($_GET['showTableRequest'])) {
            handleGETRequest();
        }
        ?>
    </body>
</html>
