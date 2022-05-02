<?php
require_once('../../../../private/config.php');
require_once('../../../../private/userbase.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkClientStatus(); // Check if the client is signed in

/* SESSION Variables */
$userID = $_SESSION["uid"];

/* POST Variables */
$accountType = $_POST['type'];
$token = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbMessage = "Failed to request new account";

$accountTypes = array('checking', 'savings', 'credit');

/* Calculate expected token */
$calc = hash_hmac('sha256', '/requestAccount.php', $_SESSION['key']);

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && !empty($accountType)) {
        
    /* Input Validation */
    $isMatch = in_array($accountType, $accountTypes);
    
    if ($isMatch) {
        /* DB Connection */
        $db = getUpdateConnection();
        
        if ($db !== null) {
            /* Insert account request (DB Constraints will prevent duplicates) */
            $insertRequest = $db->prepare("INSERT INTO accountRequests (clientID, accountType) VALUES (?, ?)");
            $insertRequest->bind_param("is", $userID, $accountType);
            $insertRequest->execute();

            /* Check execution */
            if ($db->affected_rows > 0) {
                $dbSuccess = true;
                $dbMessage = "Request to open new $accountType account submitted";
            }

            $insertRequest->close();
            $db->close();
        }
    }
}

/* Return Outcome */
$myObj = (object)array();
$myObj->response = $dbSuccess;
$myObj->message = $dbMessage;
$myJSON = json_encode($myObj);
echo $myJSON;
