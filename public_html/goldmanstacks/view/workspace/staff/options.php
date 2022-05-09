<?php
require_once('../../../../../private/config.php');
require_once('../../../../../private/sysNotification.php');
require_once('../../../../../private/userbase.php');
require_once('../../../../../private/functions.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkEmployeeStatus(); // Check if the employee is signed in

/* SESSION Variables */
$userID = $_SESSION['uid'];
$key = $_SESSION['key'];

/* CSRF token */
$passwordToken = hash_hmac('sha256', '/updatePassword.php', $key);

$db = getUpdateConnection();

if ($db === null) {
    header("Location: ");
    die();
}
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title>Options</title>
		<!-- Stylesheet -->
		<link rel="stylesheet" href="../../../css/stylesheet.css">
		<!-- Favicon -->
		<link rel="icon" href="../../../img/logo.ico">
		<!-- Google Font -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <!-- Google Font -->
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <!-- Google Font -->
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;1,100&display=swap" rel="stylesheet">
        <!-- Svg Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
        <!-- Different screen size scaling compatability -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body>
		<nav class="menubar">
			<ul class="menugroup">
				<li class="menulogo"><a href="../manager">Goldman Stacks</a></li>
                		<li class="menutoggle"><a href="#"><i class="fas fa-bars"></i></a></li>
				<li class="menuitem"><a href="../manager">Manage</a></li>
			</ul>
			<ul class="menugroup">
				<li class="menuitem"><a href="options">Options</a></li>
				<li class="menuitem"><a href="../../../requests/signout">Sign Out</a></li>
			</ul>
		</nav>
		<div class="sys-notification">Logged as Employee</div>
		<?php notification(); ?>
        <button id="notification" onClick="hideNotification()" class="notification sub success transform-button round collapse">
            <p><i id="notification-icon" class="fas fa-check icon"></i><span id="notification-text"></span></p>
            <div class="split">
   	            <div class="toggle-button">
		            <i class="fas fa-times"></i>
		        </div>
            </div>
        </button>
        <div class="container flex-center">
            <div class="list mini">
                <a href="options" class="tab-button transform-button round selected" data-id="overview" data-title="Account Overview">
                    <div class="split">
                        <div class="text-right">
                            <p>Overview</p>
                        </div>
           	            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
                </a>
                <button class="tab-button transform-button round"  data-id="change-password" data-title="Change Password">
                    <div class="split">
                        <div class="text-right">
                            <p>Password</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>
		    </div>
            <div class="list sub">
                <h2 id="title">Account Overview</h2>
                <div id="overview">
                    <?php
                        /* Query */
                        $queryUser = $db->prepare("SELECT firstName, middleName, lastName, email, phoneNumber, line1, line2, city, state, postalCode 
                                                    FROM users U 
                                                    INNER JOIN address A ON U.userID=A.userID 
                                                    WHERE U.userID=?");
                        $queryUser->bind_param("i", $userID);
                        $queryUser->execute();
                        
                        /* Result */
                        $resultUser = $queryUser->get_result();
                        $user = $resultUser->fetch_assoc();
                    ?>
                    <h5 class="big-info">User Information</h5>
                    <p class="info"><b>First Name</b>: <?php echo htmlspecialchars($user['firstName']) ?></p>
                    <p class="info"><b>Last Name</b>: <?php echo htmlspecialchars($user['lastName']) ?></p>
                    <hr>
                    <h5 class="big-info">Contact Information</h5>
                    <p class="info"><b>Email Address</b>: <?php echo htmlspecialchars($user['email']) ?></p>
                    <p class="info"><b>Phone Number</b>: <?php echo htmlspecialchars(convertToPhoneNumber($user['phoneNumber'])) ?></p>
                    <hr>
                    <h5 class="big-info">Address Information</h5>
                    <p class="info"><b>Line 1</b>: <?php echo htmlspecialchars($user['line1']) ?></p>
                    <?php
			        if (!empty($user['line2'])) echo "<p class=\"info\"><b>Line 2</b>:".htmlspecialchars($user['line2'])."</p>";
                    ?>
                    <p class="info"><b>City</b>: <?php echo htmlspecialchars($user['city']) ?></p>
                    <p class="info"><b>State</b>: <?php echo htmlspecialchars($user['state']) ?></p>
                    <p class="info"><b>Postal Code</b>: <?php echo htmlspecialchars($user['postalCode']) ?></p>
                    <?php
                        /* Release */
                        $resultUser->free();
                        $queryUser->close();
                        $db->close(); 
                    ?>
                </div>
                <form id="change-password" action="../../../requests/workspace/user/updatePassword" class="flex-form hidden">
    	            <label for="old-password" class="info">Current Password</label>
    		        <input id="old-password" type="password" name="old" class="input-field" required>
    		        <hr>
    	            <label for="new-password" class="info">New Password</label>
    		        <input id="new-password" type="password" name="new" class="input-field" required>
    	            <label for="confirm-password" class="info">Confirm Password</label>
    		        <input id="confirm-password" type="password" name="confirm" class="input-field" required>
                    <input type="hidden" name="token" value="<?php echo $passwordToken ?>">
                    <input type="hidden" name="id" value="<?php echo $_SESSION['uid'] ?>">
                    <button type="submit" class="standard-button transform-button flex-center round">
                        <div class="split">
                            <p class="animate-left">Apply<p>
           		            <div class="toggle-button">
            		            <i class="fas fa-chevron-right"></i>
            		        </div>
                        </div>
                    </button>
                </form>
            </div>
            <div class="list mini">
                <!--Dummy block for alignment-->
            </div>
    	</div>
	</body>
	<script type="text/javascript" src="../../../js/navigation.js"></script>
	<script type="text/javascript" src="../../../js/notification.js"></script>
	<script type="text/javascript" src="../../../js/post.js"></script>
	<script type="text/javascript" src="../../../js/tabs.js"></script>
	<script type="text/javascript">
	    document.getElementById('change-password').addEventListener('submit', async (event) => {
	        event.preventDefault();
	        
            let form = event.target;
            let formData = new FormData(form);
            
            if (formData.get('new') === formData.get('confirm')) {
                let json = await getJson(form.action, formData);
                
                if (!isEmptyJson()) {
                    if (json.response) {
                        setSuccessNotification(json.message);
                        form.reset();
                    } else {
                        setFailNotification(json.message);
                    }
                } else {
                    setFailNotification("Failed to update password");
                }
            } else {
                document.getElementById('new-password').focus();
                setFailNotification('Passwords do not match');
            }
            
            showNotification();
	    });
	</script>
</html>
