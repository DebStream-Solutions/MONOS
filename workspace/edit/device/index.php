<?php

    # MONOS --- MONitoring Open-source Solution
    # MONOS --- MONitoring Over Snmp
    # MONOS --- Mobile Optimal Network Open-source System
    # There is more than you could imagine ... MONOS

    //include "snmp.php";
    include "../../main.php";

    if (isset($_GET['device'])) {
        $device = $_GET['device'];

        $conditions = ["id" => $device];
        $deviceName = findValueByConditions($devices, $conditions, "name");
    
    }

    if (isset($_SESSION['profile'])) {
        $profile = $_SESSION['profile'];

        $conditions = ["id" => $profile];
        $profileName = findValueByConditions($profiles, $conditions, "name");
    
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



    // PASTE
    
    $(document).ready(function() {
        const dropdownContent = $('.dropdown-content');
        const selectedItemsContainer = $('.selected-items-container');
        const dropdownInput = $('.dropdown-input');
        const addButton = $('.add-button');
        const inputContainer = $('.input-container');
        const dropdownArrow = $('.dropdown-arrow');
        const hiddenInputsContainer = $('.hidden-inputs');

        addButton.click(function() {
            inputContainer.css("display", "flex");
            addButton.hide();
            dropdownInput.focus();
            filterItems();
        });

        dropdownInput.focus(function() {
            filterItems();
        });

        dropdownInput.on('input', function() {
            filterItems();
        });

        dropdownArrow.click(function(event) {
            event.stopPropagation();
            dropdownContent.toggleClass('show');
            $(this).find("img").toggleClass('rotated-180');
        });

        dropdownContent.find('input[type="checkbox"]').change(function() {
            const checkbox = $(this);
            if (checkbox.is(':checked')) {
                addItem(checkbox.closest('label').data("item"), checkbox.closest('label').data('id'));
            } else {
                removeItem(checkbox.val());
            }
        });

        dropdownInput.keydown(function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const firstVisibleCheckbox = dropdownContent.find('input[type="checkbox"]:visible:not(:checked)').first();
                if (firstVisibleCheckbox.length && !isItemSelected(firstVisibleCheckbox.val())) {
                    firstVisibleCheckbox.prop('checked', true).trigger('change');
                    dropdownInput.val('');
                    dropdownContent.find('label').show(); // Reset the search filter
                }
            }
        });

        function filterItems() {
            const searchTerm = dropdownInput.val().toLowerCase();
            const selectedItems = getSelectedItems();
            dropdownContent.find('label').each(function() {
                const item = $(this).data('item').toLowerCase();
                const isSelected = selectedItems.includes($(this).data('item'));
                $(this).toggle(item.startsWith(searchTerm) && !isSelected || (!item.startsWith(searchTerm) && item.includes(searchTerm) && !isSelected));
            });
            dropdownContent.addClass('show');
        }

        function addItem(value, id) {
            const item = $('<div class="selected-item">' + value + '<span class="remove-item">x</span></div>');
            const hiddenInput = $('<input type="hidden" name="profiles[]" value="' + id + '">');

            item.find('.remove-item').click(function() {
                item.remove();
                hiddenInput.remove();
                uncheckItem(value);
                filterItems();
            });

            selectedItemsContainer.append(item);
            selectedItemsContainer.append(item);
            hiddenInputsContainer.append(hiddenInput);
            item.insertBefore($(".add-button"));
            dropdownInput.focus(); // Keep input focused after adding item
        }

        function removeItem(value) {
            console.log("removeItem("+value+")")
            selectedItemsContainer.find('.selected-item').each(function() {
                const item = $(this);
                if (item.text().trim() === value + 'x') {
                    item.remove();
                }
            });
            hiddenInputsContainer.find('input').each(function() {
                if ($(this).val() === value) {
                    $(this).remove();
                }
            });
        }

        function uncheckItem(value) {
            dropdownContent.find('input[type="checkbox"]').each(function() {
                const checkbox = $(this);
                if (checkbox.val() === value) {
                    checkbox.prop('checked', false);
                }
            });
        }

        function isItemSelected(value) {
            return getSelectedItems().includes(value);
        }

        function getSelectedItems() {
            const selectedItems = [];
            selectedItemsContainer.find('.selected-item').each(function() {
                selectedItems.push($(this).text().replace('x', '').trim());
            });
            return selectedItems;
        }

        $(document).click(function(event) {
            if (!$(event.target).closest('.dropdown').length) {
                dropdownContent.removeClass('show');
                inputContainer.hide();
                addButton.show();
            }
        });

        dropdownInput.click(function(event) {
            event.stopPropagation(); // Prevent the dropdown from closing when clicking inside input
        });


        $(".remove-item").click(function(event) {
            const selectedItem = $(event.target).parents('.selected-item');
            //selectedItem.remove();
            let selectedItemName = selectedItem.text().replace(/x(?!.*x)/, '');
            console.log(selectedItemName);
            removeItem(selectedItemName);
            uncheckItem(selectedItemName);
        });


        hideLoad();
    });

</script>
<script src="../../scripts/main.js"></script>
</head>
<body>
<div id="loading">
    <div class="logo-img"></div>
</div>
    <div class="navbar">
        <a href="../../">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M400-80 0-480l400-400 71 71-329 329 329 329-71 71Z"/></svg>
        </a>
        <div class="path">
            <a href="../../"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="20px" fill="currentColor"><path d="M240-200h120v-240h240v240h120v-360L480-740 240-560v360Zm-80 80v-480l320-240 320 240v480H520v-240h-80v240H160Zm320-350Z"/></svg></a>
            <?php
                if (isset($profileName)) {
                    echo "
                        <a href='../../?profile={$profile}'>{$profileName}</a>
                    ";
                }

                if (isset($deviceName)) {
                    echo "
                        <a href='../../?profile={$profile}&device={$device}'>{$deviceName}</a>
                    ";
                }
            ?>
        </div>
        <?php
            if ($USER == "admin") {
                echo "
                <div class=\"admin-tools\">
                    ADMIN
                </div>";
            }
        ?>
    </div>
    <div class="all">
        <div class="header">
            <h1>MONOS</h1>
        </div>
        <div class="content">
            <?php echo isset($_GET["device"]) ? editDevice(true) : editDevice(false) ?>
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
                <img src="../../icons/close-menu.png" class="close-menu" alt="close-menu">
            </div>
        </div>
        <div class="footer">
            <div class="small add">
                <img src="../../icons/plus.png" alt="">
                <div class="roll pop-add">
                    <div>
                        <a href="../../edit/profile/">
                            <img src="../../icons/plus.png" alt="">
                            <div class="add-img">Add profile</div>
                        </a>
                        <a href="../../edit/device/">
                            <img src="../../icons/plus.png" alt="">
                            <div class="add-img">Add device</div>
                        </a>
                    </div>
                </div>
            </div>
            <a href="../../">
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