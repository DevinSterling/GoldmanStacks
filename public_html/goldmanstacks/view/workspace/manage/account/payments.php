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
			</ul>
			<ul class="menugroup">
				<li class="menuitem"><a href="../../staff/options">Options</a></li>
				<li class="menuitem"><a href="../../../../requests/signout">Sign Out</a></li>
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
                <a href="transactions?id=<? echo $user ?>" class="tab-button transform-button round">
                    <div class="split">
                        <div class="text-right">
                            <p>Transactions</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>
                <a href="payments?id=<? echo $user ?>" class="tab-button transform-button round selected">
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
                <h2 id="title"><?php echo $currentAccountName?> User <?php echo $user ?> Ongoing Payments</h2>
                <table id="users" class="responsive-table">
                    <thead>
                        <tr>
                            <th>Recurring</th>
                            <th>Next Payment</th>
                            <th>Sender</th>
                            <th>Recipient</th>
                            <th>Recipient Name</th>
                            <th>Amount</th>
                            <th>Step</th>
                            <th>End Date</th>
                        </tr>
                    </thead>
                    <tbody>
    	            <?php
    				/* Statement to obtain transaction information */
    				$paymentsStatement = $db->prepare("SELECT P.paymentDate, P.accountNum, P.recipientAccount, P.recipientNickName, P.amount, P.step, P.endDate
                                                            FROM payments P
                                                            INNER JOIN accountDirectory A ON P.accountNum=A.accountNum
                                                            WHERE A.clientID=?
                                                            ORDER BY P.paymentDate DESC");
    				$paymentsStatement->bind_param("i", $user);
    				$paymentsStatement->execute();
    				
    				/* Obtain results */
    				$paymentsResult = $paymentsStatement->get_result();
    				$paymentsRows = $paymentsResult->fetch_all(MYSQLI_ASSOC);
    	            
                    foreach($paymentsRows as $payment) {
                        echo "<tr onClick=\"showPopUp('request-details-popup-content', this)\">
                                <td data-label=\"Recurring\">" . (empty($payment['step']) ? 'False' : 'True') . "</td>
                                <td data-label=\"Next Payment\">" . $payment['paymentDate'] . "</td>
                                <td data-label=\"Sender\">(*" . substr($payment['accountNum'], -4) . ")</td>
                                <td data-label=\"Recipient\">(*" . substr($payment['recipientAccount'], -4) . ")</td>
                                <td data-label=\"Recipient Name\">" . (empty($payment['recipientNickName']) ? '...' : $payment['recipientNickName']) . "</td>
                                <td data-label=\"Amount\">$" . number_format($payment['amount'], 2) . "</td>
                                <td data-label=\"Step\">" . (empty($payment['step']) ? '...' : $payment['step']) . "</td>
                                <td data-label=\"End Date\">" . (empty($payment['endDate']) ? '...' : $payment['endDate']) . "</td>
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
