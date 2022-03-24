<?
/* PHP external files */
require_once('/home/sterlid2/Private/sysNotification.php');

?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
	<title>User Options</title>
	<!-- Stylesheet -->
	<link rel="stylesheet" href="../CSS/stylesheet.css">
	<!-- Favicon -->
	<link rel="icon" href="../Images/logo.ico">
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
			<li class="menulogo"><a href="../home.php">TempBank</a></li>
	<li class="menutoggle"><a href="#"><i class="fas fa-bars"></i></a></li>
			<li class="menuitem"><a href="../home.php">Home</a></li>
			<li class="menuitem"><a href="../account/transfer.php">Transfer</a></li>
			<li class="menuitem"><a href="../account/payments.php">Payments</a></li>
			<li class="menuitem"><a href="../account/open.php">Open New Account</a></li>
			<li class="menuitem submenu">
			    <a tabindex="0">Statements</a>
			    <!--<ul class="submenugroup">
				<li class="subitem"><a href="#PrintAll">Print Statement</a></li>
				<li class="subitem"><a href="#PrintOne">Print Specific</a></li>
			    </ul>-->
			</li>
		</ul>
		<ul class="menugroup">
			<li class="menuitem"><a href="options.php">Options</a></li>
			<li class="menuitem"><a href="../login.php">Sign Out</a></li>
		</ul>
	</nav>
	<? notification(); ?>
        <div class="container flex-center">
            <div class="list mini">
                <button class="tab-button transform-button round selected" data-id="change-username" data-title="Change Username">
                    <div class="split">
                        <div class="text-right">
                            <p>Username</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>
                <button class="tab-button transform-button round"  data-id="change-password" data-title="Change Password">
                    <div class="split">
                        <div class="text-right">
                            <p>Password</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>
                <button class="tab-button transform-button round"  data-id="change-address" data-title="Change Address">
                    <div class="split">
                        <div class="text-right">
                            <p>Address</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>
                <button class="tab-button transform-button round"  data-id="change-phone" data-title="Change Phone Number">
                    <div class="split">
                        <div class="text-right">
                            <p>Phone</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>
                <button class="tab-button transform-button round"  data-id="change-email" data-title="Change Email Address">
                    <div class="split">
                        <div class="text-right">
                            <p>Email</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>  
		    </div>
            <div class="list sub">
                <h2 id="title">Change Username</h2>
                <form id="change-username">
                    <label for="username" class="info">New Username</label>
    	            <div class="form-item">
    		            <input id="username" class="input-field" type="text">
    	            </div>
                    <hr>
                    <div class="form-item">
                        <button form="change-username" class="standard-button transform-button flex-center round">
                            <div class="split">
                                <p class="animate-left">Apply<p>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
                        </button>
                    </div>
                </form>
                <form id="change-password" class="hidden">
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
                <form id="change-address" class="hidden">
                    <label for="address-line-1" class="info">Address Line 1</label>
                    <div class="form-item">
                        <input id="address-line-1" type="info" class="input-field">
                    </div>
                    <label for="address-line-2" class="info">Address Line 2</label>
                    <div class="form-item">
                        <input id="address-line-2" type="info" class="input-field">
                    </div>
                    <label for="address-city" class="info">City</label>
                    <div class="form-item">
                        <input id="address-city" type="text" class="input-field">
                    </div>
                    <label for="address-state" class="info">State</label>
                    <div class="form-item">
                        <input id="address-state" type="text" class="input-field">
                    </div>
                    <label for="address-postal-code" class="info">Postal Code</label>
                    <div class="form-item">
                        <input id="address-postal-code" type="text" class="input-field">
                    </div>
                    <hr>
                    <div class="form-item">
                        <button form="change-address" class="standard-button transform-button flex-center round">
                            <div class="split">
                                <p class="animate-left">Apply<p>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
                        </button>
                    </div>
                </form>
                <form id="change-phone" class="hidden">
                    <label for="phone-number" class="info">Phone Number</label>
                    <div class="form-item">
                        <input id="phone-number" type="text" class="input-field">
                    </div>                  <hr>
                    <div class="form-item">
                        <button form="change-phone" class="standard-button transform-button flex-center round">
                            <div class="split">
                                <p class="animate-left">Apply<p>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
                        </button>
                    </div>
                </form>
                <form id="change-email" class="hidden">
                    <label for="email-address" class="info">Email Address</label>
                    <div class="form-item">
                        <input id="email-address" type="text" class="input-field">
                    </div>
                    <hr>
                    <div class="form-item">
                        <button form="change-email" class="standard-button transform-button flex-center round">
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
	<script type="text/javascript" src="../Scripts/navigation.js"></script>
	<script type="text/javascript" src="../Scripts/tabs.js"></script>
</html>
