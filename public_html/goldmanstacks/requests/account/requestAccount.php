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
$dbMessage = "";

$accountTypes = array('debit', 'savings', 'credit');

$dbFailMessage = "Failed to request";

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
            /* Check if the account type has been requested by the user already */
            $queryRequest = $db->prepare("SELECT clientID FROM accountRequests WHERE clientID=? AND accountType=?");
            $queryRequest->bind_param("is", $userID, $accountType);
            $queryRequest->execute();
            
            /* Get result and close */
            $result = $queryRequest->get_result();
            $queryRequest->close();
            
            if ($result->num_rows === 0) {
                /* Insert account request */
                $insertRequest = $db->prepare("INSERT INTO accountRequests (clientID, accountType) VALUES (?, ?)");
                $insertRequest->bind_param("is", $userID, $accountType);
                $insertRequest->execute();
                
                /* Check execution */
                if ($db->affected_rows > 0) {
                    $dbSuccess = true;
                    $dbMessage = "Request to open new $accountType account submitted";
                } else {
                    $dbMessage = $dbFailMessage;
                }
                
                $insertRequest->close();
            } else {
                $dbMessage = "You have already requested an account";
            }
            
            $result->free();
            $db->close();
        }
    } else {
        $dbMessage = $dbFailMessage;
    }
} else {
    $dbMessage = $dbFailMessage;
}

/* Return Outcome */
$myObj = (object)array();
$myObj->response = $dbSuccess;
$myObj->message = $dbMessage;
$myJSON = json_encode($myObj);
echo $myJSON;
?>
