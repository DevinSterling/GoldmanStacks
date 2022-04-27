<?php
require_once('../../../../../private/sysNotification.php');
require_once('../../../../../private/userbase.php');
require_once('../../../../../private/config.php');
require_once('../../../../../private/userbase.php');

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
	<title>Manage User</title>
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
            <div class="list sub">
    	        <div class="container">
        	        <h2 id="title"><?php echo $currentAccountName?> User <?php echo $user ?></h2>
                    <?php
                        /* Query */
                        $queryUser = $db->prepare("SELECT firstName, middleName, lastName, email, phoneNumber, line1, line2, city, state, postalCode FROM users U INNER JOIN address A ON U.userID=A.userID WHERE U.userID=?");
                        $queryUser->bind_param("i", $user);
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
            </div>
            <div class="list fixed-sub">
		        <div class="container fixed-sub fixed-item">
		            <div class="margin-bottom">
    		            <label class="banner-text">Account Actions</label>
    		            <hr>
    		            <button class="highlight-button transform-button split round">
                            <div class="list">
                                <p class="banner-text">Remove Account</p>
                            </div>
                            <div class="animate-left">
                	            <div class="toggle-button">
                	                <i class="fas fa-chevron-right"></i>
                	            </div>
                            </div>
        		        </button>
        		        <hr>
    		            <button class="highlight-button transform-button split round">
                            <div class="list">
                                <p class="banner-text">Edit Account</p>
                            </div>
                            <div class="animate-left">
                	            <div class="toggle-button">
                	                <i class="fas fa-chevron-right"></i>
                	            </div>
                            </div>
        		        </button>
        		        <hr>
    		            <button class="highlight-button transform-button split round">
                            <div class="list">
                                <p class="banner-text">View Transactions</p>
                            </div>
                            <div class="animate-left">
                	            <div class="toggle-button">
                	                <i class="fas fa-chevron-right"></i>
                	            </div>
                            </div>
        		        </button>
        		        <hr>
    		            <button class="highlight-button transform-button split round">
                            <div class="list">
                                <p class="banner-text">View Payments</p>
                            </div>
                            <div class="animate-left">
                	            <div class="toggle-button">
                	                <i class="fas fa-chevron-right"></i>
                	            </div>
                            </div>
        		        </button>
    		        </div>
		            <label class="banner-text">Other</label>
		            <hr>
		            <a href="open" class="highlight-button transform-button split round">
                        <div class="list">
                            <p class="banner-text">Manage Open Requests</p>
                            <small>Bank Account Management</small>
                        </div>
                        <div class="animate-left">
            	            <div class="toggle-button">
            	                <i class="fas fa-chevron-right"></i>
            	            </div>
                        </div>
    		        </a>
    		        <hr>
    		        <a href="../registration" class="highlight-button transform-button split round">
                        <div class="list">
                            <p class="banner-text">Manage Registration Requests</p>
                            <small>Registration Management</small>
                        </div>
                        <div class="animate-left">
            	            <div class="toggle-button">
            	                <i class="fas fa-chevron-right"></i>
            	            </div>
                        </div>
    		        </a>
    		        <hr>
    		        <a href="../user" class="highlight-button transform-button split round">
                        <div class="list">
                            <p class="banner-text">Manage Userbase</p>
                            <small>User Management</small>
                        </div>
                        <div class="animate-left">
            	            <div class="toggle-button">
            	                <i class="fas fa-chevron-right"></i>
            	            </div>
                        </div>
    		        </a>
		        </div>
		    </div>
        </div>
	</body>
</html>
