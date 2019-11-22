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
      <?php //this tells the system that it's no longer just parsing html; it's now parsing PHP
      include 'connectToDB.php';

      $success = True; //keep track of errors so it redirects the page only if there are no errors
      $db_conn = NULL; // edit the login credentials in connectToDB()
      $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())
      $rentals_status = 'unchecked';
      $returns_status = 'unchecked';
      $date = date("Y/m/d");

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

      function printRentals() { //prints results from a select statement
          global $date;
          $branchSelected = false;
          echo "<b><u>$date</u></b></b>" .strtoupper("<b><u> Daily Rentals Report</u></b><br>");

          if ($_GET['branch']=="all") {
            echo strtoupper("<b><u>for all branches</u></b>");
            echo strtoupper("<br></br><br><b><u>Summary of results</u></b><br>");

            executePlainSQL("CREATE OR REPLACE VIEW rentalsToday AS SELECT * FROM rental where to_char(cast(rental.fromdt as date), 'YYYY/MM/DD')='$date'");

          } else {
            $branchSelected = true;
            $branch = $_GET['branch'];
            $position = strpos($branch, ',');
            $location = substr($branch, 0, $position);
            $city = substr($branch, $position+1);

            echo strtoupper("<b>for branch: </b>".$location.", ".$city);
            echo strtoupper("<br></br><br><b><u>Summary of results</u></b><br>");
            executePlainSQL("CREATE OR REPLACE VIEW rentalsToday AS SELECT rent.rentid, rent.cardno, rent.odometer, rent.vlicense, rent.fromdt, rent.todt, rent.dlicense, rent.confno FROM rental rent, vehicle v where rent.vlicense = v.vlicense AND to_char(cast(rent.fromdt as date), 'YYYY/MM/DD')='$date' AND v.location = '$location' AND v.city = '$city'");
          }

          // number of vehicles rented per category
          $noPerCategory = executePlainSQL("SELECT v.vtname, COUNT(*) FROM rentalsToday rT, vehicle v WHERE rT.vlicense = v.vlicense GROUP BY v.vtname");
          echo "<br>Number of Rentals Per Vehicle Category</br>";
          printResult($noPerCategory);

          // number of rentals at each branch
          $noPerBranch = executePlainSQL("SELECT v.location, v.city, COUNT(*) FROM rentalsToday rT, vehicle v WHERE rT.vlicense = v.vlicense GROUP BY v.location, v.city");
          if ($branchSelected) {
            // number of rentals at each branch
            if ($row = OCI_Fetch_Array($noPerBranch, OCI_BOTH)) {
              echo "<br>Branch Total Number of Rentals Today: " . $row[2]."</br>";
            } else {
              echo "<br>Branch Total Number of Rentals Today: 0</br>";
            }
          } else {
            echo "<br>Number of Rentals Per Branch</br>";
            printResult($noPerBranch);
          }

          // total number of new rentals across whole company
          $totalRentals= executePlainSQL("SELECT Count(*) FROM rentalsToday");
          if ($row = OCI_Fetch_Array($totalRentals, OCI_BOTH)) {
            echo "<br>Company Total Number of Rentals Today: " . $row[0]."</br>";
          } else {
            echo "<br>Company Total Number of Rentals Today: 0</br>";
          }

          echo strtoupper("<br><b><u>DATA</u></b><br>");
          $result = executePlainSQL("SELECT v.city, v.location, v.vtname, v.vlicense, v.vid, v.make, v.model, v.color, v.odometer, v.status FROM rentalsToday rT, rental rent, vehicle v WHERE rT.rentid = rent.rentid AND rent.vlicense = v.vlicense ORDER BY v.city, v.location, v.vtname");
          echo "<br>Retrieved vehicle data:<br>";
          printResult($result);
      }

      function printReturns() { //prints results from a select statement
        global $date;
        $branchSelected = false;
        echo "<b><u>$date</u></b></b>" .strtoupper("<b><u> Daily Rentals Report</u></b><br>");


        if ($_GET['branch']=="all") {
          echo strtoupper("<b>for all branches</b>");
          echo strtoupper("<br></br><br><b><u>Summary of results</u></b><br>");

          executePlainSQL("CREATE OR REPLACE VIEW returnsToday AS SELECT * FROM return where to_char(cast(return.returndt as date), 'YYYY/MM/DD')='$date'");

        } else {
          $branchSelected = true;
          $branch = $_GET['branch'];
          $position = strpos($branch, ',');
          $location = substr($branch, 0, $position);
          $city = substr($branch, $position+1);

          echo strtoupper("<b>for branch: </b>".$location.", ".$city);
          echo strtoupper("<br></br><br><b><u>Summary of results</u></b><br>");
          executePlainSQL("CREATE OR REPLACE VIEW returnsToday AS SELECT r.rentid, r.returndt, r.odometer, r.fulltank, r.value FROM return r, rental rent, vehicle v where r.rentid = rent.rentid AND rent.vlicense = v.vlicense AND to_char(cast(r.returndt as date), 'YYYY/MM/DD')='$date' AND v.location = '$location' AND v.city = '$city'");
        }

        // number of returns and revenue per vehicle category
        $noPerCategory = executePlainSQL("SELECT v.vtname, COUNT(*), SUM(rT.value) FROM returnsToday rT, rental rent, vehicle v WHERE rT.rentid = rent.rentid AND rent.vlicense = v.vlicense GROUP BY v.vtname");
        echo "<br>Number of Returns and Revenue Per Vehicle Category<br>";
        printResult($noPerCategory);

        // subtotals for the number of vehicles and revenue per branch;
        $subTotals = executePlainSQL("SELECT v.location, v.city, COUNT(*), SUM(rT.value) FROM returnsToday rT, rental rent, vehicle v WHERE rT.rentid = rent.rentid AND rent.vlicense = v.vlicense GROUP BY v.location, v.city");
        if ($branchSelected) {
          if ($row = OCI_Fetch_Array($subTotals, OCI_BOTH)) {
            echo "<br>Branch Subtotal Today: ". $row[3]. "<br>";
          } else {
            echo "<br>Branch Subtotal Today: N/A<br>";
          }
        } else {
          echo "<br>Subtotals Per Branch<br>";
          printResult($subTotals);
        }

        // grandtotals for the day;
        $grandTotals = executePlainSQL("SELECT SUM(rT.value) FROM returnsToday rT");
        if ($row = OCI_Fetch_Array($grandTotals, OCI_BOTH)) {
          echo "<br>Company Grand Total Today: ". $row[0]. "<br>";
        } else {
          echo "<br>Company Grand Total Today: N/A<br>";
        }

        echo strtoupper("<br><b><u>DATA</u></b><br>");
        $result = executePlainSQL("SELECT v.city, v.location, v.vtname, v.vlicense, v.vid, v.make, v.model, v.color, v.odometer, v.status FROM returnsToday rT, rental rent, vehicle v WHERE rT.rentid = rent.rentid AND rent.vlicense = v.vlicense ORDER BY v.city, v.location, v.vtname");
        echo "<br>Retrieved vehicle data:<br>";
        printResult($result);
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

      function handleResetRequest() {
          global $db_conn;
          // Drop old table
          executePlainSQL("DROP TABLE demoTable");

          // Create new table
          echo "<br> creating new table <br>";
          executePlainSQL("CREATE TABLE demoTable (id int PRIMARY KEY, name char(30))");
          OCICommit($db_conn);
      }

      function handleInsertRequest() {
          global $db_conn;

          //Getting the values from user and insert data into the table
          $tuple = array (
              ":bind1" => $_POST['insNo'],
              ":bind2" => $_POST['insName']
          );

          $alltuples = array (
              $tuple
          );

          executeBoundSQL("insert into demoTable values (:bind1, :bind2)", $alltuples);
          OCICommit($db_conn);
      }

      function handleGenerateRequest() {
          global $db_conn;
          global $rentals_status;
          global $returns_status;
          echo "<br></br>";
          if (isset($_GET['reportType']) && $_GET['reportType']=="rentals") {
            $rentals_status = 'checked';
            printRentals();
          } else if (isset($_GET['reportType']) && $_GET['reportType']=="returns") {
            $returns_status = 'checked';
            printReturns();
          }
      }

      function handleCountRequest() {
          global $db_conn;

          $rentals = executePlainSQL("SELECT Count(*) FROM rental");
          if (($row = oci_fetch_row($rentals)) != false) {
              echo "<br> The number of tuples in rental: " . $row[0] . "<br>";
          }
          $returns = executePlainSQL("SELECT Count(*) FROM return");
          if (($row = oci_fetch_row($returns)) != false) {
              echo "<br> The number of tuples in return: " . $row[0] . "<br>";
          }
      }

      // HANDLE ALL POST ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
      function handlePOSTRequest() {
          if (connectToDB()) {
              if (array_key_exists('resetTablesRequest', $_POST)) {
                  handleResetRequest();
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
                  handleCountRequest();
              } else if (array_key_exists('generateReport', $_GET)) {
                  handleGenerateRequest();
              }

              disconnectFromDB();
          }
      }
  ?>
        <h2>Generate reports</h2>
        <form method="GET" action="clerk_generate_report.php"> <!--refresh page when submitted-->
            <input type="hidden" id="generateReportRequest" name="generateReportRequest">
            Type: <input type="radio" name="reportType" value="rentals"> Rentals
            <input type="radio" name="reportType" value="returns"> Returns<br><br />
            Branch:
                  <?php
                    connectToDB();
                    $branches = executePlainSQL("SELECT branch.location, branch.city FROM branch ORDER BY branch.city, branch.location");
                    echo  '<select name="branch"  multiple="no">';
                    $branchNo = 0;
                    echo "<option value=\"all\">All locations</option>";
                    while ($row = OCI_Fetch_Array($branches, OCI_RETURN_NULLS+OCI_ASSOC))
                    {
                        echo "<option value=\"". $row['LOCATION']. "," .$row['CITY'] . "\">" . $row['LOCATION'] . ", " . $row['CITY'] . "</option>";
                    }
                    echo '</select>';
                  ?>
                <br><br/>
            <input type="submit" value="Generate" name="generateReport"></p>
        </form>

        <?php
        if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest']) || isset($_GET['generateReportRequest'])) {
            handleGETRequest();
            }
         ?>
	</body>
</html>
