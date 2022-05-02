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
$paymentToken = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbMessage = '';

/* Calculate expected token */
$calc = hash_hmac('sha256', '/getPaymentDetails.php', $key);

/* Confirm token and user input */
if (hash_equals($calc, $paymentToken)
    && !empty($paymentId)) {
        
    /* Input validation code goes here */
    $isMatch = is_numeric($paymentId);
    
    if ($isMatch) {
        $db = getUpdateConnection();
        
        if ($db !== null) {
            $paymentStatement = $db->prepare("SELECT A.nickName, A.accountType, P.accountNum, P.recipientAccount, P.recipientNickName, P.amount, P.paymentDate, P.step, P.endDate FROM payments P INNER JOIN accountDirectory A ON P.accountNum=A.accountNum WHERE paymentID=? AND clientID=?");
            $paymentStatement->bind_param("si", $paymentId, $userID);
            $paymentStatement->execute();
            $paymentStatement->store_result();
            
            $paymentStatement->bind_result($accountName, $accountType, $paymentFrom, $paymentTo, $recipientName, $paymentAmount, $paymentDate, $paymentStep, $paymentEndDate);
            $paymentStatement->fetch();
            
            if ($paymentFrom != null) {
                /* Data to return to client */
                $paymentFrom = $accountName . ' (' . ucfirst($accountType) . ') (*' . substr($paymentFrom, -4) . ')';
                $paymentTo = $recipientName . ' (*' . substr($paymentTo, -4) . ')';
                $paymentAmount = number_format($paymentAmount, 2);

                /* Returns a string containing the step, period, and end date */
                if ($paymentStep != null) {
                    /* Calculate period and relative step */
                    if ($paymentStep % 7 === 0) {
                      $period = 'week';
                      $paymentStep /= 7;
                    }
                    else if ($paymentStep % 30 === 0) {
                      $period = 'month';
                      $paymentStep /= 30;
                    }
                    else if ($paymentStep % 365 === 0) {
                      $period = 'year';
                      $paymentStep /= 365;
                    }
                    else {
                      $period = 'day';
                    }
                    
                    /* Plural/Singular */
                    if ($paymentStep > 1) $period =  $paymentStep . ' ' . $period . 's';
                    
                    /* String to return */
                    $recurInfo = "Every $period until $paymentEndDate";
                }
                
                $dbSuccess = true;
                $dbMessage = "Payment details retrieved";
            }
                        
            $db->close();
        }
    }
}

$response = (object)array();
$response->response = $dbSuccess;
$response->message = $dbMessage;
$response->from = $paymentFrom;
$response->to = $paymentTo;
$response->amount = $paymentAmount;
$response->date = $paymentDate;
$response->recurInfo = $recurInfo;

/* Return outcome */
$json = json_encode($response);

echo $json;
