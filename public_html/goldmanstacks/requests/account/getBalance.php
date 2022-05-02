<?php
require_once('../../../../private/config.php');
require_once('../../../../private/userbase.php');
require_once('../../../../private/functions.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkClientStatus(); // Check if the client is signed in

/* SESSION Variables */
$userID = $_SESSION["uid"];
$key = $_SESSION['key'];

/* POST Variables */
$accountNumber = decrypt($_POST['account'], $key);
$token = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbMessage = "";

$dbFailMessage = "Failed to retrieve balance";

/* Calculate expected token */
$calc = hash_hmac('sha256', '/getBalance.php', $_SESSION['key']);

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && !empty($accountNumber)) {
        
    /* Input Validation */
    $isMatch = preg_match('/^[0-9]{10}$/', $accountNumber);
    
    if ($isMatch) {
        /* DB Connection */
        $db = getUpdateConnection();
        
        if ($db !== null) {
            /* Check if the account exists */
            $queryBalance = $db->prepare("SELECT balance FROM accountDirectory WHERE clientID=? AND accountNum=?");
            $queryBalance->bind_param("ii", $userID, $accountNumber);
            $queryBalance->execute();
            $queryBalance->store_result();
            
            $queryBalance->bind_result($balance);
            $queryBalance->fetch();

            if ($queryBalance->num_rows > 0) {
                $dbSuccess = true;
                $dbMessage = number_format($balance, 2);
            }
            
            $queryBalance->close();
        }
    }
}

/* Return Outcome */
$object = (object)array();
$object->response = $dbSuccess;
$object->message = $dbMessage;
$json = json_encode($object);
echo $json;
