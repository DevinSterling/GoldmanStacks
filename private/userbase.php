<?
function forceHTTPS() {
    if($_SERVER["HTTPS"] != "on") {
        header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        exit();
    }
}

// Determines if a user is logged into the website
function checkIfLoggedIn() {
    if (isset($_SESSION['uid']) && isset($_SESSION['role'])) return true;
    return false;
}

// Check if the current user is a client
function isClient() {
    if ($_SESSION['role'] === "client") return true;
    return false;
}
?>
