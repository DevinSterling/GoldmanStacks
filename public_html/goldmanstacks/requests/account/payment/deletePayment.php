<?php
require_once('../../../../../private/config.php');
require_once('../../../../../private/userbase.php');
require_once('../../../../../private/functions.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkClientStatus(); // Check if the client is signed in

/* SESSION Variables */
$userID = $_SESSION["uid"];
$key = $_SESSION['key'];

/* POST Variables */
$paymentID = decrypt($_POST['payment'], $key);
$token = $_POST['token'];

/* Database response */
$response = (object)array();
$response->response = false;
$response->message = '';

$dbFailMessage = 'Failed to remove payment';

/* Calculate expected token */
$calc = hash_hmac('sha256', '/deletePayment.php', $key);

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && !empty($paymentID)) {
    
    /* Input validation code goes here */
    
    if ($isMatch) {
        /* Get database connection */
        $db = getUpdateConnection();
        
        /* Check database connection */
        if ($db !== null) {
            
            /* Code goes here */
            
            $db->close();
        } else {
            $response->message = $dbFailMessage;
        }
    }
} else {
    $response->message = $dbFailMessage;
}

/* Return outcome */
$json = json_encode($response);

echo $json;
