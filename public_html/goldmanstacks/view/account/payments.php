<?php
require_once('../../../../private/sysNotification.php');
require_once('../../../../private/functions.php');
require_once('../../../../private/config.php');
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
$amountOfPayments = 5;

/* Csrf form tokens */
$newPaymentToken = hash_hmac('sha256', '/newPayment.php', $key);
$getBalanceToken = hash_hmac('sha256', '/getBalance.php', $key);

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
    
    if ($referencedName === $account['nickName']) $isReferenced = true;
}

$resultAccounts->free();
$queryAccounts->close();
$db->close();

/* Redirect user if non-existent account given */
if (!$isReferenced && !empty($referencedName)) {
    header('Location: payments.php');
    die();
}
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
	<title>Payments</title>
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
    	<button id="notification" type="button" onClick="hideNotification()" class="notification sub success transform-button round collapse">
            <p><i id="notification-icon" class="fas fa-check icon"></i><span id="notification-text"></span></p>
            <div class="split">
                   <div class="toggle-button">
    	            <i class="fas fa-times"></i>
    	        </div>
            </div>
        </button>
    	<?php
        if ($isReferenced) {
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
        <div class="container flex-center <?php if ($isReferenced) echo "marginless" ?>">
            <div class="list mini">
                <button class="tab-button transform-button round <?php if ($referencedName === null) echo "selected" ?>" data-id="current-payments" data-title="Current Payments">
                    <div class="split">
                        <div class="text-right">
                            <p>View Payments</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>
                <button class="tab-button transform-button round <?php if ($referencedName !== null) echo "selected" ?>" data-id="new-payment" data-title="New Payment">
                    <div class="split">
                        <div class="text-right">
                            <p>New Payment</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>   
		    </div>
            <div class="list sub">
                <div class="">
                    <h2 id="title">Payments</h2>
                </div>
                <div id="current-payments" class="<?php if ($referencedName !== null) echo "hidden" ?>">
                    <p class="info">Current payments</p><br>
                    <div class="">
                        <hr>
        	            <?php
        	            
        	            $recentAccount = "Checking"; // temp
        	            
                        for ($n = 1; $n <= $amountOfPayments; $n++) {
                            echo "<button onClick=\"showPopUp('view-payment-popup-content')\" class=\"highlight-button transform-button split round\">
                                    <div class=\"list-padded\">
                                        <h3 class=\"bold\">Payment $n</h3>
                                        <p>$lastVisit<p>
                                    </div>
                                    <div class=\"split animate-left\">
                                        <div class=\"list-padded text-right\">
                                            <h3>$.00</h3>
                                            <p>Payment</p>
                                        </div>
                       		            <div class=\"toggle-button\">
                        		            <i class=\"fas fa-chevron-right\"></i>
                        		        </div>
                                    </div>
                                  </button>
                                  <hr>";
                        }
        
                        ?>
                    </div>
                </div>
                <form id="new-payment" action="../../requests/account/newPayment" class="flex-form <?php if ($referencedName === null) echo "hidden" ?>">
                    <p class="info">Start a new payment</p><br>
                    <label for="input-sender" class="info">From</label>
		            <select id="input-sender" name="from" class="input-field" required>
                        <?php
                        if (!$isReferenced) {
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
                    <p class="info">Balance: $<span id="payment-sender-balance"><?php echo number_format($balance, 2) ?></span></p>
                    <hr>
    	            <label for="input-receiver" class="info">Receiver Bank Account Number</label>
                    <input id="input-receiver" type="text" name="to" pattern="[0-9]{10}" class="input-field"required>
    	            <hr>
                    <label for="input-date" class="info">Date</label>
                    <input id="input-date" type="date" name="date" min="<?php echo date("Y-m-d") ?>" class="input-field" required>
                    <label for="input-amount" class="info">Amount</label>
                    <input id="input-amount" type="number" name="usd" class="input-field" placeholder="USD" required>
                    <hr>
                    <div id="optional-recurring-payment" class="collapsable-item collapse">
                        <div class="flex-form">
                            <label for="input-step" class="info form-item">Step</label>
                            <input id="input-step" type="number" name="step" min="1" max="50" class="input-field">
                            <label for="input-period" class="info form-item">Period</label>
                            <select id="input-period" type="number" name="period" class="input-field">
                                <option>Day</option>
                                <option>Week</option>
                                <option>Month</option>
                                <option>Year</option>
                            </select>
                        </div>
                    </div>
                    <div class="switch-field">
                        <label class="switch-item">
                            <input type="checkbox" id="input-checkbox-recurring">
                            <span class="slider"></span>
                        </label>
                        <label for="input-checkbox-recurring" class="info">Recurring Payment</label>
                    </div>
                    <input type="hidden" name="token" value="<?php echo $newPaymentToken; ?>">
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
            <div class="list mini">          
    	    </div>
        </div>
        <div id="pop-up" class="pop-up">
            <div onClick="hidePopUp()" class="flex-center-item">
            </div>
            <div id="pup-up-element" class="pop-up-content fixed-sub round hidden">
                <div class="split">
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
                    <button id="remove-payment" onClick="hidePopUp()" class="expand-button transform-button extend-left round">
    	                <div class="split">
    	                    <div class="animate-left">
            		            <div class="toggle-button">
            		                <p class="expanded-info">Remove Payment</p>
            		            </div>
        		            </div>
    	                    <p class="condensed-info"><i class="fas fa-trash-alt"></i></p>
    	                </div>
    	            </button>
	            </div>
	            <br>
	            <div id="confirm-payment-popup-content" class="pop-up-item flex-form hidden">
                    <h2 id="title">Confirm Payment</h2>
                    <p class="info">Sender</p>
                    <p id="payment-sender"></p>
                    <p class="info">Receiver</p>
                    <p id="payment-receiver"></p>
                    <p class="info">Date</p>
                    <p id="payment-date"></p>
                    <p class="info">Amount</p>
                    <p>$<span id="payment-amount"></span></p>
                    <div id="optional-recurring-confirmation" class="flex-form">
                        <p class="info">Recurring Payment</p>
                        <p>Every <span id="payment-step"></span> <span id="payment-period"></span> (from given date)</p>
                    </div>
                    <button id="confirm-payment" type="button" class="standard-button transform-button flex-center round">
                        <div class="split">
                            <p class="animate-left">Confirm<p>
           		            <div class="toggle-button">
            		            <i class="fas fa-chevron-right"></i>
            		        </div>
                        </div>
                    </button>
                </div>
                <div id="view-payment-popup-content" class="pop-up-item flex-form hidden">
                    <h2 id="title">Payment</h2>
                    <b class="info">Account</b>
                    <p id="account-name"><?php echo $currentAccountName ?></p>
                    <b class="info">Reciever</b>
                    <p id="account-balance"><?php echo $balance ?></p>
                    <b class="info">Date</b>
                    <p id="account-routing-number"><?php echo $routingNumber ?></p>
                    <b class="info">Amount</b>
                    <p id="placeholder1">$999999</p>
                    <b class="info">Reccuring</b>
                    <p id="placeholder2">False (This is a one-time payment)</p>
                    <button type="button" onClick="hidePopUp()" class="standard-button transform-button flex-center round">
                        <div class="split">
                            <p class="animate-left">Edit<p>
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
	    /* PopUp Confirmation Contents */
	    const popUpPaymentSender = document.getElementById('payment-sender');
	    const popUpPaymentReceiver = document.getElementById('payment-receiver');
	    const popUpPaymentDate = document.getElementById('payment-date');
	    const popUpPaymentAmount = document.getElementById('payment-amount');
	    
	    /* PopUp Optional Confirmation Contents */
	    const paymentOptionalConfirmationElement = document.getElementById('optional-recurring-confirmation');
	    const paymentStep = document.getElementById('payment-step');
	    const paymentPeriod = document.getElementById('payment-period');
	    
	    const checkBoxElement = document.getElementById('input-checkbox-recurring');
	    const dateInputElement = document.getElementById('input-date');
	    
	    const removePaymentButton = document.getElementById('remove-payment');
	    
	    /* User Form Input */
	    const paymentSender = document.getElementById('input-sender');
	    const recurringPayment = document.getElementById('optional-recurring-payment');
	    const recurringStep = document.getElementById('input-step');
	    const recurringPeriod = document.getElementById('input-period');
	    
	    /* User Balance */
	    const senderBalance = document.getElementById('payment-sender-balance');
	    
	    /* Memory for plural/singular recurring period selection text */
	    let oldValue = 1;
	    
	    let form = null;
	    let formData = null;
	
	    document.addEventListener('DOMContentLoaded', () => {
            paymentSender.addEventListener('change', async event => {
    	        let url = '../../requests/account/getBalance';
    	        let data = new FormData();
    	        
    	        data.append('account', event.target.value);
    	        data.append('token', '<?php echo $getBalanceToken ?>');
    	        
    	        request = new Request(url, {
    	            body: data,
    	            method: 'POST',
    	        });
    	        
    	        await fetch(request)
    	            .then((response) => response.json())
    	            .then((data) => {          
    	                if (data.response) {
    	                    senderBalance.textContent = data.message;
    	                } else {
                            senderBalance.textContent = '0.00';
    
    	                    setFailNotification(data.message);
                            showNotification();
    
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
    	            })
    	            .catch(console.warn);
            });
            
            document.getElementById('new-payment').addEventListener('submit', event => {
                event.preventDefault();
                
                form = event.target;
                formData = new FormData(form);
                
                let verified = false;
                
                if (formData.get('usd') > Number(senderBalance.textContent.replace(',', ''))) {
                    setFailNotification("Requested amount is over the current balance");
                    showNotification();
                } else {
                    popUpPaymentSender.textContent = paymentSender.selectedOptions[0].text;
                    popUpPaymentReceiver.textContent = formData.get('to');
                    popUpPaymentDate.textContent = formData.get('date');
                    popUpPaymentAmount.textContent = formData.get('usd');
                    if (checkBoxElement.checked) {
                        let step = formData.get('step');
                        
                        if (step > 1) {
                            paymentStep.textContent = step;
                        } else {
                            paymentStep.textContent = '';
                        }
                        
                        paymentPeriod.textContent = formData.get('period').toLowerCase();
                        paymentOptionalConfirmationElement.classList.remove('hidden');
                    } else {
                        paymentOptionalConfirmationElement.classList.add('hidden');
                    }
                    
                    hideNotification(); // Hide Notification if visible
                    showPopUp('confirm-payment-popup-content'); // Show popup
                }
            });
            
            document.getElementById('confirm-payment').addEventListener('click', () => {
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
    	                    
	                        setSuccessNotification(data.message);
	                        paymentSender.dispatchEvent(new Event('change'));
    	                } else {
    	                    setFailNotification(data.message);
    	                }
    			
    	                showNotification();
    	            })
    	            .catch(console.warn);
    	            
                window.scrollTo({ top: 0, behavior: 'smooth' });
                hidePopUp();
            });
	    });
	    
	    checkBoxElement.addEventListener('change', function() {
	        if (this.checked) {
	            recurringPayment.classList.remove('collapse');
                recurringStep.required = true;
                recurringPeriod.required = true;
	        } else {
	            recurringPayment.classList.add('collapse');
                recurringStep.required = false;
                recurringPeriod.required = false;
	        }
	    });
	    
	    recurringStep.addEventListener('input', function() {
	        if (this.value > 1 && oldValue <= 1) {
	            oldValue = this.value;
	            
	            Array.prototype.forEach.call(recurringPeriod.options, option => {
	                option.text += 's';
	            });
	        } else if (this.value <= 1 && oldValue > 1) {
	            oldValue = this.value;
	            
	            Array.prototype.forEach.call(recurringPeriod.options, option => {
	                option.text = option.text.substr(option.text, option.text.length - 1);
	            });
	        }
	    });

        function showPopUp(ContentId) {
            if (ContentId === 'view-payment-popup-content') {
                removePaymentButton.classList.remove('hidden');
            } else {
                removePaymentButton.classList.add('hidden');
            }
            
            document.querySelectorAll(".pop-up-item").forEach((element) => {
                if (element.id === ContentId) {
                    element.classList.remove('hidden');
                }
                else {
                    element.classList.add('hidden');
                }
            });
            
            document.getElementById('pop-up').classList.add('show-popup-content');
            document.getElementById('pup-up-element').classList.remove('hidden');
        }
        
        function hidePopUp() {
            document.getElementById('pop-up').classList.remove('show-popup-content');
            document.getElementById('pup-up-element').classList.add('hidden');
        }
	</script>
</html>
