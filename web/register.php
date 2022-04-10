<?
require_once('/home/sterlid2/Private/userbase.php');

/* Force https connection */
forceHTTPS();

/* Check if the user is logged in already */
session_start();
if(checkIfLoggedIn()) {
    if (isClient()) {
        header("Location: home.php");
        die();
    }
}

/* Check if there is a key */
if (!isset($_SESSION['key'])) {
    $_SESSION['key'] = bin2hex(random_bytes(32));
}

$registrationToken = hash_hmac('sha256', '/authenticateRegistration.php', $_SESSION['key']);
?>

<!DOCTYPE html>
<html lang="en-US">
    <head>
    	<title>Register</title>
    	<!-- Stylesheet -->
    	<link rel="stylesheet" href="CSS/stylesheet.css">
    	<!-- Favicon -->
	    <link rel="icon" href="Images/logo.ico">
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
            <div class="list sub">
                <div class="container">
                    <a href="signin.php" class="highlight-button transform-button split round">
                        <div class="list">
                            <p><i class="fas fa-info icon"></i> Have an account? Sign in here</p>
                        </div>
                        <div class="animate-left">
            	            <div class="toggle-button">
            	                <i class="fas fa-chevron-right"></i>
            	            </div>
                        </div>
                    </a>
                    <div class="accent-border top-round">
                        <h2 class="big"><b>Goldman Stacks</b><!-- <span class="info">Registration</span>--><h2>
                    </div>
                    <br>
                    <form id="register">
			    <label for="email" class="info">Email</label>
			    <div class="form-item">
				    <input id="email" name="email" type="email" class="input-field" required>
			    </div>
			    <label for="password" class="info">Password</label>
			    <div class="form-item">
				    <input id="password" name="password" type="password" class="input-field"required>
			    </div>
			    <label for="confirm-password" class="info">Confirm Password</label>
			    <div class="form-item">
				    <input id="confirm-password" name="confirm-password" type="password" class="input-field" required>
			    </div>
			    <hr>
			    <label for="phone-number" class="info">Phone Number</label>
			    <div class="form-item">
				    <input id="phone-number" name="phone-number" type="text" pattern="1?\d{3}-?\d{3}-?{4}" class="input-field" required>
			    </div>
			    <label for="ssn" class="info">SSN</label>
			    <div class="form-item">
				    <input id="ssn" name="ssn" type="text" pattern="^\d{3}-?\d{2}-?\d{4}$" class="input-field" required>
			    </div>
			    <hr>
			    <label for="address-line1" class="info">Address Line 1</label>
			    <div class="form-item">
				    <input id="address-line1" name="address-line1" type="text" pattern="^\d+ [A-z ]+.?$" class="input-field" required>
			    </div>
			    <label for="address-line2" class="info">Address Line 2</label>
			    <div class="form-item">
				    <input id="address-line2" name="address-line2" type="text" pattern="^[A-z0-9#, ]+$" class="input-field">
			    </div>
			    <label for="address-city" class="info">City</label>
			    <div class="form-item">
				    <input id="address-city" name="address-city" type="text" pattern="^[A-z. ]+$" class="input-field" required>
			    </div>
			    <label for="address-state" class="info">State</label>
			    <div class="form-item">
				    <input id="address-state" name="address-state" type="text" pattern="^[A-z ]+$" class="input-field" required>
			    </div>
			    <label for="address-postal-code" class="info">Postal Code</label>
			    <div class="form-item">
				    <input id="address-postal-code" name="address-postal-code" type="text" pattern="^[0-9]{5}$" class="input-field" required>
			    </div>
			    <hr>
			    <input type="hidden" name="token" value="<? echo $registrationToken ?>">
			    <div class="form-item">
				<button type="submit" class="standard-button transform-button flex-center round">
				    <div class="split">
					<p class="animate-left">Request Registration<p>
					<div class="toggle-button">
					    <i class="fas fa-chevron-right"></i>
					</div>
				    </div>
				</button>
			</div>
                    </form>
                </div>
            </div>
        </div>
    </body>
    <script type="text/javascript" src="Scripts/notification.js"></script>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('register').addEventListener('submit', handleForm);
        });
        
        function handleForm(event) {
            event.preventDefault();
	        
            let form = event.target;
            let formData = new FormData(form);
	        
            let url = "requests/register.php";
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
	            } else {
	                setFailNotification(data.message);
	            }
	        })
	        .catch(console.warn);
    }
    </script>
</html>
