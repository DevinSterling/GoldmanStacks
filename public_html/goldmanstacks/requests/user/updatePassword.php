<?php
require_once('../../../../private/config.php');
require_once('../../../../private/userbase.php');
require_once('../../../../private/functions.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkUserStatus(); // Check if the client is signed in

/* SESSION Variables */
$userID = $_SESSION["uid"];
$key = $_SESSION['key'];

/* POST Variables */
$oldPassword = $_POST['old'];
$newPassword = $_POST['new'];
$confirmPassword = $_POST['confirm'];
$token = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbMessage = "";

$dbFailMessage = "Failed to update password";

/* Calculate expected token */
$calc = hash_hmac('sha256', '/updatePassword.php', $key);

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && checkNotEmpty($oldPassword, $newPassword)
    && $newPassword === $confirmPassword) { // if true, non-empty parameters given and passwords match
    
    /* DB Connection */
    $db = getUpdateConnection();
    
    if ($db !== null) {
        /* Verify Current Password */
        $query = $db->prepare("SELECT password FROM users WHERE userID=?");
        $query->bind_param("i", $userID);
        $query->execute();
        
        /* Get result */
        $query->store_result();
        $query->bind_result($password);
        $query->fetch();
        $query->close();

        if ($password !== null) {
            if (password_verify($oldPassword, $password)) {
                /* Encrypt Password */
                $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                
                /* Prepared Statement */
                $stmt = $db->prepare("UPDATE users SET password=? WHERE userID=?");
                $stmt->bind_param("si", $newPassword, $userID);
                $stmt->execute();
                
                /* Check Execution */
                if ($db->affected_rows === 0) { // If 0, update failed to execute
                    $dbMessage = $dbFailMessage;
                } else {
                    $dbSuccess = true;
                    $dbMessage = "Password has been updated";
                }
                
                /* Close Statement */
                $stmt->close();
            } else {
                $dbMessage = $dbFailMessage;
            }
        } else {
            $dbMessage = $dbFailMessage;
        }
        
        /* Close Streams */
        $db->close();
    } else {
        $dbMessage = $dbFailMessage;
    }
} else {
    die();
}

/* Return Outcome */
$myObj = (object)array();
$myObj->response = $dbSuccess;
$myObj->message = $dbMessage;
$myJSON = json_encode($myObj);
echo $myJSON;
