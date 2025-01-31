<?php

    # MONOS --- MONitoring Open-source Solution
    # MONOS --- MONitoring Over Snmp
    # MONOS --- Mobile Optimal Network Open-source System
    # There is more than you could imagine ... MONOS

    //include "snmp.php";
    //include "../../main.php";
    include "../../db.php";

    function editDevice($edit) {
        global $conn;
    
        if ($edit) {
            if (isset($_GET['device'])) {
                $device = "SELECT * FROM devices WHERE id = {$_GET['device']}";
                $device = $conn->query($device);
                $device = $device->fetch_all(MYSQLI_ASSOC)[0];
                $_SESSION["device"] = $device;
            } else {
                $content = "There was a mistake! No device to edit..";
            }
    
            if (isset($device)) {
                $profileNameArr = "SELECT * FROM profiles";
                $profileNameArr = $conn->query($profileNameArr);
                $profileNameArr = $profileNameArr->fetch_all(MYSQLI_ASSOC);
    
                $typeArr = "SELECT * FROM types";
                $typeArr = $conn->query($typeArr);
                $typeArr = $typeArr->fetch_all(MYSQLI_ASSOC);
    
                $typeList = "";
                $i = 1;
                foreach ($typeArr as $key => $value) {
                    $selected = "";
                    if ($device["type"] == $i) {
                        $selected = "selected";
                    }
                    $typeList .= '<option value="'.$i.'" '.$selected.'>'.$value["name"].'</option>';
                    $i += 1;
                }
    
                $profilesReleated = "SELECT * FROM profileReleations WHERE deviceId = {$device["id"]}";
                $profilesReleated = $conn->query($profilesReleated);
                $profilesReleated = $profilesReleated->fetch_all(MYSQLI_ASSOC);
                
                $selectedProfiles = "";
                foreach ($profilesReleated as $key => $value) {
                    $releatedProfile = "SELECT * FROM profiles WHERE id = {$value["profileId"]}";
                    $releatedProfile = $conn->query($releatedProfile);
                    $releatedProfile = $releatedProfile->fetch_all(MYSQLI_ASSOC)[0];
    
                    $selectedProfiles .= '<div class="selected-item">'.$releatedProfile["name"].'<span class="remove-item">x</span></div>';
                }
    
                $profileList = "";
                $i = 0;
    
                foreach ($profileNameArr as $key => $value) {
                    $i += 1;
                    $checked = "";
                    foreach ($profilesReleated as $key_p => $value_p) {
                        if ($value_p["profileId"] === $value["id"]) {
                            $checked = "checked";
                        }
                    }
                    #$profileList .= '<label data-item="'.$value["name"].'" data-id="'.$value["id"].'"><input type="checkbox" value="'.$value["name"].'">'.$value["name"].'</label>';
                    $profileList .= '<label data-item="'.$value["name"].'" data-id="'.$value["id"].'"><input type="checkbox" name="profile'.$i.'" value="'.$value["id"].'" '.$checked.'>'.$value["name"].'</label>';
                }
    
                if (isset($_SESSION["error"])) {
                    $error = $_SESSION["error"];
                    $error_msg = "";
                    if (is_array($error)) {
                        foreach ($error as $key => $value) {
                            $error_msg .= $value."<br>";
                        }
                    } else {
                        $error_msg = $error;
                    }
                } else {
                    $error_msg = "";
                }
    
                $content = '
                    <div class="form-wrap">
                        <div class="log">
                            <div class="login-wrap">
                                <h2>Edit device</h2>
                                <div id="device-form">
                                    <form method=POST action="../../action/validate.php?device='.$device["id"].'">
                                        <div class="input-fly">
                                            <div>
                                                <input type="text" id="name" name="name" value="'.$device["name"].'">
                                                <label for="name">Name</label>
                                            </div>
                                        </div>
                                        <div>
                                            <h3 for="profile">Select profiles</h3>
                                            <div class="dropdown">
                                                <div class="selected-items-container">
                                                    
                                                    <button type="button" class="add-button">+</button>
                                                </div>
                                                <div class="input-container">
                                                    <input type="text" class="dropdown-input" placeholder="Select items">
                                                    <span class="dropdown-arrow"><img src="../../icons/dropdown.png" alt="arrow"></span>
                                                </div>
                                                <div class="dropdown-content">
                                                    '.$profileList.'
                                                </div>
                                                <div class="hidden-inputs">
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input-fly">
                                            <div>
                                                <input type="text" id="ip" name="ip" value="'.$device["ip"].'">
                                                <label for="ip">IP Address</label>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="types">Type</label>
                                            <select id="types" name="type">
                                                '.$typeList.'
                                            </select>
                                        </div>
                                        <div>
                                            <input type="submit" name="submit" value="Edit">
                                        </div>
                                    </form>
                                    <form method=POST action="../../action/validate.php?device='.$device["id"].'">
                                        <div class="delete-wrap">
                                            <input type="hidden" name="delete_id" value="'.$device["id"].'">
                                            <button type="submit" onclick="return confirm(\'Are you sure you want to remove this device?\')">Remove device</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="error">
                                    '.$error_msg.'
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
    
        } else {
            if (isset($_SESSION['device'])) {
                $device = $_SESSION['device'];
            } else {
                $device = [
                    "id" => "",
                    "name" => "",
                    "ip" => "",
                    "type" => ""
                ];
            }
    
            if (isset($device)) {
                $profileNameArr = "SELECT * FROM profiles";
                $profileNameArr = $conn->query($profileNameArr);
                $profileNameArr = $profileNameArr->fetch_all(MYSQLI_ASSOC);
                
                $profileList = "";
                $i = 1;
                foreach ($profileNameArr as $key => $value) {
                    #$profileList .= '<label data-item="'.$value["name"].'" data-id="'.$value["id"].'"><input type="checkbox" value="'.$value["name"].'">'.$value["name"].'</label>';
                    $profileList .= '<label data-item="'.$value["name"].'" data-id="'.$value["id"].'"><input type="checkbox" name="profile'.$i.'" value="'.$value["id"].'">'.$value["name"].'</label>';
                    $i += 1;
                }
    
    
                $typeArr = "SELECT * FROM types";
                $typeArr = $conn->query($typeArr);
                $typeArr = $typeArr->fetch_all(MYSQLI_ASSOC);
    
                $typeList = "";
                $i = 1;
                foreach ($typeArr as $key => $value) {
                    $typeList .= '<option value="'.$i.'">'.$value["name"].'</option>';
                    $i += 1;
                }
    
                if (isset($_SESSION["error"])) {
                    $error = $_SESSION["error"];
                    $error_msg = "";
                    if (is_array($error)) {
                        foreach ($error as $key => $value) {
                            $error_msg .= $value."<br>";
                        }
                    } else {
                        $error_msg = $error;
                    }
                } else {
                    $error_msg = "";
                }
    
                
    
                $content = '
                    <div class="form-wrap">
                        <div class="log">
                            <div class="login-wrap">
                                <h2>Add device</h2>
                                <div id="device-form">
                                    <form method=POST action="../../action/validate.php?device">
                                        <div class="input-fly">
                                            <div>
                                                <input type="text" id="name" name="name" value="'.$device["name"].'">
                                                <label for="name">Name</label>
                                            </div>
                                        </div>
                                        <div>
                                            <h3 for="profile">Select profiles</h3>
                                            <div class="dropdown">
                                                <div class="selected-items-container">
                                                    <button type="button" class="add-button">+</button>
                                                </div>
                                                <div class="input-container">
                                                    <input type="text" class="dropdown-input" placeholder="Select items">
                                                    <span class="dropdown-arrow"><img src="../../icons/dropdown.png" alt="arrow"></span>
                                                </div>
                                                <div class="dropdown-content">
                                                    '.$profileList.'
                                                </div>
                                                <div class="hidden-inputs">
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input-fly">
                                            <div>
                                                <input type="text" id="ip" name="ip" value="'.$device["ip"].'">
                                                <label for="ip">IP Address</label>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="types">Type</label>
                                            <select id="types" name="type">
                                                '.$typeList.'
                                            </select>
                                        </div>
                                        <div>
                                            <input type="submit" name="submit" value="Add">
                                        </div>
                                    </form>
                                </div>
                                <div class="error">
                                    '.$error_msg.'
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
        }
    
        return $content;
    }


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
    
    $(document).ready(function () {
        const dropdownContent = $('.dropdown-content');
        const selectedItemsContainer = $('.selected-items-container');
        const dropdownInput = $('.dropdown-input');
        const addButton = $('.add-button');
        const inputContainer = $('.input-container');
        const dropdownArrow = $('.dropdown-arrow');
        const hiddenInputsContainer = $('.hidden-inputs');

        // Handle PHP-added items (if they exist)
        dropdownContent.find('input[type="checkbox"]:checked').each(function () {
            const checkbox = $(this);
            addItem(checkbox.closest('label').data("item"), checkbox.closest('label').data("id"), false);
        });

        // Show input field and filter items
        addButton.click(function () {
            inputContainer.css("display", "flex");
            addButton.hide();
            dropdownInput.focus();
            filterItems();
        });

        dropdownInput.on('input focus', filterItems);

        dropdownArrow.click(function (event) {
            event.stopPropagation();
            dropdownContent.toggleClass('show');
            $(this).find("img").toggleClass('rotated-180');
        });

        // Dynamic event binding for checkboxes (handles PHP-injected items)
        dropdownContent.on('change', 'input[type="checkbox"]', function () {
            const checkbox = $(this);
            const label = checkbox.closest('label');
            const value = label.data("item");
            const id = label.data("id");

            if (checkbox.is(':checked')) {
                addItem(value, id, true);
            } else {
                removeItem(id);
            }
        });

        // Handle Enter key selection
        dropdownInput.keydown(function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const firstUnchecked = dropdownContent.find('input[type="checkbox"]:visible:not(:checked)').first();
                if (firstUnchecked.length) {
                    firstUnchecked.prop('checked', true).trigger('change');
                    dropdownInput.val('');
                    dropdownContent.find('label').show();
                }
            }
        });

        // Function to filter dropdown items
        function filterItems() {
            const searchTerm = dropdownInput.val().toLowerCase();
            dropdownContent.find('label').each(function () {
                const item = $(this).data('item').toLowerCase();
                const isSelected = isItemSelected($(this).data('id'));
                $(this).toggle(item.includes(searchTerm) && !isSelected);
            });
            dropdownContent.addClass('show');
        }

        // Add selected item
        function addItem(value, id, focusInput) {
            if (isItemSelected(id)) return; // Prevent duplicates

            const item = $(`
                <div class="selected-item" data-id="${id}">
                    ${value} <span class="remove-item">x</span>
                </div>
            `);
            const hiddenInput = $(`<input type="hidden" name="profiles[]" value="${id}">`);

            item.find('.remove-item').click(() => removeItem(id));

            selectedItemsContainer.find(".add-button").before(item);
            hiddenInputsContainer.append(hiddenInput);

            if (focusInput) dropdownInput.focus();
        }

        // Remove selected item
        function removeItem(id) {
            selectedItemsContainer.find(`.selected-item[data-id="${id}"]`).remove();
            hiddenInputsContainer.find(`input[value="${id}"]`).remove();
            dropdownContent.find(`input[type="checkbox"][value="${id}"]`).prop('checked', false);
        }

        // Helper to check if an item is selected
        function isItemSelected(id) {
            return hiddenInputsContainer.find(`input[value="${id}"]`).length > 0;
        }

        // Close dropdown when clicking outside
        $(document).click(function (event) {
            if (!$(event.target).closest('.dropdown').length) {
                dropdownContent.removeClass('show');
                inputContainer.hide();
                addButton.show();
            }
        });

        dropdownInput.click(event => event.stopPropagation());


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