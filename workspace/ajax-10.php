<?php
session_start();
include "real-time-snmp.php";

header('Content-Type: application/json');

# -- Functions ----

function device() {
    $error = "";

    if (isset($_SESSION['device-type']) && isset($_SESSION['device-ip']) && !empty($_SESSION['device-type']) && !empty($_SESSION['device-ip'])) {
        $deviceIp = $_SESSION['device-ip'];

        if (!empty($deviceIp)) {
            $data = getRealStateArray(false, $deviceIp);
        } else {
            $error = "Error: Missing either IP or Device Type";
            $data = false;
        }
    } elseif (isset($_SESSION['profile'])) {
        $profileId = $_SESSION['profile'];
    
        if (!empty($profileId)) {
            $data = getRealStateArray($profileId);
        } else {
            $error = "Error: Missing either IP or Device Type";
            $data = false;
        }
    } else {
        $data = false;
    }

    return ["data" => $data, "error" => $error];
}


# -- Calling Function ----

if (isset($_GET['func'])) {
    $func = $_GET['func'];
    if (function_exists($func)) {
        $array = $func();
        $data = $array["data"];
        $error = $array["error"];
    } else {
        $data = false;
    }
} else {
    $data = false;
}


if ($data !== false) {
    echo json_encode(['success' => true, 'data' => $data]);
} else {
    echo json_encode(['success' => false, 'message' => $error]);
}