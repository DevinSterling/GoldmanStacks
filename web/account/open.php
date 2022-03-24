<?
/* PHP external files */
require_once('/home/sterlid2/Private/sysNotification.php');

?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title>Open New Account</title>
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
	</body>
	<script type="text/javascript" src="/~sterlid2/bank/Scripts/navigation.js">
	</script>
</html>