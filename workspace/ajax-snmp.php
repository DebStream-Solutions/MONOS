<?php
session_start();
include "real-time-snmp.php";

header('Content-Type: application/json');

if (isset($_SESSION['device-type']) && $_SESSION['device-ip']) {
    $deviceType = $_SESSION['device-type'];
    $deviceIp = $_SESSION['device-ip'];

    if (!empty($deviceType) && !empty($deviceIp)) {
        $data = getRealTimeArray($deviceType, $deviceIp);
    } else {
        $error = "Error: Missing either IP or Device Type";
        $data = false;
    }
} else {
    $data = false;
}

if ($data !== false) {
    echo json_encode(['success' => true, 'data' => $data]);
} else {
    echo json_encode(['success' => false, 'message' => 'SNMP fetch failed']);
}