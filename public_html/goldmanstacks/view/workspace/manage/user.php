<?php
require_once('../../../../../private/sysNotification.php');
require_once('../../../../../private/userbase.php');
require_once('../../../../../private/config.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
//checkClientStatus(); // Check if the client is signed in

/* GET Variables */
$user = $_GET['id'];

$db = getUpdateConnection();

if ($db === null) {
    header("Location: ");
}

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
				<li class="menuitem"><a href="../search">Search</a></li>
			</ul>
			<ul class="menugroup">
				<li class="menuitem"><a href="../staff/options">Options</a></li>
				<li class="menuitem"><a href="../../login">Sign Out</a></li>
			</ul>
		</nav>
		<div class="sys-notification">Logged as Employee</div>
		<?php notification(); ?>
    	<div class="container flex-center">
            <div class="list mini">
                <a href="user?id=<?php echo $user ?>" class="tab-button transform-button round selected" data-id="overview" data-title="Change Password">
                    <div class="split">
                        <div class="text-right">
                            <p>Overview</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </a>
                <a href="account/accounts?id=<? echo $user ?>" class="tab-button transform-button round" data-id="overview">
                    <div class="split">
                        <div class="text-right">
                            <p>Accounts</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </a>
                <a href="account/transactions?id=<? echo $user ?>" class="tab-button transform-button round" data-id="overview">
                    <div class="split">
                        <div class="text-right">
                            <p>Transactions</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>
                <a href="account/payments?id=<? echo $user ?>" class="tab-button transform-button round" data-id="overview">
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
		        </button>
		        <hr class="margin-bottom">
                <button class="tab-button transform-button round" data-id="remove-user" data-title="Remove User">
                    <div class="split">
                        <div class="text-right">
                            <p><span class="fas fa-times text-center icon"></span> Remove</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>
		    </div>
            <div class="list sub">
        	    <h2 id="title"><?php echo $currentAccountName?> User <?php echo $user ?> Overview</h2>
    	        <div id="overview">
                    <?php
                        /* Query */
                        $queryUser = $db->prepare("SELECT userRole, firstName, middleName, lastName, email, phoneNumber, line1, line2, city, state, postalCode FROM users U INNER JOIN address A ON U.userID=A.userID WHERE U.userID=?");
                        $queryUser->bind_param("i", $user);
                        $queryUser->execute();
                        
                        /* Result */
                        $resultUser = $queryUser->get_result();
                        $userInfo = $resultUser->fetch_assoc();
                    ?>
                    <h5 class="big-info">User Information</h5>
                    <p class="info"><b>First Name</b>: <?php echo htmlspecialchars($userInfo['firstName']) ?></p>
                    <p class="info"><b>Last Name</b>: <?php echo htmlspecialchars($userInfo['lastName']) ?></p>
                    <hr>
                    <h5 class="big-info">Contact Information</h5>
                    <p class="info"><b>Email Address</b>: <?php echo htmlspecialchars($userInfo['email']) ?></p>
                    <p class="info"><b>Phone Number</b>: <?php echo htmlspecialchars($userInfo['phoneNumber']) ?></p>
                    <hr>
                    <h5 class="big-info">Address Information</h5>
                    <p class="info"><b>Line 1</b>: <?php echo htmlspecialchars($userInfo['line1']) ?></p>
                    <?php
			        if (!empty($user['line2'])) echo "<p class=\"info\"><b>Line 2</b>:".htmlspecialchars($userInfo['line2'])."</p>";
                    ?>
                    <p class="info"><b>City</b>: <?php echo htmlspecialchars($userInfo['city']) ?></p>
                    <p class="info"><b>State</b>: <?php echo htmlspecialchars($userInfo['state']) ?></p>
                    <p class="info"><b>Postal Code</b>: <?php echo htmlspecialchars($userInfo['postalCode']) ?></p>
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
                <form id="remove-user" action="" class="flex-form hidden">
                    <p class="info">Upon confirmation this will <b>remove</b> this user account permanently.</p>
                    <p class="info"><b>Current User ID:</b> <?php echo $user ?></p>
                    <p class="info"><b>Current User Role:</b> <?php echo ucfirst($userInfo['userRole']) ?></p>
                    <input type="hidden" name="token" value="<?php echo $emailToken ?>">
                    <button type="submit" class="standard-button transform-button flex-center round">
                        <div class="split">
                            <p class="animate-left">Remove User<p>
           		            <div class="toggle-button">
            		            <i class="fas fa-chevron-right"></i>
            		        </div>
                        </div>
                    </button>
                </form>
            </div>
            <div class="list mini">
		    </div>
        </div>
	</body>
	<script type="text/javascript" src="../../../js/navigation.js"></script>
	<script type="text/javascript" src="../../../js/tabs.js"></script>
	<script type="text/javascript" src="../../../js/post.js"></script>
	<script type="text/javascript" src="../../../js/notification.js"></script>
</html>
