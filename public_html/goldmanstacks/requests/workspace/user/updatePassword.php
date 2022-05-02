<?php
require_once('../../../../../private/config.php');
require_once('../../../../../private/userbase.php');
require_once('../../../../../private/functions.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkEmployeeStatus(); // Check if the employee is signed in

/* POST Variables */
$userID = $_POST['id'];
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
    && !empty($newPassword)
    && $newPassword === $confirmPassword) { // if true, non-empty parameters given and passwords match
    
    /* Validate Input */
    $isMatch = is_numeric($userID);
    
    if ($isMatch) {
        /* DB Connection */
        $db = getUpdateConnection();
        if ($db !== null) {
            $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            /* Prepared Statement */
            $updateStatement = $db->prepare("UPDATE users SET password=? WHERE userID=?");
            $updateStatement->bind_param("si", $newPassword, $userID);
            $updateStatement->execute();
            
            /* Check Execution */
            if ($db->affected_rows > 0) {
                $dbSuccess = true;
                $dbMessage = "Password has been updated";
            }
            
            /* Close */
            $updateStatement->close();
            $db->close();
        }
    }
}

/* Return Outcome */
$myObj = (object)array();
$myObj->response = $dbSuccess;
$myObj->message = $dbMessage;
$myJSON = json_encode($myObj);
echo $myJSON;
