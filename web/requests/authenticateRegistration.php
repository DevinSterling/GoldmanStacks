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
/* Name related info */
$firstName = $_POST['first-name'];
$middleName = $_POST['middle-name'];
$lastName = $_POST['last-name'];

/* Security related info */
$email = $_POST['email'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirm-password'];
$phoneNumber = $_POST['phone-number'];
$ssn = $_POST['ssn'];

/* Address related info */
$addressLine1 = $_POST['address-line1'];
$addressLine2 = $_POST['address-line2'];
$addressCity = $_POST['address-city'];
$addressState = $_POST['address-state'];
$addressPostalCode = $_POST['address-postal-code'];

/* Token */
$token = $_POST['token'];

/* Defaults */
$dbSuccess = false;
$dbMessage = "";

$dbFailMessage = "Failed to register account";

/* Confirm token and parameters */
$calc = hash_hmac('sha256', '/authenticateRegistration.php', $_SESSION['key']);
if (hash_equals($calc, $token) // Check token
    && !(empty($firstName) // Check for empty parameters
    || empty($lastName)
    || empty($email)
    || empty($password)
    || empty($phoneNumber)
    || empty($ssn)
    || empty($addressLine1)
    || empty($addressCity)
    || empty($addressState)
    || empty($addressPostalCode))
    && ($password === $confirmPassword)) { // Check if given passwords match
    
    $phoneNumber = str_replace('-', '', $phoneNumber); // Remove hyphens if provided by user
    $ssn = str_replace('-', '', $ssn); // Remove hyphens if provided by user
  
    /* Input Validation */
    $isMatch = (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    $isMatch &= ctype_alpha($firstName);
    $isMatch &= ctype_alpha($lastName);
    $isMatch &= preg_match('/^\d{10}$/', $phoneNumber); // Check if phone number matches requirement
    $isMatch &= preg_match('/^\d{9}$/', $ssn);
    $isMatch &= preg_match('/^\d+ [A-z ]+.?$/', $addressLine1);
    $isMatch &= preg_match('/^[A-z. ]+$/', $addressCity);
    $isMatch &= preg_match('/^[A-z ]+$/', $addressState);
    $isMatch &= preg_match('/^[0-9]{5}$/', $addressPostalCode);
    
    if (!empty($middleName)) $isMatch &= ctype_alpha($middleName);
    if (!empty($addressLine2)) $isMatch &= preg_match('/^[A-z0-9#, ]+$/', $addressLine2);
  
    if ($isMatch) {
        /* DB Connection */
        $db = getUpdateConnection();
      
        if ($db !== null) {
            $queryEmail = $db->prepare("SELECT email FROM users WHERE email=?");
            $queryEmail->bind_param("s", $email);
            $queryEmail->execute();
            $result = $queryEmail->get_result();
            
            /* Verify if email is not registered yet */
            if ($result->num_rows > 0) {
                /* Encrypt provided password */
                $password = password_hash($password, PASSWORD_DEFAULT);
                
                /* Verify Current Password */
                $insertClient = $db->prepare("INSERT INTO users (userRole, email, password, firstName, middleName, lastName, phoneNumber, ssn) VALUES ('client', ?, ?, ?, ?, ?, ?, ?)");
                $insertClient->bind_param("sssssss", $email, $password, $firstName, $middleName, $lastName, $phoneNumber, $ssn);
                $insertClient->execute();
              
                $clientId = $insertClient->insert_id;
                $insertClient->close();
                
                $insertAddress = $db->prepare("INSERT INTO address VALUES (?, ?, ?, ?, ?) WHERE userID=?");
                $insertAddress->bind_param("sssssi", $addressLine1, $addressLine2, $addressCity, $addressState, $addressPostalCode, $clientId);
                $insertAddress->execute();

                /* Check Execution */
                if ($db->affected_rows === 0) {
                    $dbSuccess = true;
                    $dbMessage = "Account has been registered";
                } else {
                    $dbMessage = "FAIL:".$db->error;
                }
              
                $insertAddress->close();
            } else {
                $dbMessage = "Provided email is registered already";
            }

            /* Close Streams */
            $result->free();
            $queryEmail->close();
            $db->close();
        } else {
            $dbMessage = $dbFailMessage;
        }
    } else {
        $dbMessage = $dbFailMessage;
    }
} else {
    $dbMessage = "test";
}

/* Return Outcome */
$myObj = (object)array();
$myObj->response = $dbSuccess;
$myObj->message = $dbMessage;
$myJSON = json_encode($myObj);
echo $myJSON;
?>
