<?php
require_once('../../../../private/sysNotification.php');
require_once('../../../../private/config.php');
require_once('../../../../private/userbase.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkClientStatus(); // Check if the client is signed in

/* SESSION Variables */
$userID = $_SESSION['uid'];
$userID = 1;

/* GET Variables */
$referencedName = $_GET['acc'];

/* Variables */
$accounts = array();

/* Get Database Connection */
$db = getUpdateConnection();

/* Check Database Connection */
if ($db === null) {
    header("Location: ../error/error.php");
    die();
}

/* Get client accounts */
$queryAccounts = $db->prepare("SELECT nickName, accountType, accountNum FROM accountDirectory WHERE clientID=?");
$queryAccounts->bind_param("i", $userID);
$queryAccounts->execute();

$resultAccounts = $queryAccounts->get_result();
$rowAccounts = $resultAccounts->fetch_all(MYSQLI_ASSOC);

foreach ($rowAccounts as $account) {
    /* Create three dimensional associative array */
    $accounts[] = array('nickName' => $account['nickName'], 'type' => $account['accountType'], 'number' => $account['accountNum']);
}

$resultAccounts->free();
$queryAccounts->close();
$db->close();
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
	<title>Transfer</title>
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
	<?php 
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
        
        <div class="container flex-center <?php if ($referencedName !== null) echo "marginless" ?>">
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
                            <?php
                            foreach ($accounts as $account) {
                                echo "<option value=\"" . $account['number'] . "\"";
                               
                                if ($referencedName === $account['nickName']) {
                                    echo " selected";
                                }
                               
                                echo ">" . ($account['nickName'] . " (" . ucfirst($account['type']) . ")" ) . "</option>";
                            }
                            ?>
    		            </select>
    	            </div>
    	            <label for="receiverreceiver" class="info">Receiver</label>
    	            <div class="form-item">
    		            <select id="internal-receiver" class="input-field">
                            <?php
                            foreach ($accounts as $account) {
                                echo "<option value=\"" . $account['number'] . "\">" . ($account['nickName'] . " (" . ucfirst($account['type']) . ")" ) . "</option>";
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
                            <?php
                            foreach ($accounts as $account) {
                                echo "<option value=\"" . $account['number'] . "\"";
                               
                                if ($referencedName === $account['nickName']) {
                                    echo " selected";
                                }
                               
                                echo ">" . ($account['nickName'] . " (" . ucfirst($account['type']) . ")" ) . "</option>";
                            }
                            ?>
    		            </select>
    	            </div>
    	            <label for="external-receiver" class="info">Receiver Bank Account Number</label>
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
	<script type="text/javascript" src="../../js/navigation.js"></script>
	<script type="text/javascript" src="../../js/tabs.js"></script>
</html>
