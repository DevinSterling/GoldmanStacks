<?php
require_once('../../../../../private/config.php');
require_once('../../../../../private/userbase.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkEmployeeStatus(); // Check if the employee is signed in

/* POST Variables */
$userID = $_POST['id'];
$phoneNumber = $_POST['phone'];
$token = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbMessage = "Failed to update phone number";

/* Confirm token and parameters */
$calc = hash_hmac('sha256', '/updatePhoneNumber.php', $_SESSION['key']);

if (hash_equals($calc, $token)
    && !empty($phoneNumber)) { // if true, non-empty parameter given
    
    /* Validate Input */
    $phoneNumber = str_replace('-', '', $phoneNumber); // Remove hyphens if provided by user
    $isMatch = is_numeric($userID);
    $isMatch = preg_match('/^\d{10}$/', $phoneNumber); // Check if phone number matches requirement
    
    if ($isMatch) { 
        /* DB Connection */
        $db = getUpdateConnection();

        if ($db !== null) {
            /* Prepared Statement */
            $updateStatement = $db->prepare("UPDATE users SET phoneNumber=? WHERE userID=?");
            $updateStatement->bind_param("si", $phoneNumber, $userID);
            $updateStatement->execute();

            /* Check Execution */
            if ($db->affected_rows > 0) {
                $dbSuccess = true;
                $dbMessage = "Phone number has been updated";
            } else {
                $dbMessage = "Provided phone number is registered already";
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
