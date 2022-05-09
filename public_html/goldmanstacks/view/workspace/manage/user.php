<?php
require_once('../../../../../private/sysNotification.php');
require_once('../../../../../private/userbase.php');
require_once('../../../../../private/config.php');
require_once('../../../../../private/functions.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkEmployeeStatus(); // Check if the employee is signed in

/* SESSION Variables */
$userID = $_SESSION['uid'];
$key = $_SESSION['key'];

/* GET Variables */
$user = $_GET['id'];

/* Create CSRF tokens */
$passwordToken = hash_hmac('sha256', '/updatePassword.php', $key);
$addressToken = hash_hmac('sha256', '/updateAddress.php', $key);
$phoneNumberToken = hash_hmac('sha256', '/updatePhoneNumber.php', $key);
$emailToken = hash_hmac('sha256', '/updateEmail.php', $key);
$removeUserToken = hash_hmac('sha256', '/removeUser.php', $key);

/* Database Connection */
$db = getUpdateConnection();

if ($db === null) {
    header("Location: ");
}

/* Query */
$queryUser = $db->prepare("SELECT userRole, firstName, middleName, lastName, email, phoneNumber, line1, line2, city, state, postalCode FROM users U INNER JOIN address A ON U.userID=A.userID WHERE U.userID=?");
$queryUser->bind_param("i", $user);
$queryUser->execute();

/* Result */
$resultUser = $queryUser->get_result();
$userInfo = $resultUser->fetch_assoc();

/* Release */
$resultUser->free();
$queryUser->close();
$db->close(); 

$isClient = $userInfo['userRole'] === 'client'; // Determine if the user is a client
$isNotOwnAccount = $userID != $user; // Determine if the account is not the current signed in account
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
	<title>User</title>
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
				<li class="menuitem"><a href="../staff/options">Options</a></li>
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
        <div id="user-info">
        	<div class="container flex-center">
                <div class="list mini">
                    <a href="user?id=<?php echo $user ?>" class="tab-button transform-button round selected" data-id="overview" data-title="User ... Overview">
                        <div class="split">
                            <div class="text-right">
                                <p>Overview</p>
                            </div>
           		            <div class="toggle-button">
            		            <i class="fas fa-chevron-right"></i>
            		        </div>
                        </div>
    		        </a>
    		        <?php
                    if ($isClient) 
                        echo '<a href="account/accounts?id=' . $user . '" class="tab-button transform-button round" data-id="overview">
                            <div class="split">
                                <div class="text-right">
                                    <p>Accounts</p>
                                </div>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
        		        </a>
        		        <a href="account/transactions?id=' . $user . '" class="tab-button transform-button round" data-id="overview">
                            <div class="split">
                                <div class="text-right">
                                    <p>Transactions</p>
                                </div>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
        		        </button>
                        <a href="account/payments?id=' . $user . '" class="tab-button transform-button round" data-id="overview">
                            <div class="split">
                                <div class="text-right">
                                    <p>Payments</p>
                                </div>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
        		        </a>
        		        <hr class="margin-bottom">
                        <button class="tab-button transform-button round" data-id="change-password" data-title="Change Password">
                            <div class="split">
                                <div class="text-right">
                                    <p><span class="fas fa-edit text-center icon"></span> Password</p>
                                </div>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
        		        </button>
                        <button class="tab-button transform-button round" data-id="change-address" data-title="Change Address">
                            <div class="split">
                                <div class="text-right">
                                    <p><span class="fas fa-edit text-center icon"></span> Address</p>
                                </div>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
        		        </button>
                        <button class="tab-button transform-button round" data-id="change-phone" data-title="Change Phone Number">
                            <div class="split">
                                <div class="text-right">
                                    <p><span class="fas fa-edit text-center icon"></span> Phone</p>
                                </div>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
        		        </button>
                        <button class="tab-button transform-button round" data-id="change-email" data-title="Change Email Address">
                            <div class="split">
                                <div class="text-right">
                                    <p><span class="fas fa-edit text-center icon"></span> Email</p>
                                </div>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
        		        </button>';
    		        
    		        if ($isNotOwnAccount) 
        		        echo '<hr class="margin-bottom">
                            <button class="tab-button transform-button round" data-id="remove-user" data-title="Remove User">
                                <div class="split">
                                    <div class="text-right">
                                        <p><span class="fas fa-times text-center icon"></span> Remove</p>
                                    </div>
                   		            <div class="toggle-button">
                    		            <i class="fas fa-chevron-right"></i>
                    		        </div>
                                </div>
            		        </button>';
    		        ?>
    		    </div>
                <div class="list sub">
            	    <h2 id="title"><?php echo $currentAccountName?> User <?php echo $user ?> Overview</h2>
        	        <div id="overview">
                        <h5 class="big-info">User Information</h5>
                        <p class="info"><b>First Name</b>: <?php echo htmlspecialchars($userInfo['firstName']) ?></p>
                        <p class="info"><b>Last Name</b>: <?php echo htmlspecialchars($userInfo['lastName']) ?></p>
                        <hr>
                        <h5 class="big-info">Contact Information</h5>
                        <p class="info"><b>Email Address</b>: <?php echo htmlspecialchars($userInfo['email']) ?></p>
                        <p class="info"><b>Phone Number</b>: <?php echo htmlspecialchars(convertToPhoneNumber($userInfo['phoneNumber'])) ?></p>
                        <hr>
                        <h5 class="big-info">Address Information</h5>
                        <p class="info"><b>Line 1</b>: <?php echo htmlspecialchars($userInfo['line1']) ?></p>
                        <?php
    			        if (!empty($user['line2'])) echo "<p class=\"info\"><b>Line 2</b>:".htmlspecialchars($userInfo['line2'])."</p>";
                        ?>
                        <p class="info"><b>City</b>: <?php echo htmlspecialchars($userInfo['city']) ?></p>
                        <p class="info"><b>State</b>: <?php echo htmlspecialchars($userInfo['state']) ?></p>
                        <p class="info"><b>Postal Code</b>: <?php echo htmlspecialchars($userInfo['postalCode']) ?></p>
            	    </div>
            	    <?php
            	    if ($isClient)
            	        echo '<form id="change-password" action="../../../requests/workspace/user/updatePassword" class="flex-form hidden">
        	            <label for="new-password" class="info">New Password</label>
        		        <input id="new-password" type="password" name="new" class="input-field" required>
        	            <label for="confirm-password" class="info">Confirm Password</label>
        		        <input id="confirm-password" type="password" name="confirm" class="input-field" required>
                        <input type="hidden" name="token" value="' .  $passwordToken . '">
                        <input type="hidden" name="id" value="' . $user . '">
                        <button form="change-password" class="standard-button transform-button flex-center round">
                            <div class="split">
                                <p class="animate-left">Apply<p>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
                        </button>
                    </form>
                    <form id="change-address" action="../../../requests/workspace/user/updateAddress" class="flex-form hidden">
                        <label for="address-line-1" class="info">Address Line 1</label>
                        <input id="address-line-1" type="text" name="line1" class="input-field" required>
                        <label for="address-line-2" class="info">Address Line 2</label>
                        <input id="address-line-2" type="text" name="line2" class="input-field">
                        <label for="address-city" class="info">City</label>
                        <input id="address-city" type="text" name="city" class="input-field" required>
                        <label for="address-state" class="info">State</label>
                        <select id="address-state" name="state" class="input-field" required>
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
                        </select>                        <label for="address-postal-code" class="info">Postal Code</label>
                        <input id="address-postal-code" type="text" name="code" class="input-field" required>
                        <input type="hidden" name="token" value="' . $addressToken . '">
                        <input type="hidden" name="id" value="' . $user . '">
                        <button type="submit" class="standard-button transform-button flex-center round">
                            <div class="split">
                                <p class="animate-left">Apply<p>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
                        </button>
                    </form>
                    <form id="change-phone" action="../../../requests/workspace/user/updatePhoneNumber" class="flex-form hidden">
                        <label for="phone-number" class="info">Phone Number</label>
                        <input id="phone-number" type="text" pattern="^\d{3}[\s.-]?\d{3}[\s.-]?\d{4}$" name="phone" class="input-field" required>
                        <input type="hidden" name="token" value="' . $phoneNumberToken . '">
                        <input type="hidden" name="id" value="' . $user . '">
                        <button type="submit" class="standard-button transform-button flex-center round">
                            <div class="split">
                                <p class="animate-left">Apply<p>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
                        </button>
                    </form>
                    <form id="change-email" action="../../../requests/workspace/user/updateEmail" class="flex-form hidden">
                        <label for="email-address" class="info">Email Address</label>
                        <input id="email-address" type="email" name="email" class="input-field" required>
                        <input type="hidden" name="token" value="' . $emailToken . '">
                        <input type="hidden" name="id" value="' . $user . '">
                        <button type="submit" class="standard-button transform-button flex-center round">
                            <div class="split">
                                <p class="animate-left">Apply<p>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
                        </button>
                    </form>';
                    
                    if ($isNotOwnAccount)
                        echo '<form id="remove-user" action="../../../requests/workspace/user/removeUser" class="flex-form hidden">
                            <p class="info">Upon confirmation this will <b>remove</b> this user account permanently.</p>
                            <p class="info"><b>Current User ID:</b> ' . $user . '</p>
                            <p class="info"><b>Current User Role:</b> ' . ucfirst($userInfo['userRole']) . '</p>
                            <input type="hidden" name="token" value="' . $removeUserToken . '">
                            <input type="hidden" name="id" value="' . $user . '">
                            <button type="submit" class="standard-button transform-button flex-center round">
                                <div class="split">
                                    <p class="animate-left">Remove User<p>
                   		            <div class="toggle-button">
                    		            <i class="fas fa-chevron-right"></i>
                    		        </div>
                                </div>
                            </button>
                        </form>';
            	    ?>
                </div>
                <div class="list mini">
    		    </div>
            </div>
            <?php
            if ($isNotOwnAccount)
            echo '<div id="pop-up" class="pop-up">
                    <div onClick="hidePopUp()" class="flex-center-item">
                    </div>
                    <div id="pup-up-element" class="pop-up-content fixed-sub round hidden">
                        <button id="return-button" onClick="hidePopUp()" class="expand-button margin-bottom transform-button extend-right round">
        	                <div class="split">
        	                    <p class="condensed-info"><i class="fas fa-arrow-left"></i></p>
        	                    <div class="animate-right">
                		            <div class="toggle-button">
                		                <p class="expanded-info">Return</p>
                		            </div>
            		            </div>
        	                </div>
        	            </button>
        	            <div class="flex-form">
                            <h2 id="title">Confirm User Removal</h2>
                            <p class="info">Confirmation will <b>remove</b> the current user permanently</p>
                            <button id="confirm-removal" type="button" class="standard-button transform-button flex-center round">
                                <div class="split">
                                    <p class="animate-left">Confirm Removal<p>
                   		            <div class="toggle-button">
                    		            <i class="fas fa-chevron-right"></i>
                    		        </div>
                                </div>
                            </button>  
                        </div>
                    </div>
                </div>';
            ?>
        </div>
	</body>
	<script type="text/javascript" src="../../../js/navigation.js"></script>
	<script type="text/javascript" src="../../../js/tabs.js"></script>
	<script type="text/javascript" src="../../../js/post.js"></script>
	<script type="text/javascript" src="../../../js/notification.js"></script>
	<script type="text/javascript">
	    let form = null;
	
        <?php
        if ($isClient)
        echo "document.getElementById('change-password').addEventListener('submit', handlePasswordForm);
            document.getElementById('change-address').addEventListener('submit', handleForm);
            document.getElementById('change-phone').addEventListener('submit', handleForm);
            document.getElementById('change-email').addEventListener('submit', handleForm);
            
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
    	            document.getElementById('new-password').focus();
    	            
    	            setFailNotification('Passwords Do Not Match');
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
    	    }";

        if ($isNotOwnAccount)
            echo "document.getElementById('remove-user').addEventListener('submit', showPopUp);
                document.getElementById('confirm-removal').addEventListener('click', async () => {
                    let formData = new FormData(form);
                    
                    let json = await getJson(form.action, formData);
                    
                    if (!isEmptyJson(json)) {
                        if (json.response) {
                            setSuccessNotification(json.message);
                            document.getElementById('user-info').remove();
                        } else {
                            setFailNotification(json.message);
                        }
                    } else {
                        setFailNotification('Failed to remove user');
                    }
                    
                    showNotification();
                    hidePopUp();
                })

            function showPopUp(event) {
    	        event.preventDefault();
                
                form = event.target;
                
                document.getElementById('pop-up').classList.add('show-popup-content');
                document.getElementById('pup-up-element').classList.remove('hidden');
            }
            
            function hidePopUp() {
                document.getElementById('pop-up').classList.remove('show-popup-content');
                document.getElementById('pup-up-element').classList.add('hidden');
            }";
        
        ?>
	</script>
</html>
