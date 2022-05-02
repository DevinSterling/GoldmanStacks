<?php
require_once('../../../private/config.php');
require_once('../../../private/userbase.php');
require_once('../../../private/functions.php');

forceHTTPS(); // Force https connection
session_start(); // Start session

/* Check if a user is logged in already */
if (checkIfLoggedIn()) die();

/* POST Variables */
$username = $_POST['username'];
$password = $_POST['password'];
$token = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbMessage = "Invalid Username or Password";

/* Calculate expected token */
$calc = hash_hmac('sha256', '/authenticateSignin.php', $_SESSION['key']);

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && checkNotEmpty($username, $password)) { // if true, non-empty parameters given
    /* DB Connection */
    $db = getUpdateConnection();
    
    if ($db !== null) {
        /* Verify Current Password */
        $query = $db->prepare("SELECT userID, userRole, password, IFNULL((
                                    SELECT verified FROM client WHERE clientID=userID
                                ), 1) AS isVerified 
                                FROM users WHERE email=?");
        $query->bind_param("s", $username);
        $query->execute();
        $query->store_result();
        $query->bind_result($userID, $userRole, $hashedPassword, $isVerified);
        $query->fetch();
        $query->close();
        
        /* Check if user exists and if passwords match */
        if ($isVerified && password_verify($password, $hashedPassword)) {
            session_regenerate_id(true);
            
            $_SESSION['uid'] = $userID; // Set User Id for Session
            $_SESSION['role'] = $userRole; // Set User Role for Session
            $_SESSION['key'] = bin2hex(random_bytes(32)); // Create Session Key for CSRF tokens
            
            $_SESSION['last_activity'] = time(); // Set active time (used for inactivity detection)
            $_SESSION['expiry_time'] = 10 * 60; // Time till timeout (10 minutes)
            
            /* Get current last sign in */
            $selectLastSignIn = $db->prepare("SELECT lastSignin FROM users WHERE userID=?");
            $selectLastSignIn->bind_param("i", $userID);
            $selectLastSignIn->execute();
            $selectLastSignIn->store_result();
            $selectLastSignIn->bind_result($_SESSION['lastSignin']);
            $selectLastSignIn->fetch();
            $selectLastSignIn->close();
            
            /* Update last sign in information for current user */
            $updateLastSignIn = $db->prepare("UPDATE users SET lastSignin=CURRENT_TIMESTAMP() WHERE userID=?");
            $updateLastSignIn->bind_param("i", $userID);
            $updateLastSignIn->execute();
            $updateLastSignIn->close();
            
            $dbSuccess = true;
            $dbMessage = "Sign In Verified";
        }
        
        $db->close();
    } else {
        $dbMessage = "Cannot connect to service";
    }
} else {
    header("Location: signout.php");
}

/* Return Outcome */
$myObj = (object)array();
$myObj->response = $dbSuccess;
$myObj->message = $dbMessage;
$myJSON = json_encode($myObj);
echo $myJSON;
