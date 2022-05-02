<?php
require_once('../../../../private/sysNotification.php');
require_once('../../../../private/config.php');
require_once('../../../../private/functions.php');
require_once('../../../../private/userbase.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkClientStatus(); // Check if the client is signed in

/* SESSION Variables */
$key = $_SESSION['key'];
$userID = $_SESSION['uid'];

/* GET Variables */
$view = $_GET['v'];
$referencedName = $_GET['acc'];

/* Variables */
$accounts = array();
$isReferenced = false;

/* Csrf form tokens */
$depositToken = hash_hmac('sha256', '/newDeposit.php', $key);
$withdrawToken = hash_hmac('sha256', '/newWithdraw.php', $key);
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
	    <?php notification() ?>
    	<button id="notification" type="button" onClick="hideNotification()" class="notification sub success transform-button round collapse">
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
                <button class="tab-button transform-button round <?php if ($view === "deposit" || empty($view)) echo "selected" ?>" data-id="deposit-form" data-title="Deposit">
                    <div class="split">
                        <div class="text-right">
                            <p>Deposit</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
    	        </button>
                <button class="tab-button transform-button round <?php if ($view === "withdraw") echo "selected" ?>"  data-id="withdraw-form" data-title="Withdraw">
                    <div class="split">
                        <div class="text-right">
                            <p>Withdraw</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
    	        </button>
    	    </div>
    	    <div class="list sub">
                <div class="">
                    <h2 id="title"><?php
                        if (empty($view) || $view === "deposit") echo "Deposit";
                        else echo "Withdraw"
                    ?></h2>
                </div>
                <form id="deposit-form" action="../../requests/account/newDeposit" class="flex-form <?php if (!empty($view) && $view !== "deposit") echo "hidden" ?>">
                    <p class="info">Deposit funds into account</p><br>
		            <label for="deposit-account" class="info">Account</label>
		            <select id="deposit-account" name="account" class="input-field" required>
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
		            <p class="info">Balance: $<span id="deposit-balance"><?php echo number_format($balance, 2) ?></span></p>
		            <hr>
                    <label for="deposit-amount" class="info">Amount</label>
                    <input id="deposit-amount" type="number" step="0.01" name="amount" class="input-field" required>
                    <input type="hidden" name="token" value="<?php echo $depositToken ?>">
                    <button type="submit" class="standard-button transform-button flex-center round">
                        <div class="split">
                            <p class="animate-left">Deposit<p>
           		            <div class="toggle-button">
            		            <i class="fas fa-chevron-right"></i>
            		        </div>
                        </div>
                    </button>
                </form>
                <form id="withdraw-form" action="../../requests/account/newWithdraw" class="flex-form <?php if ($view !== "withdraw") echo "hidden" ?>">
                    <p class="info">Withdraw funds from account</p><br>
		            <label for="withdraw-account" class="info">Account</label>
		            <select id="withdraw-account" name="account" class="input-field" required>
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
		            <p class="info">Balance: $<span id="withdraw-balance"><?php echo number_format($balance, 2) ?></span></p>
                    <hr>
                    <label for="withdraw-amount" class="info">Amount</label>
                    <input id="withdraw-amount" type="number" step="0.01" name="amount" class="input-field" required>
                    <input type="hidden" name="token" value="<?php echo $withdrawToken ?>">
                    <button type="submit" class="standard-button transform-button flex-center round">
                        <div class="split">
                            <p class="animate-left">Withdraw<p>
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
                    <h2 id="title"><span id="transaction-type"></span> Confirmation</h2>
                    <p class="info">Bank Account</p>
                    <p id="transaction-account"></p>
                    <p class="info">Amount</p>
                    <p>$<span id="transaction-amount"></span></p>
                    <button id="confirm-transaction" type="submit" class="standard-button transform-button flex-center round">
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
    <script type="text/javascript" src="../../js/notification.js"></script>
    <script type="text/javascript" src="../../js/post.js"></script>
    <script type="text/javascript" src="../../js/tabs.js"></script>
	<script type="text/javascript">
	    /* PopUp Contents */
	    const transactionType = document.getElementById('transaction-type');
	    const transactionAccount = document.getElementById('transaction-account');
	    const transactionAmount = document.getElementById('transaction-amount');
	    
	    /* Form Contents */
	    const depositAccount = document.getElementById('deposit-account');
	    const depositBalance = document.getElementById('deposit-balance');
	    const withdrawAccount = document.getElementById('withdraw-account');
	    const withdrawBalance = document.getElementById('withdraw-balance');
	    
	    let form = null;
	    let formData = null;
	    let balance = null;
	
	    document.getElementById('deposit-form').addEventListener('submit', showPopUp);
	    document.getElementById('withdraw-form').addEventListener('submit', showPopUp);
	    document.getElementById('confirm-transaction').addEventListener('click', handleForm);
	    depositAccount.addEventListener('change', async () => {
	        await retrieveBalance(event.target.value);
	        depositBalance.textContent = balance;
	    });
	    withdrawAccount.addEventListener('change', async () => {
	        await retrieveBalance(event.target.value);
	        withdrawBalance.textContent = balance;
	    });
	    
        function showPopUp(event) {
            event.preventDefault();
            
	        form = event.target;
	        formData = new FormData(form);
	        
	        if (verifyUserInput()) {
	            hideNotification(); // Hide notification if visible
	            
                transactionAmount.textContent = formData.get('amount');
                
                document.getElementById("pop-up").classList.add('show-popup-content');
                document.getElementById("pup-up-element").classList.remove('hidden');
	        } else {
                showNotification();
	        }
        }
        
        function verifyUserInput() {
            let verified = false;
            
	        if (form.id === 'deposit-form') {
                transactionType.textContent = 'Deposit';
                transactionAccount.textContent = depositAccount.selectedOptions[0].text;
                verified = true;
	        } else {
                if (Number(formData.get('amount')) > Number(withdrawBalance.textContent.replace(',', ''))) {
	                setFailNotification("Requested amount is over the current balance");
	            } else {
	                transactionType.textContent = 'Withdraw';
                    transactionAccount.textContent = withdrawAccount.selectedOptions[0].text;
                    verified = true;
	            }
	        }
	        
	        return verified;
        }
        
        function hidePopUp() {
            document.getElementById("pop-up").classList.remove("show-popup-content");
            document.getElementById("pup-up-element").classList.add("hidden");
        }
	    
	    async function handleForm(event) {
	        let json = await getJson(form.action, formData);
	        
	        if (!isEmptyJson()) {
	            if (json.response) {
                    if (form.id === 'deposit-form') {
                        setSuccessNotification('Deposited $' + formData.get('amount') + ' to ' + transactionAccount.textContent);
                        depositAccount.dispatchEvent(new Event('change'));
                    } else {
                        setSuccessNotification('Withdrawed $' + formData.get('amount') + ' from ' + transactionAccount.textContent);
                        withdrawAccount.dispatchEvent(new Event('change'));
                    }
                    
                    form.reset();	            
	            } else {
	                setFailNotification(json.message);
	            }
	        } else {
	            setFailNotification("Failed");
	        }
	        
	        showNotification();
	        hidePopUp();
	    }
        
	    async function retrieveBalance(value) {
            let failure = true; // Used to notify user if something went wrong
            
            /* Create data to POST */
	        let data = new FormData();
	        data.append('account', value);
	        data.append('token', '<?php echo $getBalanceToken ?>');
	        
            /* Retrieve associated json */
	        let json = await getJson('../../requests/account/getBalance', data);
	       
	        /* Check if the given json is not empty*/
	        if (!isEmptyJson(json)) {
	            /* Check if computation done by server was successful */
                if (json.response) {
	                failure = false;
                    balance = json.balance;
	            } else {
	                balance = '0.00';
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
	    }
	</script>
</html>
