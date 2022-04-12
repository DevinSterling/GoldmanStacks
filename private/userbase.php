<?php
/* Force browser to use https connection */
function forceHTTPS() {
    if($_SERVER["HTTPS"] != "on") {
        header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        exit();
    }
}

/* Check if a user is signed in */
function checkIfLoggedIn() {
    if (isset($_SESSION['uid']) && isset($_SESSION['role'])) {
        return true;
    } else {
        return false;
    }
}

/* Check if user is a client */
function isClient() {
    if ($_SESSION['role'] === "client") {
        return true;
    } else {
        return false;
    }
}

/* Check if user is an admin */
function isAdmin() {
    if ($_SESSION['role'] === "admin") {
        return true;
    } else {
        return false;
    }
}

/* Check if a user has been inactive */
function checkInactive() {
    if ($_SESSION['last_activity'] < time() - $_SESSION['expiry_time'] ) { // Inactive User Condition
        header("Location: ../goldmanstacks/requests/signout.php");
        die();
    } else {
        $_SESSION['last_activity'] = time();
    }
}

/* Redirect user to their respective main page */
function redirect() {
    if (isClient()) {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . getHomeDirectory() . '/goldmanstacks/view/home.php'); // Redirect client to their home page
        die();
    } else if (isAdmin()) {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . getHomeDirectory() . '/goldmanstacks/view/workspace/manage.php'); // Redirect admin to workspace
        die();
    } else {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . getHomeDirectory() . '/goldmanstacks/view/signin.php'); // Redirect to sign in page
        die();
    }
}

/* Check the status and determine if a user is a client */
function checkClientStatus() {
    if (checkIfLoggedIn()) { // Check if user is logged in
        if (isClient()) { // Check if user is a client
            checkInactive(); // Check if the client has been inactive
            return;
        }
    }

    /* Guard Block */
    redirect();
}

/* Check the status and determine if a user is a visitor (not signed into the site) */
function checkVisitorStatus() {
    if (checkIfLoggedIn()) { // Redirect a signed in user to their main page
        redirect();
    } else {
        /* Generate a key for the visitor for form access (csrf protection) */
        if (!isset($_SESSION['key'])) {
            $_SESSION['key'] = bin2hex(random_bytes(32));
        }
    }
}

 /* Temporary solution/function (only for university): Different home directory compatibility */
 function getHomeDirectory() {
     return '/' . strtok($_SERVER["REQUEST_URI"], '/');
 }
