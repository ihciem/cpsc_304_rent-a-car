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
        <title>CPSC 304 PHP/Oracle Demonstration</title>
    </head>

    <body>
        <h2>Welcome to Rent-A-Car!</h2>

        <hr />

        <h2>Show Available Vehicles</h2>
        <form method="GET" action="customer_search.php"> <!--refresh page when submitted-->
            Search By
            <br/>

            <input type="hidden" id="showAvailableVehiclesRequest" name="showAvailableVehiclesRequest">
            Vehicle Type: <input type="text" name="vType"> <br /><br />
            Location: <input type="text" name="Location"> <br /><br />
            Start Date: <input type="datetime-local" name="startDate"> <br /><br />
            Return Date: <input type="datetime-local" name="returnDate"> <br /><br />
            <p><input type="submit" value="Show Available Vehicles" name="showAvailableVehicles"></p>
        </form>

        <hr />

        <h2>Make Reservation</h2>
        <form method="POST" action="customer_search.php"> <!--refresh page when submitted-->
            <input type="hidden" id="makeReservationRequest" name="makeReservationRequest">
            Vehicle Type: <input type="text" name="vType"> <br /><br />
            Driver's License: <input type="text" name="dLicense" value="<?php echo ((isset($_GET["dLicense"]))?htmlspecialchars($_GET["dLicense"]):""); ?>"> <br /><br />
            Start Date: <input type="datetime-local" name="startDate"> <br /><br />
            Return Date: <input type="datetime-local" name="returnDate"> <br /><br />

            <input type="submit" value="Make Reservation" name="makeReservation"></p>
        </form>

        <?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP
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
            return $statement;
        }

        // TODO: HERE
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

        function handleShowAvailableVehiclesRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM demoTable");

            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
                printResult(executePlainSQL("SELECT id, name FROM demoTable"));
                // echo print_r($names);
            }
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

		if (isset($_POST['makeReservation'])) {
            handlePOSTRequest();
        } else if (isset($_GET['showAvailableVehiclesRequest'])) {
            handleGETRequest();
        }
		?>
	</body>
</html>
