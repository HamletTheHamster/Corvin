<!--


Coded by: Joel N. Johnson
-->

<!-- 0 Check If Logged In -->
<?php
session_start();

//Check if user is logged in
if (!isset($_SESSION["loginUser"]) && $_SESSION["loginUser"] != TRUE) {
  header("Location: login.php");
}

// Display any errors
ini_set("display_errors", 1);

// And be verbose about it
error_reporting(E_ALL);

// MySQL server connection
$conn = mysqli_connect("127.0.0.1", "joel", "Daytona675");

// Check if connected to MySQL server
if (!$conn) {
  echo("Failed to connect to database: " . mysqli_connect_error()) .
  "<br /><br />";
}

// Go into Corvin database
mysqli_query($conn, "USE Corvin;");

// Assign user's ID, set in validate.php
$userID = $_SESSION["userID"];

$sql = "SELECT firstName, lastName FROM UserInfo WHERE id = '$userID'";
$user = mysqli_fetch_array(mysqli_query($conn, $sql));

// Session Timeout after 854 seconds (14.2 minutes)
// If last request was more than 854 seconds ago (14.2 minutes)
if (
  isset($_SESSION['LastActivity']) &&
  (time() - $_SESSION['LastActivity'] > 854)
) {

  // Then unset $_SESSION variable for the run-time
  session_unset();

  // Destroy session data in storage
  session_destroy();

  // And kick the user back to the login screen
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
elseif (time() - $_SESSION['Created'] > 1200)
{
  // Then change session ID for the current session and invalidate old session
  // ID
  session_regenerate_id(true);

  // Update creation time
  $_SESSION['Created'] = time();
}
?>

<!DOCTYPE html>
<html lang = "en" class = "settings">

<!-- 1 Header -->
<head>
  <title>Home | Corvin</title>

  <link href = "one.css" type = "text/css" rel = "stylesheet"/>

  <link rel = "apple-touch-icon" sizes = "57x57"
    href = "Art/Favicon/apple-icon-57x57.png" />
  <link rel = "apple-touch-icon" sizes = "60x60"
    href = "Art/Favicon/apple-icon-60x60.png" />
  <link rel = "apple-touch-icon" sizes = "72x72"
    href = "Art/Favicon/apple-icon-72x72.png" />
  <link rel = "apple-touch-icon" sizes = "76x76"
    href = "Art/Favicon/apple-icon-76x76.png" />
  <link rel = "apple-touch-icon" sizes = "114x114"
    href = "Art/Favicon/apple-icon-114x114.png" />
  <link rel = "apple-touch-icon" sizes = "120x120"
    href = "Art/Favicon/apple-icon-120x120.png" />
  <link rel = "apple-touch-icon" sizes = "144x144"
    href = "Art/Favicon/apple-icon-144x144.png" />
  <link rel = "apple-touch-icon" sizes = "152x152"
    href = "Art/Favicon/apple-icon-152x152.png" />
  <link rel = "apple-touch-icon" sizes = "180x180"
    href = "Art/Favicon/apple-icon-180x180.png" />
  <link rel = "icon" type = "image/png" sizes = "192x192"
    href = "Art/Favicon/android-icon-192x192.png" />
  <link rel = "icon" type = "image/png" sizes = "32x32"
    href = "Art/Favicon/favicon-32x32.png" />
  <link rel = "icon" type = "image/png" sizes = "96x96"
    href = "Art/Favicon/favicon-96x96.png" />
  <link rel = "icon" type = "image/png" sizes = "16x16"
    href = "Art/Favicon/favicon-16x16.png" />
  <link rel = "manifest" href = "/manifest.json" />

  <meta name = "msapplication-TileColor" content = "#ffffff"/>
  <meta name = "msapplication-TileImage" content = "/ms-icon-144x144.png"/>
  <meta name = "theme-color" content = "#ffffff"/>

  <meta http-equiv = "refresh" content = "855"/>

  <meta name = "google" content = "notranslate"/>
</head>

<body class = "settings">
<div class = "settingsWrapper">

  <!-- 2 Top Bar -->
  <div class = "settingsTopBar">
    <div class = "settingsCorvin">
      <?php
      echo "<a href = 'home.php'>" . "<h class = 'settingsCorvinHeader'>C</h>" .
        "</a>";
      ?>
    </div>
    <div class = "settingsAccountMenuDropDown">
      <p onclick = "accountDropDownMenu()" class = "settingsAccountButton">Account</p>
      <div id = "AccountMenuContent" class = "settingsAccountMenuContent">
        <div class = "settingsTopAccountMenuContent">
          <?php
          echo "<p class = 'settingsAccountMenuName'>" . $user[0] . " " . $user[1] .
            "</p>";

          include "humanSize.php";
          include "folderSize.php";

          $usedBytes = folderSize("../../../../mnt/Raid1Array/Corvin/" .
            $userID . " - " . $user[0] . $user[1]);

          $sql = "SELECT storageSpaceInMegabytes FROM UserInfo WHERE id = '" .
            $userID . "'";
          $storageSpaceInMegabytes = mysqli_fetch_row(mysqli_query(
            $conn, $sql));

          if ($storageSpaceInMegabytes[0] == "-1") {
            $totalBytes = disk_total_space(
              "../../../../mnt/Raid1Array/Corvin");
            $freeBytes = disk_free_space("../../../../mnt/Raid1Array/Corvin");

            echo "<p class = 'settingsDiskSpace'>" . humanSize($usedBytes) . " used of " .
              humanSize($freeBytes) . " (Unlimited)</p>";
          }
          else {
            $totalBytes = $storageSpaceInMegabytes[0] * 1000000;
            $freeBytes = $totalBytes - $usedBytes;

            echo "<p class = 'settingsDiskSpace'>" . humanSize($usedBytes) .
              " used of " . humanSize($totalBytes) . "</p>";
          }
          ?>
        </div>
        <div class = "settingsMenuLine">
          <hr class = "settingsMenuLine"/>
        </div>
        <div class = "settingsBottomAccountMenuContent">
          <a class = "settingsGetMoreSpaceMenuItem" href = "getMoreSpace.php">
            Get More Space</a>
          <a class = "settingsMenuItem" href = "home.php">Home</a>
          <a class = "settingsMenuItem" href = "help.php">Help</a>
          <a class = "settingsMenuItem" href = "logout.php">Log Out</a>
        </div>
      </div>
    </div>
  </div>

  <script>
  function accountDropDownMenu() {
    document.getElementById("AccountMenuContent").classList.toggle("settingsShow");
  }
  /*
  window.onclick = function(event) {
    if (!document.getElementById("AccountMenuContent").contains(event.target)) {
      if (document.getElementById("AccountMenuContent").classList.contains("show") {
        document.getElementById("AccountMenuContent").classList.remove("show");
      }
    }
  }
  */
  </script>

  <!-- 5 Main Content -->
  <div class = "settingsMainContent">
    <form
      action = "getMoreSpace.php"
      method = "post"
      enctype = "multipart/form-data"
    >
      <input
        type = "submit"
        class = "settingsGetMoreSpaceButton"
        value = "Get More Space"
        name = "submit"
      />
    </form>

    <div class = "settingsHeader">
      <p class = "settingsHeader">Settings</p>
    </div>

    <div class = "settingsContent">
      <hr class = "settingsFirstLine">
      <!-- Name -->
      <div class = "settingsSetting">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Name</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div class = "settingsValue">
            <?php
            $sql = "SELECT firstName, lastName FROM UserInfo WHERE id = '" .
              $userID . "'";
            $user = mysqli_fetch_row(mysqli_query($conn, $sql));
            echo "<p>" . $user[0] . " " . $user[1] . "</p>";
            ?>
          </div>
        </div>
      </div>
      <hr>
      <!-- Email -->
      <div class = "settingsSetting">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Email</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div class = "settingsValue">
            <?php
            $sql = "SELECT email FROM UserInfo WHERE id = '" . $userID . "'";
            $email = mysqli_fetch_row(mysqli_query($conn, $sql));
            echo "<p>" . $email[0] . "</p>";
            ?>
          </div>
        </div>
      </div>
      <hr>
      <!-- Username -->
      <div class = "settingsSetting">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Username</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div class = "settingsValue">
            <?php
            $sql = "SELECT username FROM UserInfo WHERE id = '" . $userID . "'";
            $username = mysqli_fetch_row(mysqli_query($conn, $sql));
            echo "<p>" . $username[0] . "</p>";
            ?>
          </div>
        </div>
      </div>
      <hr>
      <!-- Password -->
      <div class = "settingsSettingWithButton">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Password</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div class = "settingsValue">
            <button class = "settingsChangePasswordButton">
              Change Password
            </button>
          </div>
        </div>
      </div>
      <hr>
      <!-- Dark Mode -->
      <div class = "settingsSettingWithButton">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Dark Mode</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div class = "settingsValue">
            <button class = "settingsDarkModeButton">Off</button>
          </div>
        </div>
      </div>
      <hr>
      <!-- Delete my Corvin Account -->
      <div class = "settingsSettingWithButton">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Delete my Corvin account</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div class = "settingsValue">
            <button class = "settingsDeleteAccountButton">
              Delete Account
            </button>
          </div>
        </div>
      </div>
      <hr>
    </div>
  </div>

  <div class = "settingsPush"></div>

</div>

<!-- 6 Footer -->
<div class = "settingsFooter">&copy; Corvin, Inc.</div>

</body>
</html>
