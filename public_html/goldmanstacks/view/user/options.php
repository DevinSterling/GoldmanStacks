<?php
require_once('../../../../private/config.php');
require_once('../../../../private/sysNotification.php');
require_once('../../../../private/userbase.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkClientStatus(); // Check if the client is signed in

$userID = $_SESSION['uid'];

/* Create CSRF tokens */
$passwordToken = hash_hmac('sha256', '/updatePassword.php', $_SESSION['key']);
$addressToken = hash_hmac('sha256', '/updateAddress.php', $_SESSION['key']);
$phoneNumberToken = hash_hmac('sha256', '/updatePhoneNumber.php', $_SESSION['key']);
$emailToken = hash_hmac('sha256', '/updateEmail.php', $_SESSION['key']);

/* DB Connection */
$db = getUpdateConnection(); // Query

if ($db === null) {
    header('Location: ../redirect/error.php');
    die();
}
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
	    <title>User Options</title>
	    <!-- Stylesheet -->
	    <link rel="stylesheet" href="../../css/stylesheet.css">
	    <!-- Favicon -->
	    <link rel="icon" href="../../img/logo.ico">
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
        		<li class="menulogo"><a href="../home">Goldman Stacks</a></li>
                <li class="menutoggle"><a href="#"><i class="fas fa-bars"></i></a></li>
        		<li class="menuitem"><a href="../home">Home</a></li>
        		<li class="menuitem"><a href="../account/transfer">Transfer</a></li>
        		<li class="menuitem"><a href="../account/payments">Payments</a></li>
        		<li class="menuitem"><a href="../account/open">Open New Account</a></li>
        		<li class="menuitem"><a href="../account/statement">Statement</a></li>
        	</ul>
        	<ul class="menugroup">
        		<li class="menuitem"><a href="options">Options</a></li>
        		<li class="menuitem"><a href="../../requests/signout">Sign Out</a></li>
        	</ul>
        </nav>
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
                <!--<button class="tab-button transform-button round" data-id="change-username" data-title="Change Username">
                    <div class="split">
                        <div class="text-right">
                            <p>Username</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>-->
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
                <button class="tab-button transform-button round"  data-id="change-address" data-title="Change Address">
                    <div class="split">
                        <div class="text-right">
                            <p>Address</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>
                <button class="tab-button transform-button round"  data-id="change-phone" data-title="Change Phone Number">
                    <div class="split">
                        <div class="text-right">
                            <p>Phone</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>
                <button class="tab-button transform-button round"  data-id="change-email" data-title="Change Email Address">
                    <div class="split">
                        <div class="text-right">
                            <p>Email</p>
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
                    <p class="info"><b>Phone Number</b>: <?php echo htmlspecialchars($user['phoneNumber']) ?></p>
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
                <form id="change-password" action="../../requests/user/updatePassword" class="flex-form hidden">
                    <label for="current-password" class="info">Current Password</label>
    		        <input id="current-password" type="password" name="old" class="input-field" required>
    	            <hr>
    	            <label for="new-password" class="info">New Password</label>
    		        <input id="new-password" type="password" name="new" class="input-field" required>
    	            <label for="confirm-password" class="info">Confirm Password</label>
    		        <input id="confirm-password" type="password" name="confirm" class="input-field" required>
                    <input type="hidden" name="token" value="<?php echo $passwordToken ?>">
                    <button form="change-password" class="standard-button transform-button flex-center round">
                        <div class="split">
                            <p class="animate-left">Apply<p>
           		            <div class="toggle-button">
            		            <i class="fas fa-chevron-right"></i>
            		        </div>
                        </div>
                    </button>
                </form>
                <form id="change-address" action="../../requests/user/updateAddress" class="flex-form hidden">
                    <label for="address-line-1" class="info">Address Line 1</label>
                    <input id="address-line-1" type="text" name="line1" class="input-field" required>
                    <label for="address-line-2" class="info">Address Line 2</label>
                    <input id="address-line-2" type="text" name="line2" class="input-field">
                    <label for="address-city" class="info">City</label>
                    <input id="address-city" type="text" name="city" class="input-field" required>
                    <label for="address-state" class="info">State</label>
                    <input id="address-state" type="text" name="state" class="input-field" required>
                    <label for="address-postal-code" class="info">Postal Code</label>
                    <input id="address-postal-code" type="text" name="code" class="input-field" required>
                    <input type="hidden" name="token" value="<?php echo $addressToken ?>">
                    <button type="submit" class="standard-button transform-button flex-center round">
                        <div class="split">
                            <p class="animate-left">Apply<p>
           		            <div class="toggle-button">
            		            <i class="fas fa-chevron-right"></i>
            		        </div>
                        </div>
                    </button>
                </form>
                <form id="change-phone" action="../../requests/user/updatePhoneNumber" class="flex-form hidden">
                    <label for="phone-number" class="info">Phone Number</label>
                    <input id="phone-number" type="text" pattern="^\d{3}[\s.-]?\d{3}[\s.-]?\d{4}$" name="phone" class="input-field" placeholder="635-855-4929" required>
                    <input type="hidden" name="token" value="<?php echo $phoneNumberToken ?>">
                    <button type="submit" class="standard-button transform-button flex-center round">
                        <div class="split">
                            <p class="animate-left">Apply<p>
           		            <div class="toggle-button">
            		            <i class="fas fa-chevron-right"></i>
            		        </div>
                        </div>
                    </button>
                </form>
                <form id="change-email" action="../../requests/user/updateEmail" class="flex-form hidden">
                    <label for="email-address" class="info">Email Address</label>
                    <input id="email-address" type="email" name="email" class="input-field" required>
                    <input type="hidden" name="token" value="<?php echo $emailToken ?>">
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
	<script type="text/javascript" src="../../js/navigation.js"></script>
	<script type="text/javascript" src="../../js/tabs.js"></script>
	<script type="text/javascript" src="../../js/post.js"></script>
	<script type="text/javascript" src="../../js/notification.js"></script>
	<script type="text/javascript">
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('change-password').addEventListener('submit', handlePasswordForm);
            document.getElementById('change-address').addEventListener('submit', handleForm);
            document.getElementById('change-phone').addEventListener('submit', handleForm);
            document.getElementById('change-email').addEventListener('submit', handleForm);
        });

	    function handleForm(event) {
	        event.preventDefault();
	        
	        let form = event.target;
	        let formData = new FormData(form);
	        
	        let url = form.action;
	        let request = new Request(url, {
	            body: formData,
	            method: 'POST',
	        });
	        
            submitForm(request);
	    }
	    
	    function handlePasswordForm(event) {
	        event.preventDefault();
	        
	        let form = event.target;
	        let formData = new FormData(form);
	        
	        let url = form.action;
	        let request = new Request(url, {
	            body: formData,
	            method: 'POST',
	        });
	        
	        if (formData.get('new') === formData.get('confirm')) {
	            submitForm(request);
	            form.reset();
	        } else {
	            document.getElementById("new-password").focus();
	            
	            setFailNotification("Passwords Do Not Match");
	            showNotification();
	        }
	    }
	    
	    function submitForm(request) {
	        fetch(request)
	            .then((response) => response.json())
	            .then((data) => {          
	                if (data.response) setSuccessNotification(data.message);
	                else setFailNotification(data.message);
	            })
	            .catch(console.warn);
		    
		showNotification();
		window.scrollTo({ top: 0, behavior: 'smooth' });
	    }
	</script>
</html>
