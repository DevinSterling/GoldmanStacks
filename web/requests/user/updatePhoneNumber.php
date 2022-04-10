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

/* Check if the user has been inactive */
if (checkInactive()) {
    header("Location: ../signout.php");
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

$dbFailMessage = "Failed to update phone number";

/* Confirm token and parameters */
$calc = hash_hmac('sha256', '/updatePhoneNumber.php', $_SESSION['key']);
if (hash_equals($calc, $token)
    && !empty($phoneNumber)) { // if true, non-empty parameter given
    
    /* Vlidate Input */
    $phoneNumber = str_replace('-', '', $phoneNumber); // Remove hyphens if provided by user
    $isMatch = preg_match('/^\d{10}$/', $phoneNumber); // Check if phone number matches requirement
    
    if ($isMatch) { 
        /* DB Connection */
        $db = getUpdateConnection();

        if ($db !== null) {
            /* Prepared Statement */
            $stmt = $db->prepare("UPDATE users SET phoneNumber=? WHERE userID=?");
            $stmt->bind_param("si", $phoneNumber, $userID);
            $stmt->execute();

            /* Check Execution */
            if ($db->affected_rows === 0) { // If 0, update failed to execute
                $dbMessage = $dbFailMessage;
            }
            else {
                $dbSuccess = true;
                $dbMessage = "Phone Number Has Been Updated";
            }

            /* Close Streams */
            $stmt->close();
            $db->close();
        } else {
            $dbMessage = $dbFailMessage;
        }
    } else {
        $dbMessage = $dbFailMessage;
    }
}

/* Return Outcome */
$myObj = (object)array();
$myObj->response = $dbSuccess;
$myObj->message = $dbMessage;
$myJSON = json_encode($myObj);
echo $myJSON;
?>
