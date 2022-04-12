<?php
/* PHP external files */
require_once('../../../../private/sysNotification.php');
require_once('../../../../private/userbase.php');

/* Force https connection */
forceHTTPS();

session_start();
if(!checkIfLoggedIn() || !isClient()) {
    header("Location: ../signin.php");
    die();
}

/* Check if the user has been inactive */
if (checkInactive()) {
    header("Location: ../../requests/signout.php");
    die();
}

?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
	    <title>Open New Account</title>
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
    			<li class="menulogo"><a href="../home.php">Goldman Stacks</a></li>
    			<li class="menutoggle"><a href="#"><i class="fas fa-bars"></i></a></li>
    			<li class="menuitem"><a href="../home.php">Home</a></li>
    			<li class="menuitem"><a href="transfer.php">Transfer</a></li>
    			<li class="menuitem"><a href="payments.php">Payments</a></li>
    			<li class="menuitem"><a href="open.php">Open New Account</a></li>
    			<li class="menuitem"><a href="statement.php">Statement</a></li>
    		</ul>
    		<ul class="menugroup">
    			<li class="menuitem"><a href="../user/options.php">Options</a></li>
    			<li class="menuitem"><a href="../../requests/signout.php">Sign Out</a></li>
    		</ul>
    	</nav>
    	<?php notification(); ?>
    	<div class="container flex-center">
    	    <div class="list main maximize">
    	        <h2 id="title">Select Account Type</h2>
    	        <label class="info">Request to open a new account</label>
    	        <hr>
        	    <div class="split">
        	        <button class="block-button round" onClick="showPopUp('debit')">
        	            <div class="text-left">
            	            <p class="focused-info">DEBIT</p>
            	            <p>Account</p>
        	            </div>
        	        </button>
        	        <button class="block-button round" onClick="showPopUp('savings')">
        	            <div class="text-left">
        	                <p class="focused-info">SAVINGS</p>
        	                <p>Account</p>
        	            </div>
        	        </button>
        	        <button class="block-button round" onClick="showPopUp('credit')">
        	            <div class="text-left">
        	                <p class="focused-info">CREDIT</p>
        	                <p>Account</p>
        	            </div>
        	        </button>
        	    </div>
    	    </div>
    	</div>
        <div id="pop-up" class="pop-up">
            <div onClick="hidePopUp()" class="flex-center-item">
            </div>
            <div id="pup-up-element" class="pop-up-content fixed-sub round hidden">
                <button onClick="hidePopUp()" class="expand-button transform-button extend-right round">
                    <div class="split">
                        <p class="condensed-info"><i class="fas fa-arrow-left"></i></p>
                        <div class="animate-right">
        		            <div class="toggle-button">
        		                <p class="expanded-info">Return</p>
        		            </div>
        	            </div>
                    </div>
                </button>
                <br>
                <br>
                <h2 id="title">Open New Account</h2>
                <p class="info">Enter an account nickname for the requested account.</p><br>
                <form id="edit">
    	            <label for="name" class="info">Account Nickame</label>
    	            <div class="form-item">
    		            <input id="name" class="input-field" type="text">
    	            </div>
                    <hr>
                    <div class="form-item">
                        <button form="edit" class="standard-button transform-button flex-center round">
                            <div class="split">
                                <p class="animate-left">Request New Account<p>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
	</body>
	<script type="text/javascript" src="../../js/navigation.js"></script>
	<script>
        function showPopUp(type) {
            document.getElementById("pop-up").classList.add("show-popup-content");
            document.getElementById("pup-up-element").classList.remove("hidden");
        }
        
        function hidePopUp() {
            document.getElementById("pop-up").classList.remove("show-popup-content");
            document.getElementById("pup-up-element").classList.add("hidden");
        }
	</script>
</html>
