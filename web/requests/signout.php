<?
require_once('/home/sterlid2/Private/userbase.php');

/* Check if the user is logged in already */
session_start();
if (checkIfLoggedIn()) {
    // Unset all session variables.
    $_SESSION = array();
    
    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session.
    session_destroy();
}

// Redirect
header("Location: ../signin.php");
?>
