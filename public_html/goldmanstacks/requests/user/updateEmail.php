<?php
require_once('../../../../private/config.php');
require_once('../../../../private/userbase.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkClientStatus(); // Check if the client is signed in

/* SESSION Variables */
$userID = $_SESSION["uid"];

/* POST Variables */
$email = $_POST['email'];
$token = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbMessage = "Failed to update email address";

/* Calculate expected token */
$calc = hash_hmac('sha256', '/updateEmail.php', $_SESSION['key']);

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && !empty($email)) {
    
    /* Input Validation */
    $isMatch = (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    
    if ($isMatch) {
        /* DB Connection */
        $db = getUpdateConnection();

        if ($db !== null) {
            /* Prepared Statement */
            $updateStatement = $db->prepare("UPDATE users SET email=? WHERE userID=?");
            $updateStatement->bind_param("si", $email, $userID);
            $updateStatement->execute();

            /* Check Execution */
            if ($db->affected_rows > 0) {
                $dbSuccess = true;
                $dbMessage = "Email address has been updated";
            } else {
                $dbMessage = "Provided email is registered already";
            }

            /* Close Streams */
            $updateStatement->close();
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
