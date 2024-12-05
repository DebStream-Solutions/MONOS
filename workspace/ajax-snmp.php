<?php
session_start();
include "snmp.php";

header('Content-Type: application/json');

// Fetch SNMP data


# TODO - only paste the real time data not everything
$data = getSNMPData($_SESSION["device-ip"], $_SESSION["device-type"], "public");

if ($data !== false) {
    echo json_encode(['success' => true, 'data' => $data]);
} else {
    echo json_encode(['success' => false, 'message' => 'SNMP fetch failed']);
}