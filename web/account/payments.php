<?
/* PHP external files */
require_once('/home/sterlid2/Private/sysNotification.php');

$referencedName = $_GET['acc'];

/* Temp Variables*/
$accounts = ["Checking", "Savings", "Account 3", "Account 4", "Account 5"]; // User Account names taken from DB

$amountOfPayments = 5;
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
			<li class="menuitem submenu">
			    <a tabindex="0">Statements</a>
			    <!--<ul class="submenugroup">
				<li class="subitem"><a href="#PrintAll">Print Statement</a></li>
				<li class="subitem"><a href="#PrintOne">Print Specific</a></li>
			    </ul>-->
			</li>
		</ul>
		<ul class="menugroup">
			<li class="menuitem"><a href="../user/options.php">Options</a></li>
			<li class="menuitem"><a href="../login.php">Sign Out</a></li>
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
                <button class="tab-button transform-button round <? if ($referencedName === null) echo "selected" ?>" data-id="Current-Payments" data-title="Current Payments">
                    <div class="split">
                        <div class="text-right">
                            <p>View Payments</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>
                <button class="tab-button transform-button round <? if ($referencedName !== null) echo "selected" ?>" data-id="New-Payment" data-title="New Payment">
                    <div class="split">
                        <div class="text-right">
                            <p>New Payment</p>
                        </div>
       		            <div class="toggle-button">
        		            <i class="fas fa-chevron-right"></i>
        		        </div>
                    </div>
		        </button>            
		    </div>
            <div class="list sub">
                <div class="">
                    <h2 id="title">Payments</h2>
                </div>
                <div id="Current-Payments" class="<? if ($referencedName !== null) echo "hidden" ?>">
                    <p class="info">Current payments</p><br>
                    <div class="">
                        <hr>
        	            <?
        	            
        	            $recentAccount = "Checking"; // temp
        	            
                        for ($n = 1; $n <= $amountOfPayments; $n++) {
                            echo "<button onClick=\"showPopUp('view-payment-popup-content')\" class=\"highlight-button transform-button split round\">
                                    <div class=\"list-padded\">
                                        <h3 class=\"bold\">Payment $n</h3>
                                        <p>$lastVisit<p>
                                    </div>
                                    <div class=\"split animate-left\">
                                        <div class=\"list-padded text-right\">
                                            <h3>$.00</h3>
                                            <p>Payment</p>
                                        </div>
                       		            <div class=\"toggle-button\">
                        		            <i class=\"fas fa-chevron-right\"></i>
                        		        </div>
                                    </div>
                                  </button>
                                  <hr>";
                        }
        
                        ?>
                    </div>
                </div>
                <div id="New-Payment" class="<? if ($referencedName === null) echo "hidden" ?>">
                    <p class="info">Start a new payment</p><br>
                    <form id="filterDate">
                        <label for="input-sender" class="info">From</label>
        	            <div class="form-item">
                            <select id="input-sender" class="input-field">
                                
                            </select>
        	            </div>
                        <label for="input-reciever" class="info">To</label>
        	            <div class="form-item">
                            <select id="input-reciever" class="input-field">
                            </select>
        	            </div>
        	            <hr>
                        <label for="input-date" class="info">Date</label>
        	            <div class="form-item">
                            <input id="input-date" type="date" class="input-field">
        	            </div>
        	            <hr>
                        <label for="input-amount" class="info">Amount</label>
        	            <div class="form-item">
                            <input id="input-amount" type="number" class="input-field">
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
            </div>
            <div class="list mini">          
    	    </div>
        </div>
        <div id="pop-up" class="pop-up">
            <div onClick="hidePopUp()" class="flex-center-item">
            </div>
            <div id="pup-up-element" class="pop-up-content fixed-sub round hidden">
                <div class="split">
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
                    <button onClick="hidePopUp()" class="expand-button transform-button extend-left round">
    	                <div class="split">
    	                    <div class="animate-left">
            		            <div class="toggle-button">
            		                <p class="expanded-info">Remove Payment</p>
            		            </div>
        		            </div>
    	                    <p class="condensed-info"><i class="fas fa-trash-alt"></i></p>
    	                </div>
    	            </button>
	            </div>
	            <br>
	            <div id="add-payment-popup-content" class="pop-up-item hidden">
                <h2 id="title">New Payment</h2>
                </div>
                <div id="view-payment-popup-content" class="pop-up-item hidden">
                    <h2 id="title">Payment</h2>
                    <p class="info"></p>
                    <div class="container">
                        <b class="info">Stuff</b>
                        <p id="account-name"><? echo $currentAccountName ?></p>
                    </div>
                    <div class="container">
                        <b class="info">Stuff</b>
                        <p id="account-balance"><? echo $balance ?></p>
                    </div>
                    <div class="container">
                        <b class="info">Stuff</b>
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
                                <p class="animate-left">Edit<p>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
	</body>
	<script type="text/javascript" src="../Scripts/navigation.js">
	</script>
	<script type="text/javascript" src="../Scripts/tabs.js"></script>
	<script type="text/javascript">
        function showPopUp(ContentId) {
            document.querySelectorAll(".pop-up-item").forEach((element) => {
                if (element.id === ContentId) {
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
</html>
