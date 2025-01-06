<?php

include "../db.php";


function validate($input, $empty = false) {
    $error = [];

    //overovani jestli jsou vsechna pole vyplnena

    foreach ($input as $key => $value) {
        if (is_array($value)) {
            foreach ($value as $key => $value2) {
                if (isset($_POST[$value2])) {
                    if ($empty) {
                        if (!empty($value2)) {
                            $$value2 = $_POST[$value2];
                        } else {
                            $error[] = $value2;
                        }
                    } else {
                        $$value2 = $_POST[$value2];
                    }

                    if (!isset($_POST["password"])) {
                        $_SESSION[$value2] = $$value2;
                    }
                } else {
                    $error[] = $value2;
                }
            }		
        } else {
            if (isset($_POST[$value])) {
                if ($empty) {
                    if (!empty($value)) {
                        $$value = $_POST[$value];
                    } else {
                        $error[] = $value;
                    }
                } else {
                    $$value = $_POST[$value];
                }

                if (!isset($_POST["password"])) {
                    $_SESSION[$value] = $$value;
                }
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


function hashAlgoritm($str1, $str2) {
    $len1 = strlen($str1);
    $len2 = strlen($str2);
    $len = max($len1, $len2);
    $result = '';
    
    for ($i = 0; $i < $len; $i++) {
        $char1 = $i < $len1 ? $str1[$i] : '';
        $char2 = $i < $len2 ? $str2[$i] : '';
        
        if ($char1 === $char2) {
            $result .= $char1; // Same characters are merged
        } else {
            $result .= "{$char1}{$char2}"; // Different characters with delimiter
        }
    }
    return $result;
}


function pass_hash($pass) {
    $raw_salt = "solnicka";
    $algo = "sha256";

    $hash_pass = hash($algo, $pass);
    $salt = hash($algo, $raw_salt);

    

    $hash = hashAlgoritm($hash_pass, $salt);
    return $hash;
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

if (isset($_GET['login'])) {
    $input = ["password"];

    if (count(validate($input)) == 0) {
        $hash = pass_hash($_POST["password"]);
        $_SESSION["hash"] = $hash;

        if (!exists("users", "hash")) {
            $_SESSION["hash"] = "";
            $_SESSION["user"] = true;

            header("location: ../");
            
        } else {
            $_SESSION["error"] = "Wrong password";
            # TODO -- set password by random in db-setup.sh
            header("location: ../login/login.php");
        }
        
    } else {
        $_SESSION["error"] = "Wrong format";
        header("location: ../login/login.php");
    }

} elseif (isset($_GET["profile"])) {
    $profileId = $_GET["profile"];
    $input = ["name"];

    if (count(validate($input)) == 0) {

        if (exists("profiles", "name")) {
            if (!empty($profileId)) {

                $update = "UPDATE profiles SET name='{$_SESSION['name']}' WHERE id={$profileId}";
                $updateStatus = $conn->query($update);

                if ($updateStatus === false) {
                    $_SESSION['error'] = $updateStatus;
                } else {
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

    if (count(validate($input, true)) == 0) {

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
                            $profileId = $i;
                            $profileIds[] = $profileId;
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