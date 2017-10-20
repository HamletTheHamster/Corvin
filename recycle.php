<!--
        This is a PHP file for Corvin site which is called by each user's userhub page to handle files or folders being deleted. It does not
        truly delete the files, but rather sends them to the user's hidden recycle folder [for later retrieval within some grace period of ~ 1
        month.]

		Hierarchy

			0	Expire Session
			1	Header
			2	Recycle Function

        Variables

            CurrentDirectory				-	full path to the current directory containing the file or folder to be recycled
            FileToRecycle					-	name of the file to be recycled; recieved from html form
            FileToRecycleFullPath			-	full path to the file, including file name and file type extension
            UserRecycleDirectory			-	full path to the user's hidden recycle folder
            UserRecycleDirectoryFullPath	-	full path to the recycled file in user's recycle folder, including file name and file type extension


        Last updated: 4-23-2017

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

    <meta http-equiv="refresh" content="60" />
</head>

<body>

<!-- 2 Recycle Function -->
<?php

$CurrentDirectory = "/../../home/joel/Castle/" . $User . "/" . $CurrentPathString;			/* Assign path of current directory				*/
$FileToRecycle = filter_input(INPUT_POST, "fileToRecycle", FILTER_SANITIZE_STRING);			/* Assign file name to recycle to variable		*/
$FileToRecycleFullPath = $CurrentDirectory . $FileToRecycle;								/* Assign full path plus name to variable		*/
$UserRecycleDirectory = "/../../home/joel/Castle/Recycle/" . $User . "/";					/* Assign user's recycle folder path			*/
$UserRecycleDirectoryFullPath = $UserRecycleDirectory . $FileToRecycle;						/* Assign recycled file name full path.			*/
if (is_readable($FileToRecycleFullPath))													/* If the full path and file is readable		*/
{
	if (rename($FileToRecycleFullPath, $UserRecycleDirectoryFullPath))						/* Then move the file to user's hidden recycle	*/
																							/*  folder. If this was successful				*/
	{
		echo "" . $FileToRecycle . " has been successfully deleted.";						/* Then print successfully deleted statement	*/
	}
	else																					/* Else, if the file was unsuccessfully moved	*/
																							/*  to the user's recycle folder				*/
	{
		echo "There was a problem sending " . $FileToRecycle . " to your recycle folder.";	/* Print move to recycle fail statement			*/
	}
}
else																						/* Else, if the file is unreadable				*/
{
	echo "There was a problem reading " . $FileToRecycle . "'s name or location.";			/* Print read failure statement					*/
}

echo "<br /><br />";
echo "<form method = 'get' action = '" . $UserPage . "' />";								/* Button to return to index.php				*/
echo "<input type = 'submit' value = 'Return' \>";
echo "<input type = 'hidden' value = '" . $CurrentPathString . "' name = 'ReturnPathString' />";
echo "</form>";
?>

</body>
</html>