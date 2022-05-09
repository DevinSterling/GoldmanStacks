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

/* Database Connection */
$db = getUpdateConnection();

/* Check Connection */
if ($db === null) {
    header("Location: ");
    die();
}
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
	    <title>Home</title>
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
		        <div class="split">
		            <h2 id="title">Statement</h2>
                    <button onClick="printSelected('Statement')" class="expand-button transform-button extend-left round">
                        <div class="split">
                            <div class="animate-left">
            		            <div class="toggle-button">
            		                <p class="expanded-info">Print Statement</p>
            		            </div>
            	            </div>
                            <p class="condensed-info"><i class="fas fa-print"></i></p>
                        </div>
                    </button>
		        </div>
		        <div id="Statement">
	            <p class="info">Details<p>
		            <?php
		                $clientStatement = $db->prepare("SELECT firstName, middleName, lastName, email, phoneNumber, line1, line2, city, state, postalCode FROM users U INNER JOIN address A ON A.userID=U.userID WHERE U.userID=?");
		                $clientStatement->bind_param("i", $userId);
		                $clientStatement->execute();
		                
                        $result = $clientStatement->get_result();
                        $details = $result->fetch_assoc();
                        
                        $clientStatement->close();
		            ?>
		            <div class="split margin-bottom">
		                <div>
        		            <p class="info"><b>Name:</b> <?php echo ucfirst($details['firstName']) . " " . ucfirst($details['middleName']) . " " . ucfirst($details['lastName']) ?></p>
        		            <p class="info"><b>Email:</b> <?php echo $details['email'] ?></p>
                            <p class="info"><b>Phone:</b> <?php echo convertToPhoneNumber($details['phoneNumber']) ?></p>
    		            </div>
    		            <div class="text-left">
    		                <p class="info"><b>Address</b></p>
        		            <p class="info"><?php echo $details['line1'] ?></p>
                            <?php
        			        if (!empty($details['line2'])) echo "<p class=\"info\"><b>Line 2:</b>" . ($details['line2']) . "</p>";
                            ?>
        		            <p class="info"><?php echo $details['city'] . ", " . $details['state'] ?>, </p>
                            <p class="info"><?php echo $details['postalCode'] ?></p>
    		            </div>
		            </div>
                    <table id="accounts" class="responsive-table margin-bottom">
                        <thead>
    	                    <tr>
    	                        <th class="date">Account</th>
    	                        <th class="desc">Type</th>
    	                        <th class="desc">Balance</th>
    	                    </tr>
                        </thead>
                        <tbody>
    		            <?php
    		                $accountStatement = $db->prepare("SELECT nickName, accountNum, accountType, balance FROM accountDirectory WHERE clientID=?");
    		                $accountStatement->bind_param("i", $userId);
    		                $accountStatement->execute();
    		                
                            $result = $accountStatement->get_result();
                            $accounts = $result->fetch_all(MYSQLI_ASSOC);
    		                
    		                foreach ($accounts as $account) {
    		                    echo "<tr>
        		                        <td data-label=\"Account\">" . ucfirst($account['nickName']) . " (*" . substr($account['accountNum'], -4) . ")</td>
        		                        <td data-label=\"Type\">" . ucfirst($account['accountType']) . "</td>
        		                        <td data-label=\"Balance\">\$" . number_format($account['balance'], 2) . "</td>
    		                        </tr>";
    		                }
    		                
    		                $accountStatement->close();
    		            ?>
    		            </tbody>
		            </table>
		            <p class="info">Transactions<p>
                    <table id="transactions" class="responsive-table">
                        <thead>
    	                    <tr>
    	                        <th class="date">Date</th>
    	                        <th class="desc">Account</th>
    	                        <th class="desc">Description</th>
    	                        <th class="amount text-right">Amount</th>
    	                    </tr>
                        </thead>
                        <tbody>
    		            <?php
                        /* Query to get all transactions from the selected account */
                        $transactionStatement = $db->prepare("SELECT T.transactionTime, T.accountNum, T.recipientAccount, T.transactionAmount, T.type, A.nickName, A.accountType, (T.recipientAccount=A.accountNum AND T.clientID<>A.clientID) AS isRecipient
                                                                    FROM transactions T
                                                                    INNER JOIN accountDirectory A ON T.accountNum=A.accountNum OR (T.recipientAccount=A.accountNum AND T.clientID<>A.clientID)
                                                                    WHERE A.clientID=?
                                                                    ORDER BY T.transactionTime DESC");
                        $transactionStatement->bind_param("s", $userId);
                        $transactionStatement->execute();
                        
                        /* Obtain result */
                        $result = $transactionStatement->get_result();
                        $rows = $result->fetch_all(MYSQLI_ASSOC);
    
                        foreach ($rows as $transaction) {
                            switch ($transaction['type']) {
                                case 'transfer':
                                case 'payment':
                                    if ($transaction['isRecipient']) {
                                        $description = ucfirst($transaction['type']) . " from (*" . substr($transaction['accountNum'], -4) . ")";
                                        $transaction['transactionAmount'] *= -1;
                                    } else {
                                        $description = ucfirst($transaction['type']) . " to (*" . substr($transaction['recipientAccount'], -4) . ")";
                                    }
                                    break;
                                case 'deposit':
                                    $description = "Deposit into account";
                                    break;
                                case 'withdraw':
                                    $description = "Withdraw from account";
                                    break;
                            }
                            
                            
                            echo "<tr class=\"transaction-element\">
                                <td data-label=\"Date\" class=\"date\">".$transaction['transactionTime']."</td>
                                <td data-label=\"Account\" class=\"date\">" . ucfirst($transaction['nickName']) . " (*".substr($transaction['accountNum'], -4).")</td>
                                <td data-label=\"Description\" class=\"desc\">$description</td>
                                <td data-label=\"Amount\" class=\"amount text-right\">".convertToCurrency($transaction['transactionAmount'])."</td>
                            </tr>";
                        }
                        
                        $result->free();
                        $transactionStatement->close();
                        $db->close();
    		            ?>
    		            </tbody>
    	            </table>
		        </div>
		    </div>
		</div>
	</body>
	<script type="text/javascript" src="../../js/navigation.js"></script>
	<script type="text/javascript" src="../../js/print.js"></script>
</html>
