<?
require_once('/home/sterlid2/Private/config.php');
require_once('/home/sterlid2/Private/userbase.php');

/* Force https connection */
forceHTTPS();

/* Check if the user is logged in already */
session_start();
if(checkIfLoggedIn()) {
    die();
}

/* POST Variables */
$username = $_POST['username'];
$password = $_POST['password'];
$token = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbFailMessage = "Invalid Credentials Provided";
$dbMessage = "";

/* Confirm token and parameters */
$calc = hash_hmac('sha256', '/authenticateSignin.php', $_SESSION['key']);
if (hash_equals($calc, $token)
    && !(empty($username) || empty($password))) { // if true, non-empty parameters given and passwords match
    /* DB Connection */
    $db = getUpdateConnection();
    
    if ($db !== null) {
        /* Verify Current Password */
        $query = $db->prepare("SELECT userID, userRole, password FROM users WHERE email=?");
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result();
        
        /* Check if user exists */
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            /* Compare passwords */
            if ($password === $user['password']) {
                /* Start Session */
                session_start();
                session_regenerate_id(true);
                
                $_SESSION['uid'] = $user['userID']; // Set User Id for Session
                $_SESSION['role'] = $user['userRole']; // Set User Role for Session
                $_SESSION['key'] = bin2hex(random_bytes(32)); // Create Session Key for CSRF tokens
                
                $dbSuccess = true;
            } else {
                $dbMessage = $dbFailMessage;
            }
        } else {
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

/* Return Outcome */
$myObj = (object)array();
$myObj->response = $dbSuccess;
$myObj->message = $dbMessage;
$myJSON = json_encode($myObj);
echo $myJSON;
?>
