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

//If change requires password check
if (isset($_POST["submittedPassword"])) {

  // Get submitted password and verify
  $submittedPassword = $_POST["submittedPassword"];
  $sql = "SELECT password from UserInfo WHERE id = '$userID'";
  $referencePassword = mysqli_fetch_row(mysqli_query($conn, $sql));

  if (password_verify($submittedPassword, $referencePassword[0])) {

    // If changing name
    if (isset($_POST["firstNameChange"])) {

      // Update first and last name in database
      $sql = "SELECT firstName, lastName FROM UserInfo WHERE id = '$userID'";
      $oldUser = mysqli_fetch_array(mysqli_query($conn, $sql));
      $newFirstName = filter_input(
        INPUT_POST, "firstNameChange", FILTER_SANITIZE_STRING);
      $newLastName = filter_input(
        INPUT_POST, "lastNameChange", FILTER_SANITIZE_STRING);

      $sql = "UPDATE UserInfo SET firstName = '$newFirstName' WHERE id = '$userID'";
      mysqli_query($conn, $sql);

      $sql = "UPDATE UserInfo SET lastName = '$newLastName' WHERE id = '$userID'";
      mysqli_query($conn, $sql);

      // Change the name of the user's main folder to reflect new name
      $oldUserFolderFullPath = "../../../mnt/Raid1Array/Corvin/" . $userID .
        " - " . $oldUser[0] . $oldUser[1];
      $oldUserRecycleFolderFullPath = "../../../mnt/Raid1Array/Corvin/0 - Recycle/" .
        $userID . " - " . $oldUser[0] . $oldUser[1];
      $user = array($newFirstName, $newLastName);
      $newUserFolderName = $userID . " - " . $user[0] . $user[1];
      $newSanitizedUserFolderName = filter_var(
        $newUserFolderName, FILTER_SANITIZE_STRING);
      $newUserFolderFullPath = "../../../mnt/Raid1Array/Corvin/" .
        $newSanitizedUserFolderName;
      $newUserRecycleFolderFullPath =
        "../../../mnt/Raid1Array/Corvin/0 - Recycle/" .
        $newSanitizedUserFolderName;

      if (rename($oldUserFolderFullPath, $newUserFolderFullPath)) {
        if (rename($oldUserRecycleFolderFullPath, $newUserRecycleFolderFullPath)) {

          echo "<meta http-equiv = 'refresh' content = '0; settings.php'>";
        }
        else {echo "There was a problem renaming your recycle folder.";}
      }
      else {echo "There was a problem renaming your main folder.";}
    }
    // Else, if changing email
    elseif (isset($_POST["emailChange"])) {

      // Update email in database
      $newEmail = filter_input(INPUT_POST, "emailChange", FILTER_SANITIZE_EMAIL);

      $sql = "UPDATE UserInfo SET email = '$newEmail' WHERE id = '$userID'";
      mysqli_query($conn, $sql);

      echo "<meta http-equiv = 'refresh' content = '0; settings.php'>";
    }
    // Else, if changing username
    elseif (isset($_POST["usernameChange"])) {

      // Update username in database
      $newUsername = filter_input(
        INPUT_POST, "usernameChange", FILTER_SANITIZE_STRING);

      $sql = "UPDATE UserInfo SET username = '$newUsername' WHERE id = '$userID'";
      mysqli_query($conn, $sql);

      echo "<meta http-equiv = 'refresh' content = '0; settings.php'>";
    }
    // Else, if changing password
    elseif (isset($_POST["newPassword"])) {

      // Check if passwords match
      if ($_POST["newPassword"] == $_POST["newPassword2"]) {

        // Check if password meets criteria
        if (strlen($_POST["newPassword"]) > 7) {

          // Password_hash automatically uses currently recommended hashing
          // algorithm with salt
          $hashedPassword = password_hash($_POST["newPassword"], PASSWORD_DEFAULT);

          $sql = "UPDATE UserInfo SET password = '$hashedPassword' WHERE id = '$userID'";
          mysqli_query($conn, $sql);

          echo "<meta http-equiv = 'refresh' content = '0; settings.php'>";
        }
        else {
          echo "Password needs to be at least 8 characters.";
        }
      }
      else {
        echo "Passwords do not match.";
      }
    }
    else {
      echo "<meta http-equiv = 'refresh' content = '0; settings.php'>";
    }
  }
  else {echo "Incorrect password.";}
}
?>
