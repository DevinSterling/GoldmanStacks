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

/* Check if user is an employee */
function isEmployee() {
    if ($_SESSION['role'] === "employee") {
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

/* Check if a user has been inactive */
function checkInactive() {
    if ($_SESSION['last_activity'] < time() - $_SESSION['expiry_time'] ) { // Inactive User Condition
        header('Location: https://' . $_SERVER['HTTP_HOST'] . getHomeDirectory() . '/goldmanstacks/requests/signout.php?timeout=1');
        die();
    } else {
        $_SESSION['last_activity'] = time();
    }
}

/* Redirect user to their respective main page */
function redirect() {
    if (isClient()) {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . getHomeDirectory() . '/goldmanstacks/view/home.php'); // Redirect client to their home page
    } else if (isEmployee()) {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . getHomeDirectory() . '/goldmanstacks/view/workspace/manager.php'); // Redirect admin to workspace
    } else {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . getHomeDirectory() . '/goldmanstacks/view/signin.php'); // Redirect to sign in page
    }
    die();
}

/* Check the status and determine if the user is registered */
function checkUserStatus() {
    if (checkIfLoggedIn()) { // Check if user is logged in
        if (isEmployee() || isClient()) { // Check if user is a client or employee
            checkInactive(); // Check if the client has been inactive
            return;
        }
    }

    /* Guard Block */
    redirect();
}


/* Check the status and determine if a user is an employee */
function checkEmployeeStatus() {
    if (checkIfLoggedIn()) { // Check if user is logged in
        if (isEmployee()) { // Check if user is an employee
            checkInactive(); // Check if the employee has been inactive
            return;
        }
    }

    /* Guard Block */
    redirect();
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
