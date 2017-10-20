<!DOCTYPE html>
<html>

<!-- 0 Header -->
<head>
	<title>Corvin Castle</title>
	<link href = "login.css" type = "text/css" rel = "stylesheet" />
</head>

<body>

<!-- 1 Top Bar -->
<div class = "TopBar">
    <div class = "CorvinCastle">
        <img src="Corvin Castle.png" alt="Corvin Castle" style="width: 500px;padding-left:70%">
    </div>
    <div class = "User">
        <p class = "User">New User</p>
    </div>
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
		<input type = "submit" value = "Login" name = "submit" />
	</form>

	<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
	<br /><br /><br /><br /><br /><br /><br />

</div>

<br /><br /><br /><br /><br /><br /><br />

<!-- 3 Footer -->
<div id = "Footer">&copy; Joel N. Johnson</div>

</body>
</html>






