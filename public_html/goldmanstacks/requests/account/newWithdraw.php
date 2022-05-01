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
$dbMessage = 'Failed to withdraw';

/* Calculate expected token */
$calc = hash_hmac('sha256', '/newWithdraw.php', $key);

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && !empty($accountNumber)) {
    
    /* Input validation */
    $isMatch = preg_match('/^[0-9]{10}$/', $accountNumber);
    
    if ($isMatch) {
        /* Get database connection */
        $db = getUpdateConnection();
        
        /* Verify connection */
        if ($db !== null) {
            $balanceStatement = $db->prepare("SELECT balance FROM accountDirectory WHERE accountNum=? AND clientID=?");
            $balanceStatement->bind_param("ii", $accountNumber, $userID);
            $balanceStatement->execute();
            $balanceStatement->store_result();
            
            /* Get balance from statement */
            $balanceStatement->bind_result($balance);
            $balanceStatement->fetch();
            $balanceStatement->close();
            
            /* Ensure the amount requested is not greater than the balance */
            if ($balance >= $amount) {
                /* Ensure the user is depositing funds into their own account */
                $updateStatement = $db->prepare("UPDATE accountDirectory SET balance=balance-? WHERE accountNum=? AND clientID=?");
                $updateStatement->bind_param("dii", $amount, $accountNumber, $userID);
                $updateStatement->execute();
                
                if ($db->affected_rows > 0) {
                    $updateStatement->close();
                    
                    /* Insert new transaction */
                    $insertTransaction = $db->prepare("INSERT INTO transactions (type, clientID, accountNum, transactionAmount) VALUES ('withdraw', ?, ?, -?)");
                    $insertTransaction->bind_param("iid", $userID, $accountNumber, $amount);
                    $insertTransaction->execute();
                    
                    if ($db->affected_rows > 0) {
                        $dbSuccess = true;
                        $dbMessage = 'Funds have been withdrawn';  
                    }
                    
                    $insertTransaction->close();
                }
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
