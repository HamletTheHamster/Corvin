<!--
This is a php file for Corvin which is called to handle renaming files in the
current directory.

Hierarchy

  0   Expire Session
  1   Header
  2   New Folder Function

Variables

  user                - string containing user's first name
  userLastName        - string containing user's last name
  currentDirectory    - location of user's root folder
	folderName          - new folder name
	folderNameFullPath  - full path of the new folder to be created

Coded by: Joel N. Johnson
-->

<!-- 0 Expire Session -->
<?php
// Display any errors
ini_set("display_errors", 1);

// And be verbose about it
error_reporting(E_ALL);

session_start();

// MySQL server connection
$conn = mysqli_connect("127.0.0.1", "joel", "Daytona675");

// Check if connected to MYSQLI server
if (!$conn) {
  echo("Failed to connect to database: " . mysqli_connect_error()) .
    "<br /><br />";
}

// Go into Corvin database
mysqli_query($conn, "USE Corvin;");

// Assign user's ID passed from validate.php
$userID = $_SESSION["userID"];

$sql = "SELECT firstName, lastName FROM UserInfo WHERE id = '$userID'";
$user = mysqli_fetch_array(mysqli_query($conn, $sql));

$currentPathString = filter_input(
  INPUT_POST,
  "currentPathString",
  FILTER_SANITIZE_STRING
);

$queryArray = explode("/", substr($currentPathString, 0, -1));
$returnURL = "home.php?" . http_build_query($queryArray, '');

// Session timeout after 14.9 minutes
// If last request was more than 894 seconds ago (14.9 minutes)
if (
	isset($_SESSION['LastActivity']) &&
  (time() - $_SESSION['LastActivity'] > 894)
) {
	// Then kick the user back to login.php, which php-redirects to cor.vin
	header("Location: login.php");
}

// Update last activity time stamp
$_SESSION['LastActivity'] = time();

// Regenerate Session ID every 20 Minutes
// If session started timestamp is not set
if (!isset($_SESSION['Created'])) {
	// Then set the session start time to now
  $_SESSION['Created'] = time();
}
// If session started more than 20 minutes ago
elseif (time() - $_SESSION['Created'] > 1200) {
	// Then change session ID for the current session and invalidate old session
  // ID
  session_regenerate_id(true);

  // Update creation time
  $_SESSION['Created'] = time();
}
?>

<!DOCTYPE html>
<html lang = "en">

<!-- 1 Header -->
<head>
  <title>Corvin</title>

  <link href = "index.css" type = "text/css" rel = "stylesheet" />

  <link
    rel = "apple-touch-icon"
    sizes = "57x57"
    href = "/Art/Favicon/apple-icon-57x57.png"
  />
  <link
    rel = "apple-touch-icon"
    sizes = "60x60"
    href = "/Art/Favicon/apple-icon-60x60.png"
  />
  <link
    rel = "apple-touch-icon"
    sizes = "72x72"
    href = "/Art/Favicon/apple-icon-72x72.png"
  />
  <link
    rel = "apple-touch-icon"
    sizes = "76x76"
    href = "/Art/Favicon/apple-icon-76x76.png"
  />
  <link
    rel = "apple-touch-icon"
    sizes = "114x114"
    href = "/Art/Favicon/apple-icon-114x114.png"
  />
  <link
    rel = "apple-touch-icon"
    sizes = "120x120"
    href = "/Art/Favicon/apple-icon-120x120.png"
  />
  <link
    rel = "apple-touch-icon"
    sizes = "144x144"
    href = "/Art/Favicon/apple-icon-144x144.png"
  />
  <link
    rel = "apple-touch-icon"
    sizes = "152x152"
    href = "/Art/Favicon/apple-icon-152x152.png"
  />
  <link
    rel = "apple-touch-icon"
    sizes = "180x180"
    href = "/Art/Favicon/apple-icon-180x180.png"
  />
  <link
    rel = "icon"
    type = "image/png"
    sizes = "192x192"
    href = "/Art/Favicon/android-icon-192x192.png"
  />
  <link
    rel = "icon"
    type = "image/png"
    sizes = "32x32"
    href = "/Art/Favicon/favicon-32x32.png"
  />
  <link
    rel = "icon"
    type = "image/png"
    sizes = "96x96"
    href = "/Art/Favicon/favicon-96x96.png"
  />
  <link
    rel = "icon"
    type = "image/png"
    sizes = "16x16"
    href = "/Art/Favicon/favicon-16x16.png"
  />
  <link rel = "manifest" href = "/manifest.json" />

  <meta name = "msapplication-TileColor" content = "#ffffff" />
  <meta name = "msapplication-TileImage" content = "/ms-icon-144x144.png" />
  <meta name = "theme-color" content = "#ffffff" />

  <meta http-equiv = "refresh" content = "284" />
</head>

<body>

<!-- 2 New Folder Function -->
<?php
function returnButton($returnURLParam)
{
  echo "<br /><br />";
  echo "
  <form method = 'get' action = '" . $returnURLParam . "' />
    <input type = 'submit' value = 'Return' />
  </form>";
}

// Display any errors
ini_set("display_errors", 1);

// And be verbose about it
error_reporting(E_ALL);

// Assign path of current directory
$currentDirectory = "../../../mnt/Raid1Array/Corvin/" . $userID . " - " .
  $user[0] . $user[1] . "/" . $currentPathString;

// Assign the folder name to a variable
$folderName = filter_input(
  INPUT_POST, "folderName", FILTER_SANITIZE_STRING);

// Assign full path and old name to a variable
$folderNameFullPath = $currentDirectory . $folderName;

// Then create the folder with the name and if that was successful
if (mkdir($folderNameFullPath, 0777, true)) {
  chmod($folderNameFullPath, 0777);

  // Then refresh the page the user was on
  echo "<meta http-equiv = 'refresh' content = '0; " . $returnURL . "'>";
}
// Else, if the creation failed
else {
  // Then print creation failure statement
  echo "There was a problem creating the folder.";
  returnButton($returnURL);
}
?>

</body>
</html>
