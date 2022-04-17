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
$isNotReferenced = empty($_GET['acc']) ? true : false;

/* Csrf form tokens */
$internalTransferToken = hash_hmac('sha256', '/newInternalTransfer.php', $_SESSION['key']);
$externalTransferToken = hash_hmac('sha256', '/newExternalTransfer.php', $_SESSION['key']);
$getBalanceToken = hash_hmac('sha256', '/getBalance.php', $_SESSION['key']);

/* Get Database Connection */
$db = getUpdateConnection();

/* Check Database Connection */
if ($db === null) {
    header("Location: ../error/error.php");
    die();
}

/* Get client accounts */
$queryAccounts = $db->prepare("SELECT nickName, accountType, accountNum, balance FROM accountDirectory WHERE clientID=?");
$queryAccounts->bind_param("i", $userID);
$queryAccounts->execute();

$resultAccounts = $queryAccounts->get_result();
$rowAccounts = $resultAccounts->fetch_all(MYSQLI_ASSOC);

foreach ($rowAccounts as $account) {
    /* Create three dimensional associative array */
    $accounts[] = array('nickName' => $account['nickName'], 'type' => $account['accountType'], 'number' => $account['accountNum'], 'balance' => $account['balance']);
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
                        if ($isNotReferenced) {
                            $balance = $accounts[0]['balance'];
                        }
                        
                        foreach ($accounts as $account) {
                            echo "<option value=\"" . encrypt($account['number'], $key) . "\"";
                           
                            if ($referencedName === $account['nickName']) {
                                $balance = $account['balance'];
                                echo " selected";
                            }
                           
                            echo ">" . ($account['nickName'] . " (" . ucfirst($account['type']) . ")" ) . "</option>";
                        }
                        ?>
		            </select>
		            <p class="info">Balance: $<span id="internal-sender-balance"><?php echo number_format($balance, 2) ?></span></p>
		            <hr>
    	            <label for="receiverreceiver" class="info">Receiver</label>
		            <select id="internal-receiver" name="to" class="input-field" required>
                        <?php
                        echo "<option disabled selected value>Please select an account</option>";
                        
                        foreach ($accounts as $account) {
                            echo "<option value=\"" . encrypt($account['number'], $key) . "\">" . ($account['nickName'] . " (" . ucfirst($account['type']) . ")" ) . "</option>";
                        }
                        ?>
                    </select>
		            <p class="info">Balance: $<span id="internal-receiver-balance"><?php echo number_format(0, 2) ?></span></p>
		            <hr>
    	            <label for="internal-amount" class="info">Amount</label>
    		        <input id="internal-amount" name="usd" type="number" min="0" step="0.01" placeholder="USD" class="input-field" required>
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
                        if ($isNotReferenced) {
                            $balance = $accounts[0]['balance'];
                        }
                        
                        foreach ($accounts as $account) {
                            echo "<option value=\"" . encrypt($account['number'], $key) . "\"";
                           
                            if ($referencedName === $account['nickName']) {
                                $balance = $account['balance'];
                                echo " selected";
                            }
                           
                            echo ">" . ($account['nickName'] . " (" . ucfirst($account['type']) . ")" ) . "</option>";
                        }
                        ?>
		            </select>
		            <p class="info">Balance: $<span id="external-sender-balance"><?php echo number_format($balance, 2) ?></span></p>
		            <hr>
    	            <label for="external-receiver" class="info">Receiver Bank Account Number</label>
                    <input id="external-receiver" name="to" type="text" pattern="[0-9]{10}" class="input-field" required>
    		        <hr>
    	            <label for="external-amount" class="info">Amount</label>
    		        <input id="external-amount" name="usd" type="number" min="0" step="0.01" placeholder="USD" class="input-field" required>
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
                <div class="flex-form">
                    <h2 id="title">Transfer Confirmation</h2>
                    <p class="info">Sender</p>
                    <p id="transfer-sender"></p>
                    <p class="info">Receiver</p>
                    <p id="transfer-receiver"></p>
                    <p class="info">Amount</p>
                    <p id="transfer-amount"></p>
                    <button id="confirm-transfer" type="submit" class="standard-button transform-button flex-center round">
                        <div class="split">
                            <p class="animate-left">Confirm<p>
           		            <div class="toggle-button">
            		            <i class="fas fa-chevron-right"></i>
            		        </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
	</body>
	<script type="text/javascript" src="../../js/navigation.js"></script>
	<script type="text/javascript" src="../../js/tabs.js"></script>
	<script type="text/javascript" src="../../js/notification.js"></script>
	<script type="text/javascript">
	    /* PopUp Contents */
	    const popUpBackground = document.getElementById('pop-up');
	    const popUpElemement = document.getElementById('pup-up-element')
	    const popupTitleType = document.getElementById('account-type-title');
	    const popupDescriptionType = document.getElementById('account-type-description');
	    
	    /* PopUp Confirmation Contents */
	    const transactionSender = document.getElementById('transfer-sender');
	    const transactionReceiver = document.getElementById('transfer-receiver');
	    const transactionAmount = document.getElementById('transfer-amount');
	    
	    /* Form User Input */
	    const internalSender = document.getElementById('internal-sender');
	    const internalReceiver = document.getElementById('internal-receiver');
	    const externalSender = document.getElementById('external-sender');
	    const externalReceiver = document.getElementById('external-receiver');

        /* Form Balance*/
	    const internalSenderBalance = document.getElementById('internal-sender-balance');
	    const internalReceiverBalance = document.getElementById('internal-receiver-balance');
	    const externalSenderBalance = document.getElementById('external-sender-balance');
	    
	    let form = null;
	    let formData = null;
	    
	    let balance = '0.00';
	    
	    document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('internal-sender').addEventListener('change', retreiveInternalSenderBalance);
            document.getElementById('internal-receiver').addEventListener('change', retreiveInternalReceiverBalance);
            document.getElementById('external-sender').addEventListener('change', retreiveExternalSenderBalance);
            document.getElementById('internal-transfer').addEventListener('submit', showPopUp);
            document.getElementById('external-transfer').addEventListener('submit', showPopUp);
            document.getElementById('confirm-transfer').addEventListener('click', handleForm);
        });

        function showPopUp(event) {
            event.preventDefault();
            
	        form = event.target;
	        formData = new FormData(form);
	        
	        if (verifyUserInput()) {
	            hideNotification(); // Hide notification if visible
	            
                transactionAmount.textContent = '$' + formData.get('usd');
                
                popUpBackground.classList.add('show-popup-content');
                popUpElemement.classList.remove('hidden');
	        } else {
                showNotification();
	        }
        }
        
        function verifyUserInput() {
            let verified = false;
            
	        if (form.id === 'internal-transfer') {
	            if (internalSender.selectedOptions[0].text === internalReceiver.selectedOptions[0].text) {
	                setFailNotification("Accounts selected are the same");
	                return;
	            } else if (formData.get('usd') > Number(internalSenderBalance.textContent.replace(',', ''))) {
	                setFailNotification("Requested amount is over the current balance");
	                return;
	            } else {
                    transactionSender.textContent = internalSender.selectedOptions[0].text;
                    transactionReceiver.textContent = internalReceiver.selectedOptions[0].text;
                    verified = true;
	            }
	        } else {
                if (Number(formData.get('usd')) > Number(externalSenderBalance.textContent.replace(',', ''))) {
	                setFailNotification("Requested amount is over the current balance");
	                return;
	            } else {
                    transactionSender.textContent = externalSender.selectedOptions[0].text;
                    transactionReceiver.textContent = externalReceiver.value.substring(externalReceiver.value.length - 4);
                    verified = true;
	            }
	        }
	        
	        return verified;
        }
        
        function hidePopUp() {
            document.getElementById('pop-up').classList.remove('show-popup-content');
            document.getElementById('pup-up-element').classList.add('hidden');
        }
        
        function handleForm(event) {
	        let url = form.action;
	        request = new Request(url, {
	            body: formData,
	            method: 'POST',
	        });
	        
	        fetch(request)
	            .then((response) => response.json())
	            .then((data) => {          
	                if (data.response) {
	                    form.reset();

	                    if (form.id === 'internal-transfer') {
	                        setSuccessNotification('Transfered $' + formData.get('usd') + ' to ' + internalReceiver.selectedOptions[0].text + ' from ' + internalSender.selectedOptions[0].text);
	                        internalSender.dispatchEvent(new Event('change'));
	                        internalReceiverBalance.textContent = '0.00';
	                    }
	                    else {
	                        setSuccessNotification('Transfered $' + formData.get('usd') + ' to (*' + externalReceiver.value.substring(externalReceiver.value.length - 4) + ') from ' + externalSender.selectedOptions[0].text);
	                        externalSender.dispatchEvent(new Event('change'));
	                    }
	                } else {
	                    setFailNotification(data.message);
	                }
			
	                showNotification();
	            })
	            .catch(console.warn);
	            
            window.scrollTo({ top: 0, behavior: 'smooth' });
            hidePopUp();
	    }
	    
	    async function retreiveInternalSenderBalance(event) {
	        await retrieveBalance(event.target.value);
	        internalSenderBalance.textContent = balance;
	    }
	    
	    async function retreiveInternalReceiverBalance(event) {
	        await retrieveBalance(event.target.value);
	        internalReceiverBalance.textContent = balance;
	    }
	    
	    async function retreiveExternalSenderBalance(event) {
	        await retrieveBalance(event.target.value);
	        externalSenderBalance.textContent = balance;
	    }
	    
	    async function retrieveBalance(value) {
	        let url = '../../requests/account/getBalance';
	        let data = new FormData();
	        
	        data.append('account', value);
	        data.append('token', '<?php echo $getBalanceToken ?>');
	        
	        request = new Request(url, {
	            body: data,
	            method: 'POST',
	        });
	        
	        await fetch(request)
	            .then((response) => response.json())
	            .then((data) => {          
	                if (data.response) {
	                    balance = data.message;
	                } else {
                        balance = '0.00';

	                    setFailNotification(data.message);
                        showNotification();

                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
	            })
	            .catch(console.warn);
	    }
    </script>
</html>
