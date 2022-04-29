<?php
require_once('../../../../../../private/sysNotification.php');
require_once('../../../../../../private/userbase.php');
require_once('../../../../../../private/config.php');
require_once('../../../../../../private/functions.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkEmployeeStatus(); // Check if the employee is signed in

/* GET Variables */
$user = $_GET['id'];

$db = getUpdateConnection();

if ($db === null) {
    header("Location: ");
}

?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
	<title>User</title>
	<!-- Stylesheet -->
	<link rel="stylesheet" href="../../../../css/stylesheet.css">
	<!-- Favicon -->
	<link rel="icon" href="../../../../img/logo.ico">
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
				<li class="menulogo"><a href="../../manager">Goldman Stacks</a></li>
                <li class="menutoggle"><a href="#"><i class="fas fa-bars"></i></a></li>
				<li class="menuitem"><a href="../../manager">Manage</a></li>
				<li class="menuitem"><a href="../../search">Search</a></li>
			</ul>
			<ul class="menugroup">
				<li class="menuitem"><a href="../../staff/options">Options</a></li>
				<li class="menuitem"><a href="../../../login">Sign Out</a></li>
			</ul>
		</nav>
		<div class="sys-notification">Logged as Employee</div>
		<?php notification(); ?>
    	<div class="container flex-center">
            <div class="list mini">
                <a href="../user?id=<?php echo $user ?>" class="tab-button transform-button round">
                    <div class="split">
                        <div class="text-right">
                            <p>Overview</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </a>
                <a href="accounts?id=<? echo $user ?>" class="tab-button transform-button round">
                    <div class="split">
                        <div class="text-right">
                            <p>Accounts</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </a>
                <a href="transactions?id=<? echo $user ?>" class="tab-button transform-button round selected">
                    <div class="split">
                        <div class="text-right">
                            <p>Transactions</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>
                <a href="payments?id=<? echo $user ?>" class="tab-button transform-button round">
                    <div class="split">
                        <div class="text-right">
                            <p>Payments</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </a>
		    </div>
            <div class="list main">
                <h2 id="title"><?php echo $currentAccountName?> User <?php echo $user ?> Transaction History</h2>
                <table id="users" class="responsive-table">
                    <thead>
                        <tr>
                            <th style="width:20%">Date</th>
                            <th style="width:20%">Type</th>
                            <th style="width:20%">Sender</th>
                            <th style="width:20%">Recipient</th>
                            <th style="width:20%">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
    	            <?php
    				/* Statement to obtain transaction information */
    				$transactionStatement = $db->prepare("SELECT T.transactionTime, T.accountNum, T.recipientAccount, T.transactionAmount, T.type, (T.recipientAccount=A.accountNum AND T.clientID<>A.clientID) AS isRecipient
                                                            FROM transactions T
                                                            INNER JOIN accountDirectory A ON T.accountNum=A.accountNum OR (T.recipientAccount=A.accountNum AND T.clientID<>A.clientID)
                                                            WHERE A.clientID=?
                                                            ORDER BY T.transactionTime DESC");
    				$transactionStatement->bind_param("i", $user);
    				$transactionStatement->execute();
    				
    				/* Obtain results */
    				$transactionResult = $transactionStatement->get_result();
    				$transactionRows = $transactionResult->fetch_all(MYSQLI_ASSOC);
    	            
                    foreach($transactionRows as $transaction) {
    				    /* Get client account number for transaction */
                        if ($transaction['isRecipient']) {
                            $accountNumber = $transaction['recipientAccount'];
                            $transaction['transactionAmount'] *= -1;
                        } else {
                            $accountNumber = $transaction['accountNum'];
                        }
                        
                        echo "<tr onClick=\"showPopUp('request-details-popup-content', this)\">
                                <td data-label=\"Date\">" . $transaction['transactionTime'] . "</td>
                                <td data-label=\"Type\">" . ucfirst($transaction['type']) . "</td>
                                <td data-label=\"Sender\">(*" . substr($transaction['accountNum'], -4) . ")</td>
                                <td data-label=\"Recipient\">(*" . substr($transaction['recipientAccount'], -4) . ")</td>
                                <td data-label=\"Amount\">" . convertToCurrency($transaction['transactionAmount']) . "</td>
                                <td class=\"hidden\">$n</td>
                            </tr>";
                    }
                    
                    $db->close();
    	            ?>
    	            </tbody>
                </table>
            </div>
            <div class="list mini">
		    </div>
        </div>
	</body>
	<script type="text/javascript" src="../../../../js/navigation.js"></script>
	<script type="text/javascript" src="../../../../js/tabs.js"></script>
</html>
