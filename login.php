<!--
		This is the a PHP file for NanoLab that acts as the login page for all users. Upon submitting login
		credentials, it routs to validate.php to verify credentials and route to either the user's page or
		back to login.php if incorrect credentials.


		Last updated: August 16, 2017

		Coded by: Joel N. Johnson
-->

<!DOCTYPE html>
<html>

<!-- 0 Header -->
<head>
	<title>Corvin</title>
	<link href = "login.css" type = "text/css" rel = "stylesheet" />
    <link rel="apple-touch-icon" sizes="57x57" href="/Art/Favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/Art/Favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/Art/Favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/Art/Favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/Art/Favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/Art/Favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/Art/Favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/Art/Favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/Art/Favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/Art/Favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/Art/Favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/Art/Favicon/favicon-96x96.png">


    <link rel="icon" type="image/png" sizes="16x16" href="/Art/Favicon/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
</head>

<body>

<!-- 1 Top Bar -->
<div class = "TopBar">
    <div class = "Corvin">
        <!--<img class = "Corvin" src="/Art/Corvin Castle.PNG" alt="Corvin Castle" />-->
		<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
	Corvin
    </div>
	<!--<div class = "User">
        <p class = "User">New User</p>
    </div>-->
</div>

<br /><br /><br /><br /><br /><br /><br /><br />

<!-- 2 Login -->
<div id = "Login">

	<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
	<br /><br /><br /><br /><br /><br /><br />

	<form action = "validate.php" method = "post" enctype = "multipart/form-data">
		Username:
		<input type = "text" name = "username" style = "width: 300px"/>
		<br /><br />
		Password:
		<input type = "password" name = "password" style = "width: 300px"/>
		<br /><br />
		<input type = "submit" value = "Login" class = "LoginButton" name = "submit" />
	</form>

	<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
	<br /><br /><br /><br /><br /><br /><br />

</div>

<br /><br /><br /><br /><br /><br /><br />

<!-- 3 Footer -->
<div id = "Footer">&copy; Joel N. Johnson</div>

</body>
</html>






