<!--
This is a php file for Corvin which is called to handle renaming files in the
current directory.

Hierarchy

  0   Expire Session
  1   Header
  2   New Folder Function

Variables

  conn                - MySQL database connection
  userID              - user ID of the user; unique integer used to identify
                        the user and grab other info from their row in the
                        database
  _SESSION[userID]    - global variable used to maintain user's identity across
                        pages
  sql                 - used to hold query database strings
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

//Check if user is logged in
if (!isset($_SESSION["loginUser"]) && $_SESSION["loginUser"] != TRUE) {
  header("Location: login.php");
}

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

// MySQL server connection
$conn = mysqli_connect("127.0.0.1", "joel", "Daytona675");

// Check if connected to MYSQLI server
if (!$conn) {
  echo("Failed to connect to database: " . mysqli_connect_error()) .
    "<br /><br />";
}

// Go into Corvin database
mysqli_query($conn, "USE Corvin;");

if (isset($_SESSION["currentWorkspace"])) {

  // Assign workspace
  $workspace = $_SESSION["currentWorkspace"];

  $currentPathString = filter_input(
    INPUT_POST,
    "currentPathString",
    FILTER_SANITIZE_STRING
  );

  $queryArray = explode("/", substr($currentPathString, 0, -1));
  $returnURL = "workspace.php?" . http_build_query($queryArray, '');

  // 2 New Folder Function

  // Display any errors
  ini_set("display_errors", 1);

  // And be verbose about it
  error_reporting(E_ALL);

  // Assign path of current directory
  $currentDirectory = "../../../mnt/Raid1Array/Corvin/000 - Workspaces/" . $workspace .
    "/" . $currentPathString;

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
}
else {

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

  // 2 New Folder Function

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
}
?>

</body>
</html>
