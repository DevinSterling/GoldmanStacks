<?php
require_once('../../../../private/sysNotification.php');
require_once('../../../../private/config.php');
require_once('../../../../private/functions.php');
require_once('../../../../private/userbase.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkClientStatus(); // Check if the client is signed in

/* SESSION Variables */
$userID = $_SESSION['uid'];
$key = $_SESSION['key'];

/* GET Variables */
$referencedName = $_GET['acc'];

/* Variables */
$accounts = array();

/* Csrf form tokens */
$internalTransferToken = hash_hmac('sha256', '/newInternalTransfer.php', $_SESSION['key']);
$externalTransferToken = hash_hmac('sha256', '/newExternalTransfer.php', $_SESSION['key']);

/* Get Database Connection */
$db = getUpdateConnection();

/* Check Database Connection */
if ($db === null) {
    header("Location: ../error/error.php");
    die();
}

/* Get client accounts */
$queryAccounts = $db->prepare("SELECT nickName, accountType, accountNum FROM accountDirectory WHERE clientID=?");
$queryAccounts->bind_param("i", $userID);
$queryAccounts->execute();

$resultAccounts = $queryAccounts->get_result();
$rowAccounts = $resultAccounts->fetch_all(MYSQLI_ASSOC);

foreach ($rowAccounts as $account) {
    /* Create three dimensional associative array */
    $accounts[] = array('nickName' => $account['nickName'], 'type' => $account['accountType'], 'number' => $account['accountNum']);
}

$resultAccounts->free();
$queryAccounts->close();
$db->close();
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
	<title>Transfer</title>
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
        <button id="notification" onClick="hideNotification()" class="notification sub success transform-button round collapse">
            <p><i id="notification-icon" class="fas fa-check icon"></i><span id="notification-text"></span></p>
            <div class="split">
                   <div class="toggle-button">
    	            <i class="fas fa-times"></i>
    	        </div>
            </div>
        </button>
    	<?php
        if (!empty($referencedName)) {
            echo "<div class=\"container flex-center marginless-bottom\">
                <div class=\"list sub\">
                    <div class=\"split\">
       	                <a id=\"return\" href=\"details.php?acc=$referencedName\" class=\"expand-button transform-button extend-right round\">
        	                <div class=\"split\">
        	                    <p class=\"condensed-info\"><i class=\"fas fa-arrow-left\"></i></p>
        	                    <div class=\"animate-right\">
                		            <div class=\"toggle-button\">
                		                <p class=\"expanded-info\">View Account</p>
                		            </div>
            		            </div>
        	                </div>
        	            </a>
        	            <div></div>
                    </div>
                </div>
            </div>";
        }
            
        ?>
        
        <div class="container flex-center <?php if ($referencedName !== null) echo "marginless" ?>">
            <div class="list mini">
                <button class="tab-button transform-button round selected" data-id="internal-transfer" data-title="Internal Transactions">
                    <div class="split">
                        <div class="text-right">
                            <p>Internal</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>
                <button class="tab-button transform-button round"  data-id="external-transfer" data-title="External Transactions">
                    <div class="split">
                        <div class="text-right">
                            <p>External</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>            
		    </div>
            <div class="list sub">
                <h2 id="title">Internal Transactions</h2>
                <p class="info">Transfer funds between accounts</p>
                <br>
                <form id="internal-transfer" action="../../requests/account/newInternalTransfer" class="flex-form">
    	            <label for="internal-sender" class="info">Sender</label>
		            <select id="internal-sender" name="from" class="input-field" required>
                        <?php
                        foreach ($accounts as $account) {
                            echo "<option value=\"" . encrypt($account['number'], $key) . "\"";
                           
                            if ($referencedName === $account['nickName']) {
                                echo " selected";
                            }
                           
                            echo ">" . ($account['nickName'] . " (" . ucfirst($account['type']) . ")" ) . "</option>";
                        }
                        ?>
		            </select>
    	            <label for="receiverreceiver" class="info">Receiver</label>
		            <select id="internal-receiver" name="to" class="input-field" required>
                        <?php
                        foreach ($accounts as $account) {
                            echo "<option value=\"" . encrypt($account['number'], $key) . "\">" . ($account['nickName'] . " (" . ucfirst($account['type']) . ")" ) . "</option>";
                        }
                        ?>
                    </select>
    		        <hr>
    	            <label for="internal-amount" class="info">Amount</label>
    		        <input id="internal-amount" name="usd" type="number" min="0" placeholder="USD" class="input-field" required>
                    <input type="hidden" name="token" value="<?php echo $internalTransferToken ?>" required>
                    <button type="submit" class="standard-button transform-button flex-center round">
                        <div class="split">
                            <p class="animate-left">Apply<p>
           		            <div class="toggle-button">
            		            <i class="fas fa-chevron-right"></i>
            		        </div>
                        </div>
                    </button>
                </form>
                <form id="external-transfer" action="../../requests/account/newExternalTransfer" class="flex-form hidden">
    	            <label for="external-sender" class="info">Sender</label>
		            <select id="external-sender" name="from" class="input-field" required>
                        <?php
                        foreach ($accounts as $account) {
                            echo "<option value=\"" . encrypt($account['number'], $key) . "\"";
                           
                            if ($referencedName === $account['nickName']) {
                                echo " selected";
                            }
                           
                            echo ">" . ($account['nickName'] . " (" . ucfirst($account['type']) . ")" ) . "</option>";
                        }
                        ?>
		            </select>
    	            <label for="external-receiver" class="info">Receiver Bank Account Number</label>
                    <input id="external-receiver" name="to" class="input-field" type="text" required>
    		        <hr>
    	            <label for="external-amount" class="info">Amount</label>
    		        <input id="external-amount" name="usd" class="input-field" type="number" min="0" placeholder="USD" required>
                    <input type="hidden" name="token" value="<?php echo $externalTransferToken ?>" required>
                    <button type="submit" class="standard-button transform-button flex-center round">
                        <div class="split">
                            <p class="animate-left">Apply<p>
           		            <div class="toggle-button">
            		            <i class="fas fa-chevron-right"></i>
            		        </div>
                        </div>
                    </button>
                </form>
            </div>
            <div class="list mini"></div>
    	</div>
	</body>
	<script type="text/javascript" src="../../js/navigation.js"></script>
	<script type="text/javascript" src="../../js/tabs.js"></script>
	<script type="text/javascript" src="../../js/notification.js"></script>
	<script type="text/javascript">
	    let internalSender = document.getElementById('internal-sender');
	    let internalReceiver = document.getElementById('internal-receiver');
	    let externalSender = document.getElementById('external-sender');
	    let externalReceiver = document.getElementById('external-receiver');
	
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('internal-transfer').addEventListener('submit', handleForm);
            document.getElementById('external-transfer').addEventListener('submit', handleForm);
        });
        
        function handleForm(event) {
	        event.preventDefault();
	        
	        let form = event.target;
	        let formData = new FormData(form);
	        
	        let url = form.action;
	        let request = new Request(url, {
	            body: formData,
	            method: 'POST',
	        });
	        
	        fetch(request)
	            .then((response) => response.json())
	            .then((data) => {          
	                if (data.response) {
	                    if (form.id === 'internal-transfer') setSuccessNotification('Transfered $' + formData.get('usd') + ' to ' + internalReceiver.selectedOptions[0].text + ' from ' + internalSender.selectedOptions[0].text);
	                    else setSuccessNotification('Transfered $' + formData.get('usd') + ' to (*' + externalReceiver.value.substring(externalReceiver.value.length - 4) + ') from ' + externalSender.selectedOptions[0].text);
	                    form.reset();
	                } else {
	                    setFailNotification(data.message);
	                }
			
	                showNotification();
	            })
	            .catch(console.warn);
	            
            window.scrollTo({ top: 0, behavior: 'smooth' });
	    }
    </script>
</html>
