<?php
require_once('../../../../../private/sysNotification.php');
require_once('../../../../../private/userbase.php');
require_once('../../../../../private/config.php');
require_once('../../../../../private/functions.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkEmployeeStatus(); // Check if the employee is signed in

/* SESSION Variables */
$key = $_SESSION['key'];

/* GET Variables */
$searchTerm = $_GET['q'];

/* Variables */
$requests = array();
$isSearchFiltered = !empty($searchTerm);

/* CSRF tokens */
$approveRegistrationToken = hash_hmac('sha256', '/approveRegistration.php', $key);
$rejectRegistrationToken = hash_hmac('sha256', '/rejectRegistration.php', $key);

/* Database Connection */
$db = getUpdateConnection();

if ($db === null) {
    header("Location: ");
    die();
}

/* Get results */
$query = "SELECT userID, lastSignin, userRole, email, firstName, lastName, phoneNumber FROM users
            INNER JOIN client ON userID=clientID 
            WHERE verified=0";

/* Search Interpreter */
if ($isSearchFiltered) {
    $command = explode(":", $_GET['q']);
    
    $term = strtolower(preg_replace('/ /', '', $command[0]));
    $value = trim($command[1]);
    
    switch ($term) {
        case 'date':
        case 'requestdate':
            /* Filter by date */
            $value2 = trim($command[2]);
            $hasRange = false;
            
            $queryExtension = " AND (lastSignin";
            
            if (strlen($value2) == 4) {
                $value2 = date("Y-m-d", strtotime($value2 . '-01-01'));
                $value3 = date("Y-m-d", strtotime($value2 . ' +1 year'));
            } 
            else if (strlen($value2) == 7) {
                $value2 = date("Y-m-d", strtotime($value2 . '-01'));
                $value3 = date("Y-m-d", strtotime($value2 . ' +1 month'));
            }
            else {
                $value2 = date("Y-m-d", strtotime($value2));
                $value3 = date("Y-m-d", strtotime($value2 . ' +1 day'));
            }
            
            /* Determine operator */
            switch ($value) {
                case '>':
                    $queryExtension .= ">?)";
                    break;
                case '<':
                    $queryExtension .= "<?)";
                    break;
                case '=':
                    $hasRange = true;
                    $queryExtension .= ">=? AND lastSignin<?)";
                    break;
                case '>=':
                    $queryExtension .= ">=?)";
                    break;
                case '<=':
                    $queryExtension .= "<=?)";
                    break;
                default:
                    $isSearchFiltered = false;
            }
            
            /* Confirm user input passes operator check */
            if ($isSearchFiltered) {
                $statement = $db->prepare($query . $queryExtension);
                
                if ($hasRange) {
                    $statement->bind_param("ss", $value2, $value3);
                }
                else {
                    $statement->bind_param("s", $value2);
                }
            }
            
            break;
        case 'client':
        case 'username':
        case 'email':
            /* Filter by username/email */
            $value = "%$value%";

            $statement = $db->prepare($query . " AND email LIKE ?");
            $statement->bind_param("s", $value);
            
            break;
        case 'firstname':
        case 'lastname':
        case 'name':
            /* Filter by name */
            $value = "%$value%";

            $statement = $db->prepare($query . " AND (firstName LIKE ? OR lastName LIKE ?)");
            $statement->bind_param("ss", $value, $value);
            
            break;
        case 'phone':
        case 'phonenumber':
        case 'number':
            /* Filter by phone */
            $value = "%$value%";

            $value = preg_replace('/-/', '', $value);
            $statement = $db->prepare($query . " AND phoneNumber LIKE ?");
            $statement->bind_param("s", $value);
            
            break;
        default:
            $isSearchFiltered = false;
    }
}

if (!$isSearchFiltered) $statement = $db->prepare($query);

$statement->execute();
$result = $statement->get_result();
$requests = $result->fetch_all(MYSQLI_ASSOC);

$searchResults = count($requests); // For use only when a search is initiated

$result->free();
$statement->close();
$db->close();
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
    	<title>Manage Registration Requests</title>
    	<!-- Stylesheet -->
    	<link rel="stylesheet" href="../../../css/stylesheet.css">
    	<!-- Favicon -->
    	<link rel="icon" href="../../../img/logo.ico">
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
				<li class="menulogo"><a href="../manager">Goldman Stacks</a></li>
                <li class="menutoggle"><a href="#"><i class="fas fa-bars"></i></a></li>
				<li class="menuitem"><a href="../manager">Manage</a></li>
			</ul>
			<ul class="menugroup">
				<li class="menuitem"><a href="../staff/options">Options</a></li>
				<li class="menuitem"><a href="../../../requests/signout">Sign Out</a></li>
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
    		        <h2 id="title">Manage Registration Requests</h2>
    		        <?php
    		        if ($isSearchFiltered) {
    		            echo "<a href=\"registration\" class=\"expand-button transform-button extend-left round shadow\">
    		                <div class=\"split\">
    		                    <div class=\"animate-left\">
                		            <div class=\"toggle-button\">
                		                <p class=\"info\">Clear</p>
                		            </div>
            		            </div>
    		                    <p class=\"condensed-info\"><p class=\"info\">$searchResults results for <b>$searchTerm</b></p></p>
    		                </div>
    		            </a>";
    		        }
    		        ?>
    		    </div>
                <table id="users" class="responsive-table">
                    <thead>
	                    <tr>
	                        <th>Request Date</th>
	                        <th>Username</th>
	                        <th>First Name</th>
	                        <th>Last Name</th>
	                        <th>Phone</th>
	                    </tr>
                    </thead>
                    <tbody>
		            <?php
    	                foreach ($requests as $request) {
    	                    echo "<tr id=\"" . $request['userID'] . "\" onClick=\"showPopUp('request-details-popup-content', this)\">
    	                            <td data-label=\"Request Date\">" . $request['lastSignin'] . "</td>
    	                            <td data-label=\"Username\">" . $request['email'] . "</td>
    	                            <td data-label=\"First Name\">" . $request['firstName'] . "</td>
    	                            <td data-label=\"Last Name\">" . $request['lastName'] . "</td>
    	                            <td data-label=\"Phone\">" . convertToPhoneNumber($request['phoneNumber']) . "</td>
    	                        </tr>";
    	                }
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
    		        <a href="user" class="highlight-button transform-button split round">
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
    		        <hr>
    		        <a href="account/open" class="highlight-button transform-button split round">
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
    		        <a href="account/close" class="highlight-button transform-button split round">
                        <div class="list">
                            <p class="banner-text">Manage Close Requests</p>
                            <small>Bank Account Management</small>
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
                            <p class="info">Phone</p>
                            <p id="client-phone"></p>
                            <p class="info">Request Date</p>
                            <p id="request-date"></p>
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
                    <p class="info">Confirmation of this request will <b>approve</b> registration of the above client</p>
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
                    <p class="info">Confirmation of this request will <b>reject</b> registration of the above client</p>
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
	<script type="text/javascript" src="../../../js/navigation.js"></script>
	<script type="text/javascript" src="../../../js/notification.js"></script>
	<script type="text/javascript" src="../../../js/post.js"></script>
	<script type="text/javascript">
	    /* PopUp Buttons */
	    const popupReturnButton = document.getElementById('return-button');
	    
	    /* PopUp Contents */
	    const popupRequestInfo = document.getElementById('request-info');
	    
	    /* Selected Content */
	    let selectedRow = null;
	    
	    /* Event Listeners */
	    document.getElementById('user-details-button').addEventListener('click', () => {
	        window.location.href = '../manage/user?id=' + selectedRow.id;
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
            formData.append('token', <?php echo "'$approveRegistrationToken'"?>);
            
            submitForm('../../../requests/workspace/user/client/registration/approveRegistration', formData);
	    });
	    document.getElementById('confirm-rejection').addEventListener('click', () => {
            let formData = new FormData();
            formData.append('id', selectedRow.id);
            formData.append('token', <?php echo "'$rejectRegistrationToken'"?>);
            
            submitForm('../../../requests/workspace/user/client/registration/rejectRegistration', formData);
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
                	        
                	        document.getElementById('email').textContent = item[1].textContent;
                	        document.getElementById('first-name').textContent = item[2].textContent;
                	        document.getElementById('last-name').textContent = item[3].textContent;
                	        document.getElementById('request-date').textContent = item[0].textContent;
                	        document.getElementById('client-phone').textContent = item[4].textContent;
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
