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
$sql = "SELECT darkmode FROM Preferences WHERE id = '$userID'";
$darkmodeSetting = mysqli_fetch_array(mysqli_query($conn, $sql));

if ($darkmodeSetting[0] == 1) {
  $o = "darkWorkspaceSettings";
}
elseif ($darkmodeSetting[0] == 0) {
  $o = "lightWorkspaceSettings";
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
<html lang = 'en' class = '<?php echo $o;?>'>

<!-- 1 Header -->
<head>
  <title><?php echo $thisWorkspaceName;?> Settings | Corvin</title>

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

<body class = '<?php echo $o;?>'>
<div id = 'wrapper' class = '<?php echo $o;?>Wrapper'>

  <!-- Create New Workspace -->
  <div id = 'createWorkspacePopup' class = '<?php echo $o;?>CreateWorkspacePopup'>
    <div class = '<?php echo $o;?>CreateWorkspaceHeader'>
      <h>Create A Corvin Space</h>
    </div>
    <div class = '<?php echo $o;?>CreateWorkspaceMessage'>
      <p id = 'createWorkspaceMessage' class = '<?php echo $o;?>CreateWorkspaceMessage'></p>
    </div>
    <form id = 'createNewWorkspaceForm'>
      <input
        type = 'text'
        name = 'newWorkspaceName'
        id = 'newWorkspaceNameTextField'
        class = '<?php echo $o;?>NewWorkspaceNameTextField'
        placeholder = 'Give your Space a name'
        autocomplete = 'off'
        required
      />
      <button id = 'createWorkspaceSubmitButton' class = '<?php echo $o;?>CreateWorkspaceSubmitButton'>
        Create
      </button>
    </form>
    <div>
      <button onclick = 'cancelCreateWorkspace()' class = '<?php echo $o;?>CancelCreateWorkspaceButton'>
        Cancel
      </button>
    </div>
  </div>
  <script type = 'text/javascript'>
  $('#createNewWorkspaceForm').submit(function (event) {

    event.preventDefault();
    var newWorkspaceName = $('#newWorkspaceNameTextField').val();

    $.ajax({
      type: 'POST',
      dataType: 'JSON',
      url: 'newWorkspace.php',
      data: {
        newWorkspaceName: newWorkspaceName
      },
      success: function(data) {

        var data = eval(data);
        message = data.message;

        if (message == 'true') {

          location.href = "workspace.php";
        }
        else {

          $('#createWorkspaceMessage').show().text("Corvin Spaces cannot start with a number.");
        }
      }
    });
  });
  </script>

  <!-- Join A Workspace -->
  <div id = 'joinWorkspacePopup' class = '<?php echo $o;?>JoinWorkspacePopup'>
    <div class = '<?php echo $o;?>JoinWorkspaceHeader'>
      <h>Join A Corvin Space</h>
    </div>
    <div class = '<?php echo $o;?>JoinWorkspaceMessage'>
      <p id = 'joinWorkspaceMessage' class = '<?php echo $o;?>JoinWorkspaceMessage'></p>
    </div>
    <form id = 'joinWorkspaceForm'>
      <input
        type = 'text'
        name = 'joinWorkspaceName'
        id = 'joinWorkspaceCodeTextField'
        class = '<?php echo $o;?>JoinWorkspaceCodeTextField'
        placeholder = 'Corvin Space Invite Code'
        autocomplete = 'off'
        required
      />
      <button id = 'joinWorkspaceSubmitButton' class = '<?php echo $o;?>JoinWorkspaceSubmitButton'>
        Join
      </button>
    </form>
    <div>
      <button onclick = 'cancelJoinWorkspace()' class = '<?php echo $o;?>CancelJoinWorkspaceButton'>
        Cancel
      </button>
    </div>
  </div>
  <script type = 'text/javascript'>
  $('#joinWorkspaceForm').submit(function (event) {

    event.preventDefault();
    var joinWorkspaceName = $('#joinWorkspaceCodeTextField').val();

    $.ajax({
      type: 'POST',
      dataType: 'JSON',
      url: 'joinWorkspace.php',
      data: {
        joinWorkspaceName: joinWorkspaceName
      },
      success: function(data) {

        var data = eval(data);
        message = data.message;

        if (message == 'true') {

          location.href = 'workspace.php';
        }
        else {

          $('#joinWorkspaceMessage').show().text("Invalid Corvin Space invite code.");
        }
      }
    });
  });
  </script>

<!-- 2 Top Bar -->
<div class = '<?php echo $o;?>TopBar'>
  <div class = '<?php echo $o;?>Corvin'>
    <a href = 'home.php'>
      <h id = 'corvinHeader' class = '<?php echo $o;?>CorvinHeader'>C</h>
    </a>
  </div>
  <div class = '<?php echo $o;?>AccountMenuDropDown'>
    <p onclick = "accountDropDownMenu()" class = '<?php echo $o;?>AccountButton' id = "accountButton">Account</p>
    <div id = "accountMenuContent" class = '<?php echo $o;?>AccountMenuContent'>
      <div class = '<?php echo $o;?>TopAccountMenuContent'>
        <?php
        echo "<p id = 'accountMenuName' class = '".$o."AccountMenuName'>" . $user[0] . " " . $user[1] . "</p>";

        include "humanSize.php";
        include "folderSize.php";

        $usedBytes = folderSize("../../../../mnt/Raid1Array/Corvin/" .
          $userID . " - " . $user[0] . $user[1]);

        $sql = "SELECT storageSpaceInMegabytes FROM UserInfo WHERE id = '" .
          $userID . "'";
        $storageSpaceInMegabytes = mysqli_fetch_row(mysqli_query($conn, $sql));

        if ($storageSpaceInMegabytes[0] == "-1") {
          $freeBytes = disk_free_space("../../../../mnt/Raid1Array/Corvin");

          echo "<p id = 'diskSpace' class = '".$o."DiskSpaceUnlimited'>" . humanSize($usedBytes) .  " used of " .
            humanSize($freeBytes) . " (Unlimited)</p>";
        }
        else {
          $totalBytes = $storageSpaceInMegabytes[0] * 1000000;
          $freeBytes = $totalBytes - $usedBytes;

          echo "<p id = 'diskSpace' class = '".$o."DiskSpace'>" . humanSize($usedBytes) .
            " used of " . humanSize($totalBytes) . "</p>";
        }
        ?>
      </div><!--TopAccountMenuContent-->
      <br><div id = 'accountMenuHeath' class = '<?php echo $o;?>AccountMenuHeath'><br></div>
      <div class = '<?php echo $o;?>BottomAccountMenuContent'>
        <a id = 'getMoreSpace' class = '<?php echo $o;?>GetMoreSpaceMenuItem' href = "getMoreSpace.php">Get More Storage</a>
        <a id = 'accountSettings' class = '<?php echo $o;?>MenuItem' href = "settings.php">Settings</a>
        <a id = 'help' class = '<?php echo $o;?>MenuItem' href = "help.php">Help</a>
        <a id = 'logout' class = '<?php echo $o;?>MenuItem' href = "logout.php">Log Out</a>
      </div>
    </div>
  </div>
  <div class = '<?php echo $o;?>WorkspacesMenuDropDown'>
    <p onclick = "workspacesDropDownMenu()" class = '<?php echo $o;?>WorkspacesButton' id = "workspacesButton">Corvin Spaces</p>
    <div id = "workspacesMenuContent" class = '<?php echo $o;?>WorkspacesMenuContent'>
      <?php
      // Get user's row from Workspaces as an array
      $sql = "SELECT * FROM Workspaces WHERE id = '$userID';";
      $workspaces = mysqli_fetch_row(mysqli_query($conn, $sql));

      if ($workspaces[1] != NULL) {

        // For each element in $workspaces
        foreach ($workspaces as $key => $value) {

          if ($value != NULL && $key > 0) {

            $workspaceName = ltrim($value, '0123456789'); ?>
            <form action = 'workspace.php' method = 'post' enctype = 'multipart/form-data'>
              <input type = 'hidden' name = 'workspace' value = '<?php echo $value;?>' />
              <input
                type = 'submit'
                value = '<?php echo $workspaceName;?>'
                class = '<?php echo $o;?>Workspace'
                id = '<?php echo $value;?>Workspace'
              />
            </form>
        <?php
          }
        }
        ?>
        <div id = 'workspacesMenuHeath' class = '<?php echo $o;?>WorkspacesMenuHeath'></div>
      <?php
      }
      ?>
      <a onclick = 'createWorkspacePopup()' class = '<?php echo $o;?>NewWorkspaceMenuItem'>
        Create A Space</a>
      <a onclick = 'joinWorkspacePopup()' class = '<?php echo $o;?>NewWorkspaceMenuItem'>
        Join A Space
      </a>
    </div>
  </div>
  <div class = '<?php echo $o;?>Home'>
    <p id = 'homeButton' onclick = "window.location.href = 'home.php';" class = '<?php echo $o;?>HomeButton'>Home</p>
  </div>
</div>
<script type = 'text/javascript'>var workspaces = <?php echo json_encode($workspaces);?>;</script>
<script type = 'text/javascript' src = 'topBar.js'></script>

  <!-- 5 Main Content -->
  <div  id = 'mainContent' class = '<?php echo $o;?>MainContent'>
    <form
      action = 'getMoreSpace.php'
      method = 'post'
      enctype = 'multipart/form-data'
    >
      <input
        type = 'submit'
        class = '<?php echo $o;?>GetMoreSpaceButton'
        id = 'getMoreSpaceButton'
        value = 'Get More Space'
        name = 'submit'
      />
    </form>

    <div class = '<?php echo $o;?>Header'>
      <p id = 'settingsHeader' class = '<?php echo $o;?>Header'>
        <?php echo $thisWorkspaceName;?> Settings
      </p>
    </div>

    <div class = '<?php echo $o;?>Content'>
      <div class = '<?php echo $o;?>Space'></div>
      <h class = '<?php echo $o;?>FirstHeader'>Basic</h><br>
      <br><div id = "heath1" class = '<?php echo $o;?>Heath'><br></div>

      <!-- Workspace Name -->
      <div onclick = 'toggleNameDropdown()' class = '<?php echo $o;?>Setting'>
        <div class = '<?php echo $o;?>LeftItemBox'>
          <div class = '<?php echo $o;?>Item'>
            <p id = 'name' class = '<?php echo $o;?>Item'>Name</p>
          </div>
        </div>
        <div class = '<?php echo $o;?>RightItemBox'>
          <div id = 'nameValue' class = '<?php echo $o;?>Value'>
            <p id = 'nameValuep' class = '<?php echo $o;?>Value'>
              <?php echo $thisWorkspaceName;?>
            </p>
          </div>
        </div>
      </div>
      <div id = 'dropdownName' class = '<?php echo $o;?>DropdownSetting'>
        <form
          action = "updateWorkspaceSettings.php"
          method = "post"
          enctype = "multipart/form-data"
          class = '<?php echo $o;?>ChangeName'
        >
          <input
            type = "text"
            name = "newWorkspaceName"
            id = "newNameTextBox"
            class = '<?php echo $o;?>WorkspaceNameChangeTextBox'
            value = '<?php echo $thisWorkspaceName;?>'
            placeholder = "New Workspace Name"
            required
            spellcheck = "false"
            autocomplete = "off"
          />
          <input
            type = "submit"
            value = "Save"
            id = "nameSaveChangesButton"
            class = '<?php echo $o;?>SaveChangesButton'
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
      <br><div id = "heath2" class = '<?php echo $o;?>Heath'><br></div>

      <!-- Workspace Image -->
      <div class = '<?php echo $o;?>SettingWithButton'>
        <div class = '<?php echo $o;?>LeftItemBox'>
          <div class = '<?php echo $o;?>Item'>
            <p id = 'profileImage' class = '<?php echo $o;?>Item'>Picture</p>
          </div>
        </div>
        <div class = '<?php echo $o;?>RightItemBox'>
          <div class = '<?php echo $o;?>Value'>
            <?php
            $uploadedWorkspaceImage =
              "../../../../mnt/Raid1Array/Corvin/000 - Workspaces/00 - Workspace Images/" .
              $thisWorkspace . ".jpg";
            $defaultWorkspaceImage =
              "Art/1 - Default Profile Icons/Corvin Castle Icon0.jpg";
            if (file_exists($uploadedWorkspaceImage)) {
              echo "<img class = '".$o."WorkspaceImage' src = '" .
                $uploadedWorkspaceImage . "'>";
            }
            else {
              echo "<img class = '".$o."WorkspaceImage' src = '" .
                $defaultWorkspaceImage . "'>";
            }
            ?>
          </div>
        </div>
      </div>
      <br><div id = "heath3" class = '<?php echo $o;?>Heath'><br></div>

      <!-- Require Full Name -->
      <div  class = '<?php echo $o;?>Setting'>
        <div class = '<?php echo $o;?>LeftItemBox'>
          <div class = '<?php echo $o;?>Item'>
            <p id = 'invitePriviledges' class = '<?php echo $o;?>Item'>Require Full Name</p>
          </div>
        </div>
        <div class = '<?php echo $o;?>RightItemBox'>
          <div id = 'invitePriviledgesValue' class = '<?php echo $o;?>Value'>
            <p id = 'invitePriviledgesValuep' class = '<?php echo $o;?>Value'>
              No
            </p>
          </div>
        </div>
      </div>
      <br><div id = "heath4" class = '<?php echo $o;?>Heath'><br></div>

      <!-- Active Members -->
      <div  class = '<?php echo $o;?>Setting'>
        <div class = '<?php echo $o;?>LeftItemBox'>
          <div class = '<?php echo $o;?>Item'>
            <p id = 'invitePriviledges' class = '<?php echo $o;?>Item'>Currently Active Members</p>
          </div>
        </div>
        <div class = '<?php echo $o;?>RightItemBox'>
          <div id = 'invitePriviledgesValue' class = '<?php echo $o;?>Value'>
            <p id = 'invitePriviledgesValuep' class = '<?php echo $o;?>Value'>
              Show
            </p>
          </div>
        </div>
      </div>
      <br><div id = "heath4" class = '<?php echo $o;?>Heath'><br></div>

      <!-- Nested Workspaces -->
      <div  class = '<?php echo $o;?>Setting'>
        <div class = '<?php echo $o;?>LeftItemBox'>
          <div class = '<?php echo $o;?>Item'>
            <p id = 'invitePriviledges' class = '<?php echo $o;?>Item'>Nested Spaces</p>
          </div>
        </div>
        <div class = '<?php echo $o;?>RightItemBox'>
          <div id = 'invitePriviledgesValue' class = '<?php echo $o;?>Value'>
            <p id = 'invitePriviledgesValuep' class = '<?php echo $o;?>Value'>
              Members
            </p>
          </div>
        </div>
      </div>
      <br><div id = "heath4" class = '<?php echo $o;?>Heath'><br></div>

      <div class = '<?php echo $o;?>Space'></div>
      <h class = '<?php echo $o;?>FirstHeader'>Approvals</h><br>
      <br><div id = "heath5" class = '<?php echo $o;?>Heath'><br></div>

      <!-- Gold Dot Approval -->
      <div  class = '<?php echo $o;?>Setting'>
        <div class = '<?php echo $o;?>LeftItemBox'>
          <div class = '<?php echo $o;?>Item'>
            <p id = 'invitePriviledges' class = '<?php echo $o;?>Item'>Gold Dot Approval</p>
          </div>
        </div>
        <div class = '<?php echo $o;?>RightItemBox'>
          <div id = 'invitePriviledgesValue' class = '<?php echo $o;?>Value'>
            <p id = 'invitePriviledgesValuep' class = '<?php echo $o;?>Value'>
              Admin
            </p>
          </div>
        </div>
      </div>
      <br><div id = "heath3" class = '<?php echo $o;?>Heath'><br></div>

      <!-- Blue Dot Approval -->
      <div  class = '<?php echo $o;?>Setting'>
        <div class = '<?php echo $o;?>LeftItemBox'>
          <div class = '<?php echo $o;?>Item'>
            <p id = 'invitePriviledges' class = '<?php echo $o;?>Item'>Blue Dot Approval</p>
          </div>
        </div>
        <div class = '<?php echo $o;?>RightItemBox'>
          <div id = 'invitePriviledgesValue' class = '<?php echo $o;?>Value'>
            <p id = 'invitePriviledgesValuep' class = '<?php echo $o;?>Value'>
              Members
            </p>
          </div>
        </div>
      </div>
      <br><div id = "heath3" class = '<?php echo $o;?>Heath'><br></div>

      <div class = '<?php echo $o;?>Space'></div>
      <h class = '<?php echo $o;?>FirstHeader'>Members</h><br>
      <br><div id = "heath5" class = '<?php echo $o;?>Heath'><br></div>

      <!-- Invitation Priviledges -->
      <div  class = '<?php echo $o;?>Setting'>
        <div class = '<?php echo $o;?>LeftItemBox'>
          <div class = '<?php echo $o;?>Item'>
            <p id = 'invitePriviledges' class = '<?php echo $o;?>Item'>Invitation Priviledges</p>
          </div>
        </div>
        <div class = '<?php echo $o;?>RightItemBox'>
          <div id = 'invitePriviledgesValue' class = '<?php echo $o;?>Value'>
            <p id = 'invitePriviledgesValuep' class = '<?php echo $o;?>Value'>
              Admin
            </p>
          </div>
        </div>
      </div>
      <br><div id = "heath3" class = '<?php echo $o;?>Heath'><br></div>

      <!-- Admin Promotion -->
      <div  class = '<?php echo $o;?>Setting'>
        <div class = '<?php echo $o;?>LeftItemBox'>
          <div class = '<?php echo $o;?>Item'>
            <p id = 'invitePriviledges' class = '<?php echo $o;?>Item'>Admin Promotion</p>
          </div>
        </div>
        <div class = '<?php echo $o;?>RightItemBox'>
          <div id = 'invitePriviledgesValue' class = '<?php echo $o;?>Value'>
            <p id = 'invitePriviledgesValuep' class = '<?php echo $o;?>Value'>
              Owner
            </p>
          </div>
        </div>
      </div>
      <br><div id = "heath3" class = '<?php echo $o;?>Heath'><br></div>

      <div class = '<?php echo $o;?>Space'></div>
      <h class = '<?php echo $o;?>FirstHeader'>Permissions</h><br>
      <br><div id = "heath6" class = '<?php echo $o;?>Heath'><br></div>

      <!-- Restricted Directories & Files -->
      <div  class = '<?php echo $o;?>Setting'>
        <div class = '<?php echo $o;?>LeftItemBox'>
          <div class = '<?php echo $o;?>Item'>
            <p id = 'invitePriviledges' class = '<?php echo $o;?>Item'>Allow Restricted Directories</p>
          </div>
        </div>
        <div class = '<?php echo $o;?>RightItemBox'>
          <div id = 'invitePriviledgesValue' class = '<?php echo $o;?>Value'>
            <p id = 'invitePriviledgesValuep' class = '<?php echo $o;?>Value'>
              Admin
            </p>
          </div>
        </div>
      </div>
      <br><div id = "heath3" class = '<?php echo $o;?>Heath'><br></div>

      <!-- Hidden Directories & Files -->
      <div  class = '<?php echo $o;?>Setting'>
        <div class = '<?php echo $o;?>LeftItemBox'>
          <div class = '<?php echo $o;?>Item'>
            <p id = 'invitePriviledges' class = '<?php echo $o;?>Item'>Allow Hidden Directories</p>
          </div>
        </div>
        <div class = '<?php echo $o;?>RightItemBox'>
          <div id = 'invitePriviledgesValue' class = '<?php echo $o;?>Value'>
            <p id = 'invitePriviledgesValuep' class = '<?php echo $o;?>Value'>
              Admin
            </p>
          </div>
        </div>
      </div>
      <br><div id = "heath3" class = '<?php echo $o;?>Heath'><br></div>

    </div>
  </div>
</div>

<div id = 'push' class = '<?php echo $o;?>Push'></div>

<!-- 6 Footer -->
<div id = 'footer' class = '<?php echo $o;?>Footer'>&copy; Corvin, Inc.</div>

</body>
</html>
