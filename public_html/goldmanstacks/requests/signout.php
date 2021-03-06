<?php
require_once('../../../private/userbase.php');

session_start(); // start session

/* Check if the user is signed in to sign out */
if (checkIfLoggedIn()) {
    /* Used to notify if the user has been signed out due to inactivity */
    $isInactive = (bool)$_GET['timeout'];
    
    /* Unset all session variables. */
    $_SESSION = array();
    
    /* Delete the session cookie */
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    /* Destroy the session. */
    session_destroy();
    
    /* Redirect if inactive */
    if ($isInactive) {
        header("Location: ../view/signin.php?timeout=1");
        die();
    }
}

/* Redirect user */
header("Location: ../view/signin.php");
