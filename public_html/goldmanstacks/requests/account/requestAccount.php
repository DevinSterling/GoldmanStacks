<?php
require_once('../../../../private/config.php');
require_once('../../../../private/userbase.php');

/* Force https connection */
forceHTTPS();

/* Check if the user is logged in already and is a client */
session_start();
if(!checkIfLoggedIn() || !isClient()) {
    die();
}

/* Check if the user has been inactive */
if (checkInactive()) {
    header("Location: ../signout.php");
    die();
}

$accountType = $_POST[''];
$accountNickName
?>
