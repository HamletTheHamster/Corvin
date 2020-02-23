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

  <script>var o = <?php echo json_encode($o);?></script>
  <script src = "dragMove.js"></script>
</head>

<body class = '<?php echo $o;?>'>
<div class = '<?php echo $o;?>Wrapper'>

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
        <a class = '<?php echo $o;?>GetMoreSpaceMenuItem' href = "getMoreSpace.php">Get More Storage</a>
        <a class = '<?php echo $o;?>MenuItem' href = "settings.php">Settings</a>
        <a class = '<?php echo $o;?>MenuItem' href = "help.php">Help</a>
        <a class = '<?php echo $o;?>MenuItem' href = "logout.php">Log Out</a>
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
        Create A Space
      </a>
      <a onclick = 'joinWorkspacePopup()' class = '<?php echo $o;?>NewWorkspaceMenuItem'>
        Join A Space
      </a>
    </div>
  </div>
  <div class = '<?php echo $o;?>Home'>
    <p onclick = "window.location.href = 'home.php';" class = '<?php echo $o;?>HomeButton'>
      Home
    </p>
  </div>
</div>

<script src = 'topBar.js'></script>

<!-- 3 Main Content -->
<div class = '<?php echo $o;?>MainContent'>

  <!-- 3.1 Upload File -->
  <form action = "upload.php" method = "post" enctype = "multipart/form-data">
    <span id = "hideWhenFilesSelected">
      <button type = 'button' class = '<?php echo $o;?>CustomFileUpload'>
        <label for = "filesToUpload">Upload Files</label>
      </button>
      <input
        type = "file"
        name = "filesToUpload[]"
        id = "filesToUpload"
        multiple = "multiple"
        onchange = "javascript:updateList()"
      />
    </span>
    <input
      type = "submit"
      class = '<?php echo $o;?>UploadButton'
      id = "uploadButton"
      value = "Upload"
      name = "submit"
    />
    <?php
    parse_str($_SERVER['QUERY_STRING'], $CurrentPath);
    $CurrentPathString = implode("/", $CurrentPath) . "/";
    ?>

    <input
      type = 'hidden'
      value = '<?php echo $CurrentPathString;?>'
      name = 'currentPathString'
    />
    <input type = 'hidden' value = '<?php echo $freeBytes;?>' name = 'freeBytes'/>
    <div id = "fileList"></div>
  </form>

  <script>
  function updateList() {
    var input = document.getElementById('filesToUpload');
    var output = document.getElementById('fileList');

    if (input.files.length > 0) {
      document.getElementById('uploadButton').style.display = 'inline-block';
      document.getElementById('uploadButton').focus();
      document.getElementById('uploadButton').addEventListener(
        "mouseout", mouseout);
      document.getElementById('uploadButton').addEventListener(
        "mousedown", mousedown);

      function mouseout() {
        document.getElementById('uploadButton').style.boxShadow = 'none';
      }

      function mousedown() {
        document.getElementById('uploadButton').style.boxShadow =
          '1px 1px 3px rgba(0,0,0,0.4)';
      }

      document.getElementById('hideWhenFilesSelected').style.display = 'none';
      document.getElementById('createFolderButton').style.display = 'none';
      document.getElementById('recentlyDeletedItems').style.display = 'none';

      output.innerHTML += '<br /><br /><ul>';

      for (var i = 0; i < input.files.length; ++i) {
        output.innerHTML +=
          '<li style = "list-style-type: none; padding-left: 2%;">' +
            (i + 1) + ". " + input.files.item(i).name +
          '</li>';
      }

      output.innerHTML += '</ul>';
    }
  }
  </script>

  <!-- 3.2 Create New Folder -->
  <form
    action = "newFolder.php"
    method = "post"
    enctype = "multipart/form-data"
  >
    <input
      type = "text"
      name = "folderName"
      id = "newFolderNameTextField"
      class = '<?php echo $o;?>NewFolderNameTextBox'
      spellcheck = "false"
      autocomplete = "off"
    />
    <input
      type = "button"
      class = '<?php echo $o;?>CreateFolderButton'
      id = "createFolderButton"
      value = "Create Folder"
      name = "submit"
    />
    <input
      type = 'hidden'
      value = '<?php echo $CurrentPathString;?>'
      name = 'currentPathString'
    />
  </form>



  <!-- 3.3 Recently Deleted Items -->
  <form
    action = "recycleBin.php"
    method = "post"
    enctype = "multipart/form-data"
  >
    <input
      type = "submit"
      class = '<?php echo $o;?>RecentlyDeletedItems'
      id = "recentlyDeletedItems"
      value = "Recently Deleted Items"
      name = "submit"
    />
  </form>
  <script>
  var createFolderButton = document.getElementById("createFolderButton");
  var newFolderNameTextField = document.getElementById(
    "newFolderNameTextField");
  var recentlyDeletedItems = document.getElementById("recentlyDeletedItems");

  newFolderNameTextField.style.display = "none";
  createFolderButton.addEventListener(
    "click", expandNewFolderTextField, false);

  function expandNewFolderTextField() {
    createFolderButton.style.display = "none";
    newFolderNameTextField.style.display = "block";
    recentlyDeletedItems.style.left = "0px";
    newFolderNameTextField.style.fontSize = "18px";
    newFolderNameTextField.style.cssFloat = "left";
    newFolderNameTextField.style.marginRight = "5px";
    newFolderNameTextField.style.borderWidth = "thin";
    newFolderNameTextField.style.borderRadius = "4px";
    newFolderNameTextField.style.paddingLeft = "10px";
    newFolderNameTextField.style.paddingTop = "6px";
    newFolderNameTextField.style.paddingBottom = "6px";
    newFolderNameTextField.placeholder = "New Folder Name";
    newFolderNameTextField.focus();
    newFolderNameTextField.addEventListener(
      "focusout", hideNewFolderTextField, false);
  }

  function hideNewFolderTextField() {
    newFolderNameTextField.style.display = "none";
    createFolderButton.style.display = "block";
    recentlyDeletedItems.style.right = "0px";
  }
  </script>

  <br /><br />

  <!-- 3.4 Current Directory Navigation Banner -->
  <div class = '<?php echo $o;?>DirectoryPath'>
    <?php
    include "generateURL.php";

    $homePath = "../../../../mnt/Raid1Array/Corvin/" . $userID . " - " .
      $user[0] . $user[1];
    ?>

    <a class = '<?php echo $o;?>DirectoryPath' href = 'home.php'>
      <p
        id = '<?php echo $homePath;?>'
        class = '<?php echo $o;?>DirectoryPath'
        draggable = "false"
        ondragover = 'allowDrop(event)'
        ondragleave = 'dragLeave(event)'
        ondrop = "moveUp(event, '<?php echo $homePath . "/" . implode("/", $CurrentPath);?>')"
      >
        Home
      </p>
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
          "home.php?", $DirectoryPath, $DirectoryPathFolder);
        array_push($DirectoryPath, $DirectoryPathFolder);
        $i++;
    ?>
        <p class = '<?php echo $o;?>DirectoryPath'>/</p>
        <a class = '<?php echo $o;?>DirectoryPath' href = '<?php echo $DirectoryPathFolderURL;?>'>
          <p
            id = '<?php echo $homePath . "/" . substr(implode("/", $DirectoryPath), 1);?>'
            class = '<?php echo $o;?>DirectoryPath'
            draggable = "false"
            ondragover = 'allowDrop(event)'
            ondragleave = 'dragLeave(event)'
            ondrop = "moveUp(event, '<?php echo $homePath . "/" . implode("/", $CurrentPath);?>')"
          >
            <?php
            echo $DirectoryPathFolder;
            ?>
          </p>
        </a>
    <?php
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
    $DirectoryPath = $homePath . "/" . implode("/", $CurrentPath);
  }
  else {
    $DirectoryPath = $homePath . "/" . $ReturnPathString;
  }

  $Directory = scandir($DirectoryPath);
  usort($Directory, "strnatcmp");
  $NumItems = count($Directory);

  // 3.5.1 List Folders and Folder Sizes
  // 3.5.1.1 List Folder Name
  for ($i = 2; $i < $NumItems; $i++) {
    if (is_dir($DirectoryPath . "/" . $Directory[$i])) {

      $URL = generateURL("home.php?", $CurrentPath, $Directory[$i]);
  ?>
      <div
        id = '<?php echo addslashes($Directory[$i]);?>'
        class = '<?php echo $o;?>DraggableLedger'
        draggable = "true"
        ondragstart = "drag(event)"
        ondragover = "allowDrop(event)"
        ondragleave = "dragLeave(event)"
        ondrop = "moveDown(event, '<?php echo $DirectoryPath;?>')"
        onclick = "window.location.href = '<?php echo $URL;?>'"
      >
      <script> var directory = <?php echo json_encode($Directory);?>;</script>

        <div class = '<?php echo $o;?>FileNames'>
          <div class = '<?php echo $o;?>Folders'>
            <img
              src = 'Art/5 - Folder And File Icons/<?php echo $o;?> Folder Icon.png'
              class = <?php echo "'".$o."FolderIcon'";?>
            >
            <a
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
              <input type = 'hidden' value = '' name = 'recycleBin'/>
            </form>
          </div>

          <!-- 3.5.1.3 Rename Folder -->
          <div class = '<?php echo $o;?>RenameButtonForm'>
            <input
              type = 'image'
              src = 'Art/4 - Rename Cursor Icon/NanoLab Rename Cursor Icon Width 15px.png'
              id = '<?php echo addslashes($Directory[$i]);?>CursorButton'
              class = '<?php echo $o;?>RenameButton'
            />
            <form
              action = 'rename.php'
              class = '<?php echo $o;?>RenameButtonForm'
              method = 'post'
              enctype = 'multipart/form-data'
            >
              <input
                type = 'hidden'
                value = '<?php echo addslashes($Directory[$i]);?>'
                name = 'oldName'
              />
              <input
                type = 'text'
                value = '<?php echo addslashes($Directory[$i]);?>'
                size = '<?php echo strlen($Directory[$i]);?>'
                onfocus = 'this.select()'
                id = '<?php echo addslashes($Directory[$i]);?>RenameTextField'
                class = '<?php echo $o;?>RenameTextField'
                name = 'newName'
                onclick = 'event.stopPropagation();'
                spellcheck = 'false'
                autocomplete = 'off'
              />
              <input
                type = 'hidden'
                value = '<?php echo $CurrentPathString;?>'
                name = 'currentPathString'
              />
            </form>

            <script>
            var cursorButton = (typeof cursorButton != 'undefined' && cursorButton instanceof Array) ? cursorButton : [];
            var i = (typeof i != 'undefined') ? i : 0;

            cursorButton.push('<?php echo addslashes($Directory[$i]);?>CursorButton');

            document.getElementById(cursorButton[i]).addEventListener('click', showTextBox, false);

            function showTextBox() {
              event.stopPropagation();

              document.getElementById(event.target.id.replace('CursorButton', 'DirectoryName')).style.display = 'none';
              document.getElementById(event.target.id.replace('CursorButton', 'DownloadButton')).style.display = 'none';
              document.getElementById(event.target.id).style.display = 'none';
              document.getElementById(event.target.id.replace('CursorButton', 'RecycleButton')).style.display = 'none';
              document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).style.display = 'block';
              document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).focus();
              document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).addEventListener('focusout', hideRenameTextField, false);
            }

            function hideRenameTextField() {
              event.stopPropagation();

              document.getElementById(event.target.id).style.display = 'none';
              document.getElementById(event.target.id.replace('RenameTextField', 'DirectoryName')).style.display = 'block';
              document.getElementById(event.target.id.replace('RenameTextField', 'DownloadButton')).style.display = 'block';
              document.getElementById(event.target.id.replace('RenameTextField', 'CursorButton')).style.display = 'block';
              document.getElementById(event.target.id.replace('RenameTextField', 'RecycleButton')).style.display = 'block';
            }

            i++;
            </script>
          </div>

          <!-- 3.5.1.4 Recycle Folder -->
          <div class = '<?php echo $o;?>RecycleButtonForm'>
            <form
              action = 'recycle.php'
              class = '<?php echo $o;?>RecycleButtonForm'
              method = 'post'
              enctype = 'multipart/form-data'
            >
              <input
                type = 'hidden'
                value = '<?php echo $Directory[$i];?>'
                name = 'fileToRecycle'
              />
              <input
                type = 'image'
                src = 'Art/3 - Delete Trash Can Icon/NanoLab Delete Trash Can Select Width 25px.png'
                class = '<?php echo $o;?>RecycleButton'
                id = '<?php echo addslashes($Directory[$i]);?>RecycleButton'
              />
              <input
                type = 'hidden'
                value = '<?php echo $CurrentPathString;?>'
                name = 'currentPathString'
              />
            </form>
          </div>

        </div>

        <!-- 3.5.1.5 File Sizes -->
        <div class = '<?php echo $o;?>FileSizes'>
          <?php echo humanSize(folderSize($DirectoryPath . "/" . $Directory[$i]));?>
        </div>
      </div>

      <!-- 3.5.1.6 Heath -->
      <div class = '<?php echo $o;?>Heath'><br></div>
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
      ?>

      <div
        id = '<?php echo addslashes($Directory[$i]);?>'
        class = '<?php echo $o;?>DraggableLedger'
        draggable = "true"
        ondragstart = "drag(event)"
      >
        <div class = '<?php echo $o;?>FileNames'>
          <div class = '<?php echo $o;?>Files'>

            <?php
            $pathParts = pathinfo($Directory[$i]);
            ?>

            <img
              src = 'Art/5 - Folder And File Icons/<?php echo $o.$pathParts['extension'];?> File Icon.png'
              class = '<?php echo $o;?>FileIcon'
            >

            <?php
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
              <input type = 'hidden' value = '' name = 'recycleBin'/>
            </form>
          </div>

          <!-- 3.5.2.3 Rename File -->
          <div class = '<?php echo $o;?>RenameButtonForm'>
            <input
              type = 'image'
              src = 'Art/4 - Rename Cursor Icon/NanoLab Rename Cursor Icon @ 36 ppi.png'
              id = '<?php echo addslashes($Directory[$i]);?>CursorButton'
              class = '<?php echo $o;?>RenameButton'
            />
            <form
              action = 'rename.php'
              class = '<?php echo $o;?>RenameButtonForm'
              method = 'post'
              enctype = 'multipart/form-data'
            >
              <input
                type = 'hidden'
                value = '<?php echo $Directory[$i];?>'
                name = 'oldName'
              />
              <input
                type = 'text'
                value = '<?php echo $Directory[$i];?>'
                size = '<?php echo strlen($Directory[$i]);?>'
                id = '<?php echo addslashes($Directory[$i]);?>RenameTextField'
                class = '<?php echo $o;?>RenameTextField'
                name = 'newName'
                spellcheck = 'false'
                autocomplete = 'off'
              />
              <input
                type = 'hidden'
                value = '<?php echo $CurrentPathString;?>'
                name = 'currentPathString'
              />
            </form>

            <script>
            var cursorButton = (typeof cursorButton != 'undefined' && cursorButton instanceof Array) ? cursorButton : [];
            var i = (typeof i != 'undefined') ? i : 0;

            cursorButton.push('<?php echo addslashes($Directory[$i]);?>CursorButton');

            console.log(cursorButton[i]);

            document.getElementById(cursorButton[i]).addEventListener('click', showFileTextBox, false);

            function showFileTextBox() {
              event.stopPropagation();

              document.getElementById(event.target.id.replace('CursorButton', 'FileName')).style.display = 'none';
              document.getElementById(event.target.id.replace('CursorButton', 'DownloadButton')).style.display = 'none';
              document.getElementById(event.target.id).style.display = 'none';
              document.getElementById(event.target.id.replace('CursorButton', 'RecycleButton')).style.display = 'none';
              document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).style.display = 'block';
              document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).focus();
              document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).setSelectionRange(0, <?php echo strlen(pathinfo($Directory[$i], PATHINFO_FILENAME));?>);
              document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).addEventListener('focusout', hideRenameFileTextField, false);
            }

            function hideRenameFileTextField() {
              event.stopPropagation();

              document.getElementById(event.target.id).style.display = 'none';
              document.getElementById(event.target.id.replace('RenameTextField', 'FileName')).style.display = 'block';
              document.getElementById(event.target.id.replace('RenameTextField', 'DownloadButton')).style.display = 'block';
              document.getElementById(event.target.id.replace('RenameTextField', 'CursorButton')).style.display = 'block';
              document.getElementById(event.target.id.replace('RenameTextField', 'RecycleButton')).style.display = 'block';
            }

            i++;
            </script>
          </div>

          <!-- 3.5.2.4 Recycle File -->
          <div class = '<?php echo $o;?>RecycleButtonForm'>
            <form
              action = 'recycle.php'
              class = '<?php echo $o;?>RecycleButtonForm'
              method = 'post'
              enctype = 'multipart/form-data'
            >
              <input
                type = 'hidden'
                value = '<?php echo $Directory[$i];?>'
                name = 'fileToRecycle'
              />
              <input
                type = 'image'
                src = 'Art/3 - Delete Trash Can Icon/NanoLab Delete Trash Can Select @ 36 ppi.png'
                class = '<?php echo $o;?>RecycleButton'
                id = '<?php echo addslashes($Directory[$i]);?>RecycleButton'
              />
              <input
                type = 'hidden'
                value = '<?php echo $CurrentPathString;?>'
                name = 'currentPathString'
              />
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
      </div>

      <!-- 3.5.2.6 Heath -->
      <div class = '<?php echo $o;?>Heath'><br></div>
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
