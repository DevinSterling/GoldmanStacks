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
$paymentId = decrypt($_POST['id'], $key);
$token = $_POST['token'];

/* Defaults */
$dbSuccess = true;
$dbMessage = '';

$dbFailMessage = 'Failed to retrieve payment details';

/* Calculate expected token */
$calc = hash_hmac('sha256', '/getPaymentDetails.php', $key);

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && !empty($paymentId)) {
        
    if (true) {
        if (true) {
            $from = substr('1234567012', -4);
            $to = substr('1234567012', -4);
            $amount = number_format(300.00, 2);
            $date = date('Y-m-d');
            
            $isRecurring = true;
            
            if ($isRecurring) {
                $recurInfo = 'Everyday until today';
            }
        }
    }
} else {
    
}

/* Return outcome */
$object = (object)array();
$object->response = $dbSuccess;
$object->message = 'test!';
$object->from = $from;
$object->to = $to;
$object->amount = $amount;
$object->date = $date;
$object->isRecurring = $isRecurring;
$object->recurInfo = $recurInfo;
$json = json_encode($object);

echo $json;
