<?php

function getSNMPData($hostIp, $deviceType, $community) {
    // Create SNMP session
    $session = new SNMP(SNMP::VERSION_2c, $hostIp, $community);

    // Check for type and redirect to return value

    $deviceTypeArray = [
        1 => 'router',
        3 => 'workstation',
    ];

    foreach ($deviceTypeArray as $key => $value) {
        if ($deviceType == $key) {
            $return = $value($hostIp, $community);
            break;
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

function router($hostIp, $community) {
    $oid_list = [
        "interface" => [
            "name" => "IF-MIB::ifDescr",
            "admin-stat" => "IF-MIB::ifAdminStatus",
            "oper-stat" => "IF-MIB::ifOperStatus",
            "in-bytes" => "IF-MIB::ifInOctets",
            "out-bytes" => "IF-MIB::ifOutOctets",
            "ip" => "1.3.6.1.2.1.4.20.1.1",
            "mask" => "1.3.6.1.2.1.4.20.1.3",
            "mac" => "IF-MIB::ifPhysAddress"
        ],
        "cpu" => [
            "name" => "1.3.6.1.2.1.25.3.2.1.3",
            "load" => "1.3.6.1.2.1.25.3.3.1.2",
            "temp" => "1.3.6.1.4.1.2021.11"
        ]

        /*
            up time          HOST-RESOURCES-MIB::hrSystemUptime.0
            memory size      HOST-RESOURCES-MIB::hrMemorySize.0
            system name      SNMPv2-MIB::sysName.0
            system os        SNMPv2-MIB::sysDescr.0
            cpu load         HOST-RESOURCES-MIB::hrProcessorLoad

        */

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
            if ($key == "interface") {
                $if_name_arr = @snmpwalk($hostIp, $community, $value["name"]);
                $if_name_arr = snmpFormat($if_name_arr, "STRING: ");

                $if_admin_status_arr = @snmpwalk($hostIp, $community, $value["admin-stat"]);
                $if_admin_status_arr = snmpFormat($if_admin_status_arr, "INTEGER: ");

                $if_oper_status_arr = @snmpwalk($hostIp, $community, $value["oper-stat"]);
                $if_oper_status_arr = snmpFormat($if_oper_status_arr, "INTEGER: ");

                $in_bytes_arr = @snmpwalk($hostIp, $community, $value["in-bytes"]);
                $in_bytes_arr = snmpFormat($in_bytes_arr, "Counter32: ");

                $out_bytes_arr = @snmpwalk($hostIp, $community, $value["out-bytes"]);
                $out_bytes_arr = snmpFormat($out_bytes_arr, "Counter32: ");

                $ip_arr = @snmpwalk($hostIp, $community, $value["ip"]);
                $ip_arr = snmpFormat($ip_arr, "IpAddress: ");

                $mask_arr = @snmpwalk($hostIp, $community, $value["mask"]);
                $mask_arr = snmpFormat($mask_arr, "IpAddress: ");

                $mac_arr = @snmpwalk($hostIp, $community, $value["mac"]);
                $mac_arr = snmpFormat($mac_arr, "STRING: ");

                $intefraceHTML = "";
                foreach ($if_name_arr as $key => $value) {
                    

                    $intefraceHTML .= "
                    <div>
                        <div class='title'>
                            {$if_name_arr[$key]}
                        </div>
                        <div class='roll'>
                            <div>
                                <div id='adminStatus{$key}'>Admin Status: {$if_admin_status_arr[$key]}</div>
                                <div id='operStatus{$key}'>Operational Status: {$if_oper_status_arr[$key]}</div>
                                <div id='ipAddress{$key}'>IP Address: {$ip_arr[$key]}</div>
                                <div id='mask{$key}'>Mask: {$mask_arr[$key]}</div>
                                <div id='macAddress{$key}'>MAC: {$mac_arr[$key]}</div>
                                <div id='inBytes{$key}'>Inbound Bytes: {$in_bytes_arr[$key]} bytes</div>
                                <div id='outBytes{$key}'>Outbound Bytes: {$out_bytes_arr[$key]} bytes</div>
                            </div>
                        </div>
                    </div>
                    ";
                }
            
            
            } elseif ($key == "cpus") {
                $cpu_name = @snmpwalk($hostIp, $community, $value["name"]);
                $cpu_load = @snmpwalk($hostIp, $community, $value["load"]);
                $cpu_temp = @snmpwalk($hostIp, $community, $value["temp"]);

                $cpu_load_parse = [];
                $cpu_freq_parse = [];
                $cpu_arr_load = "";


                if ($cpu_name !== false) {
                    $cpu_name = $cpu_name[0];
                    $cpu_name = preg_replace('/^.*: :/', '', $cpu_name);
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

                foreach ($cpu_load_parse as $cpu_int => $load) {
                    $cpu_int = intval($cpu_int) + 1;
                    $cpu_arr_load .= "
                    <div class='core-load'>
                        <div>Core {$cpu_int}</div>
                        <div class='percent-wrap'>
                            <div class='percent'>{$load}% </div>
                            <div class='percent-line-wrap'>
                                <div class='percent-line' style='width: calc({$load}%)'></div>
                            </div>
                        </div>
                    </div>";
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

        $generative_content = "
            <script type='text/javascript'>
            google.charts.load('current', {packages: ['corechart']});
            google.charts.setOnLoadCallback(initChart);
        
            let chart, data;

            const theme = 'dark'; // Change this to 'light' to see the light theme
        
            const options = {
                title: 'Network Traffic',
                backgroundColor: theme === 'dark' ? '#121212' : '#ffffff',
                titleTextStyle: { color: theme === 'dark' ? '#ffffff' : '#000000' },
                legendTextStyle: { color: theme === 'dark' ? '#ffffff' : '#000000' },
                legend: { position: 'bottom' },
                vAxis: {
                    title: 'Bytes/sec',
                    textStyle: { color: theme === 'dark' ? '#ffffff' : '#000000' },
                    gridlines: { color: theme === 'dark' ? 'rgba(255,255,255,0.2)' : 'rgba(0,0,0,0.1)' } // Transparent gridlines
                },
                hAxis: {
                    title: 'Time',
                    format: 'HH:mm:ss',
                    textStyle: { color: theme === 'dark' ? '#ffffff' : '#000000' },
                    gridlines: { color: theme === 'dark' ? 'rgba(255,255,255,0.2)' : 'rgba(0,0,0,0.1)' } // Transparent gridlines
                },
                curveType: 'function', // Smooth curves
                areaOpacity: 0.2, // Fill transparency (20%)
                series: {
                    0: { color: '#9b21ff', lineWidth: 2 }, // Download (purple)
                    1: { color: '#5900ff', lineWidth: 2 }  // Upload (dark purple)
                },
                focusTarget: 'category', // Highlight when hovering over a single data point
                tooltip: { isHtml: true }, // Enable detailed tooltips
            };
        
            function initChart() {
                // Initialize chart with empty data
                data = new google.visualization.DataTable();
                data.addColumn('datetime', 'Time');
                data.addColumn('number', 'Download (Bytes/sec)');
                data.addColumn('number', 'Upload (Bytes/sec)');
        
                chart = new google.visualization.AreaChart(document.getElementById('curve_chart'));
                chart.draw(data, options);
        
                // Start fetching data every 5 seconds
                setInterval(fetchAndUpdateData, 1000);

                // Add interaction for legend to toggle series visibility
                google.visualization.events.addListener(chart, 'select', function() {
                    const selection = chart.getSelection();
                    if (selection.length) {
                        const seriesIndex = selection[0].column - 1; // Adjust for first column being datetime
                        toggleSeries(seriesIndex);
                    }
                });
            }
        
            function fetchAndUpdateData() {
                $.ajax({
                    url: '../test-flow.php?host=".$hostIp."&community=".$community."', // Your endpoint
                    method: 'GET',
                    success: function(response) {
                        // Convert time string into a Date object
                        const timeParts = response.time.split(':');
                        const now = new Date();
                        const time = new Date(now.getFullYear(), now.getMonth(), now.getDate(), timeParts[0], timeParts[1], timeParts[2]);

                        const formattedTime = new Date(time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                        // Add new data point to the chart
                        data.addRow([formattedTime, response.downloadRate, response.uploadRate]);
                        chart.draw(data, options);
                    },
                    error: function() {
                        console.error('Failed to fetch data');
                    }
                });
            }

            function toggleSeries(seriesIndex) {
                // Toggle visibility by setting series color to 'transparent' or restoring original color
                const currentColor = options.series[seriesIndex].color;
                if (currentColor === 'transparent') {
                    options.series[seriesIndex].color = seriesIndex === 0 ? '#9b21ff' : '#5900ff'; // Restore original color
                } else {
                    options.series[seriesIndex].color = 'transparent'; // Hide the series
                }
                chart.draw(data, options);
            }
        </script>
            <div class='content'>
                <div class='main-banner'>
                    <div id='curve_chart' style='width: 900px; height: 500px;'></div>
                </div>
                <div class='mon-list'>
                    <div>
                        <div class='title'>
                            SYSTEM
                        </div>
                        <div class='roll'>
                            <div>
                                <div id='sysUp'>System Up: 1d</div>
                            </div>
                        </div>
                    </div>
                    ".$intefraceHTML."
                </div>
            </div>
        ";

    }

    $session->close();

    return $generative_content;
}


function workstation($hostIp, $community) {
    /*
    $oids = [
        "cpu" => [
            "usage" => [
                "oid" => "1.3.6.1.2.1.25.3.3.1.2",
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
            "name" => [
                "oid" => "1.3.6.1.2.1.25.3.2.1.3",
                "type" => [3, 4],
                "id" => [
                    "cpuName" => ""
                ],
                "separator" => "STRING: "
            ],
        ],
        "ram" => [
            "total" => [
                "oid" => "1.3.6.1.4.1.2021.4.5.0",
                "type" => [3, 4],
                "id" => [
                    "totalRam" => "Total Ram: {}"
                ],
                "separator" => "INTEGER: "
            ],
            "free" => [
                "oid" => "1.3.6.1.4.1.2021.4.6.0",
                "type" => [3, 4],
                "id" => [
                    "freeRam" => "Free Ram: {}"
                ],
                "separator" => "INTEGER: "
            ]
        ],
        "system" => [
            "uptime" => [
                "oid" => "1.3.6.1.2.1.1.3",
                "type" => [3, 4],
                "id" => [
                    "sysUp" => "System Up: {}"
                ],
                "separator" => ") "
            ]
        ]
    ];
    */

    $oid_list = [
        "disk" => [
            "size" => "1.3.6.1.2.1.25.2.3.1.5",
            "used" => "1.3.6.1.2.1.25.2.3.1.6",
            "type" => "1.3.6.1.2.1.25.2.3.1.2"
        ],
        "cpu" => [
            "name" => "1.3.6.1.2.1.25.3.2.1.3",
            "load" => "1.3.6.1.2.1.25.3.3.1.2",
            "temp" => "1.3.6.1.4.1.2021.11"
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
                $storage_type = @snmpwalk($hostIp, $community, $value["type"]);

                $size_arr = snmpFormat($disk_size, "INTEGER: ");
                $type_arr = snmpFormat($storage_type, "OID: ");
                $used_arr = snmpFormat($used_size, "INTEGER: ");
                $total_size = 0;
                $total_used = 0;

                foreach ($size_arr as $key => $value) {
                    # needle > 25.2.1.4
                    # needle > hrStorageFixedDisk
                    if (strpos($type_arr[$key], "hrStorageFixedDisk") !== false) {
                        $total_size += (int)$value;
                    }
                }

                foreach ($used_arr as $key => $value) {
                    if (strpos($type_arr[$key], "hrStorageFixedDisk") !== false) {
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
                $cpu_temp = @snmpwalk($hostIp, $community, $value["temp"]);

                $cpu_load_parse = [];
                $cpu_freq_parse = [];
                $cpu_arr_load = "";


                if ($cpu_name !== false) {
                    $cpu_name = $cpu_name[0];
                    $cpu_name = preg_replace('/^.*: :/', '', $cpu_name);
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

                foreach ($cpu_load_parse as $cpu_int => $load) {
                    $cpu_int = intval($cpu_int) + 1;
                    $cpu_arr_load .= "
                    <div class='core-load'>
                        <div>Core {$cpu_int}</div>
                        <div class='percent-wrap'>
                            <div class='percent'>{$load}% </div>
                            <div class='percent-line-wrap'>
                                <div class='percent-line' style='width: calc({$load}%)'></div>
                            </div>
                        </div>
                    </div>";
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

        # NEW VERSION OF SNMP OIDS SYSTEM
        /*
        if ($session->getError()) {
            $generative_content = "Error: " . $session->getError();
        } else {
            foreach ($oids as $key_group => $value) {
                foreach ($value as $key_type => $value2) {
                    foreach ($value2 as $key => $value3) {
                        $oid_unparsed = @snmpwalk($hostIp, $community, $key);
                        $oid_parsed = snmpFormat($oid_unparsed, $value["separator"]);
                        
                        foreach ($value3["id"] as $elementId => $htmlTemplate) {
                            if ($key_group == "cpu") {
                                if ($key_type == "name") {
    
                                }
                            }
                        }
                    }
                }
            }
        }
        */

        # FOR CHART - Make variables global
        $GLOBALS["usedSpace"] = $disk_used_percentage;
        $GLOBALS["freeSpace"] = $disk_free_percentage;

        $generative_content = "
            <script>
                google.charts.load('current', {'packages':['corechart']});
        
                 google.charts.setOnLoadCallback(drawChart);

                function drawChart() {
                    var data = google.visualization.arrayToDataTable([
                        ['Type', 'Space'],
                        ['Used Space', ".issetReturn($GLOBALS["usedSpace"])."],
                        ['Free Space', ".issetReturn($GLOBALS["freeSpace"])."]
                    ]);

                    var theme = 'dark'; // Change this to 'light' to see the light theme

                    var options = {
                        title: 'Disk Storage',
                        backgroundColor: theme === 'dark' ? '#121212' : '#ffffff',
                        titleTextStyle: { color: theme === 'dark' ? '#ffffff' : '#000000' },
                        legendTextStyle: { color: theme === 'dark' ? '#ffffff' : '#000000' },
                        pieSliceTextStyle: { color: theme === 'dark' ? '#ffffff' : '#000000' },
                        slices: {
                            0: { color: '#9b21ff' },
                            1: { color: '#5900ff' }
                        }
                    };

                    var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
                    chart.draw(data, options);
                }
            </script>
            <div class='content'>
                <div class='main-banner'>
                    <div id='donutchart'></div>
                </div>
                <div class='mon-list'>
                    <div>
                        <div class='title'>
                            SYSTEM
                        </div>
                        <div class='roll'>
                            <div>
                                <div id='sysUp'>System Up: 1d</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class='title'>
                            CPU
                        </div>
                        <div class='roll'>
                            <div>
                                <div id='cpuName'>{$cpu_name}</div>
                                <div class='drop-roll'>
                                    <div id='cpuLoad' class='title'>CPU Usage: {$cpu_load}%</div>
                                    <div id='coreLoads' class='group roll'>{$cpu_arr_load}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class='title'>
                            RAM
                        </div>
                        <div class='roll'>
                            <div>
                                <div id='freeRam'></div>
                                <div id='totalRam'></div>
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
