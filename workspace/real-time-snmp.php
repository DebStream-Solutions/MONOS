<?php

session_start();



# -- ADDON FUNCTIONS ------

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




# -- MAIN FUNCTION ---------

/*
INFO:

{} === replaced with the oid value / returned call function value
|| === replaced with integer ($i) returned from the for cycle

"id" => "element-id" => "one-printed-html"
"id" => "element-id" => ["cycled-html"]

"element-id" ========= where the OID value is printed
"one-printed-html" === printed just once
["cycled-html"] ====== printed in for cycle and transfered to string which is printed to the element-id

*/

function getRealTimeArray($type, $ip) {
    $community = "public";

    $real_time_oids = [
        "1.3.6.1.2.1.25.3.3.1.2" => [
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

    $snmpData = [];

    foreach ($real_time_oids as $key => $value) {
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

$snmpData = [
    'systemDescription' => 'Linux Test System', 
    'uptime' => '123456 seconds', 
    'contact' => 'admin@example.com'
];

?>