<?php
// Encapsulate connectToDB() call in each php file to one file
// Allows us to change which database to login to
function connectToDB() {
    global $db_conn;

    session_start();
    $_SESSION['username'] = "calwin98";
    $_SESSION['password'] = "a14604169";

    // Your username is ora_(CWL_ID) and the password is a(student number). For example,
    // ora_platypus is the username and a12345678 is the password.

    $db_conn = OCILogon("ora_" . $_SESSION['username'], $_SESSION['password'], "dbhost.students.cs.ubc.ca:1522/stu");

    if ($db_conn) {
        debugAlertMessage("Database is Connected");
        return true;
    } else {
        debugAlertMessage("Cannot connect to Database");
        $e = OCI_Error(); // For OCILogon errors pass no handle
        echo htmlentities($e['message']);
        return false;
    }
}

function disconnectFromDB() {
    global $db_conn;
    debugAlertMessage("Disconnect from Database");
    OCILogoff($db_conn);
}