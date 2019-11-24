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
<h2>New Customer</h2>

<hr />

<h2>Please Enter Personal Information</h2>
<form method="POST" action="customer_new.php"> <!--refresh page when submitted-->
    <input type="hidden" id="createCustomerRequest" name="createCustomerRequest">
    Driver's License: <input type="text" name="dLicense" value="<?php echo ((isset($_GET["dLicense"]))?htmlspecialchars($_GET["dLicense"]):""); ?>"> <br /><br />
    Name: <input type="text" name="name"> <br /><br />
    Address: <input type="text" name="address"> <br /><br />
    Cellphone: <input type="tel" name="cellphone" pattern="[0-9]{10}"><small> Format: 1234567890</small><br /><br />
    <input type="submit" value="Create Customer" name="createCustomer"></p>
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

function handleCreateCustomerRequest() {
    global $db_conn;

    //Getting the values from user and insert data into the table
    $tuple = array (
        ":bind1" => $_POST['dLicense'],
        ":bind2" => $_POST['name'],
        ":bind3" => $_POST['address'],
        ":bind4" => $_POST['cellphone']
    );

    $alltuples = array (
        $tuple
    );

    executeBoundSQL("INSERT INTO customer VALUES (:bind1, :bind2, :bind3, :bind4)", $alltuples);
    OCICommit($db_conn);
    disconnectFromDB();
    header("Location: https://www.students.cs.ubc.ca/~" . $_SESSION['username'] .  "/customer_search.php?dLicense=" . $_POST['dLicense']);
    die();
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
        if (array_key_exists('createCustomerRequest', $_POST)) {
            handleCreateCustomerRequest();
        }

        disconnectFromDB();
    }
}

if (isset($_POST['createCustomer'])) {
    handlePOSTRequest();
}
?>
</body>
</html>