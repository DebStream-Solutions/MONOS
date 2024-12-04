<?php

include "../db.php";


function validate($input) {
    $error = [];

    //overovani jestli jsou vsechna pole vyplnena

    foreach ($input as $key => $value) {
        if (is_array($value)) {
            foreach ($value as $key => $value2) {
                if (isset($_POST[$value2])) {
                    $$value2 = $_POST[$value2];
                    $_SESSION[$value2] = $$value2;
                } else {
                    $error[] = $value2;
                }
            }		
        } else {
            if (isset($_POST[$value])) {
                $$value = $_POST[$value];
                $_SESSION[$value] = $$value;
            } else {
                $error[] = $value;
            }
        }
    }

    return $error;
}


function valEditProfile() {
    $input = ["name"];

    if (count($error = validate($input)) > 0) {
        if (in_array("name", $error)) {

        }
    }

}


function exists($table, $column) {
    # Returns True if it is ok (no same row)
    global $conn;

    $exact = "SELECT id FROM {$table} WHERE {$column} = '{$_SESSION[$column]}'";
    $exact = $conn->query($exact);
    $exists = $exact->fetch_all(MYSQLI_ASSOC);

    if (empty($exists)) {
        return True;
    } else {
        return False;
    }
}


if (isset($_GET["profile"])) {
    $profileId = $_GET["profile"];
    $input = ["name"];

    if (count(validate($input)) == 0) {

        if (exists("profiles", "name")) {
            if (!empty($profileId)) {
                echo $profileId." === UPDATE";

                $update = "UPDATE profiles SET name='{$_SESSION['name']}' WHERE id={$profileId}";
                $updateStatus = $conn->query($update);

                if ($updateStatus === false) {
                    $_SESSION['error'] = $updateStatus;
                } else {
                    session_destroy();
                    header("location: ../");
                }
            } else {
                $insert = "INSERT INTO profiles (name) VALUES ('{$_SESSION['name']}')";
                $insertStatus = $conn->query($insert);

                echo $insertStatus;

                if ($insertStatus === false) {
                    $_SESSION['error'] = $insertStatus;
                } else {
                    session_destroy();
                    header("location: ../");
                }
            }

        } else {
            $_SESSION['error'] = "There is already a profile with the same name!";
        }
        
            
    } else {
        $_SESSION['error'] = "You have to enter a name for the profile.";
    }

} elseif (isset($_GET["device"])) {
    $deviceId = $_GET["device"];
    $input = ["name", "ip", "type"];

    var_dump($_POST["profile1"]);

    if (count(validate($input)) == 0) {
        var_dump($_SESSION["error"]);
        var_dump($_SESSION["type"]);

        if (true) {
            if (!empty($deviceId)) {

                $update = "UPDATE devices SET name='{$_SESSION['name']}' WHERE id={$deviceId}";
                $updateStatus = $conn->query($update);

                if ($updateStatus === false) {
                    $_SESSION['error'] = $updateStatus;
                }
            } else {
                $insert = "INSERT INTO devices (name, type, ip) VALUES ('{$_SESSION['name']}', '{$_SESSION['type']}', '{$_SESSION['ip']}')";
                $insertStatus = $conn->query($insert);

                if ($insertStatus === false) {
                    $_SESSION['error'] = $insertStatus;
                } else {
                    $deviceId = $conn->insert_id;

                    $profiles = "SELECT COUNT(*) AS profile_count FROM profiles";
                    $profiles = $conn->query($profiles);
                    $profiles = $profiles->fetch_all(MYSQLI_ASSOC);
                    
                    $max = $profiles[0]["profile_count"];
                    $tryProfiles = true;
                    $profileIds = [];

                    for ($i=1; $i <= $max; $i++) {
                        $profileCheck = "profile".$i;
                        if (isset($_POST[$profileCheck]) && !empty($_POST[$profileCheck])) {
                            $profileIds[] = $profileCheck;
                        }
                    }

                    var_dump($profileIds);
                    
                    foreach ($profileIds as $key => $value) {
                        $insert = "INSERT INTO profileReleations (profileId, deviceId) VALUES ('{$value}', '{$deviceId}')";
                        $insertStatus = $conn->query($insert);
                    }
                }
            }
        }
        
            
    } else {
        $_SESSION['error'] = "You have to enter a name for the profile.";
    }


}

?>