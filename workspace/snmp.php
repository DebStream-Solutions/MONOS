<?php

snmp_read_mib("../host_resources_mib.txt");

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


function SNMPDataRecord() {
    # SNMP Continous Recording -- returns the data overtime and writes it to database
}

# Device OID Functions

function workstation($hostIp, $community) {

    $oid_list = [
        "disk" => [
            "size" => "1.3.6.1.2.1.25.2.3.1.5",
            "used" => "1.3.6.1.2.1.25.2.3.1.6"
        ],
        "cpu" => [
            "name" => "HOST-RESOURCES-MIB::hrDeviceDescr",
            "load" => "HOST-RESOURCES-MIB::hrProcessorLoad"
        ]

    ];

    $generative_content = '';

    $session = new SNMP(SNMP::VERSION_2c, $hostIp, $community);

    $session->oid_output_format = SNMP_OID_OUTPUT_SUFFIX;
    $session->valueretrieval = SNMP_VALUE_LIBRARY;
    $session->quick_print = 1;
    $session->enum_print = 0;

    if ($session->getError()) {
        $generative_content = "Error: " . $session->getError();
    } else {
        foreach ($oid_list as $key => $value) {
            if ($key == "disk") {
                $disk_size = @snmpwalk($hostIp, $community, $value["size"]);
                $used_size = @snmpwalk($hostIp, $community, $value["used"]);
                $storage_type = @snmpwalk($hostIp, $community, "1.3.6.1.2.1.25.2.3.1.2");

                $size_arr = snmpFormat($disk_size, "INTEGER: ");
                $type_arr = snmpFormat($storage_type, "OID: ");
                $used_arr = snmpFormat($used_size, "INTEGER: ");
                $total_size = 0;
                $total_used = 0;

                foreach ($size_arr as $key => $value) {
                    if (strpos($type_arr[$key], "25.2.1.4") !== false) {
                        $total_size += (int)$value;
                    }
                }

                foreach ($used_arr as $key => $value) {
                    if (strpos($type_arr[$key], "25.2.1.4") !== false) {
                        $total_used += (int)$value;
                    }
                }
                
                $disk_size = round($total_size / 1024 / 1024, 2);
                $disk_used = round($total_used / 1024 / 1024, 2);
                $disk_free = $disk_size - $disk_used;
                $disk_used_percentage = round(($total_used / $total_size) * 100, 2);
                $disk_free_percentage = 100 - $disk_used_percentage;
            } elseif ($key == "cpu") {
                $cpu_name = @snmpwalk($hostIp, $community, $value["name"]);
                $cpu_load = @snmpwalk($hostIp, $community, $value["load"]);
                
                $cpu_load_parse = [];
                $cpu_freq_parse = [];


                if ($cpu_name !== false) {
                    $cpu_name = $cpu_name[0];
                    $cpu_name_arr = explode(":", $cpu_name);
                    $cpu_name = $cpu_name_arr[count($cpu_name_arr) - 1];
                }
                if ($cpu_load !== false) {
                    foreach ($cpu_load as $key => $value) {
                        $value = preg_replace('/^.*: :/', '', $value);
                        $value = explode("INTEGER: ", $value)[1];
                        $cpu_load_parse[] = $value;
                    }
                }

                $cpu_sum = 0;
                $freq_sum = 0;
                $cpu_count = count($cpu_load);
                foreach ($cpu_load_parse as $cpu) {
                    $cpu_sum += (int) $cpu;
                }
                foreach ($cpu_freq_parse as $cpu) {
                    $freq_sum += (int) $cpu;
                }
                $cpu_load = $cpu_sum / $cpu_count;
                $cpu_freq = $freq_sum / $cpu_count;
            }
        }


        # FOR CHART - Make variables global
        $GLOBALS["usedSpace"] = $disk_used_percentage;
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
                            <div>{$cpu_name}</div>
                            <div>CPU Usage: {$cpu_load}%</div>
                            <div>Processing Units: {$cpu_count}</div>
                            <div>Cache size: 32 MB</div>
                        </div>
                    </div>
                    <div>
                        <div class='title'>
                            GPU
                        </div>
                        <div class='roll'>
                            <div>GPU Usage: 32%</div>
                            <div>Current Frequency: 2 GHz</div>
                            <div>Processing Units: 106</div>
                        </div>
                    </div>
                    <div>
                        <div class='title'>
                            RAM
                        </div>
                        <div class='roll'>
                            <div>RAM Usage: 54%</div>
                            <div>Frequency: 3200 MHz</div>
                        </div>
                    </div>
                    <div>
                        <div class='title'>
                            DISK
                        </div>
                        <div class='roll'>
                            <div>Size: {$disk_size} GB</div>
                            <div>Free Space: {$disk_free} GB</div>
                            <div>Used Space: {$disk_used} GB</div>
                        </div>
                    </div>
                    <div>
                        <div class='title'>
                            USERS
                        </div>
                        <div class='roll'>
                            <div>user</div>
                            <div>root</div>
                        </div>
                    </div>
                </div>
            </div>
        ";


    }


    $session->close();

    return $generative_content.var_dump($cpu_name, $cpu_load);
}

?>
