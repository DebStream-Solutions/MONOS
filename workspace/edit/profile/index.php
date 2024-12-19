<?php

    # MONOS --- MONitoring Open-source Solution
    # MONOS --- MONitoring Over Snmp
    # MONOS --- Mobile Optimal Network Open-source System
    # There is more than you could imagine ... MONOS

    //include "snmp.php";
    include "../../main.php";

    if (isset($_GET['profile'])) {
        $profile = $_GET['profile'];
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../../style.css">
<title>MONOS</title>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script type="text/javascript">

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


    function loaded() {

        $(".add").click(function() {
            $(this).children(".roll").fadeToggle(200);
        });

        $(".open-menu").click(toggleSidebar);
        $(".close-menu").click(toggleSidebar);
    }

    $(document).ready(function() {
        const dropdownContent = $('.dropdown-content');
        const selectedItemsContainer = $('.dropdown-input .selected-items');
        const dropdownInput = $('.dropdown-input');
        const dropdownWrapper = $('.dropdown-input-wrapper');
        const placeholder = $('.dropdown-input .placeholder');

        dropdownWrapper.click(function() {
            dropdownContent.toggleClass('show');
            dropdownWrapper.toggleClass('active');
        });

        $('.dropdown-content input[type="checkbox"]').change(function() {
            const checkbox = $(this);
            if (checkbox.is(':checked')) {
                addItem(checkbox.val());
            } else {
                removeItem(checkbox.val());
            }
        });

        function addItem(value) {
            const item = $('<div class="selected-item">' + value + '<span class="remove-item">x</span></div>');

            item.find('.remove-item').click(function() {
                item.remove();
                uncheckItem(value);
                updatePlaceholder();
            });

            selectedItemsContainer.append(item);
            updatePlaceholder();
        }

        function removeItem(value) {
            selectedItemsContainer.find('.selected-item').each(function() {
                const item = $(this);
                if (item.text().trim() === value + 'x') {
                    item.remove();
                }
            });
            updatePlaceholder();
        }

        function uncheckItem(value) {
            dropdownContent.find('input[type="checkbox"]').each(function() {
                const checkbox = $(this);
                if (checkbox.val() === value) {
                    checkbox.prop('checked', false);
                }
            });
        }

        function updatePlaceholder() {
            if (selectedItemsContainer.children().length === 0) {
                placeholder.show();
            } else {
                placeholder.hide();
            }
        }

        $(document).click(function(event) {
            if (!$(event.target).closest('.dropdown').length) {
                dropdownContent.removeClass('show');
                dropdownWrapper.removeClass('active');
            }
        });
    });

</script>
<script src="../../scripts/main.js"></script>
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
            <?php echo isset($_GET["profile"]) ? editProfile(true) : editProfile(false) ?>
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
                <img src="icons/close-menu.png" class="close-menu" alt="close-menu">
            </div>
        </div>
        <div class="footer">
            <div class="small add">
                <img src="../../icons/plus.png" alt="">
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
                <img src="../../icons/home.png" alt="">
            </a>
            <div class="small open-menu">
                <img src="../../icons/menu.png" alt="">
            </div>
        </div>
    </div>
    <div class="darken"></div>
</body>
</html>