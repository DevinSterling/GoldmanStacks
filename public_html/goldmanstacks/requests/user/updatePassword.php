<?php
require_once('../../../../private/config.php');
require_once('../../../../private/userbase.php');
require_once('../../../../private/functions.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkClientStatus(); // Check if the client is signed in

/* SESSION Variables */
$userID = $_SESSION["uid"];

/* POST Variables */
$oldPassword = $_POST['old'];
$newPassword = $_POST['new'];
$confirmPassword = $_POST['confirm'];
$token = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbMessage = "Failed to update password";

/* Calculate expected token */
$calc = hash_hmac('sha256', '/updatePassword.php', $_SESSION['key']);

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && checkNotEmpty($oldPassword, $newPassword)
    && $newPassword === $confirmPassword) { // if true, non-empty parameters given and passwords match
    
    /* DB Connection */
    $db = getUpdateConnection();
    
    if ($db !== null) {
        /* Verify Current Password */
        $selectStatement = $db->prepare("SELECT password FROM users WHERE userID=?");
        $selectStatement->bind_param("i", $userID);
        $selectStatement->execute();
        $result = $selectStatement->get_result();
        $selectStatement->close();
        
        if ($result->num_rows > 0) {
            $password = $result->fetch_assoc();
            
            /* Verify passwords before updating */
            if (password_verify($oldPassword, $password['password'])) {
                /* Encrypt Password */
                $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                
                /* Prepared Statement */
                $updateStatement = $db->prepare("UPDATE users SET password=? WHERE userID=?");
                $updateStatement->bind_param("si", $newPassword, $userID);
                $updateStatement->execute();
                
                /* Check Execution */
                if ($db->affected_rows === 0) {
                    $dbSuccess = true;
                    $dbMessage = "Password has been updated";
                }
                
                /* Close Statement */
                $updateStatement->close();
            }
        }
        
        /* Close Streams */
        $result->free();
        $db->close();
    }
}

/* Return Outcome */
$myObj = (object)array();
$myObj->response = $dbSuccess;
$myObj->message = $dbMessage;
$myJSON = json_encode($myObj);
echo $myJSON;
