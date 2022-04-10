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

/* SESSION Variables */
$userID = $_SESSION["uid"];

/* POST Variables */
$phoneNumber = $_POST['phone'];
$token = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbMessage = "";

/* Confirm token and parameters */
$calc = hash_hmac('sha256', '/updatePhoneNumber.php', $_SESSION['key']);
if (hash_equals($calc, $token)
    && !empty($phoneNumber)) { // if true, non-empty parameter given
    /* DB Connection */
    $db = getUpdateConnection();
    
    if ($db !== null) {
        /* Prepared Statement */
        $stmt = $db->prepare("UPDATE users SET phoneNumber=? WHERE userID=?");
        $stmt->bind_param("si", $phoneNumber, $userID);
        $stmt->execute();
        
        /* Check Execution */
        if ($db->affected_rows === 0) { // If 0, update failed to execute
            $dbMessage = "Failed To Update Phone Number";
        }
        else {
            $dbSuccess = true;
            $dbMessage = "Phone Number Has Been Updated";
        }
        
        /* Close Streams */
        $stmt->close();
        $db->close();
    } else {
        $dbMessage = "Cannot Connect To Database";
    }
}

/* Return Outcome */
$myObj = (object)array();
$myObj->response = $dbSuccess;
$myObj->message = $dbMessage;
$myJSON = json_encode($myObj);
echo $myJSON;
?>