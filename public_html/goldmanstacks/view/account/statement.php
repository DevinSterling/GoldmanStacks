<?php
require_once('../../../../private/sysNotification.php');
require_once('../../../../private/userbase.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkClientStatus(); // Check if the client is signed in
?>

<!DOCTYPE html>
<html lang="en-US">
	<head>
	    <title>Home</title>
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
			<li class="menuitem"><a href="transfer">Transfer</a></li>
			<li class="menuitem"><a href="payments">Payments</a></li>
			<li class="menuitem"><a href="open">Open New Account</a></li>
			<li class="menuitem"><a href="statement">Statement</a></li>
			</ul>
			<ul class="menugroup">
				<li class="menuitem"><a href="../user/options">Options</a></li>
				<li class="menuitem"><a href="../../requests/signout">Sign Out</a></li>
			</ul>
		</nav>
		<?php notification(); ?>
		<div class="container flex-center">
		    <div class="list sub">
		        <div class="split">
		            <h2 id="title">Statement</h2>
                    <button onClick="printSelected('Statement')" class="expand-button transform-button extend-left round">
                        <div class="split">
                            <div class="animate-left">
            		            <div class="toggle-button">
            		                <p class="expanded-info">Print Statement</p>
            		            </div>
            	            </div>
                            <p class="condensed-info"><i class="fas fa-print"></i></p>
                        </div>
                    </button>
		        </div>
		        <div id="Statement">
		        </div>
		    </div>
		</div>
	</body>
	<script type="text/javascript" src="../../js/navigation.js"></script>
	<script type="text/javascript" src="../../js/print.js"></script>
</html>
