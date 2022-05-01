<?php
require_once('../../../../../private/config.php');
require_once('../../../../../private/userbase.php');
require_once('../../../../../private/functions.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkEmployeeStatus(); // Check if the employee is signed in

session_start();

/* SESSION Variables */
$key = $_SESSION['key'];

/* POST Variables */
$requestID = $_POST['id'];
$token = $_POST['token'];

/* Object variables */
$dbResponse = false;
$dbMessage = 'Failed to reject open request';

/* Calculate expected token */
$calc = hash_hmac('sha256', '/rejectOpenRequest.php', $key);

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
            $deleteStatement = $db->prepare("DELETE FROM accountRequests WHERE requestId=?");
            $deleteStatement->bind_param("i", $requestID);
            $deleteStatement->execute();
            
            if ($db->affected_rows > 0) {
                $dbResponse = true;
                $dbMessage = "Open request ($requestID) has been rejected";
            }
            
            $deleteStatement->close();
            $db->close();
        }
    }
}

$object = (object)array();
$object->response = $dbResponse;
$object->message = $dbMessage;

$json = json_encode($object);

echo $json;
