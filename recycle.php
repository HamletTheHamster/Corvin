<!--
This is a php file for Corvin which is called by home.php to handle files or
folders being deleted. It does not truly delete the files, but rather sends
them to the user's recycle bin for later retrieval [within some grace period
depending on account tier].

Hierarchy

  0 Expire Session
  1 Header
  2 Recycle Function

Variables

  conn                          - MySQL database connection
  userID                        - user ID of the user; unique integer used to
                                  identify the user and grab other info from
                                  their row in the database
  _SESSION[userID]              - global variable used to maintain user's
                                  identity across pages
  sql                           - used to hold query database strings
  user                          - array;
                                    user[0] = user's first name,
                                    user[1] = user's last name
  currentPathString             - sanitized query string from previous page and
                                  passed via post data; used to return user to
                                  the directory they were in before a download
                                  error occurred
  querryArray                   - array of currentPathString query string
                                  separated into array elements
  returnURL                     - url to return to the directory the user was
                                  in before an error with deleting occurred
  _SESSION[LastActivity]        - global variable set upon logging and updated
                                  each time an action is made; used to log
                                  users out if they are inactive for too long
  _SESSION[Created]             - global variable set upon logging in and reset
                                  periodically when the session ID is reset
  returnURLParam                - returnURL defined within scope of
                                  returnButton function
  currentDirectory              - full path to the current directory
                                  containing the file or folder to be
                                  recycled
  fileToRecycle                 - name of the file to be recycled
  userRecycleDirectoryFullPath  - full path to the recycled file in user's
                                  recycle bin folder, including file name and
                                  file type extension
  fileToRecycleFullPath         - full path to the file, including file name
                                  and file type extension
  i                             - indexer used to append files and folders with
                                  which share the name of a file or folder in
                                  their recycle bin, so as not to overwrite it
  userRecycleDirectory          - full path to the user's recycle bin folder

Issues: Return button does not return the user to the directory that they were
        previously in
        Items in the recycle bin are not automatically deleted after some time

Coded by: Joel N. Johnson
-->

<!-- 0 Expire Session -->
<?php
session_start();

//Check if user is logged in
if (!isset($_SESSION["loginUser"]) && $_SESSION["loginUser"] != TRUE) {
  header("Location: login.php");
}

// Session Timeout after 14.9 Minutes
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

// MySQL server connection
$conn = mysqli_connect("127.0.0.1", "joel", "Daytona675");

// Check if connected to MYSQLI server
if (!$conn) {
  echo("Failed to connect to database: " . mysqli_connect_error()) .
    "<br /><br />";
}

// Go into Corvin database
mysqli_query($conn, "USE Corvin;");


// 2 Recycle Function
// Check if workspace
if (isset($_SESSION["currentWorkspace"])) {

  // Assign user's ID passed from validate.php
  $workspace = $_SESSION["currentWorkspace"];

  $currentPathString = filter_input(
    INPUT_POST, "currentPathString", FILTER_SANITIZE_STRING);

  $queryArray = explode("/", substr($currentPathString, 0, -1));
  $returnURL = "workspace.php?" . http_build_query($queryArray, '');

  $currentDirectory = "../../../mnt/Raid1Array/Corvin/000 - Workspaces/" .
    $workspace . "/" . $currentPathString;

  // Assign file name to recycle to variable
  $fileToRecycle = filter_input(
    INPUT_POST, "fileToRecycle", FILTER_SANITIZE_STRING);

  // Assign user's recycle folder path
  $userRecycleDirectory = "/../../../mnt/Raid1Array/Corvin/000 - Workspaces/0 - WorkspacesRecycle/" .
    $workspace . "/";
  $fileToRecycleFullPath = $currentDirectory . $fileToRecycle;

  $i = 1;

  // While the name of the file or folder to be recycled matches the name of a
  // file or folder already in the recycle folder, append the new file/folder
  // with (#) representing the number of identically named files or folders that
  // reside in the recycle folder by that name
  while (
    array_search($fileToRecycle, scandir($userRecycleDirectory)) !== FALSE
  ) {
    // If there is already a single copy
    if ($i > 1) {
      // Take the (1) off of the end of the name and make it (2)
      $fileToRecycle = substr($fileToRecycle, 0, -3) . "(" . $i . ")";
    }
    // Else append the file/folder with (1)
    else {
      $fileToRecycle = $fileToRecycle . "(1)";
    }
    ++$i;
  }

  // If there were duplicates, rename the file to match
  rename($fileToRecycleFullPath, $currentDirectory . $fileToRecycle);

  // Assign full path plus name to variable
  $fileToRecycleFullPath = $currentDirectory . $fileToRecycle;

  // Assign recycled file name full path
  $userRecycleDirectoryFullPath = $userRecycleDirectory . $fileToRecycle;

  // If the full path and file is readable
  if (is_readable($fileToRecycleFullPath)) {
    // Then try to move the file to user's hidden recycle folder. If this was
    // successful
    if (rename($fileToRecycleFullPath, $userRecycleDirectoryFullPath)) {
      echo "<meta http-equiv = 'refresh' content = '0; " . $returnURL . "'>";
    }
    else {
      echo "There was a problem sending " . $fileToRecycle .
        " to your recycle folder.";
    }
  }
  else {
    echo "There was a problem reading " . $fileToRecycle .
      "'s name or location.";
  }
}
else if (isset($_SESSION["liveCorvinFiles"])) {

  $currentPathString = filter_input(
    INPUT_POST, "currentPathString", FILTER_SANITIZE_STRING);

  $queryArray = explode("/", substr($currentPathString, 0, -1));
  $returnURL = "liveCorvinFiles.php?" . http_build_query($queryArray, '');

  $currentDirectory = "../../../var/www/html/" . $currentPathString;

  // Assign file name to recycle to variable
  $fileToRecycle = filter_input(
    INPUT_POST, "fileToRecycle", FILTER_SANITIZE_STRING);

  // Assign user's recycle folder path
  $userRecycleDirectory = "/../../../mnt/Raid1Array/Corvin/0 - Recycle/1 - JoelJohnson/";
  $fileToRecycleFullPath = $currentDirectory . $fileToRecycle;

  $i = 1;

  // While the name of the file or folder to be recycled matches the name of a
  // file or folder already in the recycle folder, append the new file/folder
  // with (#) representing the number of identically named files or folders that
  // reside in the recycle folder by that name
  while (
    array_search($fileToRecycle, scandir($userRecycleDirectory)) !== FALSE
  ) {
    // If there is already a single copy
    if ($i > 1) {
      // Take the (1) off of the end of the name and make it (2)
      $fileToRecycle = substr($fileToRecycle, 0, -3) . "(" . $i . ")";
    }
    // Else append the file/folder with (1)
    else {
      $fileToRecycle = $fileToRecycle . "(1)";
    }
    ++$i;
  }

  // If there were duplicates, rename the file to match
  rename($fileToRecycleFullPath, $currentDirectory . $fileToRecycle);

  // Assign full path plus name to variable
  $fileToRecycleFullPath = $currentDirectory . $fileToRecycle;

  // Assign recycled file name full path
  $userRecycleDirectoryFullPath = $userRecycleDirectory . $fileToRecycle;

  // If the full path and file is readable
  if (is_readable($fileToRecycleFullPath)) {
    // Then try to move the file to user's hidden recycle folder. If this was
    // successful
    if (rename($fileToRecycleFullPath, $userRecycleDirectoryFullPath)) {
      header("Location: " . $returnURL);
    }
    else {
      echo "There was a problem sending " . $fileToRecycle .
        " to your recycle folder.";
    }
  }
  else {
    echo "There was a problem reading " . $fileToRecycle .
      "'s name or location.";
  }
}
else {

  // Assign user's ID passed from validate.php
  $userID = $_SESSION["userID"];

  $sql = "SELECT firstName, lastName FROM UserInfo WHERE id = '$userID'";
  $user = mysqli_fetch_array(mysqli_query($conn, $sql));

  $currentPathString = filter_input(
    INPUT_POST, "currentPathString", FILTER_SANITIZE_STRING);

  $queryArray = explode("/", substr($currentPathString, 0, -1));
  $returnURL = "home.php?" . http_build_query($queryArray, '');

  $currentDirectory = "../../../mnt/Raid1Array/Corvin/" . $userID . " - " .
    $user[0] . $user[1] . "/" . $currentPathString;

  // Assign file name to recycle to variable
  $fileToRecycle = filter_input(
    INPUT_POST, "fileToRecycle", FILTER_SANITIZE_STRING);

  // Assign user's recycle folder path
  $userRecycleDirectory = "/../../../mnt/Raid1Array/Corvin/0 - Recycle/" .
    $userID . " - " . $user[0] . $user[1] . "/";
  $fileToRecycleFullPath = $currentDirectory . $fileToRecycle;

  $i = 1;

  // While the name of the file or folder to be recycled matches the name of a
  // file or folder already in the recycle folder, append the new file/folder
  // with (#) representing the number of identically named files or folders that
  // reside in the recycle folder by that name
  while (
    array_search($fileToRecycle, scandir($userRecycleDirectory)) !== FALSE
  ) {
    // If there is already a single copy
    if ($i > 1) {
      // Take the (1) off of the end of the name and make it (2)
      $fileToRecycle = substr($fileToRecycle, 0, -3) . "(" . $i . ")";
    }
    // Else append the file/folder with (1)
    else {
      $fileToRecycle = $fileToRecycle . "(1)";
    }
    ++$i;
  }

  // If there were duplicates, rename the file to match
  rename($fileToRecycleFullPath, $currentDirectory . $fileToRecycle);

  // Assign full path plus name to variable
  $fileToRecycleFullPath = $currentDirectory . $fileToRecycle;

  // Assign recycled file name full path
  $userRecycleDirectoryFullPath = $userRecycleDirectory . $fileToRecycle;

  // If the full path and file is readable
  if (is_readable($fileToRecycleFullPath)) {
    // Then try to move the file to user's hidden recycle folder. If this was
    // successful
    if (rename($fileToRecycleFullPath, $userRecycleDirectoryFullPath)) {
      header("Location: " . $returnURL);
    }
    else {
      echo "There was a problem sending " . $fileToRecycle .
        " to your recycle folder.";
    }
  }
  else {
    echo "There was a problem reading " . $fileToRecycle .
      "'s name or location.";
  }
}
