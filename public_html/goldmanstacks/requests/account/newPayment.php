<?php
require_once('../../../../private/config.php');
require_once('../../../../private/userbase.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkClientStatus(); // Check if the client is signed in

/* SESSION Variables */
$userID = $_SESSION["uid"];

/* POST Variables */
$accountType = $_POST['type'];
$token = $_POST['token'];

/* Defaults */
$dbSuccess = true;
$dbMessage = "Fetch API Connected";

$dbFailMessage = "Failed to proccess payment";

/* Return Outcome */
$object = (object)array();
$object->response = $dbSuccess;
$object->message = $dbMessage;
$json = json_encode($object);
echo $json;
