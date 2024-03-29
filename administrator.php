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
        <!--Add data to specific table-->
        <h2>Insert Values into a Table</h2>
        <form action="insert.php">
          <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
          
          <p><input type="submit" value="Click me to insert a tuple"></p>
      </form>

        <hr />

        <!--Delete data in a specific table-->
        <h2>Delete Data from a Table</h2>
        <form method="POST" action="administrator.php"> <!--refresh page when submitted-->
          <input type="hidden" id="deleteTupleRequest" name="deleteTupleRequest">
          Table Name: <input type="text" name="insDTableName"> <br /><br />
          Column Name: <input type="text" name="insDColumnName"> <br /><br />
          Value: <input type="text" name="insDValue"> <br /><br />
          
          <p><input type="submit" value="Delete" name="deleteTuple"></p>
          
      </form>

        
        <hr />
        <!--Update data in a specific table-->
        <h2>Update Values in a Table</h2>

        <form method="POST" action="administrator.php"> <!--refresh page when submitted-->
            <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
            Table Name: <input type="text" name="insUTableName"> <br /><br />
            Column Name: <input type="text" name="insWhere"> <br /><br />
            Old Value: <input type="text" name="insWhereValue"> <br /><br />
            New Value: <input type="text" name="newValue"> <br /><br />
            <input type="submit" value="Update" name="updateSubmit"></p>
        </form>


        <hr />
        <!--View All Tables-->
        <h2>View all Tables</h2>
        <form method="GET" action="administrator.php"> <!--refresh page when submitted-->
            <input type="hidden" id="showAllTablesRequest" name="showAllTablesRequest">
            <p><input type="submit" value="Show Tables" name="showAllTables"></p>
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

      function handleShowAllTablesRequest() {
          global $db_conn;

          $result1 = executePlainSQL("SELECT * FROM branch ORDER BY city");
          $result2 = executePlainSQL("SELECT * FROM vehicleType ORDER BY vtname");
          $result3 = executePlainSQL("SELECT * FROM vehicle ORDER BY vid");
          $result4 = executePlainSQL("SELECT * FROM customer ORDER BY dlicense");
          $result5 = executePlainSQL("SELECT * FROM reservation ORDER BY confno");
          $result6 = executePlainSQL("SELECT * FROM rental ORDER BY rentid");
          $result7 = executePlainSQL("SELECT * FROM return ORDER BY rentid");
          
          echo "<br> <strong><p style=\"font-size:35px\">Branch</p></strong>";
          printResult($result1);

          echo "<br> <strong><p style=\"font-size:35px\">VehicleType</p></strong>";
          printResult($result2);

          echo "<br> <strong><p style=\"font-size:35px\">Vehicle</p></strong>";
          printResult($result3);

          echo "<br> <strong><p style=\"font-size:35px\">Customer</p></strong>";
          printResult($result4);

          echo "<br> <strong><p style=\"font-size:35px\">Reservation</p></strong>";
          printResult($result5);

          echo "<br> <strong><p style=\"font-size:35px\">Rental</p></strong>";
          printResult($result6);

          echo "<br> <strong><p style=\"font-size:35px\">Return</p></strong>";
          printResult($result7);
      }

      function handleShowTableRequest(){
          global $db_conn;

          $table = $_GET['insVTableName'];

          $result = executePlainSQL("SELECT * FROM $table ");

          echo "<strong><p style=\"font-size:35px\">$table</p></strong><br>";
          printResult($result);
      }

      function handleDeleteTupleRequest(){
          global $db_conn;

          $dt_name = $_POST['insDTableName'];
          $c_name = $_POST['insDColumnName'];
          $pk = $_POST['insDValue'];

          executePlainSQL("DELETE FROM $dt_name WHERE $c_name ='" . $pk . "'");
          OCI_Commit($db_conn);
      }

      function handleUpdateRequest() {
        global $db_conn;

        $ut_name = $_POST['insUTableName'];
        $cc = $_POST['insWhere'];
        $cv = $_POST['insWhereValue'];
        $newval = $_POST['newValue'];
        
        // you need the wrap the old name and new name values with single quotations
        executePlainSQL("UPDATE $ut_name SET $cc='" . $newval . "' WHERE $cc ='" . $cv . "'");
        OCICommit($db_conn);
    }

      // HANDLE ALL POST ROUTES
      // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
      function handlePOSTRequest() {
          if (connectToDB()) {
            if (array_key_exists('deleteTupleRequest', $_POST)) {
                handleDeleteTupleRequest();
            } else if (array_key_exists('updateQueryRequest', $_POST)) {
                handleUpdateRequest();
            }

            disconnectFromDB();
          }
      }

      // HANDLE ALL GET ROUTES
      // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
      function handleGETRequest() {
          if (connectToDB()) {
              if (array_key_exists('showAllTablesRequest', $_GET)) {
                  handleShowAllTablesRequest();
              } else if (array_key_exists('showTableRequest', $_GET)) {
                handleShowTableRequest();
            }

              disconnectFromDB();
          }
      }

     if (isset($_POST['deleteTuple']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
          handlePOSTRequest();
     } else if (isset($_GET['showAllTables']) || isset($_GET['showTable'])) {
          handleGETRequest();
      }
      ?>
	</body>
</html>
