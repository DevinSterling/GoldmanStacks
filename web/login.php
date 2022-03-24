<!DOCTYPE html>
<html>
    <head>
	<title>Sign In</title>
	<!-- Stylesheet -->
	<link rel="stylesheet" href="CSS/stylesheet.css">
	<!-- Favicon -->
	<link rel="icon" href="Images/logo.ico">
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
        <div class="flex-center-item">
            <div class="list container fixed-sub round">
                <div class="accent-border top-round">
                    <h2 class="big"><b>Goldman Stacks</b><h2>
                </div>
                <br>
                <form id="edit">
                    <label for="username" class="info">Username</label>
    	            <div class="form-item">
    		            <input id="username" class="input-field" type="text">
    	            </div>
    	            <label for="password" class="info">Password</label>
    	            <div class="form-item">
    		            <input id="password" class="input-field" type="password">
    	            </div>
                    
                    <div class="form-item">
                        <button form="edit" class="standard-button transform-button flex-center round">
                            <div class="split">
                                <p class="animate-left">Sign In<p>
               		        <div class="toggle-button">
                		    <i class="fas fa-chevron-right"></i>
                		</div>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
