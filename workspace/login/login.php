<?php

    # MONOS --- MONitoring Open-source Solution
    # MONOS --- MONitoring Over Snmp
    # MONOS --- Mobile Optimal Network Open-source System
    # There is more than you could imagine ... MONOS

    //include "snmp.php";
    include "main.php";

    if (isset($_SESSION['user'])) {
        header('location: ../');
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../style.css">
<title>MONOS</title>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script type="text/javascript">

    function hideLoad() {
        $("#loading").animate({ top: '-50%', opacity: 0 }, 1000, function() {
            $(this).fadeOut(1000);
        });
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
            <div class="log">
                <div class="login-wrap">
                    <h2>Login</h2>
                    <form action="../action/validate.php?login" method="POST">
                        <div class="input-fly">
                            <div>
                                <input type="password" name="password" id="password" placeholder="Your super secret password">
                                <label for="password">Password</label>
                            </div>
                        </div>
                    </form>
                    <div class="errors">
                        <?php echo isset($_SESSION["error"]) ? $_SESSION["error"] : "" ?>
                    </div>
                </div>
                <div class="monos-log">
                    <h3>MONOS Beta v0.3</h3>
                    <div class="monos-img"></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>