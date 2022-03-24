<?
/* PHP external files */
require_once('/home/sterlid2/Private/sysNotification.php');

/* Passed Variables */
$referencedName = $_GET['acc'];

/* Temp Variables*/
$accounts = ["Checking", "Savings", "Account 3", "Account 4", "Account 5"]; // User Account names taken from DB

$amountOfAccounts = 5;
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title>Transfer</title>
		<!-- Stylesheet -->
		<link rel="stylesheet" href="/~sterlid2/bank/CSS/stylesheet.css">
		<!-- Favicon -->
		<link rel="icon" href="/~sterlid2/bank/Images/logo.ico">
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
				<li class="menulogo"><a href="/~sterlid2/bank/home.php">TempBank</a></li>
                <li class="menutoggle"><a href="#"><i class="fas fa-bars"></i></a></li>
				<li class="menuitem"><a href="/~sterlid2/bank/home.php">Home</a></li>
				<li class="menuitem"><a href="/~sterlid2/bank/account/transfer.php">Transfer</a></li>
				<li class="menuitem"><a href="/~sterlid2/bank/account/payments.php">Payments</a></li>
				<li class="menuitem"><a href="/~sterlid2/bank/account/open.php">Open New Account</a></li>
				<li class="menuitem submenu">
				    <a tabindex="0">Statements</a>
				    <!--<ul class="submenugroup">
				        <li class="subitem"><a href="#PrintAll">Print Statement</a></li>
				        <li class="subitem"><a href="#PrintOne">Print Specific</a></li>
				    </ul>-->
				</li>
			</ul>
			<ul class="menugroup">
				<li class="menuitem"><a href="/~sterlid2/bank/user/options.php">Options</a></li>
				<li class="menuitem"><a href="/~sterlid2/bank/login.php">Sign Out</a></li>
			</ul>
		</nav>
		<? 
		notification();
        if (!empty($referencedName)) {
            echo "<div class=\"container flex-center marginless-bottom\">
                <div class=\"list sub\">
                    <div class=\"split\">
       	                <a id=\"return\" href=\"/~sterlid2/bank/account/details.php?acc=$referencedName\" class=\"expand-button transform-button extend-right round\">
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
                <button class="tab-button transform-button round selected" data-id="internal" data-title="Internal Transactions">
                    <div class="split">
                        <div class="text-right">
                            <p>Internal</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>
                <button class="tab-button transform-button round"  data-id="external" data-title="External Transactions">
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
                <form id="internal">
    	            <label for="internal-sender" class="info">Sender</label>
    	            <div class="form-item">
    		            <select id="internal-sender" class="input-field">
                            <?
                            for ($n = 0; $n < $amountOfAccounts; $n++) {
                               echo "<option";
                               
                               if ($referencedName === $accounts[$n]) {
                                    echo " selected";
                               }
                               
                               echo ">$accounts[$n]</option>";
                            }
                            ?>
    		            </select>
    	            </div>
    	            <label for="receiverreceiver" class="info">Receiver</label>
    	            <div class="form-item">
    		            <select id="internal-receiver" class="input-field">
                            <?
                            for ($n = 0; $n < $amountOfAccounts; $n++) {
                               echo "<option>$accounts[$n]</option>";
                            }
                            ?>
                        </select>
    		        </div>
    		        <hr>
    	            <label for="internal-amount" class="info">Amount</label>
    	            <div class="form-item">
    		            <input id="internal-amount" class="input-field" type="number" min="0" placeholder="USD">
    	            </div>
                    <hr>
                    <div class="form-item">
                        <button form="Internal" class="standard-button transform-button flex-center round">
                            <div class="split">
                                <p class="animate-left">Apply<p>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
                        </button>
                    </div>
                </form>
                <form id="external" class="hidden">
    	            <label for="external-sender" class="info">Sender</label>
    	            <div class="form-item">
    		            <select id="external-sender" class="input-field">
                            <?
                            for ($n = 0; $n < $amountOfAccounts; $n++) {
                               echo "<option";
                               
                               if ($referencedName === $accounts[$n]) {
                                    echo " selected";
                               }
                               
                               echo ">$accounts[$n]</option>";
                            }
                            ?>
    		            </select>
    	            </div>
    	            <label for="external-receiver" class="info">Receiver Address</label>
    	            <div class="form-item">
                        <input id="external-receiver" class="input-field" type="text">
    		        </div>
    		        <hr>
    	            <label for="external-amount" class="info">Amount</label>
    	            <div class="form-item">
    		            <input id="external-amount" class="input-field" type="number" min="0" placeholder="USD">
    	            </div>
                    <hr>
                    <div class="form-item">
                        <button form="External" class="standard-button transform-button flex-center round">
                            <div class="split">
                                <p class="animate-left">Apply<p>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
            <div class="list mini"></div>
    	</div>
	</body>
	<script type="text/javascript" src="/~sterlid2/bank/Scripts/navigation.js"></script>
	<script type="text/javascript" src="/~sterlid2/bank/Scripts/tabs.js"></script>
</html>