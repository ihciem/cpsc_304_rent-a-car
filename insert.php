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
        <form action="administrator.php">
          <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
          
          <p><input type="submit" value="Back"></p>
      </form>
      <h2>Insert a Tuple into this Database</h2>
      <hr />
        <!--Add data to specific table-->
        
        <!--Delete data in a specific table-->
        <h2>Branch</h2>
        <form method="POST" action="insert.php"> <!--refresh page when submitted-->
          <input type="hidden" id="insertBRequest" name="insertBRequest">
          location: <input type="text" name="bLocation"> <br /><br />
          city: <input type="text" name="bCity"> <br /><br />
                 
          <p><input type="submit" value="Insert" name="insertB"></p>
          
      </form>

        
        <hr />
        
        <h2>VehicleType</h2>
        <form method="POST" action="insert.php"> <!--refresh page when submitted-->
          <input type="hidden" id="insertVTRequest" name="insertVTRequest">
          vtname: <input type="text" name="vtVtname"> <br /><br />
          features: <input type="text" name="features"> <br /><br />
          wrate: <input type="text" name="wrate"> <br /><br />
          drate: <input type="text" name="drate"> <br /><br />
          hrate: <input type="text" name="hrate"> <br /><br />
          wirate: <input type="text" name="wirate"> <br /><br />
          dirate: <input type="text" name="dirate"> <br /><br />
          hirate: <input type="text" name="hirate"> <br /><br />
          krate: <input type="text" name="krate"> <br /><br />
          
          <p><input type="submit" value="Insert" name="insertVT"></p>
          
      </form>

      <hr />

      <h2>Vehicle</h2>
        <form method="POST" action="insert.php"> <!--refresh page when submitted-->
          <input type="hidden" id="insertVRequest" name="insertBRequest">
          vid: <input type="text" name="vid"> <br /><br />
          vlicense: <input type="text" name="vVlicense"> <br /><br />
          make: <input type="text" name="make"> <br /><br />
          model: <input type="text" name="model"> <br /><br />
          color: <input type="text" name="color"> <br /><br />
          odometer: <input type="text" name="vOdometer"> <br /><br />
          status: <input type="text" name="status"> <br /><br />
          vtname: <input type="text" name="vVtname"> <br /><br />
          location: <input type="text" name="vLocation"> <br /><br />
          city: <input type="text" name="vCity"> <br /><br />
          
          <p><input type="submit" value="Insert" name="insertV"></p>
          
      </form>

      <hr />

      <h2>Customer</h2>
        <form method="POST" action="insert.php"> <!--refresh page when submitted-->
          <input type="hidden" id="insertCRequest" name="insertCRequest">
          dlicense: <input type="text" name="cDlicense"> <br /><br />
          name: <input type="text" name="name"> <br /><br />
          address: <input type="text" name="address"> <br /><br />
          cellphone: <input type="text" name="cellphone"> <br /><br />   

          <p><input type="submit" value="Insert" name="insertC"></p>
          
      </form>

      <hr />

      <h2>Reservation</h2>
        <form method="POST" action="insert.php"> <!--refresh page when submitted-->
          <input type="hidden" id="insertResRequest" name="insertResRequest">
          confno: <input type="text" name="resConfno"> <br /><br />
          vtname: <input type="text" name="resVtname"> <br /><br />
          dlicense: <input type="text" name="resDlicense"> <br /><br />
          fromdt: <input type="datetime-local" name="resFromdt"> <br /><br />
          todt: <input type="datetime-local" name="resTodt"> <br /><br />
          
          <p><input type="submit" value="Insert" name="insertRes"></p>
          
      </form>

      <hr />

      <h2>Rental</h2>
        <form method="POST" action="insert.php"> <!--refresh page when submitted-->
          <input type="hidden" id="insertRenRequest" name="insertRenRequest">
          rentid: <input type="text" name="rentalRentid"> <br /><br />
          cardno: <input type="text" name="cardno"> <br /><br />
          odometer: <input type="text" name="rentalOdometer"> <br /><br />
          vlicense: <input type="text" name="rentalVlicense"> <br /><br />
          fromdt: <input type="datetime-local" name="rentalFromdt">  <br /><br />
          todt: <input type="datetime-local" name="rentalTodt"> <br /><br />
          dlicense: <input type="text" name="rentalDlicense"> <br /><br />
          confno: <input type="text" name="rentalConfno"> <br /><br />
          
          <p><input type="submit" value="Insert" name="insertRen"></p>
          
      </form>

      <hr />

      <h2>Return</h2>
        <form method="POST" action="insert.php"> <!--refresh page when submitted-->
          <input type="hidden" id="insertRetRequest" name="insertRetRequest">
          rentid: <input type="text" name="returnRentid"> <br /><br />
          returndt: <input type="datetime-local" name="returnReturndt"> <br /><br />
          odometer: <input type="text" name="returnOdometer"> <br /><br />
          fulltank: <input type="radio" name="fulltank" value="YES" required> Yes
                    <input type="radio" name="fulltank" value="NO" required> No<br><br />
          
          <p><input type="submit" value="Insert" name="insertRet"></p>
          
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

      function handleBRequest(){
        global $db_conn;

          $tuple = array (
            ":bind1" => $_POST['bLocation'],
            ":bind2" => $_POST['bCity']
        );

        $alltuples = array (
            $tuple
        );

        executeBoundSQL("insert into return values (:bind1, :bind2)", $alltuples);
        OCICommit($db_conn);
      }

      function handleVTRequest(){
        global $db_conn;

        $tuple = array (
          ":bind1" => $_POST['vtVtname'],
          ":bind2" => $_POST['features'],
          ":bind3" => $_POST['wrate'],
          ":bind4" => $_POST['drate'],
          ":bind5" => $_POST['hrate'],
          ":bind6" => $_POST['wirate'],
          ":bind7" => $_POST['dirate'],
          ":bind8" => $_POST['hirate'],
          ":bind9" => $_POST['krate']

      );

      $alltuples = array (
          $tuple
      );

      executeBoundSQL("insert into return values (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6, :bind7, :bind8, :bind9)", $alltuples);
      OCICommit($db_conn);
    }

    
    function handleVRequest(){
        global $db_conn;

        $tuple = array (
          ":bind1" => $_POST['vid'],
          ":bind2" => $_POST['vVlicense'],
          ":bind3" => $_POST['make'],
          ":bind4" => $_POST['model'],
          ":bind5" => $_POST['color'],
          ":bind6" => $_POST['vOdometer'],
          ":bind7" => $_POST['status'],
          ":bind8" => $_POST['vVtname'],
          ":bind9" => $_POST['vLocation'],
          ":bind10" => $_POST['vCity']

      );

      $alltuples = array (
          $tuple
      );

      executeBoundSQL("insert into return values (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6, :bind7, :bind8, :bind9, :bind10)", $alltuples);
      OCICommit($db_conn);
    }

    function handleCRequest(){
        global $db_conn;

        $tuple = array (
          ":bind1" => $_POST['cDlicense'],
          ":bind2" => $_POST['name'],
          ":bind3" => $_POST['address'],
          ":bind4" => $_POST['cellphone']
          
      );

      $alltuples = array (
          $tuple
      );

      executeBoundSQL("insert into return values (:bind1, :bind2, :bind3, :bind4)", $alltuples);
      OCICommit($db_conn);
    }

    function handleResRequest(){
        global $db_conn;

        $tuple = array (
          ":bind1" => $_POST['resConfno'],
          ":bind2" => $_POST['resVtname'],
          ":bind3" => $_POST['resDlicense'],
          ":bind4" => $_POST['resFromdt'],
          ":bind5" => $_POST['resTodt']

      );

      $alltuples = array (
          $tuple
      );

      executeBoundSQL("insert into return values (:bind1, :bind2, :bind3, :bind4, :bind5)", $alltuples);
      OCICommit($db_conn);
    }

    function handleRenRequest(){
        global $db_conn;

        $tuple = array (
          ":bind1" => $_POST['rentalRentid'],
          ":bind2" => $_POST['cardno'],
          ":bind3" => $_POST['rentalOdometer'],
          ":bind4" => $_POST['rentalVlicense'],
          ":bind5" => $_POST['rentalFromdt'],
          ":bind6" => $_POST['rentalTodt'],
          ":bind7" => $_POST['rentalDlicense'],
          ":bind8" => $_POST['rentalConfno']

      );

      $alltuples = array (
          $tuple
      );

      executeBoundSQL("insert into return values (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6, :bind7, :bind8)", $alltuples);
      OCICommit($db_conn);
    }

    function handleRetRequest(){
        global $db_conn;

        $tuple = array (
          ":bind1" => $_POST['returnRentid'],
          ":bind2" => $_POST['returnReturnid'],
          ":bind3" => $_POST['returnOdometer'],
          ":bind4" => $_POST['fulltank']
      );

      $alltuples = array (
          $tuple
      );

      executeBoundSQL("insert into return values (:bind1, :bind2, :bind3, :bind4)", $alltuples);
      OCICommit($db_conn);
    }

      // HANDLE ALL POST ROUTES
      // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
      function handlePOSTRequest() {
          if (connectToDB()) {
            if (array_key_exists('insertBRequest', $_POST)) {
                handleBRequest();
            } else if (array_key_exists('insertVTRequest', $_POST)) {
                handleVTRequest();
            } else if (array_key_exists('insertVRequest', $_POST)) {
                handleVRequest();
            } else if (array_key_exists('insertCRequest', $_POST)) {
                handleCRequest();
            } else if (array_key_exists('insertResRequest', $_POST)) {
                handleResRequest();
            } else if (array_key_exists('insertRenRequest', $_POST)) {
                handleRenRequest();
            } else if (array_key_exists('insertRetRequest', $_POST)) {
                handleRetRequest();
            }

            disconnectFromDB();
          }
      }

      

     if (isset($_POST['insertB']) || isset($_POST['insertVT']) || isset($_POST['insertV']) || isset($_POST['insertC']) || isset($_POST['insertRes']) || isset($_POST['insertRen']) || isset($_POST['insertRet'])) {
          handlePOSTRequest();
     }
      ?>
	</body>
</html>
