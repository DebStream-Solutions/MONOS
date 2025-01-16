<?php

function snmpFormat($snmp_arr, $separator) {
    $snmp_formatted_arr = [];

    if ($snmp_arr !== false) {
        foreach ($snmp_arr as $key => $value) {
            $value = preg_replace('/^.*: :/', '', $value);
            $value = explode($separator, $value)[1];
            $snmp_formatted_arr[] = $value;
        }
    }

    return $snmp_formatted_arr;
}


// SNMP OIDs for InOctets and OutOctets
$interface = "1"; // Change this to the correct interface number
$host = "192.168.1.1";
$community = "public";

$inOid = "IF-MIB::ifInOctets.$interface";
$outOid = "IF-MIB::ifOutOctets.$interface";

// Globals to store upload/download data
$GLOBALS['networkData'] = [];

// Function to get current SNMP data
function getSnmpData($oid) {
    global $host, $community;

    $oid_req = @snmpwalk($host, $community, $oid);
    $oid_res = snmpFormat($oid_req, "Counter32: ");

    return $oid_res;
}

// Function to calculate data
function updateNetworkData() {
    global $inOid, $outOid;

    // Fetch the current values of InOctets and OutOctets
    $currentIn = getSnmpData($inOid);
    $currentOut = getSnmpData($outOid);
    $timestamp = time();

    // Get the previous data
    $previousData = end($GLOBALS['networkData']);

    // Calculate rates (bytes per second)
    if ($previousData) {
        $timeDiff = $timestamp - $previousData['timestamp'];
        $downloadRate = ($currentIn - $previousData['in']) / $timeDiff; // Bytes/sec
        $uploadRate = ($currentOut - $previousData['out']) / $timeDiff; // Bytes/sec
    } else {
        $downloadRate = $uploadRate = 0;
    }

    // Store the current data
    $GLOBALS['networkData'][] = [
        'timestamp' => $timestamp,
        'in' => $currentIn,
        'out' => $currentOut,
        'downloadRate' => round($downloadRate, 2),
        'uploadRate' => round($uploadRate, 2)
    ];
}

// Update network data every second (example: use a cron or manual refresh for real-time)
updateNetworkData();

// Prepare data for Google Chart
$chartData = [["Time", "Download (Bytes/sec)", "Upload (Bytes/sec)"]];
foreach ($GLOBALS['networkData'] as $data) {
    $chartData[] = [
        date("H:i:s", $data['timestamp']),
        $data['downloadRate'],
        $data['uploadRate']
    ];
}

// Encode the data to JSON for use in Google Charts
$jsonChartData = json_encode($chartData);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Network Traffic</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {packages: ['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable(<?php echo $jsonChartData; ?>);

            var options = {
                title: 'Network Traffic',
                curveType: 'function',
                legend: { position: 'bottom' },
                vAxis: { title: 'Bytes/sec' },
                hAxis: { title: 'Time' }
            };

            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
            chart.draw(data, options);
        }
    </script>
</head>
<body>
    <div id="curve_chart" style="width: 900px; height: 500px;"></div>
</body>
</html>
