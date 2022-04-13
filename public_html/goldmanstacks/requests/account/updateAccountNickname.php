<?php
require_once('../../../../private/config.php');
require_once('../../../../private/userbase.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkClientStatus(); // Check if the client is signed in

/* SESSION Variables */
$userID = $_SESSION["uid"];

/* POST Variables */
$accountNickName = $_POST['new'];
$currentNickName = $_POST['old'];
$token = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbMessage = "";

$dbFailMessage = "Failed to update nickname";

/* Calculate expected token */
$calc = hash_hmac('sha256', '/updateAccountNickname.php', $_SESSION['key']); 

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && !empty($accountNickName)) {
    
    /* Input Validation */
    $isMatch = preg_match('/^[A-z0-9 ]+$/', $accountNickName);
    
    if ($isMatch) {
        /* DB Connection */
        $db = getUpdateConnection();
        
        if ($db !== null) {
            /* Check if the account type has been requested by the user already */
            $updateNickName = $db->prepare("UPDATE accountDirectory SET nickName=? WHERE nickName=? AND clientID=?");
            $updateNickName->bind_param("ssi", $accountNickName, $currentNickName, $userID);
            $updateNickName->execute();
            
            if ($db->affected_rows > 0) {
                $dbSuccess = true;
                $dbMessage = "Nickname has been updated";
            } else {
                $dbMessage = $dbFailMessage;
            }
            
            $updateNickName->close();
            $db->close();
        } else {
            $dbMessage = $dbFailMessage;
        }
    } else {
        $dbMessage = $dbFailMessage;
    }
} else {
    die();
}
    
$object = (object)array();
$object->response = $dbSuccess;
$object->message = $dbMessage;
$json = json_encode($object);

echo $json;
