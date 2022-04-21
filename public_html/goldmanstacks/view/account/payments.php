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
$hasNoPayments = false;

/* Csrf form tokens */
$paymentDetailsToken = hash_hmac('sha256', '/getPaymentDetails.php', $key);
$deletePaymentToken = hash_hmac('sha256', '/deletePayment.php', $key);
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

/* Redirect user if non-existent account given */
if (!$isReferenced && !empty($referencedName)) {
    $db->close();
    
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
                <button id="payments-tab-button" class="tab-button transform-button round <?php if ($referencedName === null) echo "selected" ?>" data-id="current-payments" data-title="Current Payments">
                    <div class="split">
                        <div class="text-right">
                            <p>View Payments</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>
                <button id="new-payment-button" class="tab-button transform-button round <?php if ($referencedName !== null) echo "selected" ?>" data-id="new-payment" data-title="New Payment">
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
                    <div class="split">
                        <p class="info">Current payments</p>
                        <button onClick="changeSelected(document.getElementById('new-payment-button'))" class="expand-button transform-button extend-left round">
                            <div class="split">
                                <div class="animate-left">
                		            <div class="toggle-button">
                		                <p class="expanded-info">Add Payment</p>
                		            </div>
                	            </div>
                                <p class="condensed-info"><b>+</b></p>
                            </div>
                        </button>
                    </div><br>
                    <hr class="margin-bottom">
    	            <?php
    	            $queryPayments = $db->prepare("SELECT P.paymentID, P.recipientAccount, P.recipientNickName, P.amount, P.endDate, A.nickName, A.accountType FROM payments P INNER JOIN accountDirectory A ON P.accountNum=A.accountNum WHERE A.clientID=?");
    	            $queryPayments->bind_param("i", $userID);
    	            $queryPayments->execute();
    	            
    	            $queryResults = $queryPayments->get_result();
    	            $paymentsRows = $queryResults->fetch_all(MYSQLI_ASSOC);
    	            
    	            if ($queryResults->num_rows > 0) {
                        foreach ($paymentsRows as $payment) {
                            $paymentId = encrypt($payment['paymentID'], $key); 
                            
                            if ($payment['recipientNickName'] === null) {
                                $name = '(*' . substr($payment['recipientAccount'], -4) . ')';
                            } else {
                                $name = $payment['recipientNickName'];
                            }
                            
                            /* Determin payment type */
                            if ($payment['endDate'] === null) {
                                $paymentType = 'One-time Payment';
                            } else {
                                $paymentType = 'Recurring Payment';
                            }
                            
                            echo "<div id=\"$paymentId\">
                                    <button type=\"button\" class=\"view-payment-button highlight-button transform-button split round\">
                                        <div class=\"list-padded text-left\">
                                            <h3 class=\"bold\">Payment to $name</h3>
                                            <p>From " . $payment['nickName'] . " (" . ucfirst($payment['accountType']) . ")" . "<p>
                                        </div>
                                        <div class=\"split animate-left\">
                                            <div class=\"list-padded text-right\">
                                                <h3>$" . number_format($payment['amount'], 2) . "</h3>
                                                <p>$paymentType</p>
                                            </div>
                           		            <div class=\"toggle-button\">
                            		            <i class=\"fas fa-chevron-right\"></i>
                            		        </div>
                                        </div>
                                    </button>";
                              
    						if ($payment != end($paymentsRows)){
    							echo "<hr>";
    						}
    						
    						echo "</div>";
                        }
    	            } else {
    	                $hasNoPayments = true;
    	                echo '<p id="no-payments" class="info text-center">No Ongoing Payments</p>';
    	            }
                    
                    $queryResults->free();
                    $queryPayments->close();
                    $db->close();
                    ?>
                </div>
                <form id="new-payment" action="../../requests/account/payment/newPayment" class="flex-form <?php if ($referencedName === null) echo "hidden" ?>">
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
                    <input id="input-receiver" type="text" name="to" pattern="^[0-9]{10}$" maxlength="10" class="input-field" required>
    	            <label for="input-nickName" class="info">Receiver Name</label>
                    <input id="input-nickName" type="text" name="name" pattern="^[A-z&' ]+$" maxlength="30" placeholder="Optional" class="input-field">
    	            <hr>
                    <label for="input-date" class="info">Date</label>
                    <input id="input-date" type="date" name="date" min="<?php echo date("Y-m-d", strtotime("yesterday")) ?>" value="<?php echo date("Y-m-d", strtotime("yesterday")) ?>" class="input-field" required>
                    <label for="input-amount" class="info">Amount</label>
                    <input id="input-amount" type="number" step="0.01" name="amount" placeholder="USD" class="input-field" required>
                    <hr>
                    <div id="optional-recurring-payment" class="collapsable-item collapse">
                        <div class="flex-form">
                            <label for="input-step" class="info form-item">Step</label>
                            <input id="input-step" type="number" name="step" min="1" max="50" class="input-field">
                            <label for="input-period" class="info form-item">Period</label>
                            <select id="input-period" type="number" name="period" class="input-field">
                                <option value="day">Day</option>
                                <option value="week">Week</option>
                                <option value="month">Month</option>
                                <option value="year">Year</option>
                            </select>
                            <label for="input-end-date" class="info form-item">End Date</label>
                            <input id="input-end-date" type="date" name="end" min="<?php echo date("Y-m-d", strtotime("yesterday")) ?>" value="<?php echo date("Y-m-d", strtotime("yesterday")) ?>" class="input-field">
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
                    <button id="return-button" onClick="hidePopUp()" class="expand-button transform-button extend-right round">
    	                <div class="split">
    	                    <p class="condensed-info"><i class="fas fa-arrow-left"></i></p>
    	                    <div class="animate-right">
            		            <div class="toggle-button">
            		                <p class="expanded-info">Return</p>
            		            </div>
        		            </div>
    	                </div>
    	            </button>
                    <button id="remove-payment" class="expand-button transform-button extend-left round">
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
                <div id="view-payment-popup-content" class="pop-up-item flex-form hidden">
                    <h2 id="title">Payment</h2>
                    <div id='payment-details'>
                        <b class="info">Sender</b>
                        <p id="selected-sender"><?php echo $currentAccountName ?></p>
                        <b class="info">Receiver</b>
                        <p id="selected-receiver"><?php echo $balance ?></p>
                        <b class="info">Date</b>
                        <p id="selected-date"><?php echo $routingNumber ?></p>
                        <b class="info">Amount</b>
                        <p id="selected-payment-amount"></p>
                        <div id="selected-recurring-content" class="flex-form hidden">
                            <b class="info">Recurring Payment</b>
                            <p id="selected-recurring-info">False (This is a one-time payment)</p>
                        </div>
                    </div>
                    <button type="button" onClick="hidePopUp()" class="standard-button transform-button flex-center round">
                        <div class="split">
           		            <div class="toggle-button">
            		            <i class="fas fa-chevron-left"></i>
            		        </div>
                            <p class="animate-right">Return<p>
                        </div>
                    </button>
                </div>
	            <div id="confirm-remove-payment-popup-content" class="pop-up-item flex-form hidden">
                    <h2 id="title">Confirm Payment Removal</h2>
                    <div id="remove-payment-details" class="margin-bottom"></div>
                    <p class="info">The current payment will be removed</p>
                    <button id="confirm-remove-payment" type="button" class="standard-button transform-button flex-center round">
                        <div class="split">
                            <p class="animate-left">Confirm<p>
           		            <div class="toggle-button">
            		            <i class="fas fa-chevron-right"></i>
            		        </div>
                        </div>
                    </button>
                </div>
	            <div id="confirm-payment-popup-content" class="pop-up-item flex-form hidden">
                    <h2 id="title">Confirm Payment</h2>
                    <p class="info">Sender</p>
                    <p id="payment-sender"></p>
                    <p class="info">Receiver</p>
                    <p id="payment-receiver"></p>
                    <p class="info">Date</p>
                    <p id="payment-date"></p>
                    <p class="info">Amount</p>
                    <p id="payment-amount"></p>
                    <div id="payment-recurring-confirmation" class="flex-form">
                        <p class="info">Recurring Payment</p>
                        <p>Every <span id="payment-step"></span> <span id="payment-period"></span> (from given date) until <span id="payment-end"></span></p>
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
            </div>
        </div>
	</body>
	<script type="text/javascript" src="../../js/navigation.js"></script>
	<script type="text/javascript" src="../../js/tabs.js"></script>
	<script type="text/javascript" src="../../js/post.js"></script>
	<script type="text/javascript" src="../../js/notification.js"></script>
	<script type="text/javascript">
	    /* Payment Buttons */
	    const paymentButtons = document.querySelectorAll(".view-payment-button");
	
	    /* PopUp Buttons */
	    const popupReturnButton = document.getElementById('return-button');
	
	    /* PopUp Selected Payment Information */
	    const removePaymentButton = document.getElementById('remove-payment');
	    const selectedSender = document.getElementById('selected-sender');
	    const selectedReceiver = document.getElementById('selected-receiver');
	    const selectedDate = document.getElementById('selected-date');
	    const selectedAmount = document.getElementById('selected-payment-amount');
	    const selectedRecurringDiv = document.getElementById('selected-recurring-content');
	    const selectedRecurringInfo = document.getElementById('selected-recurring-info');
	    
	    /* PopUp Confirmation Contents */
	    const confirmSender = document.getElementById('payment-sender');
	    const confirmReceiver = document.getElementById('payment-receiver');
	    const confirmDate = document.getElementById('payment-date');
	    const confirmAmount = document.getElementById('payment-amount');
	    const confirmRecurringDiv = document.getElementById('payment-recurring-confirmation');
	    const confirmStep = document.getElementById('payment-step');
	    const confirmPeriod = document.getElementById('payment-period');
	    const confirmEndDate = document.getElementById('payment-end');
	    
	    /* New Payment Form Contents */
	    const inputSender = document.getElementById('input-sender');
	    const inputDate = document.getElementById('input-date');
	    
	    /* New Payment Form Contents: Option Recurring Contents */
	    const recurringPayment = document.getElementById('optional-recurring-payment');
	    const inputCheckBox = document.getElementById('input-checkbox-recurring');
	    const inputStep = document.getElementById('input-step');
	    const inputPeriod = document.getElementById('input-period');
	    const inputEndDate = document.getElementById('input-end-date');

	    /* User Balance */
	    const senderBalance = document.getElementById('payment-sender-balance');
	    
	    /* Memory for plural/singular recurring period selection text */
	    let oldValue = 1;
	    
	    /* Associated form information */
	    let form = null;
	    let formData = null;
	    
	    let selectedPayment = null;
	    
	    /* Currency formatter */
        let formatter = new Intl.NumberFormat('en-US', {
          style: 'currency',
          currency: 'USD',
        });
	    
	    document.addEventListener('DOMContentLoaded', () => {
    	    paymentButtons.forEach((button) => {
    	        button.addEventListener('click', () => {
    	            showPaymentDetails(button.parentElement.id);
    	        });
    	    })
	        
	        /* Listener to get current balance of selected account */
            inputSender.addEventListener('change', async (event) => {
                let failure = true; // Used to notify user if something went wrong
                
                /* Create data to POST */
    	        let data = new FormData();
    	        data.append('account', event.target.value);
    	        data.append('token', '<?php echo $getBalanceToken ?>');
    	        
                /* Retrieve associated json */
    	        let json = await getJson('../../requests/account/getBalance', data);
    	       
    	        /* Check if the given json is not empty*/
    	        if (!isEmptyJson(json)) {
    	            /* Check if computation done by server was successful */
	                if (json.response) {
    	                failure = false;
                        senderBalance.textContent = json.balance;
    	            } else {
    	                senderBalance.textContent = '0.00';
    	                setFailNotification(json.message);
    	            }
    	        } else {
    	            setFailNotification('Failed to retrieve details');
    	        }
    	        
    	        if (failure) {
        	        /* Notify user */
        	        showNotification();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
    	        }
            });
            
            /* Overrides the standard form submission process */
            document.getElementById('new-payment').addEventListener('submit', event => {
                event.preventDefault(); // Prevent form from refreshing the page
                
                /* Get associated form information */
                form = event.target;
                formData = new FormData(form);
                
                let verified = false; // Variable to check if user input is valid
                
                /* Input validation */
                if (formData.get('amount') > Number(senderBalance.textContent.replace(',', ''))) {
                    setFailNotification("Requested amount is over the current balance");
                    showNotification();
                } else {
                    /* Update popup contents to what the user has inputted */
                    confirmSender.textContent = inputSender.selectedOptions[0].text;
                    confirmReceiver.textContent = formData.get('to');
                    confirmDate.textContent = formData.get('date');
                    confirmAmount.textContent = formatter.format(formData.get('amount'));
                    
                    /* Check if a receiver name is given */
                    if (formData.get('name') !== '') {
                        confirmReceiver.textContent = formData.get('name') + ' (' + confirmReceiver.textContent + ')';
                    }
                    
                    /* Check if the recurring option is selected by the user */
                    if (inputCheckBox.checked) {
                        let step = formData.get('step');
                        
                        /* Plural/singular grammar check */
                        if (step > 1) {
                            confirmStep.textContent = step;
                        } else {
                            confirmStep.textContent = '';
                        }
                        
                        /* Update optional popup contents (recurring option) to what the user has inputted */
                        confirmPeriod.textContent = inputPeriod.selectedOptions[0].text.toLowerCase();
                        confirmEndDate.textContent = formData.get('end');
                        confirmRecurringDiv.classList.remove('hidden');
                    } else {
                        /* Hide recurring content in popup if user has not selected the recurring option */
                        confirmRecurringDiv.classList.add('hidden');
                    }
                    
                    hideNotification(); // Hide Notifications if visible
                    showPopUp('confirm-payment-popup-content'); // Show confirmation popup
                }
            });
            
            document.getElementById('remove-payment').addEventListener('click', () => {
	            popupReturnButton.onclick = function() { showPaymentDetails(selectedPayment); }
	            document.getElementById('remove-payment-details').innerHTML = document.getElementById('payment-details').innerHTML;
	            
                showPopUp('confirm-remove-payment-popup-content');
            });
            
            document.getElementById('confirm-remove-payment').addEventListener('click', async () => {
                let data = new FormData();
                data.append('token', '<?php echo $deletePaymentToken ?>')
                data.append('payment', selectedPayment);
                
                /* Retrieve associated json */
	            let json = await getJson('../../requests/account/payment/deletePayment', data);
            
    	        /* Check if the given json is not empty*/
    	        if (!isEmptyJson(json)) {
    	            /* Check if computation done by server was successful */
    	            if (json.response) {
    	                setSuccessNotification(json.message);
    	                
    	                document.getElementById(selectedPayment).remove();
    	            } else {
    	                setFailNotification(json.message);
    	            }
    	        } else {
    	            setFailNotification('Failed to retrieve details');
    	        }
    	        
    	        /* Notify user */
                hidePopUp();
    	        showNotification();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            
            /* Listener to submit "new payment" form to server to create a new payment */
            document.getElementById('confirm-payment').addEventListener('click', async () => {
                /* Retrieve associated json */
	            let json = await getJson(form.action, formData);
    	        
    	        /* Check if the given json is not empty*/
    	        if (!isEmptyJson(json)) {
    	            /* Check if computation done by server was successful */
    	            if (json.response) {
    	                form.reset(); // Reset contents of the form
    	                inputSender.dispatchEvent(new Event('change')); // Trigger event to retrieve selected account balance on form reset
    	                inputCheckBox.dispatchEvent(new Event('change')); // Trigger event to collapse recurring payment input contents on form reset
    	                setSuccessNotification(json.message);
    	                
    	                changeSelected(document.getElementById('payments-tab-button')); // Change tab to view payments
    	                createNewPaymentElement(json.id); // Create new payment button
    	            } else {
    	                setFailNotification(json.message);
    	            }
    	        } else {
    	            setFailNotification('Failed to retrieve details');
    	        }
    	        
    	        /* Notify user */
                hidePopUp();
    	        showNotification();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
	    });
	    
	    /* Listener to hide or show optional form contents for recurring payments in the "new payment" form */
	    inputCheckBox.addEventListener('change', function() {
	        if (this.checked) {
	            recurringPayment.classList.remove('collapse');
	            /* Make inputs required */
                inputStep.required = true;
                inputPeriod.required = true;
                inputEndDate.required = true;
                
                /* Make inputs not disabled */
                inputStep.disabled = false;
                inputPeriod.disabled = false;
                inputEndDate.disabled = false;
	        } else {
	            recurringPayment.classList.add('collapse');
	            /* Make inputs not required */
                inputStep.required = false;
                inputPeriod.required = false;
                inputEndDate.required = false;

                /* Make inputs disabled */
                inputStep.disabled = true;
                inputPeriod.disabled = true;
                inputEndDate.disabled = true;
	        }
	    });
	    
	    /* Listener whether to make the "period" selector option elements singular or plural depending on the number given by the user from "step" input */
	    inputStep.addEventListener('input', function() {
	        if (this.value > 1 && oldValue <= 1) {
	            oldValue = this.value;
	            
	            Array.prototype.forEach.call(inputPeriod.options, option => {
	                option.text += 's';
	            });
	        } else if (this.value <= 1 && oldValue > 1) {
	            oldValue = this.value;
	            
	            Array.prototype.forEach.call(inputPeriod.options, option => {
	                option.text = option.text.substr(option.text, option.text.length - 1);
	            });
	        }
	    });
	    
	    /* Listener to make the minimum end date for recurring payments the start date of the payment */
	    inputDate.addEventListener('input', function() {
	        inputEndDate.min = inputDate.value;
	        
	        if (new Date(inputEndDate.value) < new Date(inputDate.value) || inputEndDate.value == '') {
	            inputEndDate.value = inputDate.value;
	        }
	    });
	    
	    /* Shows user a popup with details of the payment they have selected */
	    async function showPaymentDetails(id) {
	        let failure = true;
	        
	        /* Create form data */
	        let data = new FormData();
	        data.append('id', id);
	        data.append('token', '<?php echo $paymentDetailsToken ?>');
	        
	        /* Retrieve details */
	        let json = await getJson('../../requests/account/payment/getPaymentDetails', data);
	        
	        /* Check json contents */
	        if (!isEmptyJson(json)) {
	            if (json.response) {
	                failure = false;
	                selectedPayment = id;
	                
	                /* Update current payment contents */
	                selectedSender.textContent = json.from;
	                selectedReceiver.textContent = json.to;
	                selectedDate.textContent = json.date;
	                selectedAmount.textContent = json.amount;
	                
	                if (json.isRecurring) {
	                    selectedRecurringInfo.textContent = json.recurInfo;
	                    selectedRecurringDiv.classList.remove('hidden');
	                } else {
	                    selectedRecurringDiv.classList.add('hidden');
	                }
	            } else {
	                setFailNotification(json.message);
	            }
	        } else {
	            setFailNotification('Failed to retrieve details');
	        }
	        
	        /* Show user a notification on date retrieval failure */
	        if (failure) {
	            showNotification();
	        } else {
    	        /* Show popup */
    	        showPopUp('view-payment-popup-content');
	        }
	    }
        
        /* Shows popup */
        function showPopUp(contentId) {
            /* Determine whether the popup contains a remove button */
            if (contentId === 'view-payment-popup-content') { 
                removePaymentButton.classList.remove('hidden');
            } else {
                removePaymentButton.classList.add('hidden');
            }
            
            if (contentId !== 'confirm-remove-payment-popup-content') {
                popupReturnButton.onclick = function() { hidePopUp(); }
            }
            
            /* Hide all pop-up contents except the one requested (ContentId) */
            document.querySelectorAll(".pop-up-item").forEach((element) => {
                if (element.id === contentId) {
                    element.classList.remove('hidden');
                }
                else {
                    element.classList.add('hidden');
                }
            });
            
            /* Show popup background and main popup window */
            document.getElementById('pop-up').classList.add('show-popup-content');
            document.getElementById('pup-up-element').classList.remove('hidden');
        }
        
        /* Removes popup background and hides main popup window */
        function hidePopUp() {
            document.getElementById('pop-up').classList.remove('show-popup-content');
            document.getElementById('pup-up-element').classList.add('hidden');
        }
        
        function createNewPaymentElement(id) {
            /* Check if no payments message is shown */
            if (document.getElementById('no-payments') !== null) {
                document.getElementById('no-payments').remove();
            } else {
                document.getElementById('current-payments').innerHTML += '<hr>';
            }
            
            /* Create new button for payment */
            document.getElementById('current-payments').innerHTML += `
                <button type="button" onClick="showPaymentDetails('${id}')" class="highlight-button transform-button split round">
                    <div class="list-padded text-left">
                        <h3 class="bold">Payment to ${formData.get('name') === '' ? '(*' + formData.get('to').substr(formData.get('to').length - 4) + ')' : formData.get('name')}</h3>
                        <p>From ${confirmSender.textContent}<p>
                    </div>
                    <div class="split animate-left">
                        <div class="list-padded text-right">
                            <h3>${confirmAmount.textContent}</h3>
                            <p>${formData.get('step') === '' ? 'One-time Payment' : 'Recurring Payment'}</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
                </button>`;
        }
	</script>
</html>
