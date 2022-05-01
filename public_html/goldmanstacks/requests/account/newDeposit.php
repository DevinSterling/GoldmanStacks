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
$amount = $_POST['amount'];
$token = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbMessage = 'Failed to deposit';

/* Calculate expected token */
$calc = hash_hmac('sha256', '/newDeposit.php', $key);

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && checkNotEmpty($accountNumber, $amount)) {
    
    /* Input validation */
    $isMatch = preg_match('/^[0-9]{10}$/', $accountNumber);
    
    if ($isMatch) {
        /* Get database connection */
        $db = getUpdateConnection();
        
        /* Verify connection */
        if ($db !== null) {
            /* Ensure the user is depositing funds into their own account */
            $updateStatement = $db->prepare("UPDATE accountDirectory SET balance=balance+? WHERE accountNum=? AND clientID=?");
            $updateStatement->bind_param("dii", $amount, $accountNumber, $userID);
            $updateStatement->execute();
            
            if ($db->affected_rows > 0) {
                $updateStatement->close();
                
                /* Insert new transaction */
                $insertTransaction = $db->prepare("INSERT INTO transactions (type, clientID, accountNum, transactionAmount) VALUES ('deposit', ?, ?, ?)");
                $insertTransaction->bind_param("iid", $userID, $accountNumber, $amount);
                $insertTransaction->execute();
                
                if ($db->affected_rows > 0) {
                    $dbSuccess = true;
                    $dbMessage = 'Funds have been deposited';  
                }
                
                $insertTransaction->close();
            }
            
            $db->close();
        }
    }
}

$response = (object)array();
$response->response = $dbSuccess;
$response->message = $dbMessage;

$json = json_encode($response);

echo $json;
