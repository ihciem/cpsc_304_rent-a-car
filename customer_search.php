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

        <title>CPSC 304 PHP/Oracle Demonstration</title>
    </head>

    <body>
        <?php
        include 'connectToDB.php';
        include 'printResult.php';

        date_default_timezone_set('America/Los_Angeles');
        $date = date('Y-m-d h.i.s A');

        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())


        function handleShowAvailableVehiclesRequest() {
            global $db_conn;

            $validDateTime = true;

            $result = executePlainSQL("CREATE OR REPLACE VIEW availableVehicles AS SELECT * FROM vehicle v WHERE v.status = 'available' ORDER BY v.city, v.location");

            $startDate = new DateTime($_GET['startDate']);
            $startDate = date_format($startDate, 'Y-m-d h.i.s A');
            $returnDate = new DateTime($_GET['returnDate']);
            $returnDate = date_format($returnDate,'Y-m-d h.i.s A');

            if ($startDate > $returnDate || $startDate < $date) {
              $validDateTime = false;
              echo "<br> Invalid start or return date. <br>";
            }

            if (isset($_GET['vType']) && isset($_GET['location']) && $validDateTime) {
              $vt_name = $_GET['vType'];
              $branch = $_GET['location'];

              if ($_GET['location']=="all") {
                $result = executePlainSQL("CREATE OR REPLACE VIEW availableVehicles AS SELECT * FROM vehicle v WHERE v.status = 'available' AND v.vtname = '$vt_name' ORDER BY v.city, v.location");
              } else {
                $position = strpos($branch, ',');
                $location = substr($branch, 0, $position);
                $city = substr($branch, $position+1);
                // $result = executePlainSQL("SELECT * FROM vehicle v WHERE v.status = 'available' AND v.vtname = '$vt_name' AND v.location = '$location' AND v.city = '$city' WHERE NOT EXISTS (SELECT * FROM rental r WHERE ) ORDER BY v.city, v.location");
                $result = executePlainSQL("CREATE OR REPLACE VIEW availableVehicles AS SELECT * FROM vehicle v WHERE v.status = 'available' AND v.vtname = '$vt_name' AND v.location = '$location' AND v.city = '$city' ORDER BY v.city, v.location");
              }
            } else if (isset($_GET['vType']) && isset($_GET['location'])) {
              $vt_name = $_GET['vType'];
              $branch = $_GET['location'];
              if ($_GET['location']=="all") {
                $result = executePlainSQL("CREATE OR REPLACE VIEW availableVehicles AS SELECT * FROM vehicle v WHERE v.status = 'available' AND v.vtname = '$vt_name' ORDER BY v.city, v.location");
              } else {
                $position = strpos($branch, ',');
                $location = substr($branch, 0, $position);
                $city = substr($branch, $position+1);
                // $result = executePlainSQL("SELECT * FROM vehicle v WHERE v.status = 'available' AND v.vtname = '$vt_name' AND v.location = '$location' AND v.city = '$city' WHERE NOT EXISTS (SELECT * FROM rental r WHERE ) ORDER BY v.city, v.location");
                $result = executePlainSQL("CREATE OR REPLACE VIEW availableVehicles AS SELECT * FROM vehicle v WHERE v.status = 'available' AND v.vtname = '$vt_name' AND v.location = '$location' AND v.city = '$city' ORDER BY v.city, v.location");
              }
            } else if (isset($_GET['vType']) && $validDateTime) {
              $vt_name = $_GET['vType'];

              $result = executePlainSQL("CREATE OR REPLACE VIEW availableVehicles AS SELECT * FROM vehicle v WHERE v.status = 'available' AND v.vtname = '$vt_name' ORDER BY v.city, v.location");
            } else if (isset($_GET['vType'])) {
              $vt_name = $_GET['vType'];
              $result = executePlainSQL("CREATE OR REPLACE VIEW availableVehicles AS SELECT * FROM vehicle v WHERE v.status = 'available' AND v.vtname = '$vt_name' ORDER BY v.city, v.location");
            } else if (isset($_GET['location']) && $validDateTime) {
              $branch = $_GET['location'];

              if ($_GET['location']=="all") {
                $result = executePlainSQL("CREATE OR REPLACE VIEW availableVehicles AS SELECT * FROM vehicle v WHERE v.status = 'available' ORDER BY v.city, v.location");
              } else {
                $position = strpos($branch, ',');
                $location = substr($branch, 0, $position);
                $city = substr($branch, $position+1);
                $result = executePlainSQL("CREATE OR REPLACE VIEW availableVehicles AS SELECT * FROM vehicle v WHERE v.status = 'available' AND v.location = '$location' AND v.city = '$city' ORDER BY v.city, v.location");
              }
            } else if (isset($_GET['location'])) {
              $branch = $_GET['location'];
              if ($_GET['location']=="all") {
                // is default
              } else {
                $position = strpos($branch, ',');
                $location = substr($branch, 0, $position);
                $city = substr($branch, $position+1);
                $result = executePlainSQL("CREATE OR REPLACE VIEW availableVehicles AS SELECT * FROM vehicle v WHERE v.status = 'available' AND v.location = '$location' AND v.city = '$city' ORDER BY v.city, v.location");
              }
            } else if ($validDateTime) {
              $result = executePlainSQL("CREATE OR REPLACE VIEW availableVehicles AS SELECT * FROM vehicle v WHERE v.status = 'available' ORDER BY v.city, v.location");
            }

            $numberOfResults = executePlainSQL("SELECT COUNT(*) FROM availableVehicles");
            if ($row = OCI_Fetch_Array($numberOfResults, OCI_BOTH)) {
              echo "<strong>Number of Available Vehicles: </strong>" . $row[0] . "</br>";
            }
            $result = executePlainSQL("SELECT aV.vid, aV.vlicense, aV.make, aV.model, aV.color, aV.odometer, aV.status, aV.vtname, aV.location, aV.city FROM availableVehicles aV");
            if ($startDate = $returnDate) {
              $validDateTime = true;
            }
            if ($validDateTime) {
              printResult($result);
            }
        }

        // HANDLE ALL GET ROUTES
        // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('showAvailableVehiclesRequest', $_GET)) {
                    handleShowAvailableVehiclesRequest();
                }

                disconnectFromDB();
            }
        }

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
            return $statement;
        }

        function handleMakeReservationRequest() {
            global $db_conn;

            // Generate new unused confID
            $confnoResult = executePlainSQL("SELECT confno FROM reservation WHERE confno = (SELECT MAX(confno) FROM reservation)");
            $row = oci_fetch_array($confnoResult);
            $prevConfno = $row[0];
            $prevConfnoSplit = preg_split('/[^A-Z0-9]+|(?<=[A-Z])(?=[0-9])|(?<=[0-9])(?=[A-Z])/', $prevConfno, 0, PREG_SPLIT_NO_EMPTY);
            $paddedZeroes = strlen($prevConfnoSplit[1]); // originl length of ID, needed in str_pad
            $confnoNum = (int) preg_replace('/[^0-9]*/', '', $prevConfnoSplit[1]);
            $confnoNum++;
            $confnoNum = str_pad($confnoNum, $paddedZeroes, '0', STR_PAD_LEFT);
            $confno = $prevConfnoSplit[0] . $confnoNum;
//            echo "prevConfno: " . $prevConfno  . '<br>';
//            echo "confno: " . $confno . '<br>';

            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $_POST['dLicense']
            );

            $alltuples = array (
                $tuple
            );

            $dLicenseResult = executeBoundSQL("SELECT COUNT(*) FROM customer c1 WHERE EXISTS (SELECT * FROM customer c2 WHERE c1.dlicense = :bind1 AND c1.dlicense = c2.dlicense)", $alltuples);
            $row = oci_fetch_row($dLicenseResult);
//            echo "Does dLicense exist: " . $row[0] . '<br>';
            if ($row[0] == 0) {
                header("Location: https://www.students.cs.ubc.ca/~" . $_SESSION['username'] .  "/customer_new.php?dLicense=" . $_POST['dLicense']);
                die();
            }

            $startDate = new DateTime($_POST['startDate']);
            $startDate = date_format($startDate, 'd-M-Y h.i.s A');
//            echo $startDate;
            $returnDate = new DateTime($_POST['returnDate']);
            $returnDate = date_format($returnDate,'d-M-Y h.i.s A');
//            echo $returnDate;

            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $confno,
                ":bind2" => $_POST['vType'],
                ":bind3" => $_POST['dLicense'],
                ":bind4" => $startDate,
                ":bind5" => $returnDate
            );

            $alltuples = array (
                $tuple
            );

            executeBoundSQL("INSERT INTO reservation VALUES (:bind1, :bind2, :bind3, :bind4, :bind5)", $alltuples);
            OCICommit($db_conn);
        }

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('makeReservationRequest', $_POST)) {
                    handleMakeReservationRequest();
                }

                disconnectFromDB();
            }
        }

    ?>

    <h2>Welcome to Super Rent!</h2>

    <hr />

    <h2>Show Available Vehicles</h2>
    <form method="GET" action="customer_search.php"> <!--refresh page when submitted-->
        Search By

        <br/>
        <br/>

        <input type="hidden" id="showAvailableVehiclesRequest" name="showAvailableVehiclesRequest">
        Vehicle Type:
        <?php
            connectToDB();
            // List of reservations that have yet to be processed into a rental
            $vehicleTypes = executePlainSQL("SELECT vtname FROM vehicleType");
            echo  '<select name="vType"  multiple="no">';
            while ($row = OCI_Fetch_Array($vehicleTypes, OCI_RETURN_NULLS+OCI_ASSOC)) {
                echo "<option value=\"". $row['VTNAME'] . "\">" . $row['VTNAME'] . "</option>";
            }
            echo '</select>';
        ?>
        Location:
        <?php
            connectToDB();
            $branches = executePlainSQL("SELECT branch.location, branch.city FROM branch ORDER BY branch.city, branch.location");
            echo  '<select name="location" multiple="no">';
            echo "<option value=\"all\">All locations</option>";
            while ($row = OCI_Fetch_Array($branches, OCI_RETURN_NULLS+OCI_ASSOC))
            {
              echo "<option value=\"". $row['LOCATION']. "," .$row['CITY'] . "\">" . $row['LOCATION'] . ", " . $row['CITY'] . "</option>";
            }
            echo '</select>';
            ?>
        Start Date: <input type="datetime-local" name="startDate">
        Return Date: <input type="datetime-local" name="returnDate"> <br /><br />
        <p><input type="submit" value="Show Available Vehicles" name="showAvailableVehicles"></p>
    </form>
    <?php
		if (isset($_POST['makeReservation'])) {
            handlePOSTRequest();
        }
		?>
    <?php
    if (isset($_GET['showAvailableVehicles'])) {
        handleGETRequest();
    }
    ?>
    <hr />

    <h2>Make Reservation</h2>
    <form method="POST" action="customer_search.php"> <!--refresh page when submitted-->
        <input type="hidden" id="makeReservationRequest" name="makeReservationRequest">
        Vehicle Type:
        <?php
            connectToDB();
            // List of reservations that have yet to be processed into a rental
            $vehicleTypes = executePlainSQL("SELECT vtname FROM vehicleType");
            echo  '<select name="vType"  multiple="no">';
            while ($row = OCI_Fetch_Array($vehicleTypes, OCI_RETURN_NULLS+OCI_ASSOC)) {
                echo "<option value=\"". $row['VTNAME'] . "\">" . $row['VTNAME'] . "</option>";
            }
            echo '</select>';
        ?>
        <br /><br />
        Driver's License: <input type="text" name="dLicense" value="<?php echo ((isset($_GET["dLicense"]))?htmlspecialchars($_GET["dLicense"]):""); ?>"> <br /><br />
        Start Date: <input type="datetime-local" name="startDate"> <br /><br />
        Return Date: <input type="datetime-local" name="returnDate"> <br /><br />
        <p><input type="submit" value="Make Reservation" name="makeReservation"></p>
    </form>

	</body>
</html>
