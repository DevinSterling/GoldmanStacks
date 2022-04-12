<?php
// Forces https connection
function forceHTTPS() {
    if($_SERVER["HTTPS"] != "on") {
        header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        exit();
    }
}

/* Checks the status and determines if a user is a client */
function checkClientStatus() {
    forceHTTPS(); // Force client to use https
    checkIfLoggedIn(); // Check if client is logged in

    if ($_SESSION['role'] !== "client") { // Check if logged in user is a client
        header("Location: ../public_html/goldmanstacks/index.php");
    }
}

// Determines if a user is logged into the website
function checkIfLoggedIn() {
    if (isset($_SESSION['uid']) && isset($_SESSION['role'])) return true;
    return false;
}

// Inactivity Detection
function checkInactive() {
    if ($_SESSION['last_activity'] < time() - $_SESSION['expiry_time'] ) { // Inactive User Condition
        return true;
    } else {
        $_SESSION['last_activity'] = time();
        return false;
    }
}