<!--
        This is a PHP file for Corvin site which is called by each user's userhub page to handle renaming files in the current directory.

        Hierarchy

            0	Expire Session
            1	Header
            2	Rename Function

        Variables

            User				-	first name of user, inputted through hidden html form
            UserLastName		-	last name of user, inputted through hidden html form
            Current Directory	-	location of user's root folder
            OldName				-	file to be renamed's current name
            NewName				-	file to be renamed's desired new name
            OldNameFullPath		-	location of the file with its old name
            NewNameFullPath		-	location of the file with its new name


        Last updated: April 29, 2017

        Coded by: Joel N. Johnson
-->

<!-- 0 Expire Session -->
<?php

ini_set("display_errors", 1);															/* Display any errors									*/
error_reporting(E_ALL);																	/* And be verbose about it								*/

session_start();

$User = filter_input(INPUT_POST, "User", FILTER_SANITIZE_STRING);
$UserLastName = filter_input(INPUT_POST, "UserLastName", FILTER_SANITIZE_STRING);
$CurrentPathString = filter_input(INPUT_POST, "CurrentPathString", FILTER_SANITIZE_STRING);

$UserPage = strtolower($User . $UserLastName) . ".php";

// Session Timeout after 15 Minutes
if (isset($_SESSION['LastActivity']) && (time() - $_SESSION['LastActivity'] > 894))		/* If last request was more than 30 minutes ago 1800	*/
{
	header("Location: login.php");														/* and kick the user back to the login screan			*/
}

$_SESSION['LastActivity'] = time();														/* Update last activity time stamp						*/

// Regenerate Session ID every 20 Minutes
if (!isset($_SESSION['Created']))														/* If session started timestamp is not set				*/
{
    $_SESSION['Created'] = time();														/* Then set the session start time to now				*/
}
else if (time() - $_SESSION['Created'] > 1200)											/* If session started more than 30 minutes ago			*/
{
    session_regenerate_id(true);														/* Then change session ID for the current session		*/
																						/*  and invalidate old session ID						*/
    $_SESSION['Created'] = time();														/* Update creation time									*/
}
?>
<!DOCTYPE html>
<html>

<!-- 1 Header -->
<head>
    <title>Corvin Castle</title>
    <link href="index.css" type="text/css" rel="stylesheet" />
    <link rel="apple-touch-icon" sizes="57x57" href="/Images/Favicon/apple-icon-57x57.png" />
    <link rel="apple-touch-icon" sizes="60x60" href="/Images/Favicon/apple-icon-60x60.png" />
    <link rel="apple-touch-icon" sizes="72x72" href="/Images/Favicon/apple-icon-72x72.png" />
    <link rel="apple-touch-icon" sizes="76x76" href="/Images/Favicon/apple-icon-76x76.png" />
    <link rel="apple-touch-icon" sizes="114x114" href="/Images/Favicon/apple-icon-114x114.png" />
    <link rel="apple-touch-icon" sizes="120x120" href="/Images/Favicon/apple-icon-120x120.png" />
    <link rel="apple-touch-icon" sizes="144x144" href="/Images/Favicon/apple-icon-144x144.png" />
    <link rel="apple-touch-icon" sizes="152x152" href="/Images/Favicon/apple-icon-152x152.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="/Images/Favicon/apple-icon-180x180.png" />
    <link rel="icon" type="image/png" sizes="192x192" href="/Images/Favicon/android-icon-192x192.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="/Images/Favicon/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="96x96" href="/Images/Favicon/favicon-96x96.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="/Images/Favicon/favicon-16x16.png" />
    <link rel="manifest" href="/manifest.json" />
    <meta name="msapplication-TileColor" content="#ffffff" />
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png" />
    <meta name="theme-color" content="#ffffff" />
    <meta http-equiv="refresh" content="284" />
</head>

<body>

<!-- 2 Rename Function -->
<?php
ini_set("display_errors", 1);																/* Display any errors							*/
error_reporting(E_ALL);																		/* And be verbose about it						*/

$CurrentDirectory = "/../../home/joel/Castle/" . $User . "/" . $CurrentPathString;			/* Assign path of current directory				*/
$OldName = filter_input(INPUT_POST, "oldName", FILTER_SANITIZE_STRING);						/* Assign the old name to a variable			*/
$NewName = filter_input(INPUT_POST, "newName", FILTER_SANITIZE_STRING);						/* Assign the new name to a variable			*/
$OldNameFullPath = $CurrentDirectory . $OldName;											/* Assign full path and old name to a variable  */
$NewNameFullPath = $CurrentDirectory . $NewName;											/* Assign full path and new name to a variable	*/
if (is_readable($OldNameFullPath))															/* If the old path and file name is readable	*/
{
	if (rename($OldNameFullPath, $NewNameFullPath))											/* Then rename the file to the new name and if	*/
																							/*  that was successful							*/
	{
		echo "" . $OldName . " is now renamed to " . $NewName . ".";						/* Then print success statement					*/
	}
	else																					/* Else, if the rename failed					*/
	{
		echo "There was a problem renaming the file.";										/* Then print rename failure statement			*/
	}
}
else																						/* Else, if the old path and/or file name is	*/
																							/*  not readable								*/
{
	echo "There was a problem reading the file or file name.";								/* Then print read failure statement			*/
}

echo "<br /><br />";
echo "<form method = 'get' action = '" . $UserPage . "' />";							/* Button to return to index.php				*/
echo "<input type = 'submit' value = 'Return' />";
echo "<input type = 'hidden' value = '" . $CurrentPathString . "' name = 'ReturnPathString' />";
echo "</form>";
?>
</body>
</html>