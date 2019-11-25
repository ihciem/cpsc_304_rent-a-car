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

  <?php //this tells the system that it's no longer just parsing html; it's now parsing PHP

  include 'connectToDB.php';
  include 'printResult.php';

  $success = True; //keep track of errors so it redirects the page only if there are no errors
  $db_conn = NULL; // edit the login credentials in connectToDB()
  $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

  // set timezone and get today's date
  date_default_timezone_set('America/Los_Angeles');
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

  // generates today's rentals report
  function generateRentals() {
    global $date;
    $branchSelected = false;
    echo "<b><u>$date</u></b></b>" .strtoupper("<b><u> Daily Rentals Report</u></b><br>");
    if ($_GET['branch']=="all") {
      echo strtoupper("<b>for all branches</b>");
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
    $noPerBranch = executePlainSQL("SELECT v.city, v.location, COUNT(*) FROM rentalsToday rT, vehicle v WHERE rT.vlicense = v.vlicense GROUP BY v.city, v.location");
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
    $totalRentals= executePlainSQL("SELECT Count(*) FROM rental where to_char(cast(rental.fromdt as date), 'YYYY/MM/DD')='$date'");
    if ($row = OCI_Fetch_Array($totalRentals, OCI_BOTH)) {
      echo "<br>Company Total Number of Rentals Today: " . $row[0]."</br>";
    } else {
      echo "<br>Company Total Number of Rentals Today: 0</br>";
    }
    echo strtoupper("<br><b><u>DATA</u></b><br>");
    $result = executePlainSQL("SELECT v.city, v.location, v.vtname, v.vlicense, v.vid, v.make, v.model, v.color, v.odometer, v.status FROM rentalsToday rT, rental rent, vehicle v WHERE rT.rentid = rent.rentid AND rent.vlicense = v.vlicense ORDER BY v.city, v.location, v.vtname");
    if ($branchSelected) {
      echo "<br>Retrieved vehicle data for branch<br>";
    } else {
      echo "<br>Retrieved vehicle data<br>";
    }
    printResult($result);
  }
  function generateReturns() { //prints results from a select statement
    global $date;
    $branchSelected = false;
    echo "<b><u>$date</u></b></b>" .strtoupper("<b><u> Daily Returns Report</u></b><br>");
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
    $noPerCategory = executePlainSQL("SELECT v.vtname, COUNT(*), SUM(rT.value) FROM returnsToday rT, rental rent, vehicle v WHERE rT.rentid = rent.rentid AND rent.vlicense = v.vlicense GROUP BY v.vtname ORDER BY v.vtname");
    echo "<br>Number of Returns and Revenue Per Vehicle Category<br>";
    printResult($noPerCategory);
    // subtotals for the number of vehicles and revenue per branch;
    $subTotals = executePlainSQL("SELECT v.city, v.location, COUNT(*), SUM(rT.value) FROM returnsToday rT, rental rent, vehicle v WHERE rT.rentid = rent.rentid AND rent.vlicense = v.vlicense GROUP BY v.city, v.location ORDER BY v.city, v.location");
    if ($branchSelected) {
      if ($row = OCI_Fetch_Array($subTotals, OCI_BOTH)) {
        echo "<br>Branch Subtotal Today: ". $row[3]. "<br>";
      } else {
        echo "<br>Branch Subtotal Today: 0<br>";
      }
    } else {
      echo "<br>Subtotals Per Branch<br>";
      printResult($subTotals);
    }
    // grandtotals for the day;
    $grandTotals = executePlainSQL("SELECT SUM(return.value) FROM return where to_char(cast(return.returndt as date), 'YYYY/MM/DD')='$date'");
    if ($row = OCI_Fetch_Array($grandTotals, OCI_BOTH)) {
      echo "<br>Company Grand Total Today: ". $row[0]. "<br>";
    } else {
      echo "<br>Company Grand Total Today: 0<br>";
    }
    echo strtoupper("<br><b><u>DATA</u></b><br>");
    $result = executePlainSQL("SELECT v.city, v.location, v.vtname, v.vlicense, v.vid, v.make, v.model, v.color, v.odometer, v.status FROM returnsToday rT, rental rent, vehicle v WHERE rT.rentid = rent.rentid AND rent.vlicense = v.vlicense ORDER BY v.city, v.location, v.vtname");
    if ($branchSelected) {
      echo "<br>Retrieved vehicle data for branch<br>";
    } else {
      echo "<br>Retrieved vehicle data<br>";
    }
    printResult($result);
  }

  function handleGenerateRequest() {
    global $db_conn;
    echo "<br></br>";
    if (isset($_GET['reportType']) && $_GET['reportType']=="rentals") {
      generateRentals();
    } else if (isset($_GET['reportType']) && $_GET['reportType']=="returns") {
      generateReturns();
    }
  }
  // HANDLE ALL GET ROUTES
  // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
  function handleGETRequest() {
    if (connectToDB()) {
      if (array_key_exists('generateReport', $_GET)) {
        handleGenerateRequest();
      }
      disconnectFromDB();
    }
  }

  ?>

  <h2>Generate reports</h2>
  <hr />
  <form method="GET" action="clerk_generate_report.php"> <!--refresh page when submitted-->
    <input type="hidden" id="generateReportRequest" name="generateReportRequest">
    Type: <input type="radio" name="reportType" <?php if (isset($_GET['reportType']) && $_GET['reportType']=="rentals") echo "checked";?> value="rentals" required> Rentals
    <input type="radio" name="reportType" <?php if (isset($_GET['reportType']) && $_GET['reportType']=="returns") echo "checked";?> value="returns" required> Returns<br><br />
    Branch:
    <?php
    include 'connectToDB.php';
    include 'printResult.php';
    connectToDB();
    $branches = executePlainSQL("SELECT branch.location, branch.city FROM branch ORDER BY branch.city, branch.location");
    echo  '<select name="branch" required multiple="no">';
    $branchNo = 0;
    echo "<option value=\"all\" selected>All locations</option>";
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
  if (isset($_GET['generateReportRequest'])) {
    handleGETRequest();
  }
  ?>

</body>
</html>
