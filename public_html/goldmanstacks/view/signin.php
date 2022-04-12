<?php
require_once('../../../private/userbase.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkVisitorStatus(); // Checks if the user is a visitor

/* Signin form csrf token */
$signinToken = hash_hmac('sha256', '/authenticateSignin.php', $_SESSION['key']); 

/* Check if the user can been timed out (and redirected here [signin.php]) */
if (isset($_GET['timeout'])) {
    $timeout = (bool)$_GET['timeout']; // Get timeout value
}

/* Check if the user has requested to be registered (and redirected here [signin.php]) */
if (isset($_GET['registered'])) {
    $registered = (bool)$_GET['registered']; // Get timeout value
}

/* Check if the user has been redirected here due by a server response code */
if (isset($_GET['error'])) {
    $error = true;
}

/* Notification Handling */
$notificationClasses = "failure collapse";
$notificationIcon = "fa-times";

if ($timeout || $registered || $error) {
	/* Check for error notifications */
	if ($timeout || $error) {
		if ($timeout) {
			$notificationMessage = "Signed Out Due to Inactivity";
		} else {
			$code = $_GET['error'];

			switch ($code) {
				case 404:
					$notificationMessage = "<b>404:</b> Requested Page Not Found";
					break;
				case 403:
					$notificationMessage = "<b>403:</b> Access Denied";
			}
		}
		
		$notificationClasses = "failure";

	/* Check for success notifications */
	} else {
		$notificationMessage = "Registration Request Submitted";
		$notificationClasses = "success";
		$notificationIcon = "fa-check";
	}
}
?>

<!DOCTYPE html>
<html lang="en-US">
    <head>
    	<title>Sign In</title>
    	<!-- Stylesheet -->
    	<link rel="stylesheet" href="../css/stylesheet.css">
    	<!-- Favicon -->
		<link rel="icon" href="../img/logo.ico">
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
        <div class="flex-center-item">
            <div class="list fixed-sub round">
                <button id="notification" onClick="hideNotification()" class="notification max transform-button round <?php echo $notificationClasses ?>">
                    <p><i id="notification-icon" class="fas <?php echo $notificationIcon ?> icon"></i><span id="notification-text"><?php echo $notificationMessage ?></span></p>
                    <div class="split">
                           <div class="toggle-button">
            	            <i class="fas fa-times"></i>
            	        </div>
                    </div>
                </button>
                <br>
                <div class="accent-border top-round">
                    <h2 class="big"><b>Goldman Stacks</b><h2>
                </div>
                <br>
                <form id="login">
                    <label for="username" class="info">Username</label>
    	            <div class="form-item">
    		            <input id="username" class="input-field" name="username" type="text" required>
    	            </div>
    	            <label for="password" class="info">Password</label>
    	            <div class="form-item">
    		            <input id="password" class="input-field" name="password" type="password" required>
    	            </div>
    	            <input type="hidden" name="token" value="<?php echo $signinToken ?>">
                    <a href="register" class="highlight-button transform-button split round">
                        <div class="list">
                            <p><i class="fas fa-info icon"></i>Don't have an account? Register here</p>
                        </div>
                        <div class="animate-left">
            	            <div class="toggle-button">
            	                <i class="fas fa-chevron-right"></i>
            	            </div>
                        </div>
                    </a>
                    <div class="form-item">
                        <button type="submit" class="standard-button transform-button flex-center round">
                            <div class="split">
                                <p class="animate-left">Sign In<p>
               		        <div class="toggle-button">
                		    <i class="fas fa-chevron-right"></i>
                		</div>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </body>
	<script type="text/javascript" src="../js/notification.js"></script>
	<script type="text/javascript">
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('login').addEventListener('submit', handleForm);
        });
        
        function handleForm(event) {
	        event.preventDefault();
	        
	        let form = event.target;
	        let formData = new FormData(form);
	        
	        let url = "../requests/authenticateSignin";
	        let request = new Request(url, {
	            body: formData,
	            method: 'POST',
	        });
	        
	        fetch(request)
	            .then((response) => response.json())
	            .then((data) => {
			showNotification();

	                if (data.response) {
	                    setSuccessNotification(data.message);
	                    window.location.href = "home";
	                } else {
	                    setFailNotification(data.message);
	                }
	            })
	            .catch(console.warn);
	    }
    </script>
</html>