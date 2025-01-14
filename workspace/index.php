<?php

    # MONOS --- MONitoring Open-source Solution
    # MONOS --- MONitoring Over Snmp
    # MONOS --- Mobile Optimal Network Open-source System
    # There is more than you could imagine ... MONOS

    //include "snmp.php";
    include "main.php";

    if (isset($_GET['profile'])) {
        $profile = $_GET['profile'];
        $_SESSION['profile'] = $profile;
        $_SESSION['device-ip'] = "";
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css">
<title>MONOS</title>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="ajax.js"></script>
<script type="text/javascript">

    function hideLoad() {
        $("#loading").animate({ top: '-50%', opacity: 0 }, 1000, function() {
            $(this).fadeOut(1000);
        });
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
        $("#net_chart").ready(() => {
            setTimeout(hideLoad, 1000);
        });

        $(".sidebar-content > div").click(function() {
            if ($(this).children(".title.up").length > 0) {
                $(this).children(".roll").slideToggle(200, () => {
                    $(this).children(".title").removeClass("up");
                });
            } else {
                $(this).children(".roll").slideToggle(200);
                $(this).children(".title").addClass("up");
                $(this).children(".roll").css("display", "flex");
            }
        });

        $(".add").click(function() {
            $(this).children(".roll").fadeToggle(200);
        });

        $(".open-menu").click(toggleSidebar);
        $(".close-menu").click(toggleSidebar);
    }



    
    let multiply = 4;
    const d = new Date();
    let time = d.getHours() + (d.getMinutes() / 60);

    let index = time / multiply;
    /*
    function timeTable(start, multiply, end) {
        /*
        arr = [];
        x = start;
        while (x <= 24 - multiply) {
            x += multiply;
            arr.push(x);
        }
        //

        arr = [];
        let x = start;

        while (x <= end - multiply) {
            x += multiply;
            arr.push(toString(x).":".);
        }



        return arr;
    }

    function chartData(param) {
        const d = new Date();
        let time = d.getHours() + (d.getMinutes() / 60);
        
        

        timeArray = timeTable(0, 4, time);
        dataArray = [
            "down" => [1.2, ],
            "up" => [

            ]
        ];
        


        for (timeCol in timeArray) {

        }

        ['Time', 'Download ', 'Upload ', {type: 'string', role: 'style'}],
        ['00:00',  1.2,      0.1],
        ['04:00',  0.7,      0.2],
        ['08:00',  4.8,      3.2],
        ['12:00',  7.1,      5.0],
        ['16:00',  7.3,      7.2],
        ['20:00',  3.6,      2.7]
    }*/


    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function getThemeMode() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function arrayDataFunc() {
        const startDate = new Date();
        startDate.setHours(0, 0, 0, 0); // Set to midnight of the current day
        const endDate = new Date(); // Current date and time
        const interval = 5 * 60 * 1000; // 5 minutes in milliseconds
        const arrayData = [];
        let x = 0;

        while (startDate < endDate) {
            if (x === 0) {
                const titleRow = ['Time', 'Download', 'Upload', {type: 'string', role: 'style'}, {type: 'string', role: 'annotation'}];
                arrayData.push(titleRow);
            } else {
                const download = Math.random() * 40; // Random float between 0 and 4
                const upload = Math.random() * 30; // Random float between 0 and 3
                arrayData.push([new Date(startDate), download, upload, null, null]);
                startDate.setMinutes(startDate.getMinutes() + 5);
            }
            x += 1;
        }

        return arrayData;

        /*
        while (x <= 24) {
            if (x == 0) {
                let titleRow = ['Time', 'Download ', 'Upload ', {type: 'string', role: 'style'}, {type: 'string', role: 'annotation'}];
                arrayDataFunc.push(titleRow);
            } else {

            }
        }*/
    }

    function timeTicks(gap) {
        const hourlyStartDate = new Date();
        hourlyStartDate.setHours(0, 0, 0, 0); // Set to midnight of the current day
        const endDate = new Date();
        
        const hourlyArrayData = [];

        while (hourlyStartDate < endDate) {
            hourlyStartDate.setHours(hourlyStartDate.getHours() + gap);
        }

        //console.log(hourlyArrayData);

        return hourlyArrayData;
    }

    function drawChart() {
        var data = new google.visualization.arrayToDataTable(arrayDataFunc());

        /*
        var data = google.visualization.arrayToDataTable([
            ['Time', 'Download ', 'Upload '],
            ['00:00',  1.2,      0.1],
            ['04:00',  0.7,      0.2],
            ['08:00',  4.8,      3.2],
            ['12:00',  7.1,      5.0],
            ['16:00',  7.3,      7.2],
            ['20:00',  3.6,      2.7]
        ]);*/

        var theme = getThemeMode();
        var options = {
            title: 'Network traffic',
            titleTextStyle: {color: theme === 'dark' ? '#ffffff' : '#000000'},
            legend: {position: "bottom", textStyle: {color: theme === 'dark' ? '#ffffff' : '#000000'}},
            vAxis: {title: 'Speed (Mbps)', textStyle: {color: theme === 'dark' ? '#ffffff' : '#000000'}, titleTextStyle: {color: theme === 'dark' ? '#ffffff' : '#000000'}},
            hAxis: {textStyle: {color: theme === 'dark' ? '#ffffff' : '#000000'}},
            backgroundColor: theme === 'dark' ? '#121212' : '#ffffff',
            colors: ['#9b21ff', '#5900ff']
        };

        var chart = new google.visualization.LineChart(document.getElementById('net_chart'));
        chart.draw(data, options);

        // Add a vertical line for the current time
        var currentTime = new Date();
        var currentTimeString = currentTime.getHours() + ':' + (currentTime.getMinutes() < 10 ? '0' : '') + currentTime.getMinutes();
        
        var annotationData = google.visualization.arrayToDataTable(arrayDataFunc());

        var annotationOptions = {
            title: "Network traffic",
            titleTextStyle: {color: theme === 'dark' ? '#ffffff' : '#000000'},
            legend: {position: "bottom", textStyle: {color: theme === 'dark' ? '#ffffff' : '#000000'}},
            vAxis: {title: 'Speed (Mbps)', textStyle: {color: theme === 'dark' ? '#ffffff' : '#000000'}, titleTextStyle: {color: theme === 'dark' ? '#ffffff' : '#000000'}},
            hAxis: {
                textStyle: {color: theme === 'dark' ? '#ffffff' : '#000000'},
                format: 'HH:mm',
                gridlines: { count: -1 }
            },
            backgroundColor: theme === 'dark' ? '#121212' : '#ffffff',
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
        /*
        var cli = chart.getChartLayoutInterface();
        var chartArea = cli.getChartAreaBoundingBox();

        var svg = document.querySelector('svg');
        var line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        line.setAttribute('x1', cli.getXLocation(index)); // Adjust the index to match the current time
        line.setAttribute('y1', chartArea.top);
        line.setAttribute('x2', cli.getXLocation(index)); // Adjust the index to match the current time
        line.setAttribute('y2', chartArea.top + chartArea.height);
        line.setAttribute('stroke', '#8391ff');
        line.setAttribute('stroke-width', 2);
        svg.appendChild(line);*/
    }

    // Update chart colors on theme change
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
        drawChart();
    });

    
    window.addEventListener('resize', drawChart);
    $(document).ready(onLoad);
</script>
</head>
<body>
<div id="loading">
    <div class="logo-img"></div>
</div>
    <div class="all">
        <div class="header">
            <h1>MONOS</h1>
        </div>
        <div class="content">
            <?php echo isset($_GET["profile"]) ? monitorContent($profile) : profileContent() ?>
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
                            <a href="edit/profile">
                                <div class="add-img">Add profile</div>
                            </a>
                            <a href="edit/device">
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
                <img src="icons/close-menu.png" class="close-menu" alt="close-menu">
            </div>
        </div>
        <div class="footer">
            <div class="small add">
                <img src="icons/plus.png" alt="">
                <div class="roll">
                    <a href="">
                        <div class="add-img">Add profile</div>
                    </a>
                    <a href="">
                        <div class="add-img">Add device</div>
                    </a>
                </div>
            </div>
            <a href="/">
                <img src="icons/home.png" alt="">
            </a>
            <div class="small open-menu">
                <img src="icons/menu.png" alt="">
            </div>
        </div>
    </div>
    <div class="darken"></div>
</body>
</html>