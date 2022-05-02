<?php
require_once('../../../../../private/sysNotification.php');
require_once('../../../../../private/userbase.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkEmployeeStatus(); // Check if the employee is signed in

/* CSRF token */
$passwordToken = hash_hmac('sha256', '/updatePassword.php', $key);
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
                <button class="tab-button transform-button round selected"  data-id="change-password" data-title="Change Password">
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
                <h2 id="title">Change Password</h2>
                <form id="change-password" action="../../../requests/workspace/user/updatePassword" class="flex-form">
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
                setFailNotification('Passwords Do Not Match');
            }
            
            showNotification();
	    });
	</script>
</html>
