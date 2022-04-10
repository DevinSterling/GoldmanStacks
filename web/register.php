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
                    <form id="edit">
                        <label for="username" class="info">Email</label>
        	            <div class="form-item">
        		            <input id="username" class="input-field" type="text">
        	            </div>
        	            <label for="password" class="info">Password</label>
        	            <div class="form-item">
        		            <input id="password" class="input-field" type="password">
        	            </div>
        	            <label for="password" class="info">Confirm Password</label>
        	            <div class="form-item">
        		            <input id="password" class="input-field" type="password">
        	            </div>
        	            <hr>
                        <label for="username" class="info">Phone Number</label>
        	            <div class="form-item">
        		            <input id="username" class="input-field" type="text">
        	            </div>
        	            <label for="password" class="info">SSN</label>
        	            <div class="form-item">
        		            <input id="password" class="input-field" type="txet">
        	            </div>
        	            <hr>
                        <label for="username" class="info">Address Line 1</label>
        	            <div class="form-item">
        		            <input id="username" class="input-field" type="text">
        	            </div>
        	            <label for="password" class="info">Address Line 2</label>
        	            <div class="form-item">
        		            <input id="password" class="input-field" type="txet">
        	            </div>
        	            <label for="username" class="info">City</label>
        	            <div class="form-item">
        		            <input id="username" class="input-field" type="text">
        	            </div>
        	            <label for="password" class="info">State</label>
        	            <div class="form-item">
        		            <input id="password" class="input-field" type="txet">
        	            </div>
        	            <label for="password" class="info">Postal Code</label>
        	            <div class="form-item">
        		            <input id="password" class="input-field" type="txet">
        	            </div>
        	            <hr>
                        <div class="form-item">
                            <button form="edit" class="standard-button transform-button flex-center round">
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
</html>
