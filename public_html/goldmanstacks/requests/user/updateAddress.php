<?php
require_once('../../../../private/config.php');
require_once('../../../../private/userbase.php');
require_once('../../../../private/functions.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkClientStatus(); // Check if the client is signed in

/* SESSION Variables */
$userID = $_SESSION["uid"];

/* POST Variables */
$addressLine1 = $_POST['line1'];
$addressLine2 = $_POST['line2'];
$addressCity = $_POST['city'];
$addressState = $_POST['state'];
$addressPostalCode = $_POST['code'];
$token = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbMessage = "Failed to update address";

/* Calculate expected token */
$calc = hash_hmac('sha256', '/updateAddress.php', $_SESSION['key']);

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && checkNotEmpty($addressLine1, $addressCity, $addressState, $addressPostalCode)) { // if true, non-empty parameters given
    
    /* Input Validation */
    $isMatch = preg_match('/^\d+ [A-z ]+.?$/', $addressLine1);
    $isMatch &= preg_match('/^[A-z. ]+$/', $addressCity);
    $isMatch &= preg_match('/^[A-Z]{2}$/', $addressState);
    $isMatch &= preg_match('/^[0-9]{5}$/', $addressPostalCode);
       
    if (!empty($addressLine2)) $isMatch &= preg_match('/^[A-z0-9#, ]+$/', $addressLine2);
    else $addressLine2 = NULL;

    if ($isMatch) {
        /* DB Connection */
        $db = getUpdateConnection();

        if ($db !== null) {
            /* Prepared Statement */
            $stmt = $db->prepare("UPDATE address SET line1=?, line2=?, city=?, state=?, postalCode=? WHERE userID=?");
            $stmt->bind_param("sssssi", $addressLine1, $addressLine2, $addressCity, $addressState, $addressPostalCode, $userID);
            $stmt->execute();

            /* Check Execution */
            if ($db->affected_rows > 0) {
                $dbSuccess = true;
                $dbMessage = "Address Has Been Updated";
            }

            /* Close Streams */
            $stmt->close();
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
