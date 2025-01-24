<?php

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

// Access arguments passed from the command line
if (isset($argv) && count($argv) > 1) {
    // Skip the first argument (script name) and process the rest
    $password_add_indicator = "";
    $arguments = array_slice($argv, 1);
    foreach ($arguments as $arg) {
        if (strpos($arg, $password_add_indicator)) {
            $password = str_replace($pass_needle, "", $argument);
            $hash = pass_hash($password);

            
        }
    }
} else {
    echo "No arguments provided.\n";
}

function encryptFile($inputFile, $outputFile, $key) {
    // Open the input file
    $inputData = file_get_contents($inputFile);

    // Generate an Initialization Vector (IV)
    $ivLength = openssl_cipher_iv_length('AES-256-CBC');
    $iv = openssl_random_pseudo_bytes($ivLength);

    // Encrypt the data
    $encryptedData = openssl_encrypt($inputData, 'AES-256-CBC', $key, 0, $iv);

    // Save the IV and encrypted data to the output file
    $outputData = $iv . $encryptedData;
    file_put_contents($outputFile, $outputData);
}

function decryptFile($inputFile, $outputFile, $key) {
    // Read the input file
    $inputData = file_get_contents($inputFile);

    // Extract the IV and encrypted data
    $ivLength = openssl_cipher_iv_length('AES-256-CBC');
    $iv = substr($inputData, 0, $ivLength);
    $encryptedData = substr($inputData, $ivLength);

    // Decrypt the data
    $decryptedData = openssl_decrypt($encryptedData, 'AES-256-CBC', $key, 0, $iv);

    // Save the decrypted data to the output file
    file_put_contents($outputFile, $decryptedData);
}


if (isset($argv[1])) {
    $argument = $argv[1];

    $pass_needle = "adminpass_#Ad5f78:";
    if (strpos($argument, $pass_needle)) {
        $password = str_replace($pass_needle, "", $argument);


        $filename = "crypt_pass.";
        $contentToCreate = "This is the initial content of the file.\n";

        if (file_put_contents($filename, $contentToCreate)) {
            echo "File '$filename' created successfully with initial content.<br>";
        } else {
            echo "Failed to create the file '$filename'.<br>";
        }
        
        $hash = pass_hash($password);
        $insert = "INSERT INTO users (hash) VALUES ('{$hash}')";
        $insertStatus = $conn->query($insert);
        if (!$insertStatus) {
           echo "ERROR: Password could not be set!";
        } else {
            echo $hash;
        }
    }
}



$servername = "localhost";
$username = "mroot";
$dbname = "monos";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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

# IF BASH sends admin password..

var_dump($argv);

if (isset($argv[0])) {
    $argument = $argv[0];

    $pass_needle = "adminpass_#Ad5f78:";
    if (strpos($argument, $pass_needle)) {
        $password = str_replace($pass_needle, "", $argument);
        
        $hash = pass_hash($password);
        $insert = "INSERT INTO users (hash) VALUES ('{$hash}')";
        $insertStatus = $conn->query($insert);
        if (!$insertStatus) {
           echo "ERROR: Password could not be set!";
        } else {
            echo $hash;
        }
    }
}


?>