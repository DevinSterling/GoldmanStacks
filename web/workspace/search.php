<?
/* PHP external files */
require_once('/home/sterlid2/Private/sysNotification.php');

$amountOfUsers = 40

?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title>Search Users</title>
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
				<li class="menulogo"><a href="manage.php">TempBank</a></li>
                <li class="menutoggle"><a href="#"><i class="fas fa-bars"></i></a></li>
				<li class="menuitem"><a href="manage.php">Manage</a></li>
				<li class="menuitem"><a href="search.php">Search</a></li>
			</ul>
			<ul class="menugroup">
				<li class="menuitem"><a href="staff/options.php">Options</a></li>
				<li class="menuitem"><a href="../login.php">Sign Out</a></li>
			</ul>
		</nav>
		<div class="sys-notification">
		    <p>Logged as Employee</p>
		</div>
		<? notification(); ?>
		<div class="container flex-center">
		    <div class="list main">
		        <div class="container">
    		        <h2 id="title">Manage Users</h2>
    		        <p class="info">Available Users</p>
    		    </div>
                <table id="users" class="responsive-table">
                    <thead>
	                    <tr>
	                        <th>User Type</th>
	                        <th>Username</th>
	                        <th>Balance</th>
	                    </tr>
                    </thead>
                    <tbody>
		            <?
	                for ($n = 1; $n <= $amountOfUsers; $n++) {
	                    echo "<tr onClick=\"showPopUp('user-details-popup-content', this)\">
	                            <td data-label=\"User Type\">User</td>
	                            <td data-label=\"Username\">User$n</td>
	                            <td data-label=\"Balance\">-/+\$1000.00</td>
	                        </tr>";
	                }
		            ?>
		            </tbody>
	            </table>
		    </div>
		    <div class="list fixed-sub">
		        <div class="container fixed-sub fixed-item">
		            <div >
		                <label class="banner-text">Search</label>
		            </div>
		            <hr>
		            <form id="search-user" >
        	            <label for="search-input">Enter User Details</label>
        	            <div class="form-item">
        	                <input id="search-input" type="text" class="input-field">
        	            </div>
                        <div class="form-item">
        	                <button form="search-users" class="standard-button transform-button flex-center round">
                                <div class="split">
                                    <p class="animate-left">Search<p>
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
	            <div id="user-details-popup-content" class="pop-up-item hidden">
                    <h2 id="title">User Details</h2>
                    <p class="info">Username</p>
                    <p id="user-username">Test User 123</p><br>
                    <p class="info">User Role</p>
                    <p id="user-role">Standard User</p><br>
                    <p class="info">User Balance</p>
                    <p id="user-total-balance">$0.00</p><br>
                    <div class="form-item">
                        <button onClick="hidePopUp()" class="standard-button transform-button flex-center round">
                            <div class="split">
                                <p class="animate-left">View Transction History<p>
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
</html>
