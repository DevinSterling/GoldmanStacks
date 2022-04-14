<?php
require_once('../../../../private/sysNotification.php');
require_once('../../../../private/config.php');
require_once('../../../../private/userbase.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkClientStatus(); // Check if the client is signed in

/* SESSION Variables */
$userID = $_SESSION['uid'];

/* Requestable accounts */
$accountTypes = array('checking', 'savings', 'credit');

/* Create csrf token for account request form */
$requestAccountToken = hash_hmac('sha256', '/requestAccount.php', $_SESSION['key']);

/* Get database connection */
$db = getUpdateConnection();

/* Check Database Connection */
if ($db === null) {
    header("Location: ../error/error.php");
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
        <button id="notification" onClick="hideNotification()" class="notification main success transform-button round collapse">
            <p><i id="notification-icon" class="fas fa-check icon"></i><span id="notification-text"></span></p>
            <div class="split">
   	            <div class="toggle-button">
		            <i class="fas fa-times"></i>
		        </div>
            </div>
        </button>
    	<div class="container flex-center">
    	    <div class="list main maximize">
    	        <h2 id="title">Select Account Type</h2>
    	        <label class="info">Request to open a new account</label>
    	        <hr>
        	    <div class="split">
        	        <?php
        	        /* Find accounts that is still pending for the current client  */
                    $queryAccountRequests = $db->prepare("SELECT accountType FROM accountRequests WHERE clientID=? AND verified=0");
                    $queryAccountRequests->bind_param("i", $userID);
                    $queryAccountRequests->execute();
                    
                    $resultAccountRequests = $queryAccountRequests->get_result();
                    $rowsAccountRequests = $resultAccountRequests->fetch_all(MYSQLI_ASSOC);
        	        
        	        foreach ($accountTypes as $account) {
         	            $isRequested = in_array($account, array_column($rowsAccountRequests, 'accountType'));
        	            
        	            echo "<button id=\"request-$account-button\" type=\"button\" class=\"block-button round\"";
        	            
        	            if ($isRequested) {
        	                echo "disabled>";
        	                $requestInformation = "Request Pending";
        	            } else {
        	                echo "onClick=\"showPopUp('$account')\">";
        	                $requestInformation = "Request Account";
        	            }

        	            echo "<div class=\"text-left\">
            	                <p class=\"focused-info\">$account</p>
            	                <p id=\"request-$account-information\">$requestInformation</p>
        	                </div>
        	            </button>";
        	        }
        	        
			$resultAccountRequests->free();
			$queryAccountRequests->close();
        	        $db->close();
        	        ?>
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
                <h2 id="title">Open New <span id="account-type-title"></span> Account</h2>
                <p class="info">A request will be submitted to open a new <b><span id="account-type-description"></span></b> account.</p>
                <form id="request-account">
                    <input id="account-type" type="hidden" value="" name="type" required>
                    <input type="hidden" value="<?php echo $requestAccountToken ?>" name="token" required>
                    <div class="form-item">
                        <button type="submit" class="standard-button transform-button flex-center round">
                            <div class="split">
                                <p class="animate-left">Submit Request<p>
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
	<script type="text/javascript" src="../../js/notification.js"></script>
	<script type="text/javascript">
	    const popupTitleType = document.getElementById('account-type-title');
	    const popupDescriptionType = document.getElementById('account-type-description');
	    const inputAccountType = document.getElementById('account-type');
	    
        function showPopUp(type) {
            document.getElementById("pop-up").classList.add("show-popup-content");
            document.getElementById("pup-up-element").classList.remove("hidden");
            
            popupTitleType.textContent = type.charAt(0).toUpperCase() + type.slice(1);
            popupDescriptionType.textContent = type;
            inputAccountType.value = type;
        }
        
        function hidePopUp() {
            document.getElementById("pop-up").classList.remove("show-popup-content");
            document.getElementById("pup-up-element").classList.add("hidden");
        }
	</script>
	<script type="text/javascript">
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('request-account').addEventListener('submit', handleForm);
        });
        
        function handleForm(event) {
	        event.preventDefault();
	        
	        let form = event.target;
	        let formData = new FormData(form);
	        
	        let url = "../../requests/account/requestAccount";
	        let request = new Request(url, {
	            body: formData,
	            method: 'POST',
	        });
	        
	        fetch(request)
	            .then((response) => response.json())
	            .then((data) => {          
	                if (data.response) {
	                    setSuccessNotification(data.message);
	                    document.getElementById("request-" +  inputAccountType.value + "-button").disabled = true;
	                    document.getElementById("request-" +  inputAccountType.value + "-information").textContent = "Request Pending";
	                } else {
	                    setFailNotification(data.message);
	                }
			
	                showNotification();
	            })
	            .catch(console.warn);
	            
            window.scrollTo({ top: 0, behavior: 'smooth' });
            hidePopUp();
	    }
    </script>
</html>
