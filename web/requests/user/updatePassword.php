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
$oldPassword = $_POST['old'];
$newPassword = $_POST['new'];
$confirmPassword = $_POST['confirm'];
$token = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbFailMessage = "Failed To Update Password";
$dbMessage = "";

/* Confirm token and parameters */
$calc = hash_hmac('sha256', '/updatePassword.php', $_SESSION['key']);
if (hash_equals($calc, $token)
    && !(empty($oldPassword) || empty($newPassword))
    && $newPassword === $confirmPassword) { // if true, non-empty parameters given and passwords match
    
    /* DB Connection */
    $db = getUpdateConnection();
    
    if ($db !== null) {
        /* Verify Current Password */
        $query = $db->prepare("SELECT password FROM users WHERE userID=?");
        $query->bind_param("i", $userID);
        $query->execute();
        $result = $query->get_result();
        
        if ($result->num_rows > 0) {
            $password = $result->fetch_assoc();
            if ($oldPassword === $password['password']) {
                /* Encrypt Password */
                $encryptedPassword = $newPassword;
                
                /* Prepared Statement */
                $stmt = $db->prepare("UPDATE users SET password=? WHERE userID=?");
                $stmt->bind_param("si", $encryptedPassword, $userID);
                $stmt->execute();
                
                /* Check Execution */
                if ($db->affected_rows === 0) { // If 0, update failed to execute
                    $dbMessage = $dbFailMessage;
                } else {
                    $dbSuccess = true;
                    $dbMessage = "Password Has Been Updated";
                }
                
                /* Close Statement */
                $stmt->close();
            } else {
                $dbMessage = $dbFailMessage;
            }
        } else { // Concerning Warning (Possibly Invasive)
            $dbMessage = $dbFailMessage;
        }
        
        /* Close Streams */
        $result->free();
        $query->close();
        $db->close();
    } else {
        $dbMessage = "Cannot Connect To Database";
    }
}
else $dbMessage="Test";

/* Return Outcome */
$myObj = (object)array();
$myObj->response = $dbSuccess;
$myObj->message = $dbMessage;
$myJSON = json_encode($myObj);
echo $myJSON;
?>