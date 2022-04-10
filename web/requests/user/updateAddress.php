<?
require_once('/home/sterlid2/Private/config.php');
require_once('/home/sterlid2/Private/userbase.php');

/* Check if the user is logged in already and is a client */
session_start();
if(!checkIfLoggedIn() || !isClient()) {
    die();
}

/* Check if the user has been inactive */
if (checkInactive()) {
    header("Location: requests/signout.php");
    die();
}

/* SESSION Variables */
$userID = $_SESSION["uid"];

/* POST Variables */
$addressLine1 = $_POST['line1'];
$addressLine2 = $_POST['line2'];
$addressCity = $_POST['city'];
$addressState = $_POST['state'];
$addressPostalCode = $_POST['code'];
$token = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbMessage = "";

/* Confirm token and parameters */
$calc = hash_hmac('sha256', '/updateAddress.php', $_SESSION['key']);
if (hash_equals($calc, $token)
    && !(empty($addressLine1)
    || empty($addressCity)
    || empty($addressState)
    || empty($addressPostalCode))) { // if true, non-empty parameters given
    /* DB Connection */
    $db = getUpdateConnection();
    
    if ($db !== null) {
        /* Prepared Statement */
        $stmt = $db->prepare("UPDATE address SET line1=?, line2=?, city=?, state=?, postalCode=? WHERE userID=?");
        $stmt->bind_param("sssssi", $addressLine1, $addressLine2, $addressCity, $addressState, $addressPostalCode, $userID);
        $stmt->execute();
        
        /* Check Execution */
        if ($db->affected_rows === 0) { // If 0, update failed to execute
            $dbMessage = "Failed To Update Address";
        }
        else {
            $dbSuccess = true;
            $dbMessage = "Address Has Been Updated";
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
