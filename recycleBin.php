<!--
This is the user's main page for Corvin.

Hierarchy

  0 Check If Logged In
  1 Header
  2 Top Bar
  3 Main Content
    3.1 Upload File [OR FOLDER]
    3.2 Create New Folder
    3.3 Recently Deleted Items
    3.4 Current Directory Navigation Banner
    3.5 Files in Directory
      3.5.1 List Folders and Folder Sizes
        3.5.1.1 Folder Name
        3.5.1.2 Download Folder
        3.5.1.3 Rename Folder
        3.5.1.4 Recycle Folder
        3.5.1.5 Folder Size
        3.5.1.6 Heath
      3.5.2 List Files and File Sizes
        3.5.2.1 File Name
        3.5.2.2 Download File
        3.5.2.3 Rename File
        3.5.2.4 Recycle File
        3.5.2.5 File Size
        3.5.2.6 Heath
    4 Footer

Coded by: Joel N. Johnson
 -->

<!-- 0 Check If Logged In -->
<?php
session_start();

// Check if user is logged in
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

if (isset($_SESSION["currentWorkspace"])) {
  unset($_SESSION["currentWorkspace"]);
}

// Assign user's ID, set in validate.php
$userID = $_SESSION["userID"];

$sql = "SELECT firstName, lastName FROM UserInfo WHERE id = '$userID'";
$user = mysqli_fetch_array(mysqli_query($conn, $sql));

// Set Darkmode/Lightmode
$sql = "SELECT darkmode FROM Preferences WHERE id = '$userID'";
$darkmodeSetting = mysqli_fetch_array(mysqli_query($conn, $sql));

if ($darkmodeSetting[0] == 1) {
  $o = "darkHome";
}
elseif ($darkmodeSetting[0] == 0) {
  $o = "lightHome";
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
elseif (time() - $_SESSION['Created'] > 1200) {
  // Then change session ID for the current session and invalidate old session
  // ID
  session_regenerate_id(true);

  // Update creation time
  $_SESSION['Created'] = time();
}
?>

<!DOCTYPE html>
<html lang = 'en' class = '<?php echo $o;?>'>

<!-- 1 Header -->
<head>
  <title>Recycle | Corvin</title>

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

  <script>var o = <?php echo json_encode($o);?></script>
</head>

<body class = '<?php echo $o;?>'>
<div class = '<?php echo $o;?>Wrapper'>

  <!-- Create New Workspace -->
  <div id = 'createWorkspacePopup' class = '<?php echo $o;?>CreateWorkspacePopup'>
    <div class = '<?php echo $o;?>CreateWorkspaceHeader'>
      <h>Create A Workspace</h>
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
        placeholder = 'Name of New Workspace'
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

          $('#createWorkspaceMessage').show().text("Workspaces cannot start with a number");
        }
      }
    });
  });
  </script>

<!-- 2 Top Bar -->
<div class = '<?php echo $o;?>TopBar'>
  <div class = '<?php echo $o;?>Corvin'>
    <a href = 'home.php'>
      <h class = '<?php echo $o;?>CorvinHeader'>C</h>
    </a>
  </div>
  <div class = '<?php echo $o;?>AccountMenuDropDown'>
    <p onclick = "accountDropDownMenu()" class = '<?php echo $o;?>AccountButton' id = "accountButton">Account</p>
    <div id = "accountMenuContent" class = '<?php echo $o;?>AccountMenuContent'>
      <div class = '<?php echo $o;?>TopAccountMenuContent'>
        <?php
        echo "<p class = '".$o."AccountMenuName'>" . $user[0] . " " . $user[1] . "</p>";

        include "humanSize.php";
        include "folderSize.php";

        $usedBytes = folderSize("../../../../mnt/Raid1Array/Corvin/" .
          $userID . " - " . $user[0] . $user[1]);

        $sql = "SELECT storageSpaceInMegabytes FROM UserInfo WHERE id = '" .
          $userID . "'";
        $storageSpaceInMegabytes = mysqli_fetch_row(mysqli_query($conn, $sql));

        if ($storageSpaceInMegabytes[0] == "-1") {
          $freeBytes = disk_free_space("../../../../mnt/Raid1Array/Corvin");

          echo "<p class = '".$o."DiskSpaceUnlimited'>" . humanSize($usedBytes) .  " used of " .
            humanSize($freeBytes) . " (Unlimited)</p>";
        }
        else {
          $totalBytes = $storageSpaceInMegabytes[0] * 1000000;
          $freeBytes = $totalBytes - $usedBytes;

          echo "<p class = '".$o."DiskSpace'>" . humanSize($usedBytes) .
            " used of " . humanSize($totalBytes) . "</p>";
        }
        ?>
      </div><!--TopAccountMenuContent-->
      <br><div class = '<?php echo $o;?>AccountMenuHeath'><br></div>
      <div class = '<?php echo $o;?>BottomAccountMenuContent'>
        <a class = '<?php echo $o;?>GetMoreSpaceMenuItem' href = "getMoreSpace.php">Get More Space</a>
        <a class = '<?php echo $o;?>MenuItem' href = "settings.php">Settings</a>
        <a class = '<?php echo $o;?>MenuItem' href = "help.php">Help</a>
        <a class = '<?php echo $o;?>MenuItem' href = "logout.php">Log Out</a>
      </div>
    </div>
  </div>
  <div class = '<?php echo $o;?>WorkspacesMenuDropDown'>
    <p onclick = "workspacesDropDownMenu()" class = '<?php echo $o;?>WorkspacesButton' id = "workspacesButton">Workspaces</p>
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
              />
            </form>
        <?php
          }
        }
        ?>
        <div class = '<?php echo $o;?>WorkspacesMenuHeath'></div>
      <?php
      }
      ?>
      <a onclick = 'createWorkspacePopup()' class = '<?php echo $o;?>NewWorkspaceMenuItem'>
          Create A Workspace</a>
      <a class = '<?php echo $o;?>NewWorkspaceMenuItem' href = "">Join A Workspace</a>
    </div>
  </div>
  <div class = '<?php echo $o;?>Home'>
    <p onclick = "window.location.href = 'home.php';" class = '<?php echo $o;?>HomeButton'>Home</p>
  </div>
</div>

<script>
// Workspaces
function workspacesDropDownMenu() {
  if (document.getElementById("workspacesMenuContent").classList.contains(o+"Show")) {
    document.getElementById("workspacesButton").classList.remove(o+"Active");
  }
  else {
    document.getElementById("workspacesButton").classList.add(o+"Active");
    document.getElementById("accountMenuContent").classList.remove(o+"Show");
    document.getElementById("accountButton").classList.remove(o+"Active");
  }
  document.getElementById("workspacesMenuContent").classList.toggle(o+"Show");
}

function createWorkspacePopup() {
  document.getElementById("createWorkspacePopup").classList.toggle(o+"Show");
  document.getElementById("newWorkspaceNameTextField").focus();
}

function cancelCreateWorkspace() {
  document.getElementById("createWorkspacePopup").classList.toggle(o+"Show");
}

// Account
function accountDropDownMenu() {
  if (document.getElementById("accountMenuContent").classList.contains(o+"Show")) {
    document.getElementById("accountButton").classList.remove(o+"Active");
  }
  else {
    document.getElementById("accountButton").classList.add(o+"Active");
    document.getElementById("workspacesMenuContent").classList.remove(o+"Show");
    document.getElementById("workspacesButton").classList.remove(o+"Active");
  }
  document.getElementById("accountMenuContent").classList.toggle(o+"Show");
}

// Click Off
window.onclick = function(event) {
  if (document.getElementById("accountMenuContent").classList.contains(o+"Show")) {
    if (!event.target.matches("."+o+"AccountButton")) {
      document.getElementById("accountMenuContent").classList.remove(o+"Show");
      document.getElementById("accountButton").classList.remove(o+"Active");
    }
  }
  else if (document.getElementById("workspacesMenuContent").classList.contains(o+"Show")) {
    if (!event.target.matches("."+o+"WorkspacesButton")) {
      document.getElementById("workspacesMenuContent").classList.remove(o+"Show");
      document.getElementById("workspacesButton").classList.remove(o+"Active");
    }
  }
}
</script>

<!-- 3 Main Content -->
<div class = '<?php echo $o;?>MainContent'>

  <?php
  parse_str($_SERVER['QUERY_STRING'], $CurrentPath);
  $CurrentPathString = implode("/", $CurrentPath) . "/";
  ?>

  <!-- 3.3 Home -->
  <form
    action = "home.php"
    method = "post"
    enctype = "multipart/form-data"
  >
    <input
      type = "submit"
      class = '<?php echo $o;?>RecentlyDeletedItems'
      value = "Home"
      name = "submit"
    />
  </form>

  <br /><br />

  <!-- 3.4 Current Directory Navigation Banner -->
  <div class = '<?php echo $o;?>DirectoryPath'>
    <?php include "generateURL.php";?>

    <a class = '<?php echo $o;?>DirectoryPath' href = 'recycleBin.php'>
      <p class = '<?php echo $o;?>DirectoryPath'>Recycle</p>
    </a>
    <?php
    parse_str($_SERVER['QUERY_STRING'], $CurrentPath);

    foreach ($CurrentPath as $Key => $Value) {
      if (!is_int($Key)) {
        unset($CurrentPath[$Key]);
      }
    }

    $DirectoryPath = array("");
    $i = 0;
    foreach ($CurrentPath as $Key => $DirectoryPathFolder) {
      if ($DirectoryPathFolder != "") {
        $DirectoryPathFolderURL = generateURL(
          "recycleBin.php?", $DirectoryPath, $DirectoryPathFolder);
        array_push($DirectoryPath, $DirectoryPathFolder);
        $i++;
        echo "
        <p class = '".$o."DirectoryPath'>/</p>
        <a class = '".$o."DirectoryPath' href = '" . $DirectoryPathFolderURL . "'>
          <p class = '".$o."DirectoryPath'>" . $DirectoryPathFolder . "</p>
        </a>";
      }
    }
    ?>

    <br /><br />

  </div>

  <br /><br /><br /><br />

  <!-- 3.5 Files in directory -->
  <div class = '<?php echo $o;?>Directory'>
  <?php
  $ReturnPathString = filter_input(INPUT_POST, "ReturnPathString", FILTER_SANITIZE_STRING);

  if ($ReturnPathString == null) {
    $DirectoryPath = "../../../../mnt/Raid1Array/Corvin/0 - Recycle/" . $userID . " - " .
      $user[0] . $user[1] . "/" . implode("/", $CurrentPath);
  }
  else {
    $DirectoryPath = "../../../../mnt/Raid1Array/Corvin/0 - Recycle/" . $userID . " - " .
      $user[0] . $user[1] . "/" . $ReturnPathString;
  }

  $Directory = scandir($DirectoryPath);
  usort($Directory, "strnatcmp");
  $NumItems = count($Directory);

  // 3.5.1 List Folders and Folder Sizes
  // 3.5.1.1 List Folder Name
  for ($i = 2; $i < $NumItems; $i++) {
    if (is_dir($DirectoryPath . "/" . $Directory[$i])) {
  ?>
      <div class = '<?php echo $o;?>FileNames'>
        <div class = '<?php echo $o;?>Folders'>
          <?php $URL = generateURL("recycleBin.php?", $CurrentPath, $Directory[$i]);?>
          <a
            href = '<?php echo $URL;?>'
            class = '<?php echo $o;?>Folders'
            id = '<?php echo addslashes($Directory[$i]);?>DirectoryName'
          >
            <?php echo $Directory[$i];?>
          </a>
        </div>

        <!-- 3.5.1.2 Download Folder -->
        <div class = '<?php echo $o;?>DownloadButtonForm'>
          <form
            action = 'Zip/download.php'
            class = '<?php echo $o;?>DownloadButtonForm'
            method = 'post'
            enctype = 'multipart/form-data'
          >
            <input
              type = 'hidden'
              value = '<?php echo $Directory[$i];?>'
              name = 'fileToDownload'
            />
            <input
              type = 'image'
              src = 'Art/2 - Download Arrow Icon/NanoLab Download Arrow Icon Width 15px.png'
                class = '<?php echo $o;?>DownloadButton'
                value = 'Download'
                name = 'submit'
                id = '<?php echo addslashes($Directory[$i]);?>DownloadButton'
            />
            <input
              type = 'hidden'
              value = '<?php echo $CurrentPathString;?>'
              name = 'currentPathString'
            />
            <input type = 'hidden' value = '0 - Recycle/' name = 'recycleBin'/>
          </form>
        </div>
      </div>

      <!-- 3.5.1.5 File Sizes -->
      <div class = '<?php echo $o;?>FileSizes'>
        <?php echo humanSize(folderSize($DirectoryPath . "/" . $Directory[$i]));?>
      </div>

      <!-- 3.5.1.6 Heath -->
      <br><div class = '<?php echo $o;?>Heath'><br></div>
  <?php
    }
  }
  ?>
  <!-- 3.5.2 List Files and File Sizes -->
  <!-- 3.5.2.1 File Name -->
  <?php
  function supportedFileTypes($suffix, $directoryi, $directoryPath, $oo) {
    $needstxt = ["csv", "php", "html", "cu", "c", "go"];

    if ($_GET) {
  ?>
      <a
        href = '<?php echo $_SERVER['REQUEST_URI'] . "&" . $suffix . "=" . rawurlencode($directoryi);?>'
        target = '_blank'
        class = '<?php echo $oo;?>Files'
        id = '<?php echo addslashes($directoryi);?>FileName'
      >
        <?php echo $directoryi;?>
      </a>
    <?php
    }
    else {
    ?>
      <a
        href = '<?php echo $_SERVER['REQUEST_URI'] . "?" . $suffix . "=" . rawurlencode($directoryi);?>'
        target = '_blank'
        class = '<?php echo $oo;?>Files'
        id = '<?php echo addslashes($directoryi);?>FileName'
      >
        <?php echo $directoryi;?>
      </a>
    <?php
    }

    if (isset($_GET[$suffix])) {
      $fileToView = rawurldecode($_REQUEST[$suffix]);

      echo $fileToView;

      if (in_array($suffix, $needstxt)) {
        if (copy(
          $directoryPath . "/" . $fileToView,
          "../../../../../../../../../var/www/html/ViewInBrowser/" . $suffix .
          ".txt")
        ) {
          echo "copy successful";
          echo "
          <meta http-equiv = 'refresh' content = '0; url=ViewInBrowser/" .
            $suffix . ".txt'>";
        }
        else {
          echo "copy unsuccessful";
          echo "<meta http-equiv = 'refresh' content = '2'>";
        }
      }
      else {
        if (copy(
          $directoryPath . "/" . $fileToView,
          "../../../../../../../../var/www/html/ViewInBrowser/" . $suffix .
          "." . $suffix)
        ) {
          echo "copy successful";
          echo "
            <meta http-equiv = 'refresh' content = '0; url=ViewInBrowser/" .
              $suffix . "." . $suffix . "'>";
        }
        else {
          echo "copy unsuccessful";
          echo "<meta http-equiv = 'refresh' content = '2'>";
        }
      }
    }
  }

  for ($i = 2; $i < $NumItems; $i++) {
    if (is_file($DirectoryPath . "/" . $Directory[$i])) {
      $SupportedFileTypes = [
        "pdf",
        "txt",
        "csv",
        "bmp",
        "gif",
        "jpg",
        "jpeg",
        "png",
        "webp",
        "3gp",
        "avi",
        "mov",
        "mp4",
        "m4v",
        "m4a",
        "mp3",
        "mkv",
        "ogv",
        "ogm",
        "ogg",
        "oga",
        "webm",
        "wav",
        "tex",
        "bib",
        "php",
        "html",
        "css",
        "json",
        "cu",
        "c",
        "go",
      ];

      echo "<div class = '".$o."FileNames'>";
        echo "<div class = '".$o."Files'>";

          // If the file can be viewed directly in the browser
          if (in_array(
            strtolower(substr($Directory[$i], -4)), $SupportedFileTypes)
          ) {
            supportedFileTypes(strtolower(substr(
              $Directory[$i], -4)), $Directory[$i], $DirectoryPath, $o);
          }
          else if (in_array(
            strtolower(substr($Directory[$i], -3)), $SupportedFileTypes)
          ) {
            supportedFileTypes(strtolower(substr(
              $Directory[$i], -3)), $Directory[$i], $DirectoryPath, $o);
          }
          else if (in_array(
            strtolower(substr($Directory[$i], -2)), $SupportedFileTypes)
          ) {
            supportedFileTypes(strtolower(substr(
              $Directory[$i], -2)), $Directory[$i], $DirectoryPath, $o);
          }
          else if (in_array(
            strtolower(substr($Directory[$i], -1)), $SupportedFileTypes)
          ) {
            supportedFileTypes(strtolower(substr(
              $Directory[$i], -1)), $Directory[$i], $DirectoryPath, $o);
          }
          else {
            echo $Directory[$i];
          }
    ?>
        </div>

        <!-- 3.5.2.2 Download File -->
        <div class = '<?php echo $o;?>DownloadButtonForm'>
          <form
            action = 'Zip/download.php'
            class = '<?php echo $o;?>DownloadButtonForm'
            method = 'post'
            enctype = 'multipart/form-data'
          >
            <input
              type = 'hidden'
              value = '<?php echo $Directory[$i];?>'
              name = 'fileToDownload'
            />
            <input
              type = 'image'
              src = 'Art/2 - Download Arrow Icon/NanoLab Download Arrow Icon @ 36 ppi.png'
              class = '<?php echo $o;?>DownloadButton'
              value = 'Download'
              name = 'submit'
              id = '<?php echo addslashes($Directory[$i]);?>DownloadButton'
            />
            <input
              type = 'hidden'
              value = '<?php echo $CurrentPathString;?>'
              name = 'currentPathString'
            />
            <input type = 'hidden' value = '0 - Recycle/' name = 'recycleBin'/>
          </form>
        </div>
      </div>

      <!-- 3.5.2.5 File Size -->
      <div class = '<?php echo $o;?>FileSizes'>
        <?php
        $FileSize = filesize($DirectoryPath . "/" . $Directory[$i]);
        echo HumanSize($FileSize);
        ?>
      </div>

      <!-- 3.5.2.6 Heath -->
      <br><div class = '<?php echo $o;?>Heath'><br></div>
  <?php
    }
  }
  ?>
  </div> <!-- Directory -->
</div> <!-- Main Content -->

<!-- 4 Footer -->
<div class = '<?php echo $o;?>Push'></div>
</div> <!-- Wrapper -->


<div class = '<?php echo $o;?>Footer'>&copy; Corvin, Inc.</div>

</body>
</html>
