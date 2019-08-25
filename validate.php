<!--
        This is a php file for Corvin that is called by index.html to handle
        user login validation and rerouting to the user's home page.

        Variables

            UserInfo    -	MySQLi database with user info
            _SESSION	-   global variable that allows for a user's id to
                            be set to the session that has been started;
                            verifies that the user has logged in. Their
                            main page will not be accessible and just
                            reroute to cor.vin unless they have logged in
                            with their correct username and password and
                            thus their session id has been set.

        Last updated: August 15, 2019

        Coded by: Joel N. Johnson
-->

<?php

// Display any errors
ini_set("display_errors", 1);

// And be verbose about it
error_reporting(E_ALL);

session_start();

//MYSQLi server connection
$conn = mysqli_connect("127.0.0.1", "joel", "Daytona675");

//Check if connected to MYSQLI server
if (!$conn) {
    echo("Failed to connect to database: " .
            mysqli_connect_error()) . "<br /><br />";
}

//Go into Corvin database
mysqli_query($conn, "USE Corvin;");

//Assign submitted username
$username = $_POST["username"];

//Map mysqli column data to array
$allUsernames = array();
$sql = "SELECT DISTINCT username FROM UserInfo;";
$columnData = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_array($columnData)) {
    $allUsernames[] = $row[0];
}

//Check if username exists in database
if (in_array($username, $allUsernames)) {

    //Check if password is correct
    $sql = "SELECT password FROM UserInfo WHERE username = '$username'";
    $hashedReferencePassword = mysqli_fetch_row(mysqli_query($conn, $sql));
    if (password_verify($_POST["password"], $hashedReferencePassword[0])) {

        //Get user's ID (starts at 1 and autoincrements for each new user)
        $sql = "SELECT id FROM UserInfo WHERE username = '$username';";
        $userID = mysqli_fetch_row(mysqli_query($conn, $sql));

        //Start user's session
        $_SESSION["loginUser"] = TRUE;
        $_SESSION["userID"] = $userID[0];

        //Redirect to the user's main page
        echo "<meta http-equiv='Refresh' content='0; url = home.php'>";
    }
    else {
        echo "Incorrect password.";
        // Button to return to login.php, which php-redirects to cor.vin
        echo "<br /><br /><form method = 'get' action = 'login.php' />";
        echo "<input type = 'submit' value = 'Return' />";
        echo "</form>";
    }
}
else {
    echo "Incorrect username.";
    // Button to return to login.php, which php-redirects to cor.vin
    echo "<br /><br /><form method = 'get' action = 'login.php' />";
    echo "<input type = 'submit' value = 'Return' />";
    echo "</form>";
}