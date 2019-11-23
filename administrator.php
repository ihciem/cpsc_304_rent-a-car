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
      <h2>Manipulate This Database</h2>
      <hr />
      <!--View All Tables-->
      <h2>View all Tables</h2>
      <form method="GET" action="administrator.php"> <!--refresh page when submitted-->
          <input type="hidden" id="showAllTablesRequest" name="showAllTablesRequest">
          <p><input type="submit" value="Show Tables" name="showAllTables"></p>
      </form>

      <hr />
        <!--Add data to specific table-->
        <h2>Insert Values into a Table</h2>
        <form method="POST" action="customer_search.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            Value: <input type="text" name="insValue"> <br /><br />
            Column: <input type="text" name="insValue"> <br /><br />
            Table: <input type="text" name="insTableName"> <br /><br />

            <input type="submit" value="Insert" name="insertSubmit"></p>
        </form>

        <hr />

        <!--Delete data in a specific table-->
        <h2>Delete Data from a Table</h2>
        <form method="GET" action="administrator.php"> <!--refresh page when submitted-->
          <input type="hidden" id="showTableRequest" name="showTableRequest">
          Table Name: <input type="text" name="insDTableName"> <br /><br />
          Condition
          <p><input type="submit" value="Show Table" name="showTable"></p>
          
      </form>

        
        <hr />
        <!--Update data in a specific table-->
        <h2>Update Values in a Table</h2>

        <form method="POST" action="customer_search.php"> <!--refresh page when submitted-->
            <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
            Old Name: <input type="text" name="oldName"> <br /><br />
            New Name: <input type="text" name="newName"> <br /><br />
            Table Name: <input type="text" name="insDTableName"> <br /><br />

            <input type="submit" value="Update" name="updateSubmit"></p>
        </form>

        <hr />
        <!--View data in a specific table-->
        <h2>View Data from a Table</h2>
        <form method="GET" action="administrator.php"> <!--refresh page when submitted-->
          <input type="hidden" id="showTableRequest" name="showTableRequest">
          Table Name: <input type="text" name="insVTableName"> <br /><br />
          <p><input type="submit" value="Show Table" name="showTable"></p>
          
      </form>

      <hr />

      <?php
      //this tells the system that it's no longer just parsing html; it's now parsing PHP
      include 'connectToDB.php';

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

      function disconnectFromDB() {
          global $db_conn;

          debugAlertMessage("Disconnect from Database");
          OCILogoff($db_conn);
      }

      function handleShowAllTablesRequest() {
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
              if (array_key_exists('showAllTables', $_GET)) {
                  handleShowAllTablesRequest();
              }

              disconnectFromDB();
          }
      }

//      if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
//          handlePOSTRequest();
//      } else
      if (isset($_GET['showAllTablesRequest'])) {
          handleGETRequest();
      }
      ?>
	</body>
</html>