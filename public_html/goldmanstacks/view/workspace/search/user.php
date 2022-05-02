<?php
require_once('../../../../../private/sysNotification.php');
require_once('../../../../../private/userbase.php');
require_once('../../../../../private/config.php');

forceHTTPS(); // Force https connection
session_start(); // Start Session
checkEmployeeStatus(); // Check if the employee is signed in

/* SESSION Variables */
$key = $_SESSION['key'];

/* GET Variables */
$searchTerm = $_GET['q'];

/* Variables */
$users = array();
$isSearchFiltered = !empty($searchTerm);

/* CSRF tokens */
$removeUserToken = hash_hmac('sha256', '/removeUser.php', $key);

/* Database Connection */
$db = getUpdateConnection();

if ($db === null) {
    header("Location: ");
    die();
}

/* Get results */
$query = "SELECT userID, userRole, email, firstName, lastName, (
        SELECT IFNULL(SUM(balance), 0) 
            FROM accountDirectory 
            WHERE clientID=userID
        ) AS balance 
        FROM users";
                    
/* Search Interpreter */
if ($isSearchFiltered) {
    $command = explode(":", $_GET['q']);
    
    $term = strtolower(preg_replace('/ /', '', $command[0]));
    $value = trim($command[1]);
    
    switch ($term) {
        case 'user':
        case 'type':
            /* Filter by user role */
            $value = "%$value%";
            
            $statement = $db->prepare($query . " WHERE userRole LIKE ?");
            $statement->bind_param("s", $value);
            
            break;
        case 'username':
        case 'email':
            /* Filter by username/email */
            $value = "%$value%";
            
            $statement = $db->prepare($query . " WHERE email LIKE ?");
            $statement->bind_param("s", $value);
            
            break;
        case 'firstname':
        case 'lastname':
        case 'name':
            /* Filter by name */
            $value = "%$value%";

            $statement = $db->prepare($query . " WHERE firstName LIKE ? OR lastName LIKE ?");
            $statement->bind_param("ss", $value, $value);
            
            break;
        case 'balance':
            /* Filter by balance */
            $value2 = trim(preg_replace('/,/', '', $command[2]));
            
            $queryExtension = " WHERE (
                                    SELECT IFNULL(SUM(balance), 0) 
                                    FROM accountDirectory 
                                    WHERE clientID=userID
                                )";
            
            if (!is_numeric($value2)) {
                $isSearchFiltered = false;
                break;
            }
            
            /* Determine operator */
            switch ($value) {
                case '>':
                    $queryExtension .= ">?";
                    break;
                case '<':
                    $queryExtension .= "<?";
                    break;
                case '=':
                    $queryExtension .= "=?";
                    break;
                case '>=':
                    $queryExtension .= ">=?";
                    break;
                case '<=':
                    $queryExtension .= "<=?";
                    break;
                default:
                    $isSearchFiltered = false;
            }
            
            /* Confirm user input passes operator check */
            if ($isSearchFiltered) {
                $statement = $db->prepare($query . $queryExtension);
                $statement->bind_param("d", $value2);
            }
            
            break;
        default:
            $isSearchFiltered = false;
    }
}

if (!$isSearchFiltered) $statement = $db->prepare($query);

$statement->execute();
$result = $statement->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);

$searchResults = count($users); // For use only when a search is initiated

$result->free();
$statement->close();
$db->close();
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
	<title>Manage Userbase</title>
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
				<li class="menuitem"><a href="#">Search</a></li>
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
    		        <h2 id="title">Manage Userbase</h2>
    		        <?php
    		        if ($isSearchFiltered) {
    		            echo "<a href=\"user\" class=\"expand-button transform-button extend-left round shadow\">
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
	                        <th>User Type</th>
	                        <th>Username</th>
	                        <th>First Name</th>
	                        <th>Last Name</th>
	                        <th>Balance</th>
	                    </tr>
                    </thead>
                    <tbody>
		            <?php
	                foreach ($users as $user) {
	                    echo "<tr id=\"" . $user['userID'] . "\" onClick=\"showPopUp('request-details-popup-content', this)\">
	                            <td data-label=\"User Type\">" . ucfirst($user['userRole']) . "</td>
	                            <td data-label=\"Username\">" . $user['email'] . "</td>
	                            <td data-label=\"First Name\">" . $user['firstName'] . "</td>
	                            <td data-label=\"Last Name\">" . $user['lastName'] . "</td>
	                            <td data-label=\"Balance\">" . ($user['userRole'] === 'client' ? '$' . number_format($user['balance'], 2) : '...') . "</td>
	                        </tr>";
	                }
		            ?>
		            </tbody>
	            </table>
		    </div>
		    <div class="list fixed-sub">
		        <div class="container fixed-sub fixed-item">
		            <label class="banner-text">Search Users</label>
		            <hr>
		            <form method="get" class="flex-form margin-bottom">
        	            <label for="search-input">Enter User Details</label>
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
		            <hr>
    		        <a href="registration" class="highlight-button transform-button split round">
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
                        <h2 id="title"><span id="user-type"></span> Details</h2>
                        <div id="user-info">
                            <p class="info">Client</p>
                            <p><span id="email"></span> (<span id="first-name"></span> <span id="last-name"></span>)</p>
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
        		        <button id="remove-button" type="button" class="highlight-button transform-button split round">
                            <div class="list">
                                <p class="banner-text"><i class="fas fa-times text-center icon"></i> Remove User</p>
                            </div>
                            <div class="animate-left">
                	            <div class="toggle-button">
                	                <i class="fas fa-chevron-right"></i>
                	            </div>
                            </div>
        		        </button>
                    </div>
                </div>
                <div id="remove-client-popup-content" class="pop-up-item flex-form hidden">
                    <h2 id="title">Confirm User Removal</h2>
                    <div id="approve-request-info" class="margin-bottom">
                    </div>
                    <p class="info">Confirmation will <b>remove</b> the above user</p>
                    <button id="confirm-removal" type="button" class="standard-button transform-button flex-center round">
                        <div class="split">
                            <p class="animate-left">Confirm Approval<p>
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
	    
	    /* Selected Content */
	    let selectedRow = null;
	    
	    /* Event Listeners */
	    document.getElementById('user-details-button').addEventListener('click', () => {
	        window.location.href = '../manage/user?id=' + selectedRow.id;
	    });
	    document.getElementById('remove-button').addEventListener('click', () => {
	        document.getElementById('approve-request-info').innerHTML = document.getElementById('user-info').innerHTML;
	        
	        showPopUp('remove-client-popup-content');
	    });
	    document.getElementById('confirm-removal').addEventListener('click', () => {
            let formData = new FormData();
            formData.append('id', selectedRow.id);
            formData.append('token', <?php echo "'$removeUserToken'"?>);
            
            submitForm('../../../requests/workspace/user/removeUser', formData);
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
                	        
                	        document.getElementById('user-type').textContent = item[0].textContent;
                	        document.getElementById('email').textContent = item[1].textContent;
                	        document.getElementById('first-name').textContent = item[2].textContent;
                	        document.getElementById('last-name').textContent = item[3].textContent;
                	        document.getElementById('balance').textContent = item[4].textContent;
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
