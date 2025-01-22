<?php

session_start();

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


$servername = "localhost";
$username = "root";
$password = "y%8YB@*T$@7dTPhCfhge9xNJ9fxTvEmYs8sSzrJ6";
$dbname = "monos";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


# FUNCTIONS

function query($query, $returnStatus = false) {
    global $conn;

    # DO (INSERT/UPDATE/DELETE/SELECT)
    $status = $conn->query($query);

    if (strpos($query, "SELECT")) {
        $data = $status->fetch_all(MYSQLI_ASSOC);

        if ($returnStatus) {
            $request = ["data" => $data, "status" => $status];
        } else {
            $request = $data;
        }
    } else {
        $request = $status;
    }

    return $request;
}

?>