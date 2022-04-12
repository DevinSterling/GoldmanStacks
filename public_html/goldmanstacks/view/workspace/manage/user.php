<?php
require_once('../../../../../private/sysNotification.php');
require_once('../../../../../private/userbase.php');
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
				<li class="menulogo"><a href="../manage">Goldman Stacks</a></li>
                <li class="menutoggle"><a href="#"><i class="fas fa-bars"></i></a></li>
				<li class="menuitem"><a href="../manage">Manage</a></li>
				<li class="menuitem"><a href="../search">Search</a></li>
			</ul>
			<ul class="menugroup">
				<li class="menuitem"><a href="../staff/options">Options</a></li>
				<li class="menuitem"><a href="../../login">Sign Out</a></li>
			</ul>
		</nav>
		<div class="sys-notification">Logged as Employee</div>
		<?php notification(); ?>
	    <div class="list main">
	        <div class="container">
    	        <h2 id="title"><?php echo $currentAccountName?> User (id)</h2>
    	    </div>
        </div>
	</body>
</html>
