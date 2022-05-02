<?php
require_once('../../../../private/sysNotification.php');
require_once('../../../../private/userbase.php');
require_once('../../../../private/config.php');
require_once('../../../../private/userbase.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkEmployeeStatus(); // Check if the employee is signed in

/* SESSION Variable */
$userID = $_SESSION['uid'];
$lastSignin = $_SESSION['lastSignin'];

/* Database connection */
$db = getUpdateConnection();

if ($db === null) {
    header("Location: ");
    die();
}

$selectStatement = $db->prepare("SELECT firstName, middleName, lastName FROM users WHERE userID=?");
$selectStatement->bind_param("i", $userID);
$selectStatement->execute();
$selectStatement->store_result();

$selectStatement->bind_result($firstName, $middleName, $lastName);
$selectStatement->fetch();
$selectStatement->close();
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
			</ul>
			<ul class="menugroup">
				<li class="menuitem"><a href="staff/options">Options</a></li>
				<li class="menuitem"><a href="../../requests/signout">Sign Out</a></li>
			</ul>
		</nav>
		<div class="sys-notification">Logged as Employee</div>
		<?php notification(); ?>
    	<div class="container flex-center">
    	    <div class="list main">
    	        <h2 id="title">Management</h2>
		        <label class="info">Available Options</label>
    		    <a href="search/user" class="big-color-button transform-button split round shadow">
		            <div class="list">
		                <p class="focused-info">Userbase</p>
		                <p>Client Management</p>
		            </div>
		            <div class="split animate-left">
		                <div class="list text-right">
						    <p>Users</p>
							<p class="focused-info">
							<?php
							    /* Get total amount of users */
                                $result = $db->query("SELECT count(*) FROM users");
                                $amount = $result->fetch_row()[0];
                                $result->free();
                                echo $amount;
							?>
							</p>
		                </div>
    		            <div class="toggle-button">
    		                <i class="fas fa-chevron-right"></i>
    		            </div>
    		        </div>
    		    </a>
    		    <a href="search/account/open" class="big-color-button transform-button split round shadow">
		            <div class="list">
		                <p class="focused-info">Client Bank Account Requests</p>
		                <p>Bank Account Management</p>
		            </div>
		            <div class="split animate-left">
		                <div class="list text-right">
						    <p>Requests</p>
							<p class="focused-info">
							<?php
							    /* Get total amount of bank account open requests */
                                $result = $db->query("SELECT count(*) FROM accountRequests");
                                $amount = $result->fetch_row()[0];
                                $result->free();
                                echo $amount;
							?>
							</p>
		                </div>
    		            <div class="toggle-button">
    		                <i class="fas fa-chevron-right"></i>
    		            </div>
    		        </div>
    		    </a>
    		    <a href="search/account/close" class="big-color-button transform-button split round shadow">
		            <div class="list">
		                <p class="focused-info">Client Close Bank Account Requests</p>
		                <p>Bank Account Management</p>
		            </div>
		            <div class="split animate-left">
		                <div class="list text-right">
						    <p>Requests</p>
							<p class="focused-info">
							<?php
							    /* Get total amount of bank account close requests */
                                $result = $db->query("SELECT count(*) FROM accountCloseRequests");
                                $amount = $result->fetch_row()[0];
                                $result->free();
                                echo $amount;
							?>
							</p>
		                </div>
    		            <div class="toggle-button">
    		                <i class="fas fa-chevron-right"></i>
    		            </div>
    		        </div>
    		    </a>
    		    <a href="search/registration" class="big-color-button transform-button split round shadow">
		            <div class="list">
		                <p class="focused-info">Client Registration Requests</p>
		                <p>Registration Management</p>
		            </div>
		            <div class="split animate-left">
		                <div class="list text-right">
						    <p>Requests</p>
							<p class="focused-info">
							<?php
							    /* Get total amount of users that are not verified (registration pending) */
                                $result = $db->query("SELECT count(*) FROM client WHERE verified=0");
                                $amount = $result->fetch_row()[0];
                                $result->free();
                                echo $amount;
							?>
							</p>
		                </div>
    		            <div class="toggle-button">
    		                <i class="fas fa-chevron-right"></i>
    		            </div>
    		        </div>
    		    </a>
    		    <a href="#" class="big-color-button transform-button split round shadow hidden">
		            <div class="list">
		                <p class="focused-info">Server Logs</p>
		                <p>Server Management</p>
		            </div>
		            <div class="split animate-left">
		                <div class="list text-right">
						    <p>Logs</p>
							<p class="focused-info">
							<?php
							
							?>
							</p>
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
        		    <p class="info"><?php echo $firstName . ' ' . $middleName . ' ' . $lastName ?></p>
        		    <p class="banner-text">Last Sign In</p>
        		    <p class="info"><?php echo $lastSignin ?></p>
        		    <hr>
        		    <p class="banner-text">Server Uptime</p>
        		    <p class="info"><?php echo ucfirst(shell_exec('uptime -p')) ?></p>
        		    <p class="banner-text">CPU Usage</p>
        		    <p class="info">
    		        <?php
                        $exec_loads = sys_getloadavg();
                        $exec_cores = trim(shell_exec("grep -P '^processor' /proc/cpuinfo|wc -l"));
                        echo $cpu = round($exec_loads[1]/($exec_cores + 1)*100, 0) . '%';
    		        ?>
    		        </p>
        		    <p class="banner-text">Memory Usage</p>
        		    <p class="info">
    		        <?php
                        $exec_free = explode("\n", trim(shell_exec('free')));
                        $get_mem = preg_split("/[\s]+/", $exec_free[1]);
                        echo $mem = round($get_mem[2]/$get_mem[1]*100, 0) . '%';
    		        ?>
    		        </p>
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
<?php
$db->close();
