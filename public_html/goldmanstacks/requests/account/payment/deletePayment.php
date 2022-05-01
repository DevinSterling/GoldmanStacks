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
$dbSuccess = false;
$dbMessage = 'Failed to remove payment';

/* Calculate expected token */
$calc = hash_hmac('sha256', '/deletePayment.php', $key);

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && !empty($paymentID)) {
    
    /* Input validation code goes here */
    $isMatch = is_numeric($paymentID);

    if ($isMatch) {
        /* Get database connection */
        $db = getUpdateConnection();
        
        /* Check database connection */
        if ($db !== null) {
            $selectStatement = $db->prepare("SELECT COUNT(*) 
                                            FROM payments P INNER JOIN accountDirectory A ON P.accountNum=A.accountNum 
                                            WHERE P.paymentID=? AND A.clientID=?");
            $selectStatement->bind_param("si", $paymentID, $userID);
            $selectStatement->execute();
            $selectStatement->store_result();
            
            $selectStatement->bind_result($count);
            $selectStatement->fetch();
            $selectStatement->close();
            
            if ($count) {
                $deleteStatement = $db->prepare("DELETE FROM payments WHERE paymentID=?");
                $deleteStatement->bind_param("s", $paymentID);
                $deleteStatement->execute();
                
                if ($db->affected_rows === 1) {
                    $dbSuccess = true;
                    $dbMessage = "Payment has been removed";
                }
                
                $deleteStatement->close();
            }
            
            $db->close();
        } else $dbMessage = "test";
    } else $dbMessage = "test?";
}

$response = (object)array();
$response->response = $dbSuccess;
$response->message = $dbMessage;

/* Return outcome */
$json = json_encode($response);

echo $json;
