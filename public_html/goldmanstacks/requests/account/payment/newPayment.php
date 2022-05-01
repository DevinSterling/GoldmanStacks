<?php
require_once('../../../../../private/config.php');
require_once('../../../../../private/functions.php');
require_once('../../../../../private/userbase.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkClientStatus(); // Check if the client is signed in

/* SESSION Variables */
$userID = $_SESSION["uid"];
$key = $_SESSION['key'];

/* POST Variables */
$from = decrypt($_POST['from'], $key);
$to = $_POST['to'];
$name = $_POST['name'];
$date = $_POST['date'];
$amount = $_POST['amount'];
$step = $_POST['step'];
$period = $_POST['period'];
$endDate = $_POST['end'];

$token = $_POST['token'];

/* Defaults */
$isRecurring = false;
$dbSuccess = false;
$dbMessage = 'Failed to create payment';
$dbPaymentId = '';

$periods = array('day', 'week', 'month', 'year');

/* Calculate expected token */
$calc = hash_hmac('sha256', '/newPayment.php', $key);

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && checkNotEmpty($from, $to, $date, $amount)) {
    
    /* Convert to time */
    $date = strtotime($date); // If the given string is not a valid time, an empty string will be returned.
    $endDate = strtotime($endDate);
    
    /* Validate user input */
    $isMatch = preg_match('/^[0-9]{10}$/', $from)
        && preg_match('/^[0-9]{10}$/', $to)
        && (is_numeric($amount) && $amount >= 1)
        && $date; // Confirm if the string is empty or not
    
    /* Validate optional user input for receiver name */
    if (!empty($name)) {
        $isMatch &= preg_match('/^[A-z&\' ]{1,30}$/', $name);
    } else {
        $name = null;
    }
    
    /* Validate option user input for recurring payments */ 
    if (checkNotEmpty($step, $period, $endDate)) {
        $isRecurring = true; // True if the user has given input to make the payment recurring
        
        $isMatch &= (is_numeric($step) && $step >= 1 && $step <= 55)
            && in_array($period, $periods)
            && $endDate;
            
        /* Validate logic */
        $isMatch &= $endDate > $date; // Compare and confirm that the end date is in the future
        
        /* Conversion */
        $endDate = date("Y-m-d", $endDate); // Convert $endDate to SQL compatible DATE variable
        
        /* Step is converted into the amount of days till the next payment */
        switch ($period) {
            case 'week':
                $step *= 7;
                break;
            case 'month':
                $step *= 30;
                break;
            case 'year': 
                $steps *= 365;
        }
    } else {
        $step = null;
        $endDate = null;
    }
    
    /* Validate logic */
    $isMatch &= $date >= date("Y-m-d"); // Compare given date with the current date (ensure it is not the past)
    
    /* Conversion */
    $date = date("Y-m-d", $date); // Convert $date to SQL compatible DATE variable

    if ($isMatch) {
        /* Get database connection */
        $db = getUpdateConnection();
        
        if ($db !== null) {
            /* Ensure that the "sender" is the client's own account and get balance */
            $queryAccount = $db->prepare("SELECT balance FROM accountDirectory WHERE accountNum=? AND clientID=?");
            $queryAccount->bind_param("si", $from, $userID);
            $queryAccount->execute();
            $queryAccount->store_result();
            
            /* Get result */
            $queryAccount->bind_result($balance);
            $queryAccount->fetch();
            $queryAccount->close();
            
            if ($balance !== null) {
                $isToday = $date === date("Y-m-d");
                
                /* Make payment immediately if the payment date is today */
                if ($isToday) {
                    if ($balance > $amount) {
                        /* Deduct funds from client account */
                        $updateSenderBalance = $db->prepare("UPDATE accountDirectory SET balance=balance-? WHERE accountNum=?");
                        $updateSenderBalance->bind_param("di", $amount, $from);
                        $updateSenderBalance->execute();
                        $updateSenderBalance->close();
                        
                        /* Check if the receiver exists */
                        $receiverAccount = $db->prepare("SELECT COUNT(*) FROM accountDirectory WHERE accountNum=?");
                        $receiverAccount->bind_param("i", $to);
                        $receiverAccount->execute();
                        $receiverAccount->store_result();
                        
                        /* Get result */
                        $receiverAccount->bind_result($count);
                        $receiverAccount->fetch();
                        $receiverAccount->close();
                        
                        /* Payment of funds */
                        if ($count) {
                            $updateReceiverBalance = $db->prepare("UPDATE accountDirectory SET balance=balance+? WHERE accountNum=?");
                            $updateReceiverBalance->bind_param("di", $amount, $to);
                            $updateReceiverBalance->execute();
                            $updateReceiverBalance->close();
                        }
                        
                        /* Insert new transaction */
                        $insertTransaction = $db->prepare("INSERT INTO transactions (type, clientID, accountNum, transactionAmount, recipientAccount) VALUES ('payment', ?, ?, -?, ?)");
                        $insertTransaction->bind_param("iidi", $userID, $from, $amount, $to);
                        $insertTransaction->execute();
                        
                        if ($db->affected_rows > 0) {
                            $dbSuccess = true;
                            $dbMessage = 'Payment has been paid';
                        }
                        
                        $insertTransaction->close();
                    }
                    
                    if ($isRecurring) {
                        $date = date("Y-m-d", strtotime($date . ' +' . $step . ' days')); // Calculate next payment date
                        $isRecurring = $endDate > $date; // Ensure the next payment date is not over the end date
                    }
                }
                
                /* Schedule Payment */
                if (!$isToday || ($isToday && $isRecurring)) {
                    /* Insert new payment into database */
                    $insertPayment = $db->prepare("INSERT INTO payments (accountNum, recipientAccount, recipientNickName, amount, paymentDate, step, endDate) VALUES (?,?,?,?,?,?,?)");
                    $insertPayment->bind_param("sssdsis", $from, $to, $name, $amount, $date, $step, $endDate);
                    $insertPayment->execute();
                    
                    if ($db->affected_rows > 0) {
                        $dbSuccess = true;
                        $dbMessage = 'New payment created';
                        $dbPaymentId = encrypt($db->insert_id, $key);
                    } else {
                        $dbMessage = $db->error;
                    }
                    
                    $insertPayment->close();
                }
            }
            
            $db->close();
        }
    }
}

/* Return Outcome */
$object = (object)array();
$object->response = $dbSuccess;
$object->message = $dbMessage;
$object->id = $dbPaymentId;
$json = json_encode($object);
echo $json;
