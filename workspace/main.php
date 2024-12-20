<?php

include "db.php";
include "snmp.php";

function findValueByConditions($array, $conditions, $returnKey) {
    foreach ($array as $subArray) {
        $match = true;
        foreach ($conditions as $key => $value) {
            if (!isset($subArray[$key]) || $subArray[$key] != $value) {
                $match = false;
                break;
            }
        }
        if ($match) {
            return $subArray[$returnKey] ?? null;
        }
    }
    return null;
}

$currentUrl = "$_SERVER[REQUEST_URI]";


function issetReturn($variable) {
    return isset($variable) ? $variable : "";
}


# -- Network ----

function ping($host, $timeout = 1) {
    $output = [];
    $status = null;

    // Adjust the command based on the operating system
    if (stristr(PHP_OS, 'WIN')) { 
        // Windows
        $cmd = "ping -n 1 -w " . ($timeout * 1000) . " $host";
    } else {
        // Linux / macOS
        $cmd = "ping -c 1 -W $timeout $host";
    }

    // Execute the command
    exec($cmd, $output, $status);

    // Return true if the ping was successful
    return $status === 0 ? true : false;
}


# == DATABASE ==================

$devices = "SELECT * FROM devices";
$devices = $conn->query($devices);
$devices = $devices->fetch_all(MYSQLI_ASSOC);

$profiles = "SELECT * FROM profiles";
$profiles = $conn->query($profiles);
$profiles = $profiles->fetch_all(MYSQLI_ASSOC);

$templates = "SELECT * FROM templates";
$templates = $conn->query($templates);
$templates = $templates->fetch_all(MYSQLI_ASSOC);

$types = "SELECT * FROM types";
$types = $conn->query($types);
$types = $types->fetch_all(MYSQLI_ASSOC);

$oids = "SELECT * FROM oids";
$oids = $conn->query($oids);
$oids = $oids->fetch_all(MYSQLI_ASSOC);

$GLOBALS["devices"] = $devices;
$GLOBALS["profiles"] = $profiles;
$GLOBALS["templates"] = $templates;
$GLOBALS["types"] = $types;
$GLOBALS["oids"] = $oids;

# ===============================================


function listProfiles() {
    global $conn;
    
    $profiles = $GLOBALS["profiles"];
    $profileList = "";

    if (count($profiles) > 0) {

        foreach ($profiles as $key => $value) {
            $newUrl = "?profile=".$value['id']; 
            
            $devices = "SELECT COUNT(*) AS device_count FROM profileReleations WHERE profileId = {$value['id']}";
            $devices = $conn->query($devices);
            $devices = $devices->fetch_all(MYSQLI_ASSOC);

            $profileList .= '
                <a href="'.$newUrl.'">
                    <div>
                        <h3>'.$value['name'].'</h3>
                        <span>'.$devices[0]['device_count'].' monitored devices</span>
                    </div>
                </a>
            ';
        }
    } else {
        $profileList = "There are no profiles. Add profiles to see them here.";
    }

    return $profileList;
}


function profileContent() {
    $profileContent = '

    <div class="profile-wrap">
        <div class="profile-table">
        '.listProfiles().'
        </div>
    </div>

    ';

    return $profileContent;
}



function listDevices($profile) {
    global $conn;

    $devices = query("SELECT * FROM devices");
    $types = query("SELECT * FROM types");
    $deviceList = "";

    foreach ($devices as $key => $value) {
        $profileId = "SELECT profileId FROM profileReleations WHERE deviceId = ".$value["id"]." AND profileId = ".$profile;
        $profileId = $conn->query($profileId);
        $profileId = $profileId->fetch_all(MYSQLI_ASSOC);

        var_dump($profileId);

        if (!empty($profileId)) {
            $profileId = $profileId[0]["profileId"];

            $newUrl = "device/?profile=".$profile."&device=".$value['id'];
            $conditions = ["id" => $value['type']];
            $type_str = findValueByConditions($types, $conditions, "name");
            
            if ($profile === strval($profileId)) {
                $ping_state = ping($value['ip']) ? "online" : "offline";

                $deviceList .= '
                <a href="'.$newUrl.'">
                    <div class="device">
                        <img src="icons/'.$type_str.'.png" alt="">
                        <div>
                            <h3>'.$value['name'].'</h3>
                            <span>'.$value['ip'].'</span>
                        </div>
                        <div class="'.$ping_state.'"></div>
                    </div>
                </a>
            ';
            }
        } else {
            var_dump($profileId);
            $deviceList .= "There are no devices in this profile. Go and add some!";
        }
        
    }

    return $deviceList;
}



function monitorContent($profile) {
    $monitorContent = '

    <div class="monitor">
        <div id="net_chart"></div>
        <div class="devices">
            '.listDevices($profile).'
        </div>
    </div>

    ';

    return $monitorContent;
}


// --- EDIT ---------------

function editProfile($edit) {
    global $conn;

    if ($edit) {
        if (isset($_GET['profile']) && !empty($_GET['profile'])) {
            $profile = "SELECT * FROM profiles WHERE id = {$_GET['profile']}";
            $profile = $conn->query($profile);
            $profile = $profile->fetch_all(MYSQLI_ASSOC)[0];

            $_SESSION["profile"] = $profile;
        } else {
            $content = "There is a mistake! No profile to edit..";
        }

        # ERROR checking
        if (isset($_SESSION["error"])) {
            $error = $_SESSION["error"];
            $error_msg = "";
            if (is_array($error)) {
                foreach ($error as $key => $value) {
                    $error_msg .= $value;
                }
            } else {
                $error_msg = $error;
            }
        } else {
            $error_msg = "";
        }

        if (isset($profile)) {
            $content = '
            <div class="form-wrap">
                <div class="log">
                    <div class="login-wrap">
                        <h2>Edit profile</h2>
                        <div>
                            <form method=POST action="../../action/validate.php?profile='.$profile["id"].'">
                                <div class="input-fly">
                                    <div>
                                        <input type="text" id="name" name="name" value="'.$profile["name"].'">
                                        <label for="name">Name</label>
                                    </div>
                                </div>
                                <div>
                                    <input type="submit" name="submit" value="Edit">
                                </div>
                            </form>
                            <div class="error-msg">
                                '.$error_msg.'
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';
        }

    } else {
        if (isset($_SESSION['profile'])) {
            $profile = $_SESSION['profile'];
        } else {
            $profile = ["name" => "", "id" => ""];
        }

        if (isset($_SESSION["error"])) {
            $error = $_SESSION["error"];
            $error_msg = "";
            if (is_array($error)) {
                foreach ($error as $key => $value) {
                    $error_msg .= $value;
                }
            } else {
                $error_msg = $error;
            }
        } else {
            $error_msg = "";
        }

        $content = '
            <div class="form-wrap">
                <div class="log">
                    <div class="login-wrap">
                        <h2>Add profile</h2>
                        <div>
                            <form method=POST action="../../action/validate.php?profile">
                                <div class="input-fly">
                                    <div>
                                        <input type="text" id="name" name="name" value="'.$profile["name"].'">
                                        <label for="name">Name</label>
                                    </div>
                                </div>
                                <div>
                                    <input type="submit" name="submit" value="Add">
                                </div>
                            </form>
                            <div class="error-msg">
                                '.$error_msg.'
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';
    }

    return $content;
}


function editDevice($edit) {
    global $conn;

    if ($edit) {
        if (isset($_SESSION["device"])) {
            $device = $_SESSION['device'];
        } elseif (isset($_GET['device'])) {
            $device = "SELECT * FROM devices WHERE id = {$_GET['device']}";
            $device = $conn->query($device);
            $device = $device->fetch_all(MYSQLI_ASSOC);

            $_SESSION["device"] = $device;
        } else {
            $content = "There is a mistake! No device to edit..";
        }

        if (isset($device)) {
            $profileNameArr = "SELECT * FROM profiles";
            $profileNameArr = $conn->query($profileNameArr);
            $profileNameArr = $profileNameArr->fetch_all(MYSQLI_ASSOC);
            
            $profileList = "";
            $i = 0;

            foreach ($profileNameArr as $key => $value) {
                $i += 1;
                #$profileList .= '<label data-item="'.$value["name"].'" data-id="'.$value["id"].'"><input type="checkbox" value="'.$value["name"].'">'.$value["name"].'</label>';
                $profileList .= '<label data-item="'.$value["name"].'" data-id="'.$value["id"].'"><input type="checkbox" name="'.$device.$i.'" value="'.$value["id"].'">'.$value["name"].'</label>';
            }

            $typeArr = "SELECT * FROM types";
            $typeArr = $conn->query($typeArr);
            $typeArr = $typeArr->fetch_all(MYSQLI_ASSOC);

            $typeList = "";
            $i = 1;
            foreach ($typeArr as $key => $value) {
                $typeList .= '<option value="'.$i.'">'.$value["name"].'</option>';
                $i += 1;
            }

            $content = '
                <div class="form-wrap">
                    <div class="log">
                        <div class="login-wrap">
                            <h2>Add device</h2>
                            <div id="device-form">
                                <form method=POST action="../../action/validate.php?device">
                                    <div class="input-fly">
                                        <div>
                                            <input type="text" id="name" name="name" value="'.$device["name"].'">
                                            <label for="name">Name</label>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 for="profile">Select profiles</h3>
                                        <div class="dropdown">
                                            <div class="selected-items-container">
                                                <button type="button" class="add-button">+</button>
                                            </div>
                                            <div class="input-container">
                                                <input type="text" class="dropdown-input" placeholder="Select items">
                                                <span class="dropdown-arrow"><img src="../../icons/dropdown.png" alt="arrow"></span>
                                            </div>
                                            <div class="dropdown-content">
                                                '.$profileList.'
                                            </div>
                                            <div class="hidden-inputs">
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-fly">
                                        <div>
                                            <input type="text" id="ip" name="ip" value="'.$device["ip"].'">
                                            <label for="ip">IP Address</label>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="types">Type</label>
                                        <select id="types" name="type">
                                            '.$typeList.'
                                        </select>
                                    </div>
                                    <div>
                                        <input type="submit" name="submit" value="Add">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            ';
        }

    } else {
        if (isset($_SESSION['device'])) {
            $device = $_SESSION['device'];
        } else {
            $device = [
                "id" => "",
                "name" => "",
                "ip" => "",
                "type" => ""
            ];
        }

        if (isset($device)) {
            $profileNameArr = "SELECT * FROM profiles";
            $profileNameArr = $conn->query($profileNameArr);
            $profileNameArr = $profileNameArr->fetch_all(MYSQLI_ASSOC);
            
            $profileList = "";
            $i = 1;
            foreach ($profileNameArr as $key => $value) {
                #$profileList .= '<label data-item="'.$value["name"].'" data-id="'.$value["id"].'"><input type="checkbox" value="'.$value["name"].'">'.$value["name"].'</label>';
                $profileList .= '<label data-item="'.$value["name"].'" data-id="'.$value["id"].'"><input type="checkbox" name="profile'.$i.'" value="'.$value["id"].'">'.$value["name"].'</label>';
                $i += 1;
            }


            $typeArr = "SELECT * FROM types";
            $typeArr = $conn->query($typeArr);
            $typeArr = $typeArr->fetch_all(MYSQLI_ASSOC);

            $typeList = "";
            $i = 1;
            foreach ($typeArr as $key => $value) {
                $typeList .= '<option value="'.$i.'">'.$value["name"].'</option>';
                $i += 1;
            }

            $content = '
                <div class="form-wrap">
                    <div class="log">
                        <div class="login-wrap">
                            <h2>Add device</h2>
                            <div id="device-form">
                                <form method=POST action="../../action/validate.php?device">
                                    <div class="input-fly">
                                        <div>
                                            <input type="text" id="name" name="name" value="'.$device["name"].'">
                                            <label for="name">Name</label>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 for="profile">Select profiles</h3>
                                        <div class="dropdown">
                                            <div class="selected-items-container">
                                                <button type="button" class="add-button">+</button>
                                            </div>
                                            <div class="input-container">
                                                <input type="text" class="dropdown-input" placeholder="Select items">
                                                <span class="dropdown-arrow"><img src="../../icons/dropdown.png" alt="arrow"></span>
                                            </div>
                                            <div class="dropdown-content">
                                                '.$profileList.'
                                            </div>
                                            <div class="hidden-inputs">
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-fly">
                                        <div>
                                            <input type="text" id="ip" name="ip" value="'.$device["ip"].'">
                                            <label for="ip">IP Address</label>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="types">Type</label>
                                        <select id="types" name="type">
                                            '.$typeList.'
                                        </select>
                                    </div>
                                    <div>
                                        <input type="submit" name="submit" value="Add">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            ';
        }
    }

    return $content;
}



// PHP - SNMP

/*
OIDS

Device type:


--PC--
Linux PC - .1.3.6.1.4.1.8072.3.2.10
Windows  - .1.3.6.1.4.1.8072.3.2.13
MACOS X  - .1.3.6.1.4.1.8072.3.2.16


--Router--
check  - .1.3.6.1.2.1.4.21


*/


/*
<script type="text/javascript">
        //
        let ms = Date.now();
        const d = new Date();
        let time = d.getTime();

        if (time )

        function timeTable(start, multiply) {
            arr = [];
            x = start;
            while (x < 24 - multiply) {
                x += multiply;
                arr.push(x);
            }

            return arr;
        }

        function chartData(param) {
            dataArray = [];
            timeArray = timeTable(0, 4);


            for (timeCol in timeArray) {

            }

            ['Time', 'Download ', 'Upload ', {type: 'string', role: 'style'}],
            ['00:00',  1.2,      0.1],
            ['04:00',  0.7,      0.2],
            ['08:00',  4.8,      3.2],
            ['12:00',  7.1,      5.0],
            ['16:00',  7.3,      7.2],
            ['20:00',  3.6,      2.7]
        } //


        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Time', 'Download ', 'Upload '],
                ['00:00',  1.2,      0.1],
                ['04:00',  0.7,      0.2],
                ['08:00',  4.8,      3.2],
                ['12:00',  7.1,      5.0],
                ['16:00',  7.3,      7.2],
                ['20:00',  3.6,      2.7]
            ]);

            var options = {
                title: 'Network traffic',
                legend: {position: "bottom"},
                vAxis: {title: 'Speed (Mbps)'},
                colors: ['#9b21ff', '#5900ff']
            };

            var chart = new google.visualization.LineChart(document.getElementById('net_chart'));

            chart.draw(data, options);

            // Add a vertical line for the current time
            var currentTime = new Date();
            var currentTimeString = currentTime.getHours() + ':' + (currentTime.getMinutes() < 10 ? '0' : '') + currentTime.getMinutes();
            
            var annotationData = google.visualization.arrayToDataTable([
                ['Time', 'Download', 'Upload', {type: 'string', role: 'style'}, {type: 'string', role: 'annotation'}],
                ['00:00',  1.2,      0.1, null, null],
                ['04:00',  0.7,      0.2, null, null],
                ['08:00',  4.8,      3.2, null, null],
                ['12:00',  7.1,      5.0, null, null],
                ['16:00',  7.3,      7.2, null, null],
                ['20:00',  3.6,      2.7, null, null]
            ]);
    
            var annotationOptions = {
                title: 'Network traffic',
                legend: {position: "bottom"},
                vAxis: {title: 'Speed (Mbps)'},
                colors: ['#9b21ff', '#5900ff'],

                annotations: {
                    style: 'line'
                },
                series: {
                    0: {
                        annotations: {
                            textStyle: {
                                fontSize: 0
                            }
                        }
                    }
                }
            };
    
            chart.draw(annotationData, annotationOptions);
    
            // Draw the vertical line
            var cli = chart.getChartLayoutInterface();
            var chartArea = cli.getChartAreaBoundingBox();
    
            var svg = document.querySelector('svg');
            var line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            line.setAttribute('x1', cli.getXLocation(2)); // Adjust the index to match the current time
            line.setAttribute('y1', chartArea.top);
            line.setAttribute('x2', cli.getXLocation(2)); // Adjust the index to match the current time
            line.setAttribute('y2', chartArea.top + chartArea.height);
            line.setAttribute('stroke', '#8391ff');
            line.setAttribute('stroke-width', 2);
            svg.appendChild(line);
        }
    </script>

    */
?>