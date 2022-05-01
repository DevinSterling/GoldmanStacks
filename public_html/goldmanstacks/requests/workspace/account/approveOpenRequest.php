<?php
require_once('../../../../../private/config.php');
require_once('../../../../../private/userbase.php');
require_once('../../../../../private/functions.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkEmployeeStatus(); // Check if the employee is signed in

/* SESSION Variables */
$key = $_SESSION['key'];

/* POST Variables */
$requestID = $_POST['id'];
$token = $_POST['token'];

/* Constants */
const MAX_ATTEMPTS = 10;

/* Object variables */
$dbResponse = false;
$dbMessage = 'Failed to approve open request';

/* Calculate expected token */
$calc = hash_hmac('sha256', '/approveOpenRequest.php', $key);

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && !empty($requestID)) {
    
    /* Input Validation */
    $isMatch = is_numeric($requestID);
    
    if ($isMatch) {
        /* Get database connection */
        $db = getUpdateConnection();
         
        /* Check connection */
        if ($db !== null) {
            /* Retreive client ID */
            $selectStatement = $db->prepare("SELECT clientID, accountType FROM accountRequests WHERE requestID=?");
            $selectStatement->bind_param("i", $requestID);
            $selectStatement->execute();
            $selectStatement->store_result();
            
            $selectStatement->bind_result($clientID, $accountType);
            $selectStatement->fetch();
            $selectStatement->close();
            
            if ($clientID !== null) {
                $count = 0;
                
                do {
                    /* Generate nickname for new bank account */
                    $accountName = ucfirst($accountType);
                    if ($count > 0) $accountName .= $count;
                    
                    /* Create new bank account for client */
                    $insertStatement = $db->prepare("INSERT INTO accountDirectory (accountType, nickName, clientID) VALUES (?, ?, ?)");
                    $insertStatement->bind_param("ssi", $accountType, $accountName, $clientID);
                    $insertStatement->execute();
                    
                    /* Check if successful */
                    if ($db->affected_rows > 0) {
                        $insertStatement->close();
                        
                        $deleteStatement = $db->prepare("DELETE FROM accountRequests WHERE requestID=?");
                        $deleteStatement->bind_param("i", $requestID);
                        $deleteStatement->execute();
                        
                        if ($db->affected_rows > 0) {
                            $dbResponse = true;
                            $dbMessage = "Open request ($requestID) has been approved";
                        }
                        
                        $deleteStatement->close();
                        break;
                    }
                    
                    $count++;
                } while ($count != MAX_ATTEMPTS);
            }
            
            $db->close();
        }
    }
}

$object = (object)array();
$object->response = $dbResponse;
$object->message = $dbMessage;

$json = json_encode($object);

echo $json;
