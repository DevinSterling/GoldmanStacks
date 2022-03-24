<?
function generateRandomString($length = 10) {
    $characters = '                            0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/* PHP external files */
require_once('/home/sterlid2/Private/sysNotification.php');

/* Passed Variables */
$currentAccountName = $_GET['acc'];

/* temp Variables */
$balance = "0.00";
$routingNumber = "123456789";
$lastVisit = date("F j, Y, g:i a"); // Last time of login
$accounts = ["Checking", "Savings", "Account 3", "Account 4", "Account 5"]; // User Account names taken from DB

/* Main Variables */
$amountOfAccounts = 5; // This variable will be taken from the DB (Total amount of accounts the user has)
$amountOfTransactions = 25; // Number of recent transactions to show

if (!in_array($currentAccountName, $accounts)) {
    header("Location: /~sterlid2/bank/home.php");
    die();
}
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title><?echo strtoupper($currentAccountName)?> Account Details</title>
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
		<? notification(); ?>
    	<div class="container flex-center">
    	    <div class="list main">
    	        <div class="container">
        	        <h2 id="title"><? echo $currentAccountName?> History</h2>
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
	                        <th class="amount">Amount</th>
	                        <th class="hidden">Balance After</th>
	                        <th class="hidden">Type</th>
	                    </tr>
                    </thead>
                    <tbody>
		            <?
	                for ($n = 1; $n <= $amountOfTransactions; $n++) {
	                    echo "<tr onClick=\"showPopUp('transaction-popup-content', this)\">
	                            <td data-label=\"Balance After\" class=\"hidden\">\$1000.00</td>
	                            <td data-label=\"Type\" class=\"hidden\">Withdrawal</td>
	                            <td data-label=\"Date\" class=\"date\">$lastVisit</td>
	                            <td data-label=\"Description\" class=\"desc\">Transaction $n - ".generateRandomString(50)."</td>
	                            <td data-label=\"Amount\" class=\"amount\">-/+\$1000.00</td>
	                        </tr>";
	                }
		            ?>
		            </tbody>
	            </table>
    	    </div>
    	    <div class="list sub">
    	        <div class="container round shadow">
    	            <div class="item-banner top-round">
    	                <h2 class="big text-center">Balance: $<? echo $balance ?></h2>
    	            </div>
    	            <div class="item-content bottom-round">
    	                <form id="select-account">
    	                    <label for="choose-account" class="info">Selected Account</label>
    	                    <div class="form-item">
    	                        <select id="choose-account" class="input-field last-field">
    	                            <?
    	                            for ($n = 0; $n < $amountOfAccounts; $n++) {
    	                               echo "<option";
    	                               
    	                               if ($currentAccountName === $accounts[$n]) {
    	                                    echo " selected";
    	                               }
    	                               
    	                               echo ">$accounts[$n]</option>";
    	                            }
    	                            ?>
    	                        </select>
    	                    </div>
    	                </form>
    	                <hr>
                        <button onClick="showPopUp('account-popup-content')" class="highlight-button transform-button split round">
                            <div class="list">
                                <p><i class="fas fa-times icon"></i> View Account Details</p>
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
    	        <div class="container round shadow">
    	            <div class="item-banner top-round">
    	                <label class="banner-text">Account Actions</label>
    	            </div>
    	            <div class="item-content bottom-round">
                        <a id="transfer" href="/~sterlid2/bank/account/transfer.php?acc=<? echo $currentAccountName ?>" class="highlight-button transform-button split round">
                            <div class="list">
                                <p><i class="fas fa-exchange-alt icon"></i> Initiate Transfer</p>
                            </div>
                            <div class="animate-left">
                	            <div class="toggle-button">
                	                <i class="fas fa-chevron-right"></i>
                	            </div>
                            </div>
                        </a>
                        <hr>
                        <a href="/~sterlid2/bank/account/payments.php?acc=<? echo $currentAccountName ?>" class="highlight-button transform-button split round">
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
                    <form id="filterDate">
                        <label for="date1" class="info">From</label>
        	            <div class="form-item">
                        <input id="date1" type="date" class="input-field">
        	            </div>
                        <label for="date2" class="info">To</label>
        	            <div class="form-item">
                        <input id="date2" type="date" class="input-field">
        	            </div>
                        <hr>
                        <div class="form-item">
                            <button form="filterDate" class="standard-button transform-button flex-center round">
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
	            <div id="transaction-popup-content" class="pop-up-item hidden">
                    <h2 id="title">Transaction Details</h2>
                    <p class="info"></p>
                    <div class="container">
                        <b class="info">Date</b>
                        <p id="transaction-date"></p>
                    </div>
                    <div class="container">
                        <b class="info">Description</b>
                        <p id="transaction-description"></p>
                    </div>
                    <div class="container">
                        <b class="info">Type</b>
                        <p id="transaction-type"></p>
                    </div>
                    <div class="container">
                        <b class="info">Amount</b>
                        <p id="transaction-amount"></p>
                    </div>
                    <div class="container">
                        <b class="info">Balance Afterward</b>
                        <p id="transaction-balance"></p>
                    </div>
                    <hr>
                    <div class="form-item">
                        <button onClick="hidePopUp()" class="standard-button transform-button flex-center round">
                            <div class="split">
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-left"></i>
                		        </div>
                                <p class="animate-right">Return<p>
                            </div>
                        </button>
                    </div>
                </div>
                <div id="account-popup-content" class="pop-up-item hidden">
                    <h2 id="title">Account Details</h2>
                    <p class="info"></p>
                    <div class="container">
                        <b class="info">Account Name</b>
                        <p id="account-name"><? echo $currentAccountName ?></p>
                    </div>
                    <div class="container">
                        <b class="info">Account Balance</b>
                        <p id="account-balance"><? echo $balance ?></p>
                    </div>
                    <div class="container">
                        <b class="info">Routing Number</b>
                        <p id="account-routing-number"><? echo $routingNumber ?></p>
                    </div>
                    <div class="container">
                        <b class="info">Stuff</b>
                        <p id="placeholder1"></p>
                    </div>
                    <div class="container">
                        <b class="info">Stuff</b>
                        <p id="placeholder2"></p>
                    </div>
                    <hr>
                    <div class="form-item">
                        <button onClick="hidePopUp()" class="standard-button transform-button flex-center round">
                            <div class="split">
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-left"></i>
                		        </div>
                                <p class="animate-right">Return<p>
                            </div>
                        </button>
                    </div>
                </div>
                <div id="edit-popup-content" class="pop-up-item hidden">
                    <h2 id="title">Edit Account</h2>
                    <p class="info">Change account nickname</p><br>
                    <form id="edit">
        	            <label for="name" class="info">Account Name</label>
        	            <div class="form-item">
        		            <input id="name" class="input-field" type="text">
        	            </div>
                        <hr>
                        <div class="form-item">
                            <button form="edit" class="standard-button transform-button flex-center round">
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
            </div>
        </div>
	</body>
    <script type="text/javascript"  src="/~sterlid2/bank/Scripts/jquery/jquery.js"></script>
	<script type="text/javascript" src="/~sterlid2/bank/Scripts/navigation.js"></script>
	<script type="text/javascript" src="/~sterlid2/bank/Scripts/post.js"></script>
	<script type="text/javascript">
        function showPopUp(ContentId, entity = null) {
            document.querySelectorAll(".pop-up-item").forEach((element) => {
                if (element.id === ContentId) {
                    if (entity !== null && ContentId === 'transaction-popup-content') {
            	        var item = entity.children;
            	        
                        $("#transaction-date").text(item[2].textContent);
                        $("#transaction-description").text(item[3].textContent);
                        $("#transaction-type").text(item[1].textContent);
                        $("#transaction-amount").text(item[4].textContent);
                        $("#transaction-balance").text(item[0].textContent);
                    }
                    element.classList.remove("hidden");
                }
                else {
                    element.classList.add("hidden");
                }
            });
            document.getElementById("pop-up").classList.add("show-popup-content");
            document.getElementById("pup-up-element").classList.remove("hidden");
        }
        
        function hidePopUp() {
            document.getElementById("pop-up").classList.remove("show-popup-content");
            document.getElementById("pup-up-element").classList.add("hidden");
        }
	</script>
	<script type="text/javascript">
	    var currentAccount = "<? echo $currentAccountName ?>";
	
	    /* Retrieve New DB Details */
        $("#choose-account").on("change", function (e) {
            var optionSelected = $("option:selected", this);
            currentAccount = this.value;
            
            document.getElementById("title").textContent = currentAccount+" History";
            document.getElementById("transfer").href = "/~sterlid2/bank/account/transfer.php?acc="+currentAccount;
            document.title = currentAccount.toUpperCase()+" Account Details";
            
            document.getElementById("account-name").textContent = currentAccount;
            document.getElementById("account-balance").textContent = "$0.00";
            document.getElementById("account-routing-number").textContent = "123456789";
        });
    </script>
</html>