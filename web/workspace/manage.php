<?
/* PHP external files */
require_once('/home/sterlid2/Private/sysNotification.php');

$amountOfUsers = 40;

$lastLog = date("F j, Y, g:i a"); // Last time of log

?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title>Management</title>
		<!-- Stylesheet -->
		<link rel="stylesheet" href="/~sterlid2/bank/CSS/stylesheet.css">
		<!-- Favicon -->
		<link rel="icon" href="/~sterlid2/bank/Images/logo.ico">
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
				<li class="menulogo"><a href="/~sterlid2/bank/workspace/manage.php">TempBank</a></li>
                <li class="menutoggle"><a href="#"><i class="fas fa-bars"></i></a></li>
				<li class="menuitem"><a href="/~sterlid2/bank/workspace/manage.php">Manage</a></li>
				<li class="menuitem"><a href="/~sterlid2/bank/workspace/search.php">Search</a></li>
			</ul>
			<ul class="menugroup">
				<li class="menuitem"><a href="/~sterlid2/bank/workspace/staff/options.php">Options</a></li>
				<li class="menuitem"><a href="/~sterlid2/bank/login.php">Sign Out</a></li>
			</ul>
		</nav>
		<div class="sys-notification">Logged as Employee</div>
		<? notification(); ?>
		<div class="container flex-center">
		    <div class="list main">
		        <h2 id="title">Management</h2>
		        <label class="info">Available Options</label>
    		    <a href="/~sterlid2/bank/workspace/search.php" class="big-color-button transform-button split round shadow">
		            <div class="list">
		                <p class="focused-info">Manage Users</p>
		                <p>Mange User Accounts</p>
		            </div>
		            <div class="holder">
    		            <div class="animate-left">
        		            <div class="toggle-button">
        		                <i class="fas fa-chevron-right"></i>
        		            </div>
        		        </div>
        		    </div>
    		    </a>
    		    <a href="#" class="big-color-button transform-button split round shadow">
		            <div class="list">
		                <p class="focused-info">Manage Button</p>
		                <p>A Manage Button</p>
		            </div>
		            <div class="animate-left">
    		            <div class="toggle-button">
    		                <i class="fas fa-chevron-right"></i>
    		            </div>
    		        </div>
    		    </a>
    		    <a href="#" class="big-color-button transform-button split round shadow">
		            <div class="list">
		                <p class="focused-info">Manage Button 2</p>
		                <p>A Manage Button 2</p>
		            </div>
		            <div class="animate-left">
    		            <div class="toggle-button">
    		                <i class="fas fa-chevron-right"></i>
    		            </div>
    		        </div>
    		    </a>
		    </div>
		    <div class="list sub">
		        <div class="container">
    		        <div class="item-banner top-round shadow">
        		        <p class="banner-text">Employee Details</p>
        		    </div>
        		    <div class="item-content bottom-round shadow">
        		        <p>Emplyee (name)</p>
        		        <hr>
        		        <p>Last Log: <? echo $lastLog ?></p>	
        		        <hr>
        		        <a href="/~sterlid2/bank/workspace/staff/options.php" class="highlight-button transform-button split round">
                            <div class="list">
                                <p>Options</p>
                            </div>
                            <div class="animate-left">
                	            <div class="toggle-button">
                	                <i class="fas fa-chevron-right"></i>
                	            </div>
                            </div>
        		        </a>
                        <hr>
        		        <a href="/~sterlid2/bank/login.php" class="highlight-button transform-button split round">
                            <div class="list">
                                <p>Sign Out</p>
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
		</div>
	</body>
	<script type="text/javascript" src="/~sterlid2/bank/Scripts/navigation.js">
	</script>
</html>