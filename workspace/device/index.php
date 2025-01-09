<?php

    //include "snmp.php";
    include "../main.php";

    if (isset($_GET['profile']) && isset($_GET['device'])) {
        $profile = $_GET['profile'];
        $device = $_GET['device'];
    } else {
        header('location: ../');
    }

    $conditions = ["id" => $device];
    $ipv4 = findValueByConditions($devices, $conditions, "ip");
    $typeId = findValueByConditions($devices, $conditions, "type");

    $_SESSION["device-ip"] = $ipv4;
    $_SESSION["device-type"] = $typeId;

    $result = getSNMPData($ipv4, $typeId, "public");
    
    //echo $result;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <title>Document</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../ajax.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Type', 'Space'],
                ['Used Space', <?php echo issetReturn($GLOBALS["usedSpace"]) ?>],
                ['Free Space', <?php echo issetReturn($GLOBALS["freeSpace"]) ?>]
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




        function toggleSidebar() {
            if ($(".sidebar-wrap").css("right").includes("-")) {
                $(".sidebar-wrap").animate({ right: '0%' }, 500);

                $(".close-menu").addClass("show");

                $(".darken").show(0);
                $(".darken").animate({opacity: 0.5}, 500);
            } else {
                $(".sidebar-wrap").animate({ right: '-100%' }, 500);

                $(".close-menu").removeClass("show");

                $(".darken").animate({opacity: 0}, 500).after().hide(0);
            }
        }

        function onLoad() {

            $(".mon-list > div > .title, .drop-roll > .title").click(function() {
                if ($(this).hasClass("up")) {
                    $(this).siblings(".roll").slideToggle(200, () => {
                        $(this).removeClass("up");
                    });
                } else {
                    $(this).siblings(".roll").slideToggle(200);
                    $(this).addClass("up");
                    $(this).siblings(".roll").css("display", "flex");
                }
            });

            $(".add").click(function() {
                $(this).children(".roll").fadeToggle(200);
            });

            $(".open-menu").click(toggleSidebar);
            $(".close-menu").click(toggleSidebar);
        }

        $(document).ready(onLoad);

    </script>
</head>
<body>
    <div class="all">
        <a href="../edit/profile/?device=<?php echo $device?>" class="edit-btn edit-dev">
            <img src="icons/edit.png" alt="edit-icon">
        </a>
        <div id="deviceState">
            <div class="unknown"></div>
        </div>
        <div class="header">
            <h1 class="device"><?php
                $conditions = ["id" => $device];
                echo findValueByConditions($devices, $conditions, "name");
            ?></h1>
            <h3 class="ip"><?php
                $conditions = ["id" => $device];
                echo findValueByConditions($devices, $conditions, "ip");
            ?></h3>
        </div>
        <div class="generated">
            <?php echo $result; ?>
        </div>
        <div class="sidebar-wrap">
            <div class="sidebar">
                <div class="sidebar-content">
                    <div>
                        <div class="title">Manage</div>
                        <div class="roll">
                            <a href="">
                                <div class="add-img">Manage templates</div>
                            </a>
                            <a href="">
                                <div class="add-img">Manage profiles</div>
                            </a>
                            <a href="">
                                <div class="add-img">Manage devices</div>
                            </a>
                        </div>
                    </div>
                    <div>
                        <div class="title up">Add</div>
                        <div class="roll">
                            <a href="">
                                <div class="add-img">Add profile</div>
                            </a>
                            <a href="">
                                <div class="add-img">Add device</div>
                            </a>
                        </div>
                    </div>
                    <div>
                        <a href="">
                            <div class="title">Manage</div>
                        </a>
                    </div>
                </div>
                <img src="../icons/close-menu.png" class="close-menu" alt="close-menu">
            </div>
        </div>
        <div class="footer">
            <a href="/add-device" class="small">
                <img src="../icons/plus.png" alt="">
            </a>
            <a href="/">
                <img src="../icons/home.png" alt="">
            </a>
            <div class="small open-menu">
                <img src="../icons/menu.png" alt="">
            </div>
        </div>
    </div>
    <div class="darken"></div>
</body>
</html>