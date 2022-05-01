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
$accountNumber = decrypt($_POST['id'], $key);
$token = $_POST['token'];

/* Object variables */
$dbResponse = false;
$dbMessage = 'Failed to approve close request';

/* Calculate expected token */
$calc = hash_hmac('sha256', '/approveCloseRequest.php', $key);

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && !empty($accountNumber)) {
    
    /* Input Validation */
    $isMatch = preg_match('/^[0-9]{10}$/', $accountNumber);
    
    if ($isMatch) {
        /* Get database connection */
        $db = getUpdateConnection();
        
        /* Check connection */
        if ($db !== null) {
            $deleteStatement = $db->prepare("DELETE FROM accountDirectory WHERE accountNum=? AND balance=0");
            $deleteStatement->bind_param("i", $accountNumber);
            $deleteStatement->execute();
            
            if ($db->affected_rows > 0) {
                $dbResponse = true;
                $dbMessage = "Close request for (*" . substr($accountNumber, -4) . ") has been approved";
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
