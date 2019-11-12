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

// Set Darkmode/Lightmode
$sql = "SELECT darkmode FROM UserInfo WHERE id = '$userID'";
$darkmodeSetting = mysqli_fetch_array(mysqli_query($conn, $sql));

if ($darkmodeSetting[0] == 1) {
  $o = "darkSettings";
}
elseif ($darkmodeSetting[0] == 0) {
  $o = "lightSettings";
}

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
<html lang = 'en' class = <?php echo "'".$o."'";?>>

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

  <script type = "text/javascript" src = "jquery-3.4.1.min.js"></script>
  <script>var o = <?php echo json_encode($o); ?>;</script>
</head>

<body class = <?php echo "'".$o."'";?>>
<div id = 'wrapper' class = <?php echo "'".$o."Wrapper'";?>>

  <!-- 2 Top Bar -->
  <div class = <?php echo "'".$o."TopBar'";?>>
    <div class = <?php echo "'".$o."Corvin'";?>>
      <a href = "home.php">
        <h id = "corvinHeader" class = <?php echo "'".$o."CorvinHeader'";?>>C</h>
      </a>
    </div>
    <div class = <?php echo "'".$o."AccountMenuDropDown'";?>>
      <p onclick = 'accountDropDownMenu()' id = "accountButton" class = <?php echo "'".$o."AccountButton'";?>>Account</p>
      <div id = 'accountMenuContent' class = <?php echo "'".$o."AccountMenuContent'";?>>
        <div class = <?php echo "'".$o."TopAccountMenuContent'";?>>
          <?php
          echo "<p id = 'accountMenuName' class = '".$o."AccountMenuName'>" . $user[0] . " " . $user[1] . "</p>";

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

            echo "<p id = 'diskSpace' class = '".$o."DiskSpace'>" . humanSize($usedBytes) . " used of " .
              humanSize($freeBytes) . " (Unlimited)</p>";
          }
          else {
            $totalBytes = $storageSpaceInMegabytes[0] * 1000000;
            $freeBytes = $totalBytes - $usedBytes;

            echo "<p id = 'diskSpace' class = '".$o."DiskSpace'>" . humanSize($usedBytes) .
              " used of " . humanSize($totalBytes) . "</p>";
          }
          ?>
        </div>
        <br><div id = "menuHeath" class = <?php echo "'".$o."MenuHeath'";?>><br></div>
        <div class = <?php echo "'".$o."BottomAccountMenuContent'";?>>
          <a class = <?php echo "'".$o."GetMoreSpaceMenuItem'";?> href = 'getMoreSpace.php'>
            Get More Space</a>
          <a id = 'home' class = <?php echo "'".$o."MenuItem'";?>>Settings</a>
          <a id = 'help' class = <?php echo "'".$o."MenuItem'";?> href = 'help.php'>Help</a>
          <a id = 'logout' class = <?php echo "'".$o."MenuItem'";?> href = 'logout.php'>Log Out</a>
        </div>
      </div>
    </div>
  </div>

  <script>
  function accountDropDownMenu() {
    document.getElementById('accountMenuContent').classList.toggle(o+"Show");
  }

  window.onclick = function(event) {
    if (!event.target.matches("."+o+"AccountButton")) {
      if (document.getElementById("accountMenuContent").classList.contains(o+"Show")) {
        document.getElementById("accountMenuContent").classList.remove(o+"Show");
      }
    }
  }
  </script>

  <!-- 5 Main Content -->
  <div  id = 'mainContent' class = <?php echo "'".$o."MainContent'";?>>
    <form
      action = 'getMoreSpace.php'
      method = 'post'
      enctype = 'multipart/form-data'
    >
      <input
        type = 'submit'
        class = <?php echo "'".$o."GetMoreSpaceButton'";?>
        id = 'getMoreSpaceButton'
        value = 'Get More Space'
        name = 'submit'
      />
    </form>

    <div class = <?php echo "'".$o."Header'";?>>
      <p id = 'settingsHeader' class = <?php echo "'".$o."Header'";?>>Settings</p>
    </div>

    <div class = <?php echo "'".$o."Content'";?>>
      <div class = <?php echo "'".$o."Space'";?>></div>
      <h class = <?php echo "'".$o."FirstHeader'";?>>Account Details</h><br>
      <br><div id = "heath1" class = <?php echo "'".$o."Heath'";?>><br></div>

      <!-- Name -->
      <div onclick = 'toggleNameDropdown()' class = <?php echo "'".$o."Setting'";?>>
        <div class = <?php echo "'".$o."LeftItemBox'";?>>
          <div class = <?php echo "'".$o."Item'";?>>
            <p id = 'name' class = <?php echo "'".$o."Item'";?>>Name</p>
          </div>
        </div>
        <div class = <?php echo "'".$o."RightItemBox'";?>>
          <div id = 'nameValue' class = <?php echo "'".$o."Value'";?>>
            <?php
            $sql = "SELECT firstName, lastName FROM UserInfo WHERE id = '" .
              $userID . "'";
            $user = mysqli_fetch_row(mysqli_query($conn, $sql));
            echo "<p id = 'nameValuep' class = '".$o."Value'>" . $user[0] . " " . $user[1] . "</p>";
            ?>
          </div>
        </div>
      </div>
      <div id = 'dropdownName' class = <?php echo "'".$o."DropdownSetting'";?>>
        <form
          action = "updateAccountSettings.php"
          method = "post"
          enctype = "multipart/form-data"
          class = <?php echo "'".$o."ChangeName'";?>
        >
          <input
            type = "password"
            name = "submittedPassword"
            id = "namePasswordCheck"
            class = <?php echo "'".$o."NamePasswordVerifyTextBox'";?>
            placeholder = "Password"
            autocomplete = "current-password"
            required
          >
          <input
            type = "text"
            name = "firstNameChange"
            id = "firstNameChangeTextBox"
            class = <?php echo "'".$o."FirstNameChangeTextBox'";?>
            value = <?php echo "'" . $user[0] . "'";?>
            placeholder = "First Name"
            required
            spellcheck = "false"
            autocomplete = "off"
          />
          <input
            type = "text"
            name = "lastNameChange"
            id = "lastNameChangeTextBox"
            class = <?php echo "'".$o."LastNameChangeTextBox'";?>
            value = <?php echo "'" . $user[1] . "'";?>
            placeholder = "Last Name"
            required
            spellcheck = "false"
            autocomplete = "off"
          />
          <input
            type = "submit"
            value = "Save Changes"
            id = "nameSaveChangesButton"
            class = <?php echo "'".$o."SaveChangesButton'";?>
          />
        </form>
      </div>
      <script>
      document.getElementById("dropdownName").style.display = "none";

      function toggleNameDropdown() {
        var dropdownName = document.getElementById("dropdownName");

        if (dropdownName.style.display == "none") {
          dropdownName.style.display = "block";
          nameValue.style.display = "none";
          document.getElementById("namePasswordCheck").focus();
        }
        else {
          dropdownName.style.display = "none";
          nameValue.style.display = "block";
        }
      }
      </script>
      <br><div id = "heath2" class = <?php echo "'".$o."Heath'";?>><br></div>

      <!-- Profile Image -->
      <div class = <?php echo "'".$o."SettingWithButton'";?>>
        <div class = <?php echo "'".$o."LeftItemBox'";?>>
          <div class = <?php echo "'".$o."Item'";?>>
            <p id = 'profileImage' class = <?php echo "'".$o."Item'";?>>Profile Image</p>
          </div>
        </div>
        <div class = <?php echo "'".$o."RightItemBox'";?>>
          <div class = <?php echo "'".$o."Value'";?>>
            <?php
            $uploadedProfileImage =
              "../../../../mnt/Raid1Array/Corvin/00 - Profile Images/" .
              $userID . " - " . $user[0] . $user[1] . ".jpg";
            $defaultProfileImage =
              "Art/1 - Default Profile Icons/Corvin Castle Icon" .
              ($userID % 10) . ".jpg";
            if (file_exists($uploadedProfileImage)) {
              echo "<img class = '".$o."ProfileImage' src = '" .
                $uploadedProfileImage . "'>";
            }
            else {
              echo "<img class = '".$o."ProfileImage' src = '" .
                $defaultProfileImage . "'>";
            }
            ?>
          </div>
        </div>
      </div>
      <br><div id = "heath3" class = <?php echo "'".$o."Heath'";?>><br></div>

      <!-- Email -->
      <div onclick = "toggleEmailDropdown()" class = <?php echo "'".$o."Setting'";?>>
        <div class = <?php echo "'".$o."LeftItemBox'";?>>
          <div class = <?php echo "'".$o."Item'";?>>
            <p id = 'email' class = <?php echo "'".$o."Item'";?>>Email</p>
          </div>
        </div>
        <div class = <?php echo "'".$o."RightItemBox'";?>>
          <div id = "emailValue" class = <?php echo "'".$o."Value'";?>>
            <?php
            $sql = "SELECT email FROM UserInfo WHERE id = '" . $userID . "'";
            $email = mysqli_fetch_row(mysqli_query($conn, $sql));
            echo "<p id = 'emailValuep' class = '".$o."Value'>" . $email[0] . "</p>";
            ?>
          </div>
        </div>
      </div>
      <div id = "dropdownEmail" class = <?php echo "'".$o."DropdownSetting'";?>>
        <form
          action = "updateAccountSettings.php"
          method = "post"
          enctype = "multipart/form-data"
          class = "ChangeName"
        >
          <input
            type = "password"
            name = "submittedPassword"
            id = "emailPasswordCheck"
            class = <?php echo "'".$o."EmailPasswordVerifyTextBox'";?>
            placeholder = "Password"
            autocomplete = "current-password"
            required
          >
          <input
            type = "email"
            name = "emailChange"
            id = "emailChangeTextBox"
            class = <?php echo "'".$o."EmailChangeTextBox'";?>
            value = <?php echo "'" . $email[0] . "'";?>
            placeholder = "Email"
            required
            spellcheck = "false"
            autocomplete = "email"
          />
          <input
            type = "submit"
            value = "Save Changes"
            id = "emailSaveChangesButton"
            class = <?php echo "'".$o."SaveChangesButton'";?>
          />
        </form>
      </div>
      <script>
      document.getElementById("dropdownEmail").style.display = "none";

      function toggleEmailDropdown() {
        var dropdownEmail = document.getElementById("dropdownEmail");

        if (dropdownEmail.style.display == "none") {
          dropdownEmail.style.display = "block";
          emailValue.style.display = "none";
          document.getElementById("emailPasswordCheck").focus();
        }
        else {
          dropdownEmail.style.display = "none";
          emailValue.style.display = "block";
        }
      }
      </script>
      <br><div id = "heath4" class = <?php echo "'".$o."Heath'";?>><br></div>

      <!-- Username -->
      <div onclick = "toggleUsernameDropdown()" class = <?php echo "'".$o."Setting'";?>>
        <div class = <?php echo "'".$o."LeftItemBox'";?>>
          <div class = <?php echo "'".$o."Item'";?>>
            <p id = 'username' class = <?php echo "'".$o."Item'";?>>Username</p>
          </div>
        </div>
        <div class = <?php echo "'".$o."RightItemBox'";?>>
          <div id = "usernameValue" class = <?php echo "'".$o."Value'";?>>
            <?php
            $sql = "SELECT username FROM UserInfo WHERE id = '" . $userID . "'";
            $username = mysqli_fetch_row(mysqli_query($conn, $sql));
            echo "<p id = 'usernameValuep' class = '".$o."Value'>" . $username[0] . "</p>";
            ?>
          </div>
        </div>
      </div>
      <div id = "dropdownUsername" class = <?php echo "'".$o."DropdownSetting'";?>>
        <form
          action = "updateAccountSettings.php"
          method = "post"
          enctype = "multipart/form-data"
          class = <?php echo "'".$o."ChangeName'";?>
        >
          <input
            type = "password"
            name = "submittedPassword"
            id = "usernamePasswordCheck"
            class = <?php echo "'".$o."UsernamePasswordVerifyTextBox'";?>
            placeholder = "Password"
            autocomplete = "current-password"
            required
          >
          <input
            type = "text"
            name = "usernameChange"
            id = "usernameChangeTextBox"
            class = <?php echo "'".$o."UsernameChangeTextBox'";?>
            value = <?php echo "'" . $username[0] . "'";?>
            placeholder = "Username"
            required
            spellcheck = "false"
            autocomplete = "off"
          />
          <input
            type = "submit"
            value = "Save Changes"
            id = "usernameSaveChangesButton"
            class = <?php echo "'".$o."SaveChangesButton'";?>
          />
        </form>
      </div>
      <script>
      document.getElementById("dropdownUsername").style.display = "none";

      function toggleUsernameDropdown() {
        var dropdownUsername = document.getElementById("dropdownUsername");

        if (dropdownUsername.style.display == "none") {
          dropdownUsername.style.display = "block";
          usernameValue.style.display = "none";
          document.getElementById("usernamePasswordCheck").focus();
        }
        else {
          dropdownUsername.style.display = "none";
          usernameValue.style.display = "block";
        }
      }
      </script>
      <br><div id = "heath5" class = <?php echo "'".$o."Heath'";?>><br></div>

      <!-- Password -->
      <div onclick = "togglePasswordDropdown()" class = <?php echo "'".$o."Setting'";?>>
        <div class = <?php echo "'".$o."LeftItemBox'";?>>
          <div class = <?php echo "'".$o."Item'";?>>
            <p id = 'password' class = <?php echo "'".$o."Item'";?>>Password</p>
          </div>
        </div>
        <div class = <?php echo "'".$o."RightItemBox'";?>>
          <div class = <?php echo "'".$o."Value'";?>>
          </div>
        </div>
      </div>
      <div id = "dropdownPassword"
        class = <?php echo "'".$o."DropdownSettingPassword'";?>>
        <div class = <?php echo "'".$o."PasswordRequirement'";?>>
          <p id = "passwordLengthRequirement" class = <?php echo "'".$o."PasswordRequirement'";?>>
            Password must contain at least 8 characters
          </P>
        </div>
        <div class = <?php echo "'".$o."PasswordChangeForm'";?>>
          <form
            action = "updateAccountSettings.php"
            method = "post"
            enctype = "multipart/form-data"
            class = <?php echo "'".$o."ChangeName'";?>
          >
            <input
              type = "password"
              name = "submittedPassword"
              id = "oldPassword"
              class = <?php echo "'".$o."PasswordOldPasswordVerifyTextBox'";?>
              placeholder = "Current Password"
              autocomplete = "current-password"
              required
            >
            <input
              type = "password"
              name = "newPassword"
              id = "newPasswordTextBox"
              class = <?php echo "'".$o."PasswordChangeTextBox'";?>
              placeholder = "New Password"
              autocomplete = "new-password"
              required
            />
            <input
              type = "password"
              name = "newPassword2"
              id = "newPassword2TextBox"
              class = <?php echo "'".$o."PasswordChangeTextBox'";?>
              placeholder = "Re-enter New Password"
              autocomplete = "new-password"
              required
            />
            <input
              type = "submit"
              value = "Save Changes"
              id = "passwordSaveChangesButton"
              class = <?php echo "'".$o."SaveChangesButton'";?>
            />
          </form>
        </div>
      </div>
      <script>
      document.getElementById("dropdownPassword").style.display = "none";

      function togglePasswordDropdown() {
        var dropdownPassword = document.getElementById("dropdownPassword");

        if (dropdownPassword.style.display == "none") {
          dropdownPassword.style.display = "block";
          document.getElementById("oldPassword").focus();
        }
        else {
          dropdownPassword.style.display = "none";
        }
      }
      </script>
      <br><div id = "heath6" class = <?php echo "'".$o."Heath'";?>><br></div>

      <!-- Space -->
      <div class = <?php echo "'".$o."Space'";?>></div>

      <!-- Preferences -->
      <h class = <?php echo "'".$o."FirstHeader'";?>>Preferences</h><br>
      <br><div id = "heath7" class = <?php echo "'".$o."Heath'";?>><br></div>

      <!-- Dark Mode -->
      <div class = <?php echo "'".$o."SettingWithButton'";?>>
        <div class = <?php echo "'".$o."LeftItemBox'";?>>
          <div class = <?php echo "'".$o."Item'";?>>
            <p id = 'darkmode' class = <?php echo "'".$o."Item'";?>>Dark Mode</p>
          </div>
        </div>
        <div class = <?php echo "'".$o."RightItemBox'";?>>
          <div class = <?php echo "'".$o."Value'";?>>
            <form id = "darkmodeForm">
              <label class = <?php echo "'".$o."Switch'";?>>
                <input id = "darkmodeSlider" onclick = "manualModeSwitch()" type="checkbox"
                <?php
                if ($o == "darkSettings") {echo "checked";}
                elseif ($o == "lightSettings") {echo "unchecked";}
                ?>
                >
                <span class = <?php echo "'".$o."Slider'";?>></span>
              </label>
            </form>
          </div>
        </div>
      </div>
      <script type = "text/javascript">
      $("#darkmodeSlider").change(function() {

        var mode = $(this).prop("checked");

        $.ajax({
          type: "POST",
          dataType: "JSON",
          url: "updateAccountPreferences.php",
          data: {darkmode: mode},
          /*
          success: function(data) {

            var data = eval(data);
            message = data.message;
            success = data.success;
            alert("data: " + message + "\nsuccess: " + success);
          }
          */
        });
      });
      </script>
      <script>
      function manualModeSwitch() {

        if (document.getElementById("darkmodeSlider").checked) {

          // Body
          document.getElementById("wrapper").style.backgroundColor = "rgb(28, 29, 31)";
          document.getElementById("mainContent").style.backgroundColor = "rgb(28, 29, 31)";
          document.getElementById("push").style.backgroundColor = "rgb(28, 29, 31)";
          document.getElementById("footer").style.backgroundColor = "rgb(28, 29, 31)";
          document.body.style.color = "rgba(255, 255, 255, 0.85)";

          // Corvin C & Account Menu
          document.getElementById("corvinHeader").style.color = "rgb(0, 130, 140)";
          document.getElementById("accountButton").style.color = "rgb(0, 130, 140)";
          document.getElementById("accountMenuContent").style.backgroundColor = "rgb(53, 54, 56)";
          document.getElementById("accountMenuContent").style.borderColor = "rgba(255, 255, 255, 0.25)";
          document.getElementById("accountMenuContent").style.boxShadow = "0 2px 4px 0 rgba(255, 255, 255, 0)";
          document.getElementById("accountMenuName").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("diskSpace").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("menuHeath").style.backgroundColor = "rgb(18, 19, 21)";
          document.getElementById("home").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("help").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("logout").style.color = "rgba(255, 255, 255, 0.85)";

          // Header Area
          document.getElementById("getMoreSpaceButton").style.borderColor = "rgb(28, 29, 31)";
          document.getElementById("settingsHeader").style.color = "rgb(255, 255, 255, 0.85)";

          // Bars
          for (bar = 1; bar < 20; bar++) {
            document.getElementById("heath"+bar).style.backgroundColor = "rgb(18, 19, 21)";
          }

          // Collapsed Account Details
          document.getElementById("name").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("nameValuep").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("profileImage").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("email").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("emailValuep").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("username").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("usernameValuep").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("password").style.color = "rgba(255, 255, 255, 0.85)";

          // Expanded Name
          document.getElementById("namePasswordCheck").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
          document.getElementById("namePasswordCheck").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("firstNameChangeTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
          document.getElementById("firstNameChangeTextBox").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("lastNameChangeTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
          document.getElementById("lastNameChangeTextBox").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("nameSaveChangesButton").style.backgroundColor = "rgb(53, 54, 56)";
          document.getElementById("nameSaveChangesButton").style.borderColor = "rgba(255, 255, 255, 0)";

          // Expanded Email
          document.getElementById("emailPasswordCheck").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
          document.getElementById("emailPasswordCheck").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("emailChangeTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
          document.getElementById("emailChangeTextBox").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("emailSaveChangesButton").style.backgroundColor = "rgb(53, 54, 56)";
          document.getElementById("emailSaveChangesButton").style.borderColor = "rgba(255, 255, 255, 0)";

          // Expanded Username
          document.getElementById("usernamePasswordCheck").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
          document.getElementById("usernamePasswordCheck").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("usernameChangeTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
          document.getElementById("usernameChangeTextBox").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("usernameSaveChangesButton").style.backgroundColor = "rgb(53, 54, 56)";
          document.getElementById("usernameSaveChangesButton").style.borderColor = "rgba(255, 255, 255, 0)";

          // Expanded Password
          document.getElementById("passwordLengthRequirement").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("oldPassword").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
          document.getElementById("oldPassword").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("newPasswordTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
          document.getElementById("newPasswordTextBox").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("newPassword2TextBox").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
          document.getElementById("newPassword2TextBox").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("passwordSaveChangesButton").style.backgroundColor = "rgb(53, 54, 56)";
          document.getElementById("passwordSaveChangesButton").style.borderColor = "rgba(255, 255, 255, 0)";

          // Preferences
          document.getElementById("darkmode").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("ledgerSize").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("smallerLedgerSizeButton").style.backgroundColor = "rgb(53, 54, 56)";
          document.getElementById("smallerLedgerSizeButton").style.borderColor = "rgba(255, 255, 255, 0)";
          document.getElementById("standardLedgerSizeButton").style.backgroundColor = "rgb(53, 54, 56)";
          document.getElementById("standardLedgerSizeButton").style.borderColor = "rgba(255, 255, 255, 0)";
          document.getElementById("largerLedgerSizeButton").style.backgroundColor = "rgb(53, 54, 56)";
          document.getElementById("largerLedgerSizeButton").style.borderColor = "rgba(255, 255, 255, 0)";
          document.getElementById("dateFormat").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("timeZone").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("language").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("corvinBetaProgram").style.color = "rgba(255, 255, 255, 0.85)";

          // Notifications
          document.getElementById("newSignIn").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("workspaceFileChanges").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("newWorkspaceMember").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("promotionalCorvinNews").style.color = "rgba(255, 255, 255, 0.85)";

          // Delete My Corvin
          document.getElementById("deleteMyCorvin").style.color = "rgba(255, 255, 255, 0.85)";
          document.getElementById("deleteMyCorvinButton").style.backgroundColor = "rgb(53, 54, 56)";
          document.getElementById("deleteMyCorvinButton").style.borderColor = "rgba(255, 255, 255, 0)";
        }
        else {

          // Body
          document.getElementById("wrapper").style.backgroundColor = "rgb(254, 254, 254)";
          document.getElementById("mainContent").style.backgroundColor = "rgb(254, 254, 254)";
          document.getElementById("push").style.backgroundColor = "rgb(254, 254, 254)";
          document.getElementById("footer").style.backgroundColor = "rgb(254, 254, 254)";
          document.body.style.color = "rgba(23, 23, 23, 0.85)";

          // Corvin C & Account Menu
          document.getElementById("corvinHeader").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("accountButton").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("accountMenuContent").style.backgroundColor = "rgb(254, 254, 254)";
          document.getElementById("accountMenuContent").style.borderColor = "rgba(23, 23, 23, 0.25)";
          document.getElementById("accountMenuContent").style.boxShadow = "0 2px 4px 0 rgba(23, 23, 23, 0.25)";
          document.getElementById("accountMenuName").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("diskSpace").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("menuHeath").style.backgroundColor = "rgba(18, 19, 21, 0.25)";
          document.getElementById("home").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("help").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("logout").style.color = "rgba(23, 23, 23, 0.85)";

          // Header Area
          document.getElementById("getMoreSpaceButton").style.borderColor = "rgb(254, 254, 254)";
          document.getElementById("settingsHeader").style.color = "rgba(23, 23, 23, 0.85)";

          // Bars
          for (bar = 1; bar < 20; bar++) {
            document.getElementById("heath"+bar).style.backgroundColor = "rgb(18, 19, 21, 0.25)";
          }

          // Collapsed Account Details
          document.getElementById("name").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("nameValuep").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("profileImage").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("email").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("emailValuep").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("username").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("usernameValuep").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("password").style.color = "rgba(23, 23, 23, 0.85)";

          // Expanded Name
          document.getElementById("namePasswordCheck").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
          document.getElementById("namePasswordCheck").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("firstNameChangeTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
          document.getElementById("firstNameChangeTextBox").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("lastNameChangeTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
          document.getElementById("lastNameChangeTextBox").style.color = "rgba(0, 23, 23, 0.85)";
          document.getElementById("nameSaveChangesButton").style.backgroundColor = "rgba(51, 204, 255, 0)";
          document.getElementById("nameSaveChangesButton").style.borderColor = "rgba(23, 23, 23, 0.25)";

          // Expanded Email
          document.getElementById("emailPasswordCheck").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
          document.getElementById("emailPasswordCheck").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("emailChangeTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
          document.getElementById("emailChangeTextBox").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("emailSaveChangesButton").style.backgroundColor = "rgb(51, 204, 255, 0)";
          document.getElementById("emailSaveChangesButton").style.borderColor = "rgba(23, 23, 23, 0.25)";

          // Expanded Username
          document.getElementById("usernamePasswordCheck").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
          document.getElementById("usernamePasswordCheck").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("usernameChangeTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
          document.getElementById("usernameChangeTextBox").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("usernameSaveChangesButton").style.backgroundColor = "rgb(51, 204, 255, 0)";
          document.getElementById("usernameSaveChangesButton").style.borderColor = "rgba(23, 23, 23, 0.25)";

          // Expanded Password
          document.getElementById("passwordLengthRequirement").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("oldPassword").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
          document.getElementById("oldPassword").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("newPasswordTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
          document.getElementById("newPasswordTextBox").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("newPassword2TextBox").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
          document.getElementById("newPassword2TextBox").style.color = "rgba(0, 23, 23, 0.85)";
          document.getElementById("passwordSaveChangesButton").style.backgroundColor = "rgb(51, 204, 255, 0)";
          document.getElementById("passwordSaveChangesButton").style.borderColor = "rgba(23, 23, 23, 0.25)";

          // Preferences
          document.getElementById("darkmode").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("ledgerSize").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("smallerLedgerSizeButton").style.backgroundColor = "rgba(51, 204, 255, 0)";
          document.getElementById("smallerLedgerSizeButton").style.borderColor = "rgba(23, 23, 23, 0.25)";
          document.getElementById("standardLedgerSizeButton").style.backgroundColor = "rgba(51, 204, 255, 0)";
          document.getElementById("standardLedgerSizeButton").style.borderColor = "rgba(23, 23, 23, 0.25)";
          document.getElementById("largerLedgerSizeButton").style.backgroundColor = "rgba(51, 204, 255, 0)";
          document.getElementById("largerLedgerSizeButton").style.borderColor = "rgba(23, 23, 23, 0.25)";
          document.getElementById("dateFormat").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("timeZone").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("language").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("corvinBetaProgram").style.color = "rgba(23, 23, 23, 0.85)";

          // Notifications
          document.getElementById("newSignIn").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("workspaceFileChanges").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("newWorkspaceMember").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("promotionalCorvinNews").style.color = "rgba(23, 23, 23, 0.85)";

          // Delete My Corvin
          document.getElementById("deleteMyCorvin").style.color = "rgba(23, 23, 23, 0.85)";
          document.getElementById("deleteMyCorvinButton").style.backgroundColor = "rgba(51, 204, 255, 0)";
          document.getElementById("deleteMyCorvinButton").style.borderColor = "rgba(23, 23, 23, 0.25)";
        }
      }
      </script>
      <br><div id = "heath8" class = <?php echo "'".$o."Heath'";?>><br></div>

      <!-- Ledger Size -->
      <div class = <?php echo "'".$o."SettingWithButton'";?>>
        <div class = <?php echo "'".$o."LeftItemBox'";?>>
          <div class = <?php echo "'".$o."Item'";?>>
            <p id = 'ledgerSize' class = <?php echo "'".$o."Item'";?>>Ledger Size</p>
          </div>
        </div>
        <div class = <?php echo "'".$o."RightItemBox'";?>>
          <div class = <?php echo "'".$o."Value'";?>>
            <button id = "smallerLedgerSizeButton" class = <?php echo "'".$o."LedgerSizeButton'";?>>Smaller</button>
            <button id = "standardLedgerSizeButton" class = <?php echo "'".$o."LedgerSizeButton'";?>>Standard</button>
            <button id = "largerLedgerSizeButton" class = <?php echo "'".$o."LedgerSizeButton'";?>>Larger</button>
          </div>
        </div>
      </div>
      <br><div id = "heath9" class = <?php echo "'".$o."Heath'";?>><br></div>

      <!-- Date Format -->
      <div class = <?php echo "'".$o."SettingWithButton'";?>>
        <div class = <?php echo "'".$o."LeftItemBox'";?>>
          <div class = <?php echo "'".$o."Item'";?>>
            <p id = 'dateFormat' class = <?php echo "'".$o."Item'";?>>Date Format</p>
          </div>
        </div>
        <div class = <?php echo "'".$o."RightItemBox'";?>>
          <div class = <?php echo "'".$o."Value'";?>>
            <?php //want today's date as examples ?>
            <select class = <?php echo "'".$o."DateFormatSelect'";?>>
              <option selected value = "1">10/14/2019</option>
              <option value = "2">14/10/2019</option>
              <option value = "3">October 14th, 2019</option>
            </select>
          </div>
        </div>
      </div>
      <br><div id = "heath10" class = <?php echo "'".$o."Heath'";?>><br></div>

      <!-- Time Zone -->
      <div class = <?php echo "'".$o."SettingWithButton'";?>>
        <div class = <?php echo "'".$o."LeftItemBox'";?>>
          <div class = <?php echo "'".$o."Item'";?>>
            <p id = 'timeZone' class = <?php echo "'".$o."Item'";?>>Time Zone</p>
          </div>
        </div>
        <div class = <?php echo "'".$o."RightItemBox'";?>>
          <div class = <?php echo "'".$o."Value'";?>>
            <select class = <?php echo "'".$o."TimeZoneSelect'";?>>
              <?php //loop through array of all time zones ?>
              <option value = "(UTC-07:00) Arizona">(UTC-07:00) Arizona</option>
            </select>
          </div>
        </div>
      </div>
      <br><div id = "heath11" class = <?php echo "'".$o."Heath'";?>><br></div>

      <!-- Language -->
      <div class = <?php echo "'".$o."SettingWithButton'";?>>
        <div class = <?php echo "'".$o."LeftItemBox'";?>>
          <div class = <?php echo "'".$o."Item'";?>>
            <p id = 'language' class = <?php echo "'".$o."Item'";?>>Language</p>
          </div>
        </div>
        <div class = <?php echo "'".$o."RightItemBox'";?>>
          <div class = <?php echo "'".$o."Value'";?>>
            <select class = <?php echo "'".$o."LanguageSelect'";?>>
              <?php //loop through array of all supported languages ?>
              <option value = "English (US)">English (US)</option>
            </select>
          </div>
        </div>
      </div>
      <br><div id = "heath12" class = <?php echo "'".$o."Heath'";?>><br></div>

      <!-- Corvin βeta Program -->
      <div class = <?php echo "'".$o."SettingWithButton'";?>>
        <div class = <?php echo "'".$o."LeftItemBox'";?>>
          <div class = <?php echo "'".$o."Item'";?>>
            <p id = 'corvinBetaProgram' class = <?php echo "'".$o."Item'";?>>Corvin βeta Program</p>
          </div>
        </div>
        <div class = <?php echo "'".$o."RightItemBox'";?>>
          <div class = <?php echo "'".$o."Value'";?>>
            <select class = <?php echo "'".$o."NewSignInEmailNotificationsSelect'";?>>
              <option value = "1">Opt In for Early Releases</option>
              <option selected value = "0">Opt Out</option>
            </select>
          </div>
        </div>
      </div>
      <br><div id = "heath13" class = <?php echo "'".$o."Heath'";?>><br></div>

      <!-- Space -->
      <div class = <?php echo "'".$o."Space'";?>></div>

      <!-- Notifications -->
      <h class = <?php echo "'".$o."FirstHeader'";?>>Notifications</h><br>
      <br><div id = "heath14" class = <?php echo "'".$o."Heath'";?>><br></div>

      <!-- New Sign-in -->
      <div class = <?php echo "'".$o."SettingWithButton'";?>>
        <div class = <?php echo "'".$o."LeftItemBox'";?>>
          <div class = <?php echo "'".$o."Item'";?>>
            <p id = 'newSignIn' class = <?php echo "'".$o."Item'";?>>New Sign-in</p>
          </div>
        </div>
        <div class = <?php echo "'".$o."RightItemBox'";?>>
          <div class = <?php echo "'".$o."Value'";?>>
            <select class = <?php echo "'".$o."NewSignInSelect'";?>>
              <option selected value = "2">Most Secure (Recommended)</option>
              <option value = "1">Standard</option>
              <option value = "0">None</option>
            </select>
          </div>
        </div>
      </div>
      <br><div id = "heath15" class = <?php echo "'".$o."Heath'";?>><br></div>

      <!-- Workspace File Changes -->
      <div class = <?php echo "'".$o."SettingWithButton'";?>>
        <div class = <?php echo "'".$o."LeftItemBox'";?>>
          <div class = <?php echo "'".$o."Item'";?>>
            <p id = 'workspaceFileChanges' class = <?php echo "'".$o."Item'";?>>Workspace File Changes</p>
          </div>
        </div>
        <div class = <?php echo "'".$o."RightItemBox'";?>>
          <div class = <?php echo "'".$o."Value'";?>>
            <select class = <?php echo "'".$o."FileChangesSelect'";?>>
              <option value = "3">Notify Me of All File Changes</option>
              <option value = "2">Notify Me Only of New File Uploads</option>
              <option value = "1">Notify Me Only of File Renames</option>
              <option selected value = "0">Do Not Notify Me of Any Changes</option>
            </select>
          </div>
        </div>
      </div>
      <br><div id = "heath16" class = <?php echo "'".$o."Heath'";?>><br></div>

      <!-- New Workspace Member -->
      <div class = <?php echo "'".$o."SettingWithButton'";?>>
        <div class = <?php echo "'".$o."LeftItemBox'";?>>
          <div class = <?php echo "'".$o."Item'";?>>
            <p id = 'newWorkspaceMember' class = <?php echo "'".$o."Item'";?>>New Workspace Member</p>
          </div>
        </div>
        <div class = <?php echo "'".$o."RightItemBox'";?>>
          <div class = <?php echo "'".$o."Value'";?>>
            <select class = <?php echo "'".$o."NewWorkspaceMemberSelect'";?>>
              <option value = "1">Notify Me</option>
              <option selected value = "0">Do Not Notify Me</option>
            </select>
          </div>
        </div>
      </div>
      <br><div id = "heath17" class = <?php echo "'".$o."Heath'";?>><br></div>

      <!-- Promotional -->
      <div class = <?php echo "'".$o."SettingWithButton'";?>>
        <div class = <?php echo "'".$o."LeftItemBox'";?>>
          <div class = <?php echo "'".$o."Item'";?>>
            <p id = 'promotionalCorvinNews' class = <?php echo "'".$o."Item'";?>>Promotional Corvin News</p>
          </div>
        </div>
        <div class = <?php echo "'".$o."RightItemBox'";?>>
          <div class = <?php echo "'".$o."Value'";?>>
            <select class = <?php echo "'".$o."PromotionalCorvinNewsSelect'";?>>
              <option value = "1">Notify Me of Corvin Promotional News</option>
              <option selected value = "0">Do Not Notify Me</option>
            </select>
          </div>
        </div>
      </div>
      <br><div id = "heath18" class = <?php echo "'".$o."Heath'";?>><br></div>

      <!-- Space -->
      <div class = <?php echo "'".$o."Space'";?>></div>
      <br><div id = "heath19" class = <?php echo "'".$o."Heath'";?>><br></div>

      <!-- Delete my Corvin Account -->
      <div class = <?php echo "'".$o."SettingWithButton'";?>>
        <div class = <?php echo "'".$o."LeftItemBox'";?>>
          <div class = <?php echo "'".$o."Item'";?>>
            <p id = 'deleteMyCorvin' class = <?php echo "'".$o."Item'";?>>Delete my Corvin</p>
          </div>
        </div>
        <div class = <?php echo "'".$o."RightItemBox'";?>>
          <div class = <?php echo "'".$o."Value'";?>>
            <button id = "deleteMyCorvinButton" class = <?php echo "'".$o."DeleteAccountButton'";?>>
              Delete Account
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id = 'push' class = <?php echo "'".$o."Push'";?>></div>

<!-- 6 Footer -->
<div id = 'footer' class = <?php echo "'".$o."Footer'";?>>&copy; Corvin, Inc.</div>

</body>
</html>
