<?php
require_once('../../../../../../../private/config.php');
require_once('../../../../../../../private/userbase.php');
require_once('../../../../../../../private/functions.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkEmployeeStatus(); // Check if the employee is signed in

session_start();

/* SESSION Variables */
$key = $_SESSION['key'];

/* POST Variables */
$clientID = $_POST['id'];
$token = $_POST['token'];

/* Object variables */
$dbSuccess = false;
$dbMessage = 'Failed to approve client registration request';

/* Calculate expected token */
$calc = hash_hmac('sha256', '/rejectRegistration.php', $key);

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && !empty($clientID)) {
    
    /* Input Validation */
    $isMatch = is_numeric($clientID);
    
    if ($isMatch) {
        /* Get database connection */
        $db = getUpdateConnection();
         
        /* Check connection */
        if ($db !== null) {
            $deleteStatement = $db->prepare("DELETE FROM users WHERE userID=?");
            $deleteStatement->bind_param("i", $clientID);
            $deleteStatement->execute();
            
            if ($db->affected_rows > 0) {
                $dbSuccess = true;
                $dbMessage = "Registration request for ($clientID) has been rejected";
            }
            
            $db->close();
        }
    }
}

$object = (object)array();
$object->response = $dbSuccess;
$object->message = $dbMessage;

$json = json_encode($object);

echo $json;
