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

// MySQL server connection
$conn = mysqli_connect("127.0.0.1", "joel", "Daytona675");

// Check if connected to MySQL server
if (!$conn) {
  echo("Failed to connect to database: " . mysqli_connect_error()) .
    "<br /><br />";
}

// Go into Corvin database
mysqli_query($conn, "USE Corvin;");

// Assign user's ID set in validate.php
$userID = $_SESSION["userID"];

// Session Timeout after 15 Minutes
// If last request was more than 894 seconds ago (14.9 minutes)
if (
  isset($_SESSION['LastActivity']) &&
  (time() - $_SESSION['LastActivity'] > 894)
) {

  // Kick the user back to the login screen
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

// Assign Workspace
if (isset($_POST["workspace"])) {
  $thisWorkspace = $_POST["workspace"];
  $_SESSION["currentWorkspace"] = $thisWorkspace;
}
else {
  $thisWorkspace = $_SESSION["currentWorkspace"];
}
$thisWorkspaceName = ltrim($thisWorkspace, '0123456789');
$thisWorkspaceOwnerID = preg_replace('/[^0-9]/', '', $thisWorkspace);
?>

<!DOCTYPE html>
<html lang = "en">

<!-- 1 Header -->
<head>
  <title>Corvin</title>

  <link rel = "apple-touch-icon" sizes = "57x57"
    href = "/Art/Favicon/apple-icon-57x57.png" />
  <link rel = "apple-touch-icon" sizes = "60x60"
    href = "/Art/Favicon/apple-icon-60x60.png" />
  <link rel = "apple-touch-icon" sizes = "72x72"
    href = "/Art/Favicon/apple-icon-72x72.png" />
  <link rel = "apple-touch-icon" sizes = "76x76"
    href = "/Art/Favicon/apple-icon-76x76.png" />
  <link rel = "apple-touch-icon" sizes = "114x114"
    href = "/Art/Favicon/apple-icon-114x114.png" />
  <link rel = "apple-touch-icon" sizes = "120x120"
    href = "/Art/Favicon/apple-icon-120x120.png" />
  <link rel = "apple-touch-icon" sizes = "144x144"
    href = "/Art/Favicon/apple-icon-144x144.png" />
  <link rel = "apple-touch-icon" sizes = "152x152"
    href = "/Art/Favicon/apple-icon-152x152.png" />
  <link rel = "apple-touch-icon" sizes = "180x180"
    href = "/Art/Favicon/apple-icon-180x180.png" />
  <link rel = "icon" type = "image/png" sizes = "192x192"
    href = "/Art/Favicon/android-icon-192x192.png" />
  <link rel = "icon" type = "image/png" sizes = "32x32"
    href = "/Art/Favicon/favicon-32x32.png" />
  <link rel = "icon" type = "image/png" sizes = "96x96"
    href = "/Art/Favicon/favicon-96x96.png" />
  <link rel = "icon" type = "image/png" sizes = "16x16"
    href = "/Art/Favicon/favicon-16x16.png" />
  <link rel = "manifest" href = "/manifest.json" />

  <meta http-equiv = "refresh" content = "284" />
</head>

<body>

<!-- 2 Update Setting -->
<?php
// Display any errors
ini_set("display_errors", 1);

// And be verbose about it
error_reporting(E_ALL);


// If changing name
if (isset($_POST["newWorkspaceName"])) {

  // Update workspace name in database
  $sql = "
  SELECT
    CASE
      WHEN workspace1 = '" . $thisWorkspace . "' THEN 'workspace1'
      WHEN workspace2 = '" . $thisWorkspace . "' THEN 'workspace2'
      WHEN workspace3 = '" . $thisWorkspace . "' THEN 'workspace3'
      WHEN workspace4 = '" . $thisWorkspace . "' THEN 'workspace4'
      WHEN workspace5 = '" . $thisWorkspace . "' THEN 'workspace5'
      WHEN workspace6 = '" . $thisWorkspace . "' THEN 'workspace6'
      WHEN workspace7 = '" . $thisWorkspace . "' THEN 'workspace7'
      WHEN workspace8 = '" . $thisWorkspace . "' THEN 'workspace8'
      WHEN workspace9 = '" . $thisWorkspace . "' THEN 'workspace9'
      ELSE 'Not found'
    END
  FROM
    Workspaces
  WHERE id = " . $thisWorkspaceOwnerID;

  $workspaceColumn = mysqli_fetch_array(mysqli_query($conn, $sql));

  $newWorkspaceName = filter_input(
    INPUT_POST, "newWorkspaceName", FILTER_SANITIZE_STRING);
  $newWorkspace = $thisWorkspaceOwnerID . $newWorkspaceName;

  $sql = "UPDATE Workspaces SET " . $workspaceColumn[0] . " = '$newWorkspace' WHERE id = '$thisWorkspaceOwnerID'";
  mysqli_query($conn, $sql);

  // Change the name of the workspace folder to reflect new name
  $oldWorkspaceFolderFullPath =
    "../../../mnt/Raid1Array/Corvin/000 - Workspaces/" . $thisWorkspace;
  $oldWorkspaceRecycleFolderFullPath =
    "../../../mnt/Raid1Array/Corvin/000 - Workspaces/0 - WorkspacesRecycle/" .
    $thisWorkspace;
  $newSanitizedWorkspaceFolderName = filter_var(
    $newWorkspace, FILTER_SANITIZE_STRING);
  $newWorkspaceFolderFullPath =
    "../../../mnt/Raid1Array/Corvin/000 - Workspaces/" . $newWorkspace;
  $newWorkspaceRecycleFolderFullPath =
    "../../../mnt/Raid1Array/Corvin/000 - Workspaces/0 - WorkspacesRecycle/" .
    $newWorkspace;

  if (rename($oldWorkspaceFolderFullPath, $newWorkspaceFolderFullPath)) {
    if (rename($oldWorkspaceRecycleFolderFullPath, $newWorkspaceRecycleFolderFullPath)) {

      $_SESSION["currentWorkspace"] = $newWorkspace;
      echo "<meta http-equiv = 'refresh' content = '0; workspaceSettings.php'>";
    }
    else {
      echo "There was a problem renaming the recycle folder for this Corvin Space.";
    }
  }
  else {
    echo "There was a problem renaming the main folder for this Corvin Space.";
  }
}
?>
