<?php
session_start();

header('Content-Type: application/json');

// Fetch SNMP data

$data = getSNMPData($_SESSION["device-ip"], $_SESSION["device-type"], "public");

if ($data !== false) {
    echo json_encode(['success' => true, 'data' => $data]);
} else {
    echo json_encode(['success' => false, 'message' => 'SNMP fetch failed']);
}