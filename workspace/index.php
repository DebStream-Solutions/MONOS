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

        hideLoad();
    }

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
                <div class="roll pop-add">
                    <div>
                        <a href="edit/profile/">
                            <img src="icons/plus.png" alt="">
                            <div class="add-img">Add profile</div>
                        </a>
                        <a href="edit/device/">
                            <img src="icons/plus.png" alt="">
                            <div class="add-img">Add device</div>
                        </a>
                    </div>
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