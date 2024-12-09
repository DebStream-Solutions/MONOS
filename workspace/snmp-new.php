<?php

$oids_list = [
    "1.3.6.1.2.1.25.2.3.1.5" => [ # disk Size
        "type" => [3, 4],
        "id" => [
            "diskSize" => "Used space: {} GB"
        ],
        "separator" => "INTEGER: "
    ],
    "1.3.6.1.2.1.25.2.3.1.6" => [ # disk Used
        "type" => [3, 4],
        "id" => [
            "cpuLoad" => "CPU Usage: {}%"
        ],
        "separator" => "INTEGER: "
    ],
    "1.3.6.1.2.1.25.2.3.1.2" => [ # disk Type
        "type" => [3, 4],
        "id" => [
            "cpuLoad" => "CPU Usage: {}%",
            "coreLoads" => ["
                <div class='core-load'>
                    <div>Core ||</div>
                    <div class='percent-wrap'>
                        <div class='percent'>{}% </div>
                        <div class='percent-line-wrap'>
                            <div class='percent-line' style='width: calc({}%)'></div>
                        </div>
                    </div>
                </div>"]
        ],
        "separator" => "OID: "
    ],
    "1.3.6.1.2.1.25.3.2.1.3" => [ # cpu Name
        "type" => [3, 4],
        "id" => [
            "cpuLoad" => "CPU Usage: {}%",
            "coreLoads" => ["
                <div class='core-load'>
                    <div>Core ||</div>
                    <div class='percent-wrap'>
                        <div class='percent'>{}% </div>
                        <div class='percent-line-wrap'>
                            <div class='percent-line' style='width: calc({}%)'></div>
                        </div>
                    </div>
                </div>"]
        ],
        "separator" => "INTEGER: "
    ],
    "1.3.6.1.2.1.25.3.3.1.2" => [ # cpu Usage
        "type" => [3, 4],
        "id" => [
            "cpuLoad" => "CPU Usage: {}%",
            "coreLoads" => ["
                <div class='core-load'>
                    <div>Core ||</div>
                    <div class='percent-wrap'>
                        <div class='percent'>{}% </div>
                        <div class='percent-line-wrap'>
                            <div class='percent-line' style='width: calc({}%)'></div>
                        </div>
                    </div>
                </div>"]
        ],
        "separator" => "INTEGER: "
    ],
    "1.3.6.1.4.1.2021.4.5.0" => [ # total RAM
        "type" => [3, 4],
        "id" => [
            "cpuLoad" => "CPU Usage: {}%",
            "coreLoads" => ["
                <div class='core-load'>
                    <div>Core ||</div>
                    <div class='percent-wrap'>
                        <div class='percent'>{}% </div>
                        <div class='percent-line-wrap'>
                            <div class='percent-line' style='width: calc({}%)'></div>
                        </div>
                    </div>
                </div>"]
        ],
        "separator" => "INTEGER: "
    ],
    "1.3.6.1.4.1.2021.4.6.0" => [ # free RAM
        "type" => [3, 4],
        "id" => [
            "cpuLoad" => "CPU Usage: {}%",
            "coreLoads" => ["
                <div class='core-load'>
                    <div>Core ||</div>
                    <div class='percent-wrap'>
                        <div class='percent'>{}% </div>
                        <div class='percent-line-wrap'>
                            <div class='percent-line' style='width: calc({}%)'></div>
                        </div>
                    </div>
                </div>"]
        ],
        "separator" => "INTEGER: "
    ],
    "1.3.6.1.2.1.1.3" => [ # system Up
        "type" => [3, 4],
        "id" => [
            "cpuLoad" => "CPU Usage: {}%",
            "coreLoads" => ["
                <div class='core-load'>
                    <div>Core ||</div>
                    <div class='percent-wrap'>
                        <div class='percent'>{}% </div>
                        <div class='percent-line-wrap'>
                            <div class='percent-line' style='width: calc({}%)'></div>
                        </div>
                    </div>
                </div>"]
        ],
        "separator" => ") "
    ],
];


# Referented Functions

function snmpFormat($snmp_arr, $separator) {
    $snmp_formatted_arr = [];

    if ($snmp_arr !== false || !empty($snmp_arr)) {
        foreach ($snmp_arr as $key => $value) {
            $value = preg_replace('/^.*: :/', '', $value);
            $value = explode($separator, $value)[1];
            $snmp_formatted_arr[] = $value;
        }
    }

    return $snmp_formatted_arr;
}

function snmpRawDataArray($oids, $ip, $type, $community) {
    $snmpData = [];

    foreach ($oids as $key => $value) {
        if (is_array($value["type"]) || $type == $value["type"]) {
            if (in_array($type, $value["type"])) {
                $oid_req = @snmpwalk($ip, $community, $key);
                $oid_arr = snmpFormat($oid_req, $value["separator"]);

                foreach ($value["id"] as $elemetId => $htmlTemplate) {

                    if (is_array($htmlTemplate)) {
                        foreach ($oid_arr as $key => $oid_value) {
                            $insert[] = $oid_value;
                        }
                    } else {
                        if (count($oid_arr) == 1) {
                            $oid_value = $oid_arr[0];
                            $insert = $oid_value;
                        } else {
                            # Get avarage - must be int (we are not checking if it is int, it should be)
                            $items_count = count($oid_arr);
                            foreach ($oid_arr as $item) {
                                $items_sum += (int) $item;
                            }
                            $oid_value = $items_sum / $items_count;
                            $insert = $oid_value;
                        }
                    }

                    $snmpData[$elemetId] = $insert;
                }
            }
        } # else -- not the type
    }

    return $snmpData;
}


function snmpDataArrayHtml($oids, $ip, $type, $community) {
    $snmpData = [];

    foreach ($oids as $key => $value) {
        if (is_array($value["type"]) || $type == $value["type"]) {
            if (in_array($type, $value["type"])) {
                $oid_req = @snmpwalk($ip, $community, $key);
                $oid_arr = snmpFormat($oid_req, $value["separator"]);

                foreach ($value["id"] as $elemetId => $htmlTemplate) {
                    $htmlResolved = "";

                    if (is_array($htmlTemplate)) {
                        $i = 1; # ||
                        $htmlTemplate = $htmlTemplate[0];
                        foreach ($oid_arr as $key => $oid_value) {
                            # Replacing all {} and || with actual values, append to $htmlResolved
                            $currentHtmlResolved = strval(str_replace("{}", $oid_value, $htmlTemplate));
                            $currentHtmlResolved = strval(str_replace("||", $i, $currentHtmlResolved));
                            $htmlResolved .= $currentHtmlResolved;

                            $i++;
                        }
                    } else {
                        if (count($oid_arr) == 1) {
                            $oid_value = $oid_arr[0];
                            $htmlResolved = str_replace("{}", $oid_value, $htmlTemplate);
                        } else {
                            # Get avarage - must be int (we are not checking if it is int, it should be)
                            $items_count = count($oid_arr);
                            foreach ($oid_arr as $item) {
                                $items_sum += (int) $item;
                            }
                            $oid_value = $items_sum / $items_count;
                            $htmlResolved = str_replace("{}", $oid_value, $htmlTemplate);
                        }
                    }

                    $snmpData[$elemetId] = $htmlResolved;
                }
            }
        } # else -- not the type
    }

    return $snmpData;
}



function getSNMPData($hostIp, $deviceType, $community) {
    // Create SNMP session
    $session = new SNMP(SNMP::VERSION_2c, $hostIp, $community);

    // Check for type and redirect to return value

    $deviceTypeArray = [
        3 => 'workstation',
    ];

    foreach ($deviceTypeArray as $key => $value) {
        if ($deviceType == $key) {
            $return = $value($hostIp, $community);
        } else {
            $return = "NO SUCH DEVICE TYPE!!";
        }
    }

    return $return;
}

# Device OID Functions

function workstation($hostIp, $community) {
    global $oids_list;
    $type = 3;
    $generative_content = '';

    $session = new SNMP(SNMP::VERSION_2c, $hostIp, $community);

    if ($session->getError()) {
        $generative_content = "Error: " . $session->getError();
    } else {
        # Get SNMP Data Array
        $data = snmpDataArrayHtml($oids_list, $hostIp, 3, $community);


        # FOR CHART - Make variables global
        $GLOBALS["usedSpace"] = snmpRawDataArray($oids_list, $hostIp, 3, $community)["disk"];
        $GLOBALS["freeSpace"] = $disk_free_percentage;

        $generative_content = "
            <div class='content'>
                <div class='main-banner'>
                    <div id='donutchart'></div>
                </div>
                <div class='mon-list'>
                    <div>
                        <div class='title'>
                            CPU
                        </div>
                        <div class='roll'>
                            <div>
                                <div>{$cpu_name}</div>
                                <div class='drop-roll'>
                                    <div id='cpuLoad' class='title'>CPU Usage: {$cpu_load}%</div>
                                    <div id='coreLoads' class='group roll'>{$cpu_arr_load}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class='title'>
                            GPU
                        </div>
                        <div class='roll'>
                            <div>
                                <div>GPU Usage: 32%</div>
                                <div>Current Frequency: 2 GHz</div>
                                <div>Processing Units: 106</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class='title'>
                            RAM
                        </div>
                        <div class='roll'>
                            <div>
                                <div>RAM Usage: 54%</div>
                                <div>Frequency: 3200 MHz</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class='title'>
                            DISK
                        </div>
                        <div class='roll'>
                            <div>
                                <div>Size: {$disk_size} GB</div>
                                <div>Free Space: {$disk_free} GB</div>
                                <div>Used Space: {$disk_used} GB</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class='title'>
                            USERS
                        </div>
                        <div class='roll'>
                            <div>
                                <div>user</div>
                                <div>root</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ";


    }


    $session->close();

    return $generative_content;
}

?>
