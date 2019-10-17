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
  <script src="jquery-3.4.1.min.js"></script>

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
      <div class = "settingsSpace"></div>
      <h class = "settingsFirstHeader">Account Details</h>
      <hr>

      <!-- Name -->
      <div onclick = "toggleNameDropdown()" class = "settingsSetting">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Name</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div id = "settingsNameValue" class = "settingsValue">
            <?php
            $sql = "SELECT firstName, lastName FROM UserInfo WHERE id = '" .
              $userID . "'";
            $user = mysqli_fetch_row(mysqli_query($conn, $sql));
            echo "<p>" . $user[0] . " " . $user[1] . "</p>";
            ?>
          </div>
        </div>
      </div>
      <div id = "settingsDropdownName" class = "settingsDropdownSetting">
        <form
          action = "updateAccountSettings.php"
          method = "post"
          enctype = "multipart/form-data"
          class = "settingsChangeName"
        >
          <input
            type = "password"
            name = "submittedPassword"
            id = "namePasswordCheck"
            class = "settingsNamePasswordVerifyTextBox"
            placeholder = "Password"
          >
          <input
            type = "text"
            name = "firstNameChange"
            class = "settingsFirstNameChangeTextBox"
            value = <?php echo "'" . $user[0] . "'"; ?>
            placeholder = "First Name"
          />
          <input
            type = "text"
            name = "lastNameChange"
            class = "settingsLastNameChangeTextBox"
            value = <?php echo "'" . $user[1] . "'"; ?>
            placeholder = "Last Name"
          />
          <input
            type = "submit"
            value = "Save Changes"
            class = "settingsSaveChangesButton"
          />
        </form>
      </div>
      <script>
      document.getElementById("settingsDropdownName").style.display = "none";

      function toggleNameDropdown() {
        var settingsDropdownName = document.getElementById("settingsDropdownName");

        if (settingsDropdownName.style.display == "none") {
          settingsDropdownName.style.display = "block";
          settingsNameValue.style.display = "none";
          document.getElementById("namePasswordCheck").focus();
        }
        else {
          settingsDropdownName.style.display = "none";
          settingsNameValue.style.display = "block";
        }
      }
      </script>
      <hr>

      <!-- Profile Image -->
      <div class = "settingsSettingWithButton">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Profile Image</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div class = "settingsValue">
            <?php
            $uploadedProfileImage =
              "../../../../mnt/Raid1Array/Corvin/00 - Profile Images/" .
              $userID . " - " . $user[0] . $user[1] . ".jpg";
            $defaultProfileImage =
              "Art/1 - Default Profile Icons/Corvin Castle Icon" .
              ($userID % 10) . ".jpg";
            if (file_exists($uploadedProfileImage)) {
              echo "<img class = 'settingsProfileImage' src = '" .
                $uploadedProfileImage . "'>";
            }
            else {
              echo "<img class = 'settingsProfileImage' src = '" .
                $defaultProfileImage . "'>";
            }
            ?>
          </div>
        </div>
      </div>
      <hr>

      <!-- Email -->
      <div onclick = "toggleEmailDropdown()" class = "settingsSetting">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Email</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div id = "settingsEmailValue" class = "settingsValue">
            <?php
            $sql = "SELECT email FROM UserInfo WHERE id = '" . $userID . "'";
            $email = mysqli_fetch_row(mysqli_query($conn, $sql));
            echo "<p>" . $email[0] . "</p>";
            ?>
          </div>
        </div>
      </div>
      <div id = "settingsDropdownEmail" class = "settingsDropdownSetting">
        <form
          action = "updateAccountSettings.php"
          method = "post"
          enctype = "multipart/form-data"
          class = "settingsChangeName"
        >
          <input
            type = "password"
            name = "submittedPassword"
            id = "emailPasswordCheck"
            class = "settingsEmailPasswordVerifyTextBox"
            placeholder = "Password"
          >
          <input
            type = "email"
            name = "emailChange"
            class = "settingsEmailChangeTextBox"
            value = <?php echo "'" . $email[0] . "'"; ?>
            placeholder = "Email"
          />
          <input
            type = "submit"
            value = "Save Changes"
            class = "settingsSaveChangesButton"
          />
        </form>
      </div>
      <script>
      document.getElementById("settingsDropdownEmail").style.display = "none";

      function toggleEmailDropdown() {
        var settingsDropdownEmail = document.getElementById("settingsDropdownEmail");

        if (settingsDropdownEmail.style.display == "none") {
          settingsDropdownEmail.style.display = "block";
          settingsEmailValue.style.display = "none";
          document.getElementById("emailPasswordCheck").focus();
        }
        else {
          settingsDropdownEmail.style.display = "none";
          settingsEmailValue.style.display = "block";
        }
      }
      </script>
      <hr>

      <!-- Username -->
      <div onclick = "toggleUsernameDropdown()" class = "settingsSetting">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Username</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div id = "settingsUsernameValue" class = "settingsValue">
            <?php
            $sql = "SELECT username FROM UserInfo WHERE id = '" . $userID . "'";
            $username = mysqli_fetch_row(mysqli_query($conn, $sql));
            echo "<p>" . $username[0] . "</p>";
            ?>
          </div>
        </div>
      </div>
      <div id = "settingsDropdownUsername" class = "settingsDropdownSetting">
        <form
          action = "updateAccountSettings.php"
          method = "post"
          enctype = "multipart/form-data"
          class = "settingsChangeName"
        >
          <input
            type = "password"
            name = "submittedPassword"
            id = "usernamePasswordCheck"
            class = "settingsUsernamePasswordVerifyTextBox"
            placeholder = "Password"
          >
          <input
            type = "text"
            name = "usernameChange"
            class = "settingsUsernameChangeTextBox"
            value = <?php echo "'" . $username[0] . "'"; ?>
            placeholder = "Username"
          />
          <input
            type = "submit"
            value = "Save Changes"
            class = "settingsSaveChangesButton"
          />
        </form>
      </div>
      <script>
      document.getElementById("settingsDropdownUsername").style.display = "none";

      function toggleUsernameDropdown() {
        var settingsDropdownUsername = document.getElementById("settingsDropdownUsername");

        if (settingsDropdownUsername.style.display == "none") {
          settingsDropdownUsername.style.display = "block";
          settingsUsernameValue.style.display = "none";
          document.getElementById("usernamePasswordCheck").focus();
        }
        else {
          settingsDropdownUsername.style.display = "none";
          settingsUsernameValue.style.display = "block";
        }
      }
      </script>
      <hr>

      <!-- Password -->
      <div onclick = "togglePasswordDropdown()" class = "settingsSetting">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Password</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div class = "settingsValue">
          </div>
        </div>
      </div>
      <div id = "settingsDropdownPassword"
        class = "settingsDropdownSettingPassword">
        <div class = "settingsPasswordRequirement">
          <p class = "settingsPasswordRequirement">
            Password must contain at least 8 characters
          </P>
        </div>
        <div class = "settingsPasswordChangeForm">
          <form
            action = "updateAccountSettings.php"
            method = "post"
            enctype = "multipart/form-data"
            class = "settingsChangeName"
          >
            <input
              type = "password"
              name = "submittedPassword"
              id = "oldPassword"
              class = "settingsPasswordOldPasswordVerifyTextBox"
              placeholder = "Old Password"
            >
            <input
              type = "password"
              name = "newPassword"
              class = "settingsPasswordChangeTextBox"
              placeholder = "New Password"
            />
            <input
              type = "password"
              name = "newPassword2"
              class = "settingsPasswordChangeTextBox"
              placeholder = "Re-enter New Password"
            />
            <input
              type = "submit"
              value = "Save Changes"
              class = "settingsSaveChangesButton"
            />
          </form>
        </div>
      </div>
      <script>
      document.getElementById("settingsDropdownPassword").style.display = "none";

      function togglePasswordDropdown() {
        var settingsDropdownPassword = document.getElementById("settingsDropdownPassword");

        if (settingsDropdownPassword.style.display == "none") {
          settingsDropdownPassword.style.display = "block";
          document.getElementById("oldPassword").focus();
        }
        else {
          settingsDropdownPassword.style.display = "none";
        }
      }
      </script>
      <hr>

      <!-- Space -->
      <div class = "settingsSpace"></div>

      <!-- Preferences -->
      <h class = "settingsHeaders">Preferences</h>
      <hr>

      <!-- Dark Mode -->
      <div onclick = "toggleDarkmodeDropdown()" class = "settingsSetting">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Dark Mode</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div class = "settingsValue">
            <p id = "darkmodeStatus">Off</p>
          </div>
        </div>
      </div>
      <div id = "settingsDropdownDarkmode" class = "settingsDropdownSetting">
        <label class="settingsSwitch">
          <input id = "darkmodeSlider" type="checkbox">
          <span class="settingsSlider"></span>
          <form id = "darkmodeForm">
          </form>

        </label>
      </div>
      <script>
      var darkmodeSlider = document.getElementById("darkmodeSlider");

      darkmodeSlider.addEventListener("change", function() {
        if (event.target.checked) {
          document.getElementById("darkmodeStatus").innerHTML = "On";
        }
        else {
          document.getElementById("darkmodeStatus").innerHTML = "Off";
        }
      });

      document.getElementById("settingsDropdownDarkmode").style.display = "none";
      function toggleDarkmodeDropdown() {
        var settingsDropdownDarkmode = document.getElementById("settingsDropdownDarkmode");

        if (settingsDropdownDarkmode.style.display == "none") {
          settingsDropdownDarkmode.style.display = "block";
          document.getElementById("oldPassword").focus();
        }
        else {
          settingsDropdownDarkmode.style.display = "none";
        }
      }
      </script>
      <hr>

      <!-- Ledger Size -->
      <div class = "settingsSettingWithButton">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Ledger Size</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div class = "settingsValue">
            <button class = "settingsLedgerSizeButton">Smaller</button>
            <button class = "settingsLedgerSizeButton">Standard</button>
            <button class = "settingsLedgerSizeButton">Larger</button>
          </div>
        </div>
      </div>
      <hr>

      <!-- Date Format -->
      <div class = "settingsSettingWithButton">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Date Format</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div class = "settingsValue">
            <?php //want today's date as examples ?>
            <select class = "settingsDateFormatSelect">
              <option selected value = "1">10/14/2019</option>
              <option value = "2">14/10/2019</option>
              <option value = "3">October 14th, 2019</option>
            </select>
          </div>
        </div>
      </div>
      <hr>

      <!-- Time Zone -->
      <div class = "settingsSettingWithButton">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Time Zone</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div class = "settingsValue">
            <select class = "settingsTimeZoneSelect">
              <?php //loop through array of all time zones ?>
              <option value = "(UTC-07:00) Arizona">(UTC-07:00) Arizona</option>
            </select>
          </div>
        </div>
      </div>
      <hr>

      <!-- Language -->
      <div class = "settingsSettingWithButton">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Language</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div class = "settingsValue">
            <select class = "settingsLanguageSelect">
              <?php //loop through array of all supported languages ?>
              <option value = "English (US)">English (US)</option>
            </select>
          </div>
        </div>
      </div>
      <hr>

      <!-- Corvin βeta Program -->
      <div class = "settingsSettingWithButton">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Corvin βeta Program</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div class = "settingsValue">
            <select class = "settingsNewSignInEmailNotificationsSelect">
              <option value = "1">Opt In for Early Releases</option>
              <option selected value = "0">Opt Out</option>
            </select>
          </div>
        </div>
      </div>
      <hr>

      <!-- Space -->
      <div class = "settingsSpace"></div>

      <!-- Notifications -->
      <h class = "settingsHeaders">Notifications</h>
      <hr>

      <!-- New Sign-in -->
      <div class = "settingsSettingWithButton">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>New Sign-in</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div class = "settingsValue">
            <select class = "settingsNewSignInSelect">
              <option selected value = "2">Most Secure (Recommended)</option>
              <option value = "1">Standard</option>
              <option value = "0">None</option>
            </select>
          </div>
        </div>
      </div>
      <hr>

      <!-- Workspace File Changes -->
      <div class = "settingsSettingWithButton">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Workspace File Changes</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div class = "settingsValue">
            <select class = "settingsFileChangesSelect">
              <option value = "3">Notify Me of All File Changes</option>
              <option value = "2">Notify Me Only of New File Uploads</option>
              <option value = "1">Notify Me Only of File Renames</option>
              <option selected value = "0">Do Not Notify Me of Any Changes</option>
            </select>
          </div>
        </div>
      </div>
      <hr>

      <!-- New Workspace Member -->
      <div class = "settingsSettingWithButton">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>New Workspace Member</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div class = "settingsValue">
            <select class = "settingsNewWorkspaceMemberSelect">
              <option value = "1">Notify Me</option>
              <option selected value = "0">Do Not Notify Me</option>
            </select>
          </div>
        </div>
      </div>
      <hr>

      <!-- Promotional -->
      <div class = "settingsSettingWithButton">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Promotional Corvin News</p>
          </div>
        </div>
        <div class = "settingsRightItemBox">
          <div class = "settingsValue">
            <select class = "settingsPromotionalCorvinNewsSelect">
              <option value = "1">Notify Me of Corvin Promotional News</option>
              <option selected value = "0">Do Not Notify Me</option>
            </select>
          </div>
        </div>
      </div>
      <hr>

      <!-- Space -->
      <div class = "settingsSpace"></div>
      <hr>

      <!-- Delete my Corvin Account -->
      <div class = "settingsSettingWithButton">
        <div class = "settingsLeftItemBox">
          <div class = "settingsItem">
            <p>Delete my Corvin</p>
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
</div>

<div class = "settingsPush"></div>

<!-- 6 Footer -->
<div class = "settingsFooter">&copy; Corvin, Inc.</div>

</body>
</html>
