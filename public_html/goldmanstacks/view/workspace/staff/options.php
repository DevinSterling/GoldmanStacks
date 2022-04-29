<?php
require_once('../../../../../private/sysNotification.php');
require_once('../../../../../private/userbase.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkEmployeeStatus(); // Check if the employee is signed in

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
				<li class="menuitem"><a href="../search">Search</a></li>
			</ul>
			<ul class="menugroup">
				<li class="menuitem"><a href="options">Options</a></li>
				<li class="menuitem"><a href="../../login">Sign Out</a></li>
			</ul>
		</nav>
		<div class="sys-notification">Logged as Employee</div>
		<?php notification(); ?>
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
                <form id="change-password">
                    <label for="current-password" class="info">Current Password</label>
    	            <div class="form-item">
    		            <input id="current-password" class="input-field" type="password">
    	            </div>
    	            <hr>
    	            <label for="new-password" class="info">New Password</label>
    	            <div class="form-item">
    		            <input id="new-password" class="input-field" type="password">
    	            </div>
    	            <label for="confirm-password" class="info">Confirm Password</label>
    	            <div class="form-item">
    		            <input id="confirm-password" class="input-field" type="password">
    	            </div>
                    <hr>
                    <div class="form-item">
                        <button form="change-password" class="standard-button transform-button flex-center round">
                            <div class="split">
                                <p class="animate-left">Apply<p>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
            <div class="list mini">
                <!--Dummy block for alignment-->
            </div>
    	</div>
	</body>
	<script type="text/javascript" src="../../../js/navigation.js"></script>
	<script type="text/javascript" src="../../../js/tabs.js"></script>
</html>
