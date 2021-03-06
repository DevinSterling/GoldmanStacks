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
$sender = decrypt($_POST['from'], $key);
$receiver = $_POST['to'];
$amount = $_POST['usd'];
$token = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbMessage = "Failed to process transaction";

/* Calculate expected token */
$calc = hash_hmac('sha256', '/newExternalTransfer.php', $_SESSION['key']);

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && checkNotEmpty($sender, $receiver, $amount)
    && $sender != $receiver) {
        
    /* Input Validation */
    $isMatch = preg_match('/^[0-9]{10}$/', $sender);
    $isMatch &= preg_match('/^[0-9]{10}$/', $receiver);
    $isMatch &= is_numeric($amount);

    if ($isMatch) {
        /* DB Connection */
        $db = getUpdateConnection();
        
        if ($db !== null) {
            /* Check if both internal accounts exist */
            $queryRequest = $db->prepare("SELECT (
                                        	SELECT COUNT(*) FROM accountDirectory where clientID=? AND accountNum=?
                                        ) AS senderExsists, (
                                        	SELECT COUNT(*) FROM accountDirectory where accountNum=?
                                        ) AS recipientExists");
            $queryRequest->bind_param("iss", $userID, $sender, $receiver);
            $queryRequest->execute();
            
            $queryRequest->bind_result($clientExists, $recipientExists);
            $queryRequest->fetch();
            $queryRequest->close();
            
            if ($clientExists) {
                /* Verify Balance */
                $queryRequest = $db->prepare("SELECT balance FROM accountDirectory WHERE clientID=? AND accountNum=?");
                $queryRequest->bind_param("is", $userID, $sender);
                $queryRequest->execute();
                
                $queryRequest->bind_result($balance);
                $queryRequest->fetch();
                $queryRequest->close();
                
                if ($balance >= $amount) {
                    $updateSenderBalance = $db->prepare("UPDATE accountDirectory SET balance=balance-? WHERE accountNum=?");
                    $updateSenderBalance->bind_param("di", $amount, $sender);
                    $updateSenderBalance->execute();
                    $updateSenderBalance->close();
                    
                    if ($recipientExists) {
                        $updateReceiverBalance = $db->prepare("UPDATE accountDirectory SET balance=balance+? WHERE accountNum=?");
                        $updateReceiverBalance->bind_param("di", $amount, $receiver);
                        $updateReceiverBalance->execute();
                        $updateReceiverBalance->close();
                    }
                    
                    $insertTransaction = $db->prepare("INSERT INTO transactions (type, clientID, accountNum, transactionAmount, recipientAccount) VALUES ('transfer', ?, ?, -?, ?)");
                    $insertTransaction->bind_param("iidi", $userID, $sender, $amount, $receiver);
                    $insertTransaction->execute();
                    
                    if ($db->affected_rows > 0) {
                        $dbSuccess = true;
                        $dbMessage = "Transferred $" . $amount . " to (*" . substr($receiver, -4) . ")";
                    }
    
                    $insertTransaction->close();
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
$json = json_encode($object);
echo $json;
