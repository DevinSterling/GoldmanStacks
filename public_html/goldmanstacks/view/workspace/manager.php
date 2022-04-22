<?php
require_once('../../../../private/sysNotification.php');
require_once('../../../../private/userbase.php');

$lastLog = date("F j, Y, g:i a"); // Last time of log
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title>Management</title>
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
				<li class="menulogo"><a href="manager">Goldman Stacks</a></li>
                <li class="menutoggle"><a href="#"><i class="fas fa-bars"></i></a></li>
				<li class="menuitem"><a href="manager">Manage</a></li>
				<div class="search-bar">
    				<input type="text" placeholder="Search" class="nav-search-bar">
    				<button type="submit" class="nav-search-button"><i class="fas fa-search"></i></button>
				</div>
			</ul>
			<ul class="menugroup">
				<li class="menuitem"><a href="staff/options">Options</a></li>
				<li class="menuitem"><a href="../login">Sign Out</a></li>
			</ul>
		</nav>
		<div class="sys-notification">Logged as Employee</div>
		<?php notification(); ?>
    	<div class="container flex-center">
    	    <div class="list main">
    	        <h2 id="title">Management</h2>
		        <label class="info">Available Options</label>
    		    <a href="search" class="big-color-button transform-button split round shadow">
		            <div class="list">
		                <p class="focused-info">Userbase</p>
		                <p>Client Management</p>
		            </div>
		            <div class="split animate-left">
		                <div class="list text-right">
						    <p>Users</p>
							<p class="focused-info">14</p>
		                </div>
    		            <div class="toggle-button">
    		                <i class="fas fa-chevron-right"></i>
    		            </div>
    		        </div>
    		    </a>
    		    <a href="#" class="big-color-button transform-button split round shadow">
		            <div class="list">
		                <p class="focused-info">Client Bank Account Requests</p>
		                <p>Bank Account Management</p>
		            </div>
		            <div class="split animate-left">
		                <div class="list text-right">
						    <p>Requests</p>
							<p class="focused-info">14</p>
		                </div>
    		            <div class="toggle-button">
    		                <i class="fas fa-chevron-right"></i>
    		            </div>
    		        </div>
    		    </a>
    		    <a href="#" class="big-color-button transform-button split round shadow">
		            <div class="list">
		                <p class="focused-info">Client Close Bank Account Requests</p>
		                <p>Bank Account Management</p>
		            </div>
		            <div class="split animate-left">
		                <div class="list text-right">
						    <p>Requests</p>
							<p class="focused-info">2</p>
		                </div>
    		            <div class="toggle-button">
    		                <i class="fas fa-chevron-right"></i>
    		            </div>
    		        </div>
    		    </a>
    		    <a href="#" class="big-color-button transform-button split round shadow">
		            <div class="list">
		                <p class="focused-info">Client Registration Requests</p>
		                <p>Registration Management</p>
		            </div>
		            <div class="split animate-left">
		                <div class="list text-right">
						    <p>Requests</p>
							<p class="focused-info">91</p>
		                </div>
    		            <div class="toggle-button">
    		                <i class="fas fa-chevron-right"></i>
    		            </div>
    		        </div>
    		    </a>
    		    <a href="#" class="big-color-button transform-button split round shadow">
		            <div class="list">
		                <p class="focused-info">Server Logs</p>
		                <p>Server Management</p>
		            </div>
		            <div class="split animate-left">
		                <div class="list text-right">
						    <p>Logs</p>
							<p class="focused-info">10</p>
		                </div>
    		            <div class="toggle-button">
    		                <i class="fas fa-chevron-right"></i>
    		            </div>
    		        </div>
    		    </a>
		    </div>
		    <div class="list fixed-sub">
		        <div class="container">
		            <div class="split">
        		        <p class="big">Details</p>
    		            <a href="manager" class="expand-button transform-button extend-left round shadow">
    		                <div class="split">
    		                    <div class="animate-left">
                		            <div class="toggle-button">
                		                <p class="expanded-info">Refresh</p>
                		            </div>
            		            </div>
    		                    <p class="condensed-info"><i class="fas">&#xf021;</i></p>
    		                </div>
    		            </a>
        		    </div>
        		    <hr>
        		    <p class="banner-text">Name</p>
        		    <p class="info">Test Name Last</p>
        		    <p class="banner-text">Last Sign In</p>
        		    <p class="info"><?php echo $lastLog ?></p>
        		    <p class="banner-text">Last Logged</p>
        		    <p class="info"><?php echo $lastLog ?></p>
        		    <hr>
        		    <p class="banner-text">Server Uptime</p>
        		    <p class="info"><?php echo ucfirst(shell_exec('uptime -p')) ?></p>
        		    <p class="banner-text">CPU Usage</p>
        		    <p class="info">
    		        <?php
                        $execLoads = sys_getloadavg();
                        $execCores = trim(shell_exec("grep -P '^processor' /proc/cpuinfo|wc -l"));
                        echo $cpu = round($execLoads[1]/($execCores + 1)*100, 0) . '%';
    		        ?>
    		        </p>
        		    <p class="banner-text">Memory Usage</p>
        		    <p class="info">
    		        <?php
                        $execFree = explode("\n", trim(shell_exec('free')));
                        $getMem = preg_split("/[\s]+/", $execFree[1]);
                        echo $mem = round($getMem[2]/$getMem[1]*100, 0) . '%';
    		        ?>
    		        </p>
    		        <hr>
        		    <p class="banner-text">Currently Logged Admins</p>
        		    <p class="info">0</p>
        		    <p class="banner-text">Currently Logged Clients</p>
        		    <p class="info">0</p>
        		    <hr>
    		        <a href="staff/options" class="highlight-button transform-button split round">
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
    		        <a href="../login" class="highlight-button transform-button split round">
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
	</body>
	<script type="text/javascript" src="../../js/navigation.js"></script>
</html>
