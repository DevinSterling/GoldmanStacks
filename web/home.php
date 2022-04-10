<?
/* PHP external files */
require_once('/home/sterlid2/Private/sysNotification.php');
require_once('/home/sterlid2/Private/userbase.php');

/* Force https connection */
forceHTTPS();

/* Check if the user is logged in already */
session_start();
if (!checkIfLoggedIn() || !isClient()) {
    header("Location: signin.php");
    die();
}

/* Check if the user has been inactive */
if (checkInactive()) {
    header("Location: requests/signout.php");
    die();
}

/* Temp Variables */
$user = "User"; // Taken from DB, user account name
$totalAssets = "0.00"; // Sum of all of users' accounts
$lastVisit = date("F j, Y, g:i a"); // Last time of login
$accounts = ["Checking", "Savings", "Account 3", "Account 4", "Account 5"]; // User Account names taken from DB

/* Main Variables */
$amountOfAccounts = 5; // This variable will be taken from the DB (Total amount of accounts the user has)
$amountOfTransactions = 5; // Number of recent transactions to show
?>

<!DOCTYPE html>
<html lang="en-US">
	<head>
	<title>Home</title>
	<!-- Stylesheet -->
	<link rel="stylesheet" href="CSS/stylesheet.css">
	<!-- Favicon -->
	<link rel="icon" href="Images/logo.ico">
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
				<li class="menulogo"><a href="home.php">Goldman Stacks</a></li>
                <li class="menutoggle"><a href="#"><i class="fas fa-bars"></i></a></li>
				<li class="menuitem"><a href="home.php">Home</a></li>
				<li class="menuitem"><a href="account/transfer.php">Transfer</a></li>
				<li class="menuitem"><a href="account/payments.php">Payments</a></li>
				<li class="menuitem"><a href="account/open.php">Open New Account</a></li>
				<li class="menuitem"><a href="account/statement.php">Statement</a></li>
			</ul>
			<ul class="menugroup">
				<li class="menuitem"><a href="user/options.php">Options</a></li>
				<li class="menuitem"><a href="requests/signout.php">Sign Out</a></li>
			</ul>
		</nav>
		<? notification(); ?>
		<div class="container flex-center">
		    <div class="list main">
		        <h2 id="title">Welcome, <? echo $user ?></h2>
		        <div class="split">
		            <label class="info">Available Accounts</label>
		            <a href="account/open.php" class="expand-button transform-button extend-left round shadow">
		                <div class="split">
		                    <div class="animate-left">
            		            <div class="toggle-button">
            		                <p class="expanded-info">Add Account</p>
            		            </div>
        		            </div>
		                    <p class="condensed-info animate-rotate90"><b>+</b></p>
		                </div>
		            </a>
		        </div>
		        <?
	            for ($i = 0; $i < $amountOfAccounts; $i++) {
		            echo "<a href=\"account/details.php?acc=".$accounts[$i]."\" class=\"big-color-button transform-button split round shadow\">
                            <div class=\"list\">
            		            <p class=\"focused-info\">$accounts[$i]</p>
            		            <p>Savings Account (*".(1028+$i*402+$i*433).")</p>
        		            </div>
        		            <div class=\"split animate-left\">
            		            <div class=\"list text-right\">
            		                <p>Available</p>
                		            <p class=\"focused-info\">\$0.00</p>
            		            </div>
            		            <div class=\"toggle-button\">
            		                <i class=\"fas fa-chevron-right\"></i>
            		            </div>
        		            </div>
            		    </a>";
                }
		        ?>
		    </div>
		    <div class="list sub">
		        <div class="container round shadow">
    		        <div class="item-banner top-round">
    		            <!--<label class="banner-text">Account Details</label>-->
    		            <h2 class="big text-center">Total Balance: $<? echo $totalAssets ?></h2>
    		        </div>
    		        <div class="item-content bottom-round">
    		            <p class="info text-center">Last Sign In: <? echo $lastVisit ?></p>
    		            <hr>
                        <a href="account/funds.php" class="highlight-button transform-button split round">
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
                        <a href="account/funds.php?v=withdraw" class="highlight-button transform-button split round">
                            <div class="list">
                                <p><i class="fas fa-minus icon"></i> Withdraw Funds</p>
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
    		            <label class="banner-text"> Recent Activity</label>
    		        </div>
    		        <div class="item-content bottom-round">
    		            <?
    		            $recentAccount = "Checking"; // temp
		                for ($n = 1; $n <= $amountOfTransactions; $n++) {
		                    echo "<a href=\"account/details.php?acc=".$recentAccount."\" class=\"highlight-button transform-button split round\">
		                            <div class=\"list-padded\">
		                                <h3 class=\"bold\">Transaction $n</h3>
		                                <p>$lastVisit<p>
		                            </div>
		                            <div class=\"split animate-left\">
		                                <div class=\"list-padded text-right\">
		                                    <h3>-/+$.00</h3>
		                                    <p>Payment</p>
		                                </div>
                       		            <div class=\"toggle-button\">
                        		            <i class=\"fas fa-chevron-right\"></i>
                        		        </div>
		                            </div>
		                          </a>";
		                    
		                    if ($n != $amountOfTransactions){
		                        echo "<hr>";
		                    }
		                }
    		            ?>
    		        </div>
    		    </div>
		        <div class="container round shadow">
    		        <div class="item-banner top-round">
    		            <label class="banner-text">Quick Payments</label>
    		        </div>
    		        <form id="payments" class="item-content bottom-round">
        		        <label class="info" for="PayTo">Pay To</label>
    		            <div class="form-item">
        		            <select id="PayTo" class="input-field">
                                <?
                                for ($i = 0; $i < $amountOfAccounts; $i++) {
                                    echo "<option>Recipient $i</option>";
                                }
                                ?>
        		            </select>
    		            </div>
        		        <label class="info" for="PayFrom">Pay From</label>
    		            <div class="form-item">
        		            <select id="PayFrom" class="input-field">
                                <?
                                for ($i = 0; $i < $amountOfAccounts; $i++) {
                                    echo "<option>$accounts[$i]</option>";
                                }
                                ?>
        		            </select>
        		        </div>
    		            <label class="info" for="Date">Date</label>
    		            <div class="form-item">
        		            <input id="Date" class="input-field" type="date" placeholder="yyyy-mm-dd">
    		            </div>
    		            <label class="info" for="Amount">Amount</label>
    		            <div class="form-item">
        		            <input id="Amount" class="input-field" type="number" min="0" max="<? echo $totalAssets ?>">
    		            </div>
    		            <div class="form-item">
                            <button form="payments" class="standard-button transform-button flex-center round">
                                <div class="split">
                                    <p class="animate-left">Schedule Payment<p>
                   		            <div class="toggle-button">
                    		            <i class="fas fa-chevron-right"></i>
                    		        </div>
                                </div>
                            </button>
    		            </div>
    		        </form>
    		    </div>
		    </div>
		</div>
	</body>
	<script type="text/javascript" src="Scripts/navigation.js">
	</script>
	<script type="text/javascript" src="Scripts/post.js">
	</script>
</html>
