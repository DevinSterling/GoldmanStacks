<?php
require_once('../../../private/config.php');
require_once('../../../private/userbase.php');
require_once('../../../private/functions.php');

/* Force https connection */
forceHTTPS();

/* Check if the user is logged in already */
session_start();
if(checkIfLoggedIn()) {
    die();
}

/* POST Variables */
$username = $_POST['username'];
$password = $_POST['password'];
$token = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbMessage = "";

$dbFailMessage = "Invalid Username or Password";

/* Calculate expected token */
$calc = hash_hmac('sha256', '/authenticateSignin.php', $_SESSION['key']);

/* Confirm token and user input */
if (hash_equals($calc, $token)
    && checkNotEmpty($username, $password)) { // if true, non-empty parameters given
    /* DB Connection */
    $db = getUpdateConnection();
    
    if ($db !== null) {
        /* Verify Current Password */
        $query = $db->prepare("SELECT userID, userRole, password FROM users WHERE email=?");
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result();
        
        /* Check if user exists */
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            /* Compare passwords */
            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                
                $_SESSION['uid'] = $user['userID']; // Set User Id for Session
                $_SESSION['role'] = $user['userRole']; // Set User Role for Session
                $_SESSION['key'] = bin2hex(random_bytes(32)); // Create Session Key for CSRF tokens
                
                $_SESSION['last_activity'] = time(); // Set active time (used for inactivity detection)
                $_SESSION['expiry_time'] = 10 * 60; // Time till timeout (10 minutes)
                
                $dbSuccess = true;
                $dbMessage = "Sign In Verified";
            } else {
                $dbMessage = $dbFailMessage;
            }
        } else {
            $dbMessage = $dbFailMessage;
        }
        
        /* Close Streams */
        $result->free();
        $query->close();
        $db->close();
    } else {
        $dbMessage = "Cannot connect to service";
    }
} else {
    $dbMessage = "$calc\n\n\n$token";
}

/* Return Outcome */
$myObj = (object)array();
$myObj->response = $dbSuccess;
$myObj->message = $dbMessage;
$myJSON = json_encode($myObj);
echo $myJSON;