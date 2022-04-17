<?php
require_once('../../../../private/sysNotification.php');
require_once('../../../../private/config.php');
require_once('../../../../private/userbase.php');
require_once('../../../../private/functions.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkClientStatus(); // Check if the client is signed in

/* SESSION Variables */
$userId = $_SESSION['uid'];

/* GET Variables */
$currentAccountName = $_GET['acc'];

/* Variables */
$accountType = '';
$accountBalance = 0.00;
$routingNumber = "123456789";
$lastVisit = date("F j, Y, g:i a"); // Last time of login
$accounts = array(); // User Account names taken from DB

/* csrf token */
$updateNickNameToken = hash_hmac('sha256', '/updateAccountNickname.php', $_SESSION['key']); 

/* Database Connection */
$db = getUpdateConnection();

/* Check Connection */
if ($db === null) {
    header("Location: ");
    die();
}

/* Query used to gather account names and then store them in an array */
$query = $db->prepare('SELECT balance, accountType, accountNum, nickName FROM accountDirectory WHERE clientID=?');
$query->bind_param("i", $userId);
$query->execute();

$result = $query->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);

foreach ($rows as $account) {
    $accounts[] = array('nickName' => $account['nickName'], 'type' => $account['accountType']); // User Account names taken from DB
	
    if ($account['nickName'] === $currentAccountName) {
    	$accountBalance = $account['balance'];
	    $accountType = ucfirst($account['accountType']);
	    $accountNumber = $account['accountNum'];
    }
}

/* Check if the selected account is valid */
if (!in_array($currentAccountName, array_column($accounts, 'nickName'))) {
    header("Location: ../home.php");
    die();
}
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
        <title><?php echo strtoupper($currentAccountName)?> Account Details</title>
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
    	<div class="container flex-center">
    	    <div class="list main">
            <button id="notification" onClick="hideNotification()" class="notification max success transform-button round collapse">
                <p><i id="notification-icon" class="fas fa-check icon"></i><span id="notification-text"></span></p>
                <div class="split">
       	            <div class="toggle-button">
    		            <i class="fas fa-times"></i>
    		        </div>
                </div>
            </button>
    	        <div class="container">
        	        <h2 id="title"><span id="title-account-name"><?php echo $currentAccountName ?></span> History <span id="title-account-type">(<?php echo $accountType ?>)</span></h2>
        	        <div class="split">
            	        <p class="info">Transactions</p>
    		            <button onClick="showPopUp('dateFilter-popup-content')" class="expand-button transform-button extend-left round shadow">
    		                <div class="split">
    		                    <div class="animate-left">
                		            <div class="toggle-button">
                		                <p class="expanded-info">Filter By Date</p>
                		            </div>
            		            </div>
    		                    <p class="condensed-info"><i class="far fa-calendar-alt"></i></p>
    		                </div>
    		            </button>
		            </div>
        	    </div>
                <table id="transactions" class="responsive-table">
                    <thead>
	                    <tr>
	                        <th class="date">Date</th>
	                        <th class="desc">Description</th>
	                        <th class="amount text-right">Amount</th>
	                        <th class="hidden">Balance After</th>
	                        <th class="hidden">Type</th>
	                    </tr>
                    </thead>
                    <tbody tabindex="0" id="transactions-body">
		            <?php
                    /* Query to get all transactions from the selected account */
                    $transactionStatement = $db->prepare("SELECT accountNum, recipientAccount, transactionTime, transactionAmount, type 
                                                            FROM transactions 
                                                            WHERE accountNum=? 
                                                            OR recipientAccount=?
                                                            ORDER BY transactionTime DESC");
                    $transactionStatement->bind_param("ss", $accountNumber, $accountNumber);
                    $transactionStatement->execute();
                    
                    /* Obtain result */
                    $result = $transactionStatement->get_result();
                    $rows = $result->fetch_all(MYSQLI_ASSOC);

                    foreach ($rows as $transaction) {
                        switch ($transaction['type']) {
                            case 'transfer':
                                if ($transaction['accountNum'] != $accountNumber) {
                                    $description = "Transfer from (*" . substr($transaction['accountNum'], -4) . ")";
                                    $transaction['transactionAmount'] *= -1;
                                } else {
                                    $description = "Transfer to (*" . substr($transaction['recipientAccount'], -4) . ")";
                                }
                                break;
                            case 'deposit':
                                $description = "Deposit into account";
                                break;
                            case 'withdraw':
                                $description = "Withdraw from account";
                                break;
                            case 'payment':
                                $description = "Payment to";
                        }
                        
                        
                        echo "<tr tabindex=\"-1\" onClick=\"showPopUp('transaction-popup-content', this)\" class=\"transaction-element\">
                            <td data-label=\"Balance After\" class=\"hidden\">\$1000.00</td>
                            <td data-label=\"Type\" class=\"hidden\">".ucfirst($transaction['type'])."</td>
                            <td data-label=\"Date\" class=\"date\">".$transaction['transactionTime']."</td>
                            <td data-label=\"Description\" class=\"desc\">$description</td>
                            <td data-label=\"Amount\" class=\"amount text-right\">".convertToCurrency($transaction['transactionAmount'])."</td>
                        </tr>";
                    }
                    
                    $result->free();
                    $transactionStatement->close();
		            ?>
		            </tbody>
	            </table>
    	    </div>
    	    <div class="list sub">
    	        <div class="container round shadow">
    	            <div class="item-banner top-round">
    	                <h2 class="big text-center">Balance: $<?php echo number_format($accountBalance, 2) ?></h2>
    	            </div>
    	            <div class="item-content bottom-round">
    	                <form id="select-account" class="flex-form">
    	                    <label for="choose-account" class="info">Selected Account</label>
	                        <select id="choose-account" onChange="changeAccount(this)" class="input-field last-field">
	                            <?php
	                            foreach ($accounts as $account) {
	                               echo "<option value=\"" . $account['nickName'] . "\"";
	                               
	                               if ($currentAccountName === $account['nickName']) {
	                                    echo " selected";
	                               }
	                               
	                               echo ">" . $account['nickName'] . " (" . ucfirst($account['type']) . ")" . "</option>";
	                            }
	                            ?>
	                        </select>
    	                </form>
    	                <hr>
                        <button onClick="showPopUp('account-popup-content')" class="highlight-button transform-button split round">
                            <div class="list">
                                <p><i class="fas fa-info-circle icon"></i> View Account Details</p>
                            </div>
                            <div class="animate-left">
                	            <div class="toggle-button">
                	                <i class="fas fa-chevron-right"></i>
                	            </div>
                            </div>
                        </button>
    	            </div>
    	        </div>
                <div class="container round shadow">
    	            <div class="item-banner top-round">
    	                <label class="banner-text">Account Actions</label>
    	            </div>
    	            <div class="item-content bottom-round">
                        <a href="funds?acc=<?php echo $currentAccountName ?>" class="highlight-button transform-button split round">
                            <div class="list">
                                <p><i class="fas fa-plus icon"></i> Deposit Funds</p>
                            </div>
                            <div class="animate-left">
                	            <div class="toggle-button">
                	                <i class="fas fa-chevron-right"></i>
                	            </div>
                            </div>
                        </a>
                        <hr>
                        <a href="funds?v=withdraw&acc=<?php echo $currentAccountName ?>" class="highlight-button transform-button split round">
                            <div class="list">
                                <p><i class="fas fa-minus icon"></i> Withdraw Funds</p>
                            </div>
                            <div class="animate-left">
                	            <div class="toggle-button">
                	                <i class="fas fa-chevron-right"></i>
                	            </div>
                            </div>
                        </a>
                        <hr>
                        <a id="transfer" href="transfer?acc=<?php echo $currentAccountName ?>" class="highlight-button transform-button split round">
                            <div class="list">
                                <p><i class="fas fa-exchange-alt icon"></i> Transfer Funds</p>
                            </div>
                            <div class="animate-left">
                	            <div class="toggle-button">
                	                <i class="fas fa-chevron-right"></i>
                	            </div>
                            </div>
                        </a>
                        <hr>
                        <a href="payments?acc=<?php echo $currentAccountName ?>" class="highlight-button transform-button split round">
                            <div class="list">
                                <p><i class="fas fa-money-bill icon"></i> Initiate Payment</p>
                            </div>
                            <div class="animate-left">
                	            <div class="toggle-button">
                	                <i class="fas fa-chevron-right"></i>
                	            </div>
                            </div>
                        </a>
    	            </div>
    	        </div>
    	        <div class="container round shadow">
    	            <div class="item-banner top-round">
    	                <label class="banner-text">Account Options</label>
    	            </div>
    	            <div class="item-content bottom-round">
                        <button onClick="showPopUp('edit-popup-content')" class="highlight-button transform-button split round">
                            <div class="list">
                                <p><i class="fas fa-edit icon"></i> Edit Account Information</p>
                            </div>
                            <div class="animate-left">
                	            <div class="toggle-button">
                	                <i class="fas fa-chevron-right"></i>
                	            </div>
                            </div>
                        </button>
                        <hr>
                        <a href="#Remove" class="highlight-button transform-button split round">
                            <div class="list">
                                <p><i class="fas fa-times icon"></i> Close Account</p>
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
	            <br><br>
	            <div id="dateFilter-popup-content" class="pop-up-item hidden">
                    <h2 id="title">Date</h2>
                    <p class="info">Please specify the time frame</p><br>
                    <form id="filterDate" class="flex-form">
                        <label for="date1" class="info">From</label>
                        <input id="date1" type="date" class="input-field">
                        <label for="date2" class="info">To</label>
                        <input id="date2" type="date" class="input-field">
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
	            <div id="transaction-popup-content" class="pop-up-item flex-form hidden">
                    <h2 id="title">Transaction Details</h2>
                    <b class="info">Date</b>
                    <p id="transaction-date"></p>
                    <b class="info">Description</b>
                    <p id="transaction-description"></p>
                    <b class="info">Type</b>
                    <p id="transaction-type"></p>
                    <b class="info">Amount</b>
                    <p id="transaction-amount"></p>
                    <b class="info">Balance Afterward</b>
                    <p id="transaction-balance"></p>
                    <button onClick="hidePopUp()" class="standard-button transform-button flex-center round">
                        <div class="split">
           		            <div class="toggle-button">
            		            <i class="fas fa-chevron-left"></i>
            		        </div>
                            <p class="animate-right">Return<p>
                        </div>
                    </button>
                </div>
                <div id="account-popup-content" class="pop-up-item flex-form hidden">
                    <h2 id="title">Account Details</h2>
                    <b class="info">Account Name</b>
                    <p id="account-name"><?php echo $currentAccountName ?></p>
                    <b class="info">Account Type</b>
                    <p id="account-type"><?php echo $accountType ?></p>
                    <b class="info">Account Balance</b>
                    <p id="account-balance"><?php echo $accountBalance ?></p>
                    <b class="info">Routing Number</b>
                    <p id="account-routing-number"><?php echo $routingNumber ?></p>
                    <b class="info">Stuff</b>
                    <p id="placeholder2"></p>
                    <button onClick="hidePopUp()" class="standard-button transform-button flex-center round">
                        <div class="split">
           		            <div class="toggle-button">
            		            <i class="fas fa-chevron-left"></i>
            		        </div>
                            <p class="animate-right">Return<p>
                        </div>
                    </button>
                </div>
                <div id="edit-popup-content" class="pop-up-item hidden">
                    <h2 id="title">Edit Account</h2>
                    <p class="info">Change account nickname</p><br>
                    <form id="change-nickname" action="../../requests/account/updateAccountNickname" class="flex-form">
        	            <label for="name" class="info">Account Name</label>
        		        <input id="name" class="input-field" name="new" type="text" required>
                        <input id="current-account-name" type="hidden" name="old" value="<?php echo $currentAccountName ?>" required>
                        <input type="hidden" name="token" value="<?php echo $updateNickNameToken ?>" required>
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
            </div>
        </div>
	</body>
    <script type="text/javascript" src="../../js/jquery/jquery.js"></script>
	<script type="text/javascript" src="../../js/navigation.js"></script>
	<script type="text/javascript" src="../../js/notification.js"></script>
	<script type="text/javascript">
        function showPopUp(ContentId, entity = null) {
            document.querySelectorAll('.pop-up-item').forEach((element) => {
                if (element.id === ContentId) {
                    if (entity !== null && ContentId === 'transaction-popup-content') {
            	        let item = entity.children;
            	        
                        $('#transaction-date').text(item[2].textContent);
                        $('#transaction-description').text(item[3].textContent);
                        $('#transaction-type').text(item[1].textContent);
                        $('#transaction-amount').text(item[4].textContent);
                        $('#transaction-balance').text(item[0].textContent);
                    }
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
	<script type="text/javascript">
	let transactionsTable = document.getElementById('transactions-body');
	
	document.addEventListener('keydown', (event) => {
	    switch (event.key) {
	        case 'ArrowUp':
        	    if (document.activeElement.classList.contains('transaction-element')) {
        	    	let currentElement = document.activeElement;
        		
        		    $(currentElement).prevAll('.transaction-element:first').focus();
        	    }
        	    
         	    break;
	        case 'ArrowDown':
        	    if (transactionsTable === document.activeElement) {
        	        $('.transaction-element:first').focus();
        	    } else if (document.activeElement.classList.contains('transaction-element')) {
        	    	let currentElement = document.activeElement;
        	    	
        		    $(currentElement).nextAll('.transaction-element:first').focus();
        	    }
        	    
        	    break;
        	case 'Enter':
        	    if (document.activeElement.classList.contains('transaction-element')) {
        	    	let currentElement = document.activeElement;
        	    	
        		    showPopUp('transaction-popup-content', currentElement);
        	    }
	    }
	});
	
    function changeAccount(element) {
        window.location.href = "details?acc=" + element.value;
    }
	</script>
	<script type="text/javascript">
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('change-nickname').addEventListener('submit', handleForm);
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
	        
	        if (formData.get('old') === formData.get('new')) {
	            setFailNotification('Account is already named ' + formData.get('old'));
	        } else {
    	        fetch(request)
    	            .then((response) => response.json())
    	            .then((data) => {
    	                if (data.response) {
    	                    setSuccessNotification(data.message);
    	                    document.getElementById('title-account-name').textContent = formData.get('new');
    	                    document.getElementById('choose-account').selectedOptions[0].text = formData.get('new') + ' ' + document.getElementById('title-account-type').textContent + '';
    	                    document.getElementById('current-account-name').value = formData.get('new');
    	                    history.pushState(null, '', '<? echo getHomeDirectory() /* TEMP */?>/goldmanstacks/view/account/details?acc=' + formData.get('new'));    
    	                } else {
    	                    setFailNotification(data.message);
    	                }
    	            })
    	            .catch(console.warn);
	        }

            hidePopUp();
		    showNotification();
	    }
    </script>
</html>
