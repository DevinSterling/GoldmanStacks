<?php
require_once('../../../private/config.php');
require_once('../../../private/userbase.php');
require_once('../../../private/functions.php');

forceHTTPS(); // Force https connection
session_start(); // Start session

/* Check if a user is logged in already */
if (checkIfLoggedIn()) die();

/* POST Variables */
/* Name related info */
$firstName = $_POST['first-name'];
$middleName = $_POST['middle-name'];
$lastName = $_POST['last-name'];
$birthDate = $_POST['birth-date'];

/* Security related info */
$email = $_POST['email'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirm-password'];
$phoneNumber = $_POST['phone-number'];
$ssn = $_POST['ssn'];

/* Address related info */
$addressLine1 = $_POST['address-line1'];
$addressLine2 = $_POST['address-line2'];
$addressCity = $_POST['address-city'];
$addressState = $_POST['address-state'];
$addressPostalCode = $_POST['address-postal-code'];

/* Token */
$token = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbMessage = "";

$dbFailMessage = "Failed to register account";

/* Calculate expected token */
$calc = hash_hmac('sha256', '/authenticateRegistration.php', $_SESSION['key']);

/* Confirm token and user input */
if (hash_equals($calc, $token) // Check token
    && checkNotEmpty($firstName, $lastName, $birthDate, $email, $password, $phoneNumber, $ssn, $addressLine1, $addressCity, $addressState, $addressPostalCode) // Check for empty parameters
    && ($password === $confirmPassword)) { // Check if given passwords match
    
    /* String Manipulation */
    $phoneNumber = str_replace('-', '', $phoneNumber); // Remove hyphens if provided by user
    $ssn = str_replace('-', '', $ssn); // Remove hyphens if provided by user
    $birthDate = date("Y-m-d", strtotime($birthDate)); // Convert HTML date to SQL compatible date

    /* Input Validation */
    $isMatch = (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    $isMatch &= ctype_alpha($firstName);
    $isMatch &= ctype_alpha($lastName);
    $isMatch &= preg_match('/^\d{10}$/', $phoneNumber);
    $isMatch &= preg_match('/^\d{9}$/', $ssn);
    $isMatch &= preg_match('/^\d+ [A-z ]+.?$/', $addressLine1);
    $isMatch &= preg_match('/^[A-z. ]+$/', $addressCity);
    $isMatch &= preg_match('/^[A-Z]{2}$/', $addressState);
    $isMatch &= preg_match('/^[0-9]{5}$/', $addressPostalCode);
    $isMatch &= date_diff(date_create($birthDate), date_create('now'))->y > 18;

    /* Optional Input Validation */
    if (!empty($middleName)) $isMatch &= ctype_alpha($middleName);
    else $middleName = NULL;
    if (!empty($addressLine2)) $isMatch &= preg_match('/^[A-z0-9#, ]+$/', $addressLine2);
    else $addressLine2 = NULL;
    
    if ($isMatch) {
        /* DB Connection */
        $db = getUpdateConnection();
      
        if ($db !== null) {
            /* Query to search if email is registered already */
            $queryEmail = $db->prepare("SELECT email FROM users WHERE email=?");
            $queryEmail->bind_param("s", $email);
            $queryEmail->execute();
            $result = $queryEmail->get_result();
            
            /* Verify if email is not registered yet */
            if ($result->num_rows === 0) {
                /* Query to search if phone number is registered already */
                $queryPhoneNumber = $db->prepare("SELECT phoneNumber FROM users WHERE phoneNumber=?");
                $queryPhoneNumber->bind_param("s", $phoneNumber);
                $queryPhoneNumber->execute();
                $result = $queryPhoneNumber->get_result();
                
                /* Verify if phone number is not registered yet */
                if ($result->num_rows === 0) {
                    /* Encrypt provided password */
                    $password = password_hash($password, PASSWORD_DEFAULT);
                    
                    /* Insert user into database */
                    $insertUser = $db->prepare("INSERT INTO users (userRole, email, password, firstName, middleName, lastName, phoneNumber, ssn) VALUES ('client', ?, ?, ?, ?, ?, ?, ?)");
                    $insertUser->bind_param("sssssss", $email, $password, $firstName, $middleName, $lastName, $phoneNumber, $ssn);
                    $insertUser->execute();
                  
                    $clientId = $insertUser->insert_id;
                    $insertUser->close();
                    
                    /* Insert associated client information */
                    $insertClient = $db->prepare("INSERT INTO client VALUES (?, 0, ?)");
                    $insertClient->bind_param("is", $clientId, $birthDate);
                    $insertClient->execute();
                    $insertClient->close();
                    
                    /* Insert associated address information */
                    $insertAddress = $db->prepare("INSERT INTO address VALUES (?, ?, ?, ?, ?, ?)");
                    $insertAddress->bind_param("isssss", $clientId, $addressLine1, $addressLine2, $addressCity, $addressState, $addressPostalCode);
                    $insertAddress->execute();
    
                    /* Check Execution */
                    if ($db->affected_rows > 0) {
                        $dbSuccess = true;
                        $dbMessage = "Account has been registered";
                    } else {
                        $dbMessage = $db->error;
                    }
                  
                    $insertAddress->close();
                } else {
                    $dbMessage = "Provided phone number is registered already";
                }
            } else {
                $dbMessage = "Provided email is registered already";
            }

            /* Close Streams */
            $result->free();
            $queryEmail->close();
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
