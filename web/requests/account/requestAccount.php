<?
require_once('/home/sterlid2/Private/config.php');
require_once('/home/sterlid2/Private/userbase.php');

/* Force https connection */
forceHTTPS();

/* Check if the user is logged in already and is a client */
session_start();
if(!checkIfLoggedIn() || !isClient()) {
    die();
}

$accountType = $_POST[''];
$accountNickName
?>