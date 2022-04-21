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
$response = (object)array();
$response->response = false;
$response->message = '';

$dbFailMessage = 'Failed to retrieve payment details';

/* Calculate expected token */
$calc = hash_hmac('sha256', '/getPaymentDetails.php', $key);

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && !empty($paymentId)) {
        
    /* Input validation code goes here */
    
    if ($isMatch) {
        /* Get database connection */
        $db = getUpdateConnection();
        
        /* Check database connection */
        if ($db !== null) {
            
            /* Code goes here */
            
            if ($something) {
                /* Commented code below is what is sent back after data retrieval from database */
                // $response->from = substr(DB_VALUE, -4);
                // $response->to = substr(DB_VALUE, -4);
                // $response->amount = number_format(DB_VALUE, 2);
                // $response->date = DB_VALUE;
                
                /* To check if a payment is recurring, check the step value for the current payment and see if it is null or not (if step has a value, then the payment is recurring) */
                // $isRecurring = DB_VALUE; 
                
                // $response->isRecurring = $isRecurring;
                
                // if ($isRecurring) {
                    /* Returns a string containing the step, period, and end date */
                    
                    // /* Calculate period and orginally user inputted step */
                    // if ($step % 7 === 0) {
                    //   $period = 'week';
                    //   $step /= 7;
                    // }
                    // else if ($step % 30 === 0) {
                    //   $period = 'month';
                    //   $step /= 30;
                    // }
                    // else if ($step % 365 === 0) {
                    //   $period = 'year';
                    //   $step /= 365;
                    // }
                    // else {
                    //   $period = 'day';
                    // }
                    
                    // /* Plural/Singular */
                    // if ($step > 1) $period .= 's';
                    
                    // $response->recurInfo = "Every $step $period until <end date goes here (value taken from database)>";
                // }
            }
            
            $db->close();
        } else {
            $response->message = $dbFailMessage;
        }
    } else {
        $response->message = $dbFailMessage;
    }
} else {
    $response->message = $dbFailMessage;
}

/* Return outcome */
$json = json_encode($response);

echo $json;
