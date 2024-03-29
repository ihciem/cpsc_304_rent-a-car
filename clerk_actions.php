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
      <h2>Welcome back to work!</h2>
      <hr />
      <form method="POST" action="clerk_rental.php">
          <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
          <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
          <p><input type="submit" value="Rent a Vehicle" name="reset"></p>
      </form>
      <form method="POST" action="clerk_return.php">
          <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
          <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
          <p><input type="submit" value="Return a Vehicle" name="reset"></p>
      </form>
      <form method="POST" action="clerk_generate_report.php">
          <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
          <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
          <p><input type="submit" value="Generate Reports" name="reset"></p>
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

          $statement = oci_parse($db_conn, $cmdstr);
          //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

          if (!$statement) {
              echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
              $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
              echo htmlentities($e['message']);
              $success = False;
          }

          $r = oci_execute($statement, OCI_DEFAULT);
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

      function handleShowTableRequest() {
          global $db_conn;

          $result1 = executePlainSQL("SELECT * FROM branch");
          $result2 = executePlainSQL("SELECT * FROM vehicleType");
          $result3 = executePlainSQL("SELECT * FROM branch");
          $result4 = executePlainSQL("SELECT * FROM vehicleType");
          $result5 = executePlainSQL("SELECT * FROM branch");
          $result6 = executePlainSQL("SELECT * FROM vehicleType");
          $result7 = executePlainSQL("SELECT * FROM branch");
          
          echo "<br> Branch";
          printResult($result1);

          echo "<br> VehicleType";
          printResult($result2);

          echo "<br> Vehicle";
          printResult($result3);

          echo "<br> Customer";
          printResult($result4);

          echo "<br> Reservation";
          printResult($result5);

          echo "<br> Rental";
          printResult($result6);

          echo "<br> Return";
          printResult($result7);
      }

      // HANDLE ALL POST ROUTES
      // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
      function handlePOSTRequest() {
          if (connectToDB()) {
              disconnectFromDB();
          }
      }

      // HANDLE ALL GET ROUTES
      // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
      function handleGETRequest() {
          if (connectToDB()) {
              if (array_key_exists('showTable', $_GET)) {
                  handleShowTableRequest();
              }

              disconnectFromDB();
          }
      }

//      if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
//          handlePOSTRequest();
//      } else
      if (isset($_GET['showTableRequest'])) {
          handleGETRequest();
      }
      ?>
	</body>
</html>
