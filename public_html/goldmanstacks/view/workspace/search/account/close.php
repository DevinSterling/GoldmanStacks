<?php
require_once('../../../../../../private/sysNotification.php');
require_once('../../../../../../private/userbase.php');
require_once('../../../../../../private/config.php');
require_once('../../../../../../private/userbase.php');
require_once('../../../../../../private/functions.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkEmployeeStatus(); // Check if the employee is signed in

/* SESSION Variables */
$key = $_SESSION['key'];

/* POST Variables */
$searchTerm = $_POST['q'];

/* Variables */
$searchResults = 99; // For use only when a search is initiated

/* CSRF tokens */
$approveCloseAccountToken = hash_hmac('sha256', '/approveCloseRequest.php', $key);
$rejectCloseAccountToken = hash_hmac('sha256', '/rejectCloseRequest.php', $key);

/* Database Connection */
$db = getUpdateConnection();

if ($db === null) {
    header("Location: ");
    die();
}
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
	<title>Manage Close Account Requests</title>
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
				<li class="menuitem"><a href="#">Search</a></li>
			</ul>
			<ul class="menugroup">
				<li class="menuitem"><a href="../../staff/options">Options</a></li>
				<li class="menuitem"><a href="../../../../requests/signout">Sign Out</a></li>
			</ul>
		</nav>
		<div class="sys-notification">
		    <p>Logged as Employee</p>
		</div>
		<?php notification(); ?>
    	<button id="notification" type="button" onClick="hideNotification()" class="notification main success transform-button round collapse">
            <p><i id="notification-icon" class="fas fa-check icon"></i><span id="notification-text"></span></p>
            <div class="split">
                   <div class="toggle-button">
    	            <i class="fas fa-times"></i>
    	        </div>
            </div>
        </button>
		<div class="container flex-center">
		    <div class="list main">
		        <div class="container split">
    		        <h2 id="title">Manage Close Account Requests</h2>
    		        <?php
    		        if (!empty($searchTerm)) {
    		            echo "<p class=\"info\">$searchResults results for <b>$searchTerm</b></p>";
    		        }
    		        ?>
    		    </div>
                <table id="users" class="responsive-table">
                    <thead>
	                    <tr>
	                        <th>Request Date</th>
	                        <th>Account Number</th>
	                        <th>Balance</th>
	                        <th>Account Type</th>
	                        <th>Client</th>
	                        <th>First Name</th>
	                        <th>Last Name</th>
	                    </tr>
                    </thead>
                    <tbody>
		            <?php
		            $result = $db->query("SELECT userID, C.accountNum, C.requestDate, A.accountType, A.balance, email, firstName, lastName FROM users
                                            INNER JOIN accountDirectory A ON userID=A.clientID
                                            INNER JOIN accountCloseRequests C ON A.accountNum=C.accountNum");
		            $users = $result->fetch_all(MYSQLI_ASSOC);
		            
	                foreach ($users as $user) {
	                    echo "<tr id=\"" . encrypt($user['accountNum'], $key) . "\" onClick=\"showPopUp('request-details-popup-content', this)\">
	                            <td data-label=\"Request Date\">" . $user['requestDate'] . "</td>
	                            <td data-label=\"Account Number\">(*" . substr($user['accountNum'], -4) . ")</td>
	                            <td data-label=\"Balance\">$" . number_format($user['balance'], 2) . "</td>
	                            <td data-label=\"Account Type\">" . ucfirst($user['accountType']) . "</td>
	                            <td data-label=\"Client\">" . $user['email'] . "</td>
	                            <td data-label=\"First Name\">" . $user['firstName'] . "</td>
	                            <td data-label=\"Last Name\">" . $user['lastName'] . "</td>
	                            <td class=\"hidden\">". $user['userID'] . "<td>
	                        </tr>";
	                }
	                
	                $db->close();
		            ?>
		            </tbody>
	            </table>
		    </div>
		    <div class="list fixed-sub">
		        <div class="container fixed-sub fixed-item">
		            <label class="banner-text">Search Requests</label>
		            <hr>
		            <form method="get" class="flex-form margin-bottom">
        	            <label for="search-input">Enter Request Details</label>
    	                <input id="search-input" type="text" name="q" class="input-field" required>
    	                <button type="submit" class="standard-button small-gap transform-button flex-center round">
                            <div class="split">
                                <p class="animate-left">Search<p>
               		            <div class="toggle-button">
                		            <i class="fas fa-chevron-right"></i>
                		        </div>
                            </div>
    	                </button>
		            </form>
		            <label class="banner-text">Management</label>
		            <hr>
		            <a href="open" class="highlight-button transform-button split round">
                        <div class="list">
                            <p class="banner-text">Manage Open Requests</p>
                            <small>Bank Account Management</small>
                        </div>
                        <div class="animate-left">
            	            <div class="toggle-button">
            	                <i class="fas fa-chevron-right"></i>
            	            </div>
                        </div>
    		        </a>
    		        <hr>
    		        <a href="../registration" class="highlight-button transform-button split round">
                        <div class="list">
                            <p class="banner-text">Manage Registration Requests</p>
                            <small>Registration Management</small>
                        </div>
                        <div class="animate-left">
            	            <div class="toggle-button">
            	                <i class="fas fa-chevron-right"></i>
            	            </div>
                        </div>
    		        </a>
    		        <hr>
    		        <a href="../user" class="highlight-button transform-button split round">
                        <div class="list">
                            <p class="banner-text">Manage Userbase</p>
                            <small>User Management</small>
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
        <div id="pop-up" class="pop-up">
            <div onClick="hidePopUp()" class="flex-center-item">
            </div>
            <div id="pup-up-element" class="pop-up-content sub round hidden">
                <button id="return-button" class="expand-button transform-button extend-right round">
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
	            <div id="request-details-popup-content" class="pop-up-item split hidden">
	                <div class="list main flex-form">
                        <h2 id="title">Request Details</h2>
                        <div id="request-info">
                            <p class="info">Client</p>
                            <p id="client-info"><span id="email"></span> (<span id="first-name"></span> <span id="last-name"></span>)</p>
                            <p class="info">Request Date</p>
                            <p id="request-date"></p>
                            <p class="info">Requested Account</p>
                            <p><span id="account-number"></span> (<span id="account-type"></span>)</p>
                            <p class="info">Balance</p>
                            <p id="balance"></p>
                        </div>
                    </div>
                    <hr>
                    <div class="list main">
        		        <button id="user-details-button" type="button" class="highlight-button transform-button split round">
                            <div class="list">
                                <p class="banner-text"><i class="fas fa-info-circle text-center icon"></i> View Client Details</p>
                            </div>
                            <div class="animate-left">
                	            <div class="toggle-button">
                	                <i class="fas fa-chevron-right"></i>
                	            </div>
                            </div>
        		        </button>
                        <hr>
        		        <button id="approve-button" type="button" class="highlight-button transform-button split round">
                            <div class="list">
                                <p class="banner-text"><i class="fas fa-check text-center icon"></i> Approve Request</p>
                            </div>
                            <div class="animate-left">
                	            <div class="toggle-button">
                	                <i class="fas fa-chevron-right"></i>
                	            </div>
                            </div>
        		        </button>
        		        <button id="reject-button" type="button" class="highlight-button transform-button split round">
                            <div class="list">
                                <p class="banner-text"><i class="fas fa-times text-center icon"></i> Reject Request</p>
                            </div>
                            <div class="animate-left">
                	            <div class="toggle-button">
                	                <i class="fas fa-chevron-right"></i>
                	            </div>
                            </div>
        		        </button>
                    </div>
                </div>
                <div id="approve-request-popup-content" class="pop-up-item flex-form hidden">
                    <h2 id="title">Confirm Request Approval</h2>
                    <div id="approve-request-info" class="margin-bottom">
                    </div>
                    <p class="info">Confirmation of this request will <b>close</b> the above account</p>
                    <button id="confirm-approval" type="button" class="standard-button transform-button flex-center round">
                        <div class="split">
                            <p class="animate-left">Confirm Approval<p>
           		            <div class="toggle-button">
            		            <i class="fas fa-chevron-right"></i>
            		        </div>
                        </div>
                    </button>                
                </div>
                <div id="reject-request-popup-content" class="pop-up-item flex-form hidden">
                    <h2 id="title">Confirm Request Rejection</h2>
                    <div id="reject-request-info" class="margin-bottom">
                    </div>
                    <p class="info">Confirmation of this request will <b>not</b> close the above account</p>
                    <button id="confirm-rejection" type="button" class="standard-button transform-button flex-center round">
                        <div class="split">
                            <p class="animate-left">Confirm Rejection<p>
           		            <div class="toggle-button">
            		            <i class="fas fa-chevron-right"></i>
            		        </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
	</body>
	<script type="text/javascript" src="../../../../js/navigation.js"></script>
	<script type="text/javascript" src="../../../../js/notification.js"></script>
	<script type="text/javascript" src="../../../../js/post.js"></script>
	<script type="text/javascript">
	    /* PopUp Buttons */
	    const popupReturnButton = document.getElementById('return-button');
	    
	    /* PopUp Contents */
	    const popupRequestInfo = document.getElementById('request-info');
	    
	    /* Selected Content */
	    let selectedRow = null;
	    let selectedClient = null;
	    
	    /* Event Listeners */
	    document.getElementById('user-details-button').addEventListener('click', () => {
	        window.location.href = '../../manage/user?id=' + selectedClient;
	    });
	    document.getElementById('approve-button').addEventListener('click', () => {
	        document.getElementById('approve-request-info').innerHTML = popupRequestInfo.innerHTML;
	        
	        showPopUp('approve-request-popup-content');
	    });
	    document.getElementById('reject-button').addEventListener('click', () => {
	        document.getElementById('reject-request-info').innerHTML = popupRequestInfo.innerHTML;
	        
	        showPopUp('reject-request-popup-content');
	    });
	    document.getElementById('confirm-approval').addEventListener('click', () => {
            let formData = new FormData();
            formData.append('id', selectedRow.id);
            formData.append('token', <?php echo "'$approveCloseAccountToken'"?>);
            
            submitForm('../../../../requests/workspace/account/approveCloseRequest', formData);
	    });
	    document.getElementById('confirm-rejection').addEventListener('click', () => {
            let formData = new FormData();
            formData.append('id', selectedRow.id);
            formData.append('token', <?php echo "'$rejectCloseAccountToken'"?>);
            
            submitForm('../../../../requests/workspace/account/rejectCloseRequest', formData);
	    });
	    
	    async function submitForm(url, formData) {
	        let json = await getJson(url, formData);
	        
	        if (!isEmptyJson()) {
	            if (json.response) {
	                setSuccessNotification(json.message);
	                selectedRow.remove();
	            } else {
	                setFailNotification(json.message);
	            }
	        } else {
	            setFailNotification('Failed to retrieve details');
	        }
	        
	        hidePopUp();
	        showNotification();
	    }
	    
        function showPopUp(contentId, entity = null) {
            if (contentId !== 'request-details-popup-content') {
                popupReturnButton.onclick = function() { showPopUp('request-details-popup-content'); }
            } else {
                popupReturnButton.onclick = function() { hidePopUp(); }
            }
            
            document.querySelectorAll(".pop-up-item").forEach((element) => {
                if (element.id === contentId) {
                    if (contentId === 'request-details-popup-content') {
                        document.getElementById("pup-up-element").classList.remove('fixed-sub');
                        document.getElementById("pup-up-element").classList.add('sub');
                        
            	        if (entity !== null) {
            	            selectedRow = entity;
            	            
                	        var item = entity.children;
                	        
                	        document.getElementById('balance').textContent = item[2].textContent;
                	        document.getElementById('email').textContent = item[4].textContent;
                	        document.getElementById('first-name').textContent = item[5].textContent;
                	        document.getElementById('last-name').textContent = item[6].textContent;
                	        document.getElementById('request-date').textContent = item[0].textContent;
                	        document.getElementById('account-number').textContent = item[1].textContent;
                	        document.getElementById('account-type').textContent = item[3].textContent;
                	        
                	        selectedClient = item[7].textContent;
            	        }
                    } else {
                        document.getElementById("pup-up-element").classList.remove('sub');
                        document.getElementById("pup-up-element").classList.add('fixed-sub');
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
</html>
