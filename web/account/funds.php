<?
/* PHP external files */
require_once('/home/sterlid2/Private/sysNotification.php');
require_once('/home/sterlid2/Private/userbase.php');

/* Force https connection */
forceHTTPS();

session_start();
if(!checkIfLoggedIn() || !isClient()) {
    header("Location: ../signin.php");
    die();
}

$view = $_GET['v'];
$referencedName = $_GET['acc'];

/* Temp Variables*/
$accounts = ["Checking", "Savings", "Account 3", "Account 4", "Account 5"]; // User Account names taken from DB

$amountOfAccounts = 5;
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
	<title>Payments</title>
	<!-- Stylesheet -->
	<link rel="stylesheet" href="../CSS/stylesheet.css">
	<!-- Favicon -->
	<link rel="icon" href="../Images/logo.ico">
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
			<li class="menulogo"><a href="../home.php">Goldman Stacks</a></li>
			<li class="menutoggle"><a href="#"><i class="fas fa-bars"></i></a></li>
			<li class="menuitem"><a href="../home.php">Home</a></li>
			<li class="menuitem"><a href="transfer.php">Transfer</a></li>
			<li class="menuitem"><a href="payments.php">Payments</a></li>
			<li class="menuitem"><a href="open.php">Open New Account</a></li>
			<li class="menuitem"><a href="statement.php">Statement</a></li>
		</ul>
		<ul class="menugroup">
			<li class="menuitem"><a href="../user/options.php">Options</a></li>
			<li class="menuitem"><a href="../requests/signout.php">Sign Out</a></li>
		</ul>
	</nav>
	<? 
	notification();
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
        <div class="container flex-center <? if ($referencedName !== null) echo "marginless" ?>">
            <div class="list mini">
                <button class="tab-button transform-button round <? if ($view === "deposit" || empty($view)) echo "selected" ?>" data-id="deposit-form" data-title="Deposit">
                    <div class="split">
                        <div class="text-right">
                            <p>Deposit</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
    	        </button>
                <button class="tab-button transform-button round <? if ($view === "withdraw") echo "selected" ?>"  data-id="withdraw-form" data-title="Withdraw">
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
                    <h2 id="title"><?
                        if (empty($view) || $view === "deposit") echo "Deposit";
                        else echo "Withdraw"
                    ?></h2>
                </div>
                <form id="deposit-form" class="<? if (!empty($view) && $view !== "deposit") echo "hidden" ?>">
                    <p class="info">Deposit funds from account</p><br>
		            <label for="deposit-account" class="info">Account</label>
		            <div class="form-item">
    		            <select id="deposit-account" class="input-field">
                            <?
                            for ($i = 0; $i < $amountOfAccounts; $i++) {
                                echo "<option>$accounts[$i]</option>";
                            }
                            ?>
    		            </select>
		            </div>
                    <label for="deposit-amount" class="info">Amount</label>
    	            <div class="form-item">
                        <input id="deposit-amount" type="number" class="input-field">
    	            </div>
                    <hr>
                    <div class="form-item">
                        <button form="filterDate" class="standard-button transform-button flex-center round">
                            <div class="split">
                                <p class="animate-left">Deposit<p>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
                        </button>
                    </div>
                </form>
                <form id="withdraw-form" class="<? if ($view !== "withdraw") echo "hidden" ?>">
                    <p class="info">Withdraw funds from account</p><br>
		            <label for="withdraw-account" class="info">Account</label>
		            <div class="form-item">
    		            <select id="withdraw-account" class="input-field">
                            <?
                            for ($i = 0; $i < $amountOfAccounts; $i++) {
                                echo "<option>$accounts[$i]</option>";
                            }
                            ?>
    		            </select>
		            </div>
                    <label for="withdraw-amount" class="info">Amount</label>
    	            <div class="form-item">
                        <input id="withdraw-amount" type="number" class="input-field">
    	            </div>
                    <hr>
                    <div class="form-item">
                        <button form="filterDate" class="standard-button transform-button flex-center round">
                            <div class="split">
                                <p class="animate-left">Withdraw<p>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
                        </button>
                    </div>
                </form>
    	    </div>
    	    <div class="list mini">
    	    </div>
    	</div>
	    <script type="text/javascript" src="../Scripts/navigation.js">
	    </script>
	    <script type="text/javascript" src="../Scripts/tabs.js"></script>
    </body>
</html>
