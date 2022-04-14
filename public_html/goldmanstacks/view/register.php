<?php
require_once('../../../private/userbase.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkVisitorStatus(); // Checks if the user is a visitor

/* Registration form csrf token */
$registrationToken = hash_hmac('sha256', '/authenticateRegistration.php', $_SESSION['key']);
?>

<!DOCTYPE html>
<html lang="en-US">
    <head>
    	<title>Register</title>
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
            <div class="list sub">
                <div class="container">
        		    <button id="notification" onClick="hideNotification()" class="notification max failure transform-button round margin-bottom collapse">
        			    <p><i id="notification-icon" class="fas fa-times icon"></i><span id="notification-text"></span></p>
        			    <div class="split">
        				   <div class="toggle-button">
        				    <i class="fas fa-times"></i>
        				</div>
        			    </div>
        	  	    </button>
                    <a href="signin" class="highlight-button transform-button split round">
                        <div class="list">
                            <p><i class="fas fa-info icon"></i>Have an account? Sign in here</p>
                        </div>
                        <div class="animate-left">
            	            <div class="toggle-button">
            	                <i class="fas fa-chevron-right"></i>
            	            </div>
                        </div>
                    </a>
                    <div class="accent-border top-round margin-bottom">
                        <h2 class="big"><b>Goldman Stacks</b><!-- <span class="info">Registration</span>--><h2>
                    </div>
                    <form id="register">
        			    <label for="first-name" class="info">First Name</label>
        			    <div class="form-item">
        				    <input id="first-name" name="first-name" type="text" pattern="^[A-z]+$" maxlength="43" placeholder="Required" class="input-field" required>
        			    </div>
        			    <label for="middle-name" class="info">Middle Name</label>
        			    <div class="form-item">
        				    <input id="middle-name" name="middle-name" type="text" pattern="^[A-z]+$" maxlength="43" class="input-field">
        			    </div>
        			    <label for="last-name" class="info">Last Name</label>
        			    <div class="form-item">
        				    <input id="last-name" name="last-name" type="text" pattern="^[A-z]+$" maxlength="43" placeholder="Required" class="input-field" required>
        			    </div>
        			    <label for="birth-date" class="info">Date of Birth</label>
        			    <div class="form-item">
        				    <input id="birth-date" name="birth-date" type="date" max="<?php echo date("Y-m-d") ?>" maxlength="10" placeholder="Required (YYYY-MM-DD, example: <?php echo date("Y-m-d") ?>)" class="input-field" required>
        			    </div>
        			    <hr>
        			    <label for="email" class="info">Email</label>
        			    <div class="form-item">
        				    <input id="email" name="email" type="email" maxlength="254" placeholder="Required" class="input-field" required>
        			    </div>
        			    <label for="password" class="info">Password</label>
        			    <div class="form-item">
        				    <input id="password" name="password" type="password" maxlength="100" placeholder="Required" class="input-field"required>
        			    </div>
        			    <label for="confirm-password" class="info">Confirm Password</label>
        			    <div class="form-item">
        				    <input id="confirm-password" name="confirm-password" type="password" maxlength="100" placeholder="Required" class="input-field" required>
        			    </div>
        			    <hr>
        			    <label for="phone-number" class="info">Phone Number</label>
        			    <div class="form-item">
        				    <input id="phone-number" name="phone-number" type="text" pattern="^1?\d{3}-?\d{3}-?\d{4}$" maxlength="13" placeholder="Required" class="input-field" required>
        			    </div>
        			    <label for="ssn" class="info">SSN</label>
        			    <div class="form-item">
        				    <input id="ssn" name="ssn" type="text" pattern="^\d{3}-?\d{2}-?\d{4}$" maxlength="11" placeholder="Required" class="input-field" required>
        			    </div>
        			    <hr>
        			    <label for="address-line1" class="info">Address Line 1</label>
        			    <div class="form-item">
        				    <input id="address-line1" name="address-line1" type="text" pattern="^\d+ [A-z ]+.?$" maxlength="50" placeholder="Required" class="input-field" required>
        			    </div>
        			    <label for="address-line2" class="info">Address Line 2</label>
        			    <div class="form-item">
        				    <input id="address-line2" name="address-line2" type="text" pattern="^[A-z0-9#, ]+$" maxlength="50" class="input-field">
        			    </div>
        			    <label for="address-city" class="info">City</label>
        			    <div class="form-item">
        				    <input id="address-city" name="address-city" type="text" pattern="^[A-z. ]+$" maxlength="30" placeholder="Required" class="input-field" required>
        			    </div>
        			    <label for="address-state" class="info">State</label>
        			    <div class="form-item">
                            <select id="address-state" name="address-state" class="input-field" required>
                            	<option value="AL">Alabama (AL)</option>
                            	<option value="AK">Alaska (AK)</option>
                            	<option value="AZ">Arizona (AZ)</option>
                            	<option value="AR">Arkansas (AR)</option>
                            	<option value="CA">California (CA)</option>
                            	<option value="CO">Colorado (CO)</option>
                            	<option value="CT">Connecticut (CT)</option>
                            	<option value="DE">Delaware (DE)</option>
                            	<option value="DC">District Of Columbia (DC)</option>
                            	<option value="FL">Florida (FL)</option>
                            	<option value="GA">Georgia (GA)</option>
                            	<option value="HI">Hawaii (HI)</option>
                            	<option value="ID">Idaho (ID)</option>
                            	<option value="IL">Illinois (IL)</option>
                            	<option value="IN">Indiana (IN)</option>
                            	<option value="IA">Iowa (IA)</option>
                            	<option value="KS">Kansas (KS)</option>
                            	<option value="KY">Kentucky (KY)</option>
                            	<option value="LA">Louisiana (LA)</option>
                            	<option value="ME">Maine (ME)</option>
                            	<option value="MD">Maryland (MD)</option>
                            	<option value="MA">Massachusetts (MA)</option>
                            	<option value="MI">Michigan (MI)</option>
                            	<option value="MN">Minnesota (MN)</option>
                            	<option value="MS">Mississippi (MS)</option>
                            	<option value="MO">Missouri (MO)</option>
                            	<option value="MT">Montana (MT)</option>
                            	<option value="NE">Nebraska (NE)</option>
                            	<option value="NV">Nevada (NV)</option>
                            	<option value="NH">New Hampshire (NH)</option>
                            	<option value="NJ">New Jersey (NJ)</option>
                            	<option value="NM">New Mexico (NM)</option>
                            	<option value="NY">New York (NY)</option>
                            	<option value="NC">North Carolina (NC)</option>
                            	<option value="ND">North Dakota (ND)</option>
                            	<option value="OH">Ohio (OH)</option>
                            	<option value="OK">Oklahoma (OK)</option>
                            	<option value="OR">Oregon (OR)</option>
                            	<option value="PA">Pennsylvania (PA)</option>
                            	<option value="RI">Rhode Island (RI)</option>
                            	<option value="SC">South Carolina (SC)</option>
                            	<option value="SD">South Dakota (SD)</option>
                            	<option value="TN">Tennessee (TN)</option>
                            	<option value="TX">Texas (TX)</option>
                            	<option value="UT">Utah (UT)</option>
                            	<option value="VT">Vermont (VT)</option>
                            	<option value="VA">Virginia (VA)</option>
                            	<option value="WA">Washington (WA)</option>
                            	<option value="WV">West Virginia (WV)</option>
                            	<option value="WI">Wisconsin (WI)</option>
                            	<option value="WY">Wyoming (WY)</option>
                            </select>
        			    </div>
        			    <label for="address-postal-code" class="info">Postal Code</label>
        			    <div class="form-item">
        				    <input id="address-postal-code" name="address-postal-code" type="text" pattern="^[0-9]{5}$" maxlength="5" placeholder="Required" class="input-field" required>
        			    </div>
        			    <input type="hidden" name="token" value="<?php echo $registrationToken ?>">
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
    <script type="text/javascript" src="../js/notification.js"></script>
    <script type="text/javascript">
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('confirm-password');
        const ageField = document.getElementById('birth-date');

        /* Event Listeneres */
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('register').addEventListener('submit', handleForm);
        });
        confirmPasswordField.addEventListener('focusout', (event) => {
            checkPasswords();
        });
        ageField.addEventListener('focusout', (event) => {
            checkAge();
        });
        
        function checkPasswords() {
            if (passwordField.value !== confirmPasswordField.value) {
        		window.scrollTo({ top: 0, behavior: 'smooth' })
        		setFailNotification("Passwords Do Not Match");
    	        showNotification();
    	        
    	        return false;
            }
            
            return true;
        }
        
        function checkAge() {
            if (~~ ((Date.now() - new Date(ageField.value)) / (31557600000)) < 18) {
        		window.scrollTo({ top: 0, behavior: 'smooth' })
        		setFailNotification("Age requirement not met");
    	        showNotification();
    	        
    	        return false;
            }
            
            return true;
        }
        
        function handleForm(event) {
            event.preventDefault();
	        
            let form = event.target;
            let formData = new FormData(form);
	        
            let url = "../requests/authenticateRegistration";
            let request = new Request(url, {
	        body: formData,
	        method: 'POST',
            });
		
            if (checkAge()) passwordField.focus();
            else if (checkPasswords()) ageField.focus();
            else {
    	        fetch(request)
    	            .then((response) => response.json())
    	            .then((data) => {
    		        showNotification();
        
	                if (data.response) {
	                    window.location.href = "signin.php?registered=1";
	                } else {
	                    setFailNotification(data.message);
    			        window.scrollTo({ top: 0, behavior: 'smooth' })
    	                }
    	            })
    	            .catch(console.warn);
        	    
    	    }    
        }
    </script>
</html>
