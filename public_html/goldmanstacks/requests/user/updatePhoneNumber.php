<?php
require_once('../../../../private/config.php');
require_once('../../../../private/userbase.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkClientStatus(); // Check if the client is signed in

/* SESSION Variables */
$userID = $_SESSION["uid"];

/* POST Variables */
$phoneNumber = $_POST['phone'];
$token = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbMessage = "";

$dbFailMessage = "Failed to update phone number";

/* Confirm token and parameters */
$calc = hash_hmac('sha256', '/updatePhoneNumber.php', $_SESSION['key']);
if (hash_equals($calc, $token)
    && !empty($phoneNumber)) { // if true, non-empty parameter given
    
    /* Vlidate Input */
    $phoneNumber = str_replace('-', '', $phoneNumber); // Remove hyphens if provided by user
    $isMatch = preg_match('/^\d{10}$/', $phoneNumber); // Check if phone number matches requirement
    
    if ($isMatch) { 
        /* DB Connection */
        $db = getUpdateConnection();

        if ($db !== null) {
            /* Prepared Statement */
            $stmt = $db->prepare("UPDATE users SET phoneNumber=? WHERE userID=?");
            $stmt->bind_param("si", $phoneNumber, $userID);
            $stmt->execute();

            /* Check Execution */
            if ($db->affected_rows === 0) { // If 0, update failed to execute
                $dbMessage = $dbFailMessage;
            }
            else {
                $dbSuccess = true;
                $dbMessage = "Phone number has been updated";
            }

            /* Close Streams */
            $stmt->close();
            $db->close();
        } else {
            $dbMessage = $dbFailMessage;
        }
    } else {
        $dbMessage = $dbFailMessage;
    }
} else {
    die();
}

/* Return Outcome */
$myObj = (object)array();
$myObj->response = $dbSuccess;
$myObj->message = $dbMessage;
$myJSON = json_encode($myObj);
echo $myJSON;
?>
