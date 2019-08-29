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

//Assign user's ID, set in validate.php
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
elseif (time() - $_SESSION['Created'] > 1200) {
  // Then change session ID for the current session and invalidate old session
  // ID
  session_regenerate_id(true);

  // Update creation time
  $_SESSION['Created'] = time();
}
?>

<!DOCTYPE html>
<html lang = "en" class = "home">

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

<body class = "home">
<div class = "homeWrapper">

<!-- 2 Top Bar -->
<div class = "homeTopBar">
  <div class = "homeCorvin">
    <?php
    echo "<a href = 'home.php'>" . "<h class = 'homeCorvinHeader'>C</h>" . "</a>";
    ?>
  </div>
  <div class = "homeAccountMenuDropDown">
    <p onclick = "accountDropDownMenu()" class = "homeAccountButton">Account</p>
    <div id = "AccountMenuContent" class = "homeAccountMenuContent">
      <div class = "homeTopAccountMenuContent">
        <?php
        echo "<p class = 'homeAccountMenuName'>" . $user[0] . " " . $user[1] . "</p>";

        include "humanSize.php";
        include "folderSize.php";

        $usedBytes = folderSize("../../../../mnt/Raid1Array/Corvin/" .
          $userID . " - " . $user[0] . $user[1]);

        $sql = "SELECT storageSpaceInMegabytes FROM UserInfo WHERE id = '" .
          $userID . "'";
        $storageSpaceInMegabytes = mysqli_fetch_row(mysqli_query($conn, $sql));

        if ($storageSpaceInMegabytes[0] == "-1") {
          $totalBytes = disk_total_space("../../../../mnt/Raid1Array/Corvin");
          $freeBytes = disk_free_space("../../../../mnt/Raid1Array/Corvin");

          echo "<p class = 'homeDiskSpace'>" . humanSize($usedBytes) .  " used of " .
            humanSize($freeBytes) . " (Unlimited)</p>";
        }
        else {
          $totalBytes = $storageSpaceInMegabytes[0] * 1000000;
          $freeBytes = $totalBytes - $usedBytes;

          echo "<p class = 'homeDiskSpace'>" . humanSize($usedBytes) .
            " used of " . humanSize($totalBytes) . "</p>";
        }
        ?>
      </div><!--TopAccountMenuContent-->
      <div class = "homeMenuLine">
        <hr class = "homeMenuLine"/>
      </div>
      <div class = "homeBottomAccountMenuContent">
        <a class = "homeGetMoreSpaceMenuItem" href = "getMoreSpace.php">
          Get More Space</a>
        <a class = "homeMenuItem" href = "settings.php">Settings</a>
        <a class = "homeMenuItem" href = "help.php">Help</a>
        <a class = "homeMenuItem" href = "logout.php">Log Out</a>
      </div>
    </div>
  </div>
</div>

<script>
function accountDropDownMenu() {
  document.getElementById("AccountMenuContent").classList.toggle("homeShow");
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

<!-- 3 Main Content -->
<div class = "homeMainContent">

  <!-- 3.1 Upload File -->
  <form action = "upload.php" method = "post" enctype = "multipart/form-data">
    <span id = "hideWhenFilesSelected">
      <button type = "button" class = "homeCustomFileUpload">
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
      class = "homeUploadButton"
      id = "uploadButton"
      value = "Upload"
      name = "submit"
    />
    <?php
    parse_str($_SERVER['QUERY_STRING'], $CurrentPath);
    $CurrentPathString = implode("/", $CurrentPath) . "/";

    echo "
    <input
      type = 'hidden'
      value = '" . $CurrentPathString . "'
      name = 'currentPathString'
    />
    <input type = 'hidden' value = '" . $freeBytes . "' name = 'freeBytes'/>";
    ?>
    <div id="fileList"></div>
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
      document.getElementById('CreateFolderButton').style.display = 'none';
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
    <input type = "text" name = "folderName" id = "NewFolderNameTextField"/>
    <input
      type = "button"
      class = "homeCreateFolderButton"
      id = "CreateFolderButton"
      value = "Create Folder"
      name = "submit"
      id = "CreateFolderButton"
    />
    <?php
    echo "
    <input
      type = 'hidden'
      value = '" . $CurrentPathString . "'
      name = 'currentPathString'
    />";
    ?>
  </form>

  <script>
  var createFolderButton = document.getElementById("CreateFolderButton");
  var newFolderNameTextField = document.getElementById(
    "NewFolderNameTextField");

  newFolderNameTextField.style.display = "none";
  createFolderButton.addEventListener(
    "click", expandNewFolderTextField, false);

  function expandNewFolderTextField() {
    createFolderButton.style.display = "none";
    newFolderNameTextField.style.display = "block";
    newFolderNameTextField.style.fontSize = "18px";
    newFolderNameTextField.style.cssFloat = "left";
    newFolderNameTextField.style.marginRight = "5px";
    newFolderNameTextField.style.borderColor = "rgba(23, 23, 23, 0.25)";
    newFolderNameTextField.style.borderWidth = "thin";
    newFolderNameTextField.style.borderRadius = "4px";
    newFolderNameTextField.style.paddingLeft = "10px";
    newFolderNameTextField.style.paddingTop = "6px";
    newFolderNameTextField.style.paddingBottom = "6px";
    newFolderNameTextField.placeholder = "New Folder Name";
    newFolderNameTextField.style.backgroundColor = "rgba(0, 130, 140, 0.06)";
    newFolderNameTextField.addEventListener("focus", function () {
      newFolderNameTextField.style.outline = "none";
      newFolderNameTextField.style.borderColor = "rgba(23, 23, 23, 0.85)";
    });
    newFolderNameTextField.focus();
    newFolderNameTextField.addEventListener(
      "focusout", hideNewFolderTextField, false);
  }

  function hideNewFolderTextField() {
    newFolderNameTextField.style.display = "none";
    createFolderButton.style.display = "block";
  }
  </script>

  <!-- 3.3 Recently Deleted Items -->
  <form
    action = "recycleBin.php"
    method = "post"
    enctype = "multipart/form-data"
  >
    <input
      type = "submit"
      class = "homeRecentlyDeletedItems"
      id = "recentlyDeletedItems"
      value = "Recently Deleted Items"
      name = "submit"
    />
  </form>

  <br /><br />

  <!-- 3.4 Current Directory Navigation Banner -->
  <div class = "DirectoryPath">
    <?php
    include "generateURL.php";

    echo "
    <a class = 'homeDirectoryPath' href = 'home.php'>
      <p class = 'homeDirectoryPath'>Home</p>
    </a>";

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
        echo "
        <p class = 'homeDirectoryPath'>/</p>
        <a class = 'homeDirectoryPath' href = '" . $DirectoryPathFolderURL . "'>
          <p class = 'homeDirectoryPath'>" . $DirectoryPathFolder . "</p>
        </a>";
      }
    }
    ?>

    <br /><br />

  </div>

  <br /><br /><br /><br />

  <!-- 3.5 Files in directory -->
  <div class = "homeDirectory">
  <?php
  $ReturnPathString = filter_input(
    INPUT_POST, "ReturnPathString", FILTER_SANITIZE_STRING);

  if ($ReturnPathString == null) {
    $DirectoryPath = "../../../../mnt/Raid1Array/Corvin/" . $userID . " - " .
      $user[0] . $user[1] . "/" . implode("/", $CurrentPath);
  }
  else {
    $DirectoryPath = "../../../../mnt/Raid1Array/Corvin/" . $userID . " - " .
      $user[0] . $user[1] . "/" . $ReturnPathString;
  }

  $Directory = scandir($DirectoryPath);
  usort($Directory, "strnatcmp");
  $NumItems = count($Directory);

  // 3.5.1 List Folders and Folder Sizes
  // 3.5.1.1 List Folder Name
  for ($i = 2; $i < $NumItems; $i++) {
    if (is_dir($DirectoryPath . "/" . $Directory[$i])) {
      echo "<div class = 'homeFileNames'>";
        echo "<div class = 'homeFolders'>";
          $URL = generateURL("home.php?", $CurrentPath, $Directory[$i]);
          echo "
          <a
            href = '" . $URL . "'
            id = '" . preg_replace('/\s+/', '', $Directory[$i]) .
            "DirectoryName'
          >" .
            $Directory[$i] .
          "</a>";
        echo "</div>";

        // 3.5.1.2 Download Folder
        echo "
        <div class = 'homeDownloadButtonForm'>
          <form
            action = 'Zip/download.php'
            class = 'homeDownloadButtonForm'
            method = 'post'
            enctype = 'multipart/form-data'
          >
            <input
              type = 'hidden'
              value = '" . $Directory[$i] . "'
              name = 'fileToDownload'
            />
            <input
              type = 'image'
              src = 'Art/2 - Download Arrow Icon/NanoLab Download Arrow " .
                "Icon @ 36 ppi.png'
                class = 'homeDownloadButton'
                value = 'Download'
                name = 'submit'
                id = '" . preg_replace('/\s+/', '', $Directory[$i]) .
                  "DownloadButton'
            />
            <input
              type = 'hidden'
              value = '" . $CurrentPathString . "'
              name = 'currentPathString'
            />
            <input type = 'hidden' value = '' name = 'recycleBin'/>
          </form>
        </div>";

        // 3.5.1.3 Rename Folder
        echo "
        <div class = 'homeRenameButtonForm'>
          <input
            type = 'image'
            src = 'Art/4 - Rename Cursor Icon/NanoLab Rename Cursor Icon @ " .
              "36 ppi.png'
            id = '" . preg_replace('/\s+/', '', $Directory[$i]) .
              "CursorButton'
            class = 'homeRenameButton'
          />
          <form
            action = 'rename.php'
            class = 'homeRenameButtonForm'
            method = 'post'
            enctype = 'multipart/form-data'
          >
            <input
              type = 'hidden'
              value = '" . $Directory[$i] . "'
              name = 'oldName'
            />
            <input
              type = 'text'
              value = '" . $Directory[$i] . "'
              size = '" . strlen($Directory[$i]) . "' onfocus = 'this.select()'
              id = '" . preg_replace('/\s+/', '', $Directory[$i]) .
                "RenameTextField'
                class = 'homeRenameTextField'
                name = 'newName'
            />
            <input
              type = 'hidden'
              value = '" . $CurrentPathString . "'
              name = 'currentPathString'
            />
          </form>

          <script>
          var directoryName = (typeof directoryName != 'undefined' && directoryName instanceof Array) ? directoryName : [];
          var downloadButton = (typeof downloadButton != 'undefined' && downloadButton instanceof Array) ? downloadButton : [];
          var cursorButton = (typeof cursorButton != 'undefined' && cursorButton instanceof Array) ? cursorButton : [];
          var renameTextField = (typeof renameTextField != 'undefined' && renameTextField instanceof Array) ? renameTextField : [];
          var recycleButton = (typeof recycleButton != 'undefined' && recycleButton instanceof Array) ? recycleButton : [];
          var i = (typeof i != 'undefined') ? i : 0;

          directoryName.push(document.getElementById('" . preg_replace(
            '/\s+/', '', $Directory[$i]) . "DirectoryName'));
          downloadButton.push(document.getElementById('" . preg_replace(
            '/\s+/', '', $Directory[$i]) . "DownloadButton'));
          cursorButton.push(document.getElementById('" . preg_replace(
            '/\s+/', '', $Directory[$i]) . "CursorButton'));
          renameTextField.push(document.getElementById('" . preg_replace(
            '/\s+/', '', $Directory[$i]) . "RenameTextField'));
          recycleButton.push(document.getElementById('" . preg_replace(
            '/\s+/', '', $Directory[$i]) . "RecycleButton'));

          cursorButton[i].addEventListener('click', showTextBox, false);

          function showTextBox() {
            document.getElementById(event.target.id.replace('CursorButton', 'DirectoryName')).style.display = 'none';
            document.getElementById(event.target.id.replace('CursorButton', 'DownloadButton')).style.display = 'none';
            document.getElementById(event.target.id).style.display = 'none';
            document.getElementById(event.target.id.replace('CursorButton', 'RecycleButton')).style.display = 'none';
            document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).style.display = 'block';
            document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).focus();
            document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).addEventListener('focusout', hideRenameTextField, false);
          }

          function hideRenameTextField() {
            document.getElementById(event.target.id).style.display = 'none';
            document.getElementById(event.target.id.replace('RenameTextField', 'DirectoryName')).style.display = 'block';
            document.getElementById(event.target.id.replace('RenameTextField', 'DownloadButton')).style.display = 'block';
            document.getElementById(event.target.id.replace('RenameTextField', 'CursorButton')).style.display = 'block';
            document.getElementById(event.target.id.replace('RenameTextField', 'RecycleButton')).style.display = 'block';
          }

          i++;
          </script>
        </div>";

        // 3.5.1.4 Recycle Folder
        echo "
        <div class = 'homeRecycleButtonForm'>
          <form
            action = 'recycle.php'
            class = 'homeRecycleButtonForm'
            method = 'post'
            enctype = 'multipart/form-data'
          >
            <input
              type = 'hidden'
              value = '" . $Directory[$i] . "'
              name = 'fileToRecycle'
            />
            <input
              type = 'image'
              src = 'Art/3 - Delete Trash Can Icon/NanoLab Delete Trash Can " .
                "Select @ 36 ppi.png'
              class = 'homeRecycleButton'
              id = '" . preg_replace('/\s+/', '', $Directory[$i]) .
                "RecycleButton'
            />
            <input
              type = 'hidden'
              value = '" . $CurrentPathString . "'
              name = 'currentPathString'
            />
          </form>
        </div>";

      echo "</div>";

      // 3.5.1.5 File Sizes
      echo "<div class = 'homeFileSizes'>";
      echo humanSize(folderSize($DirectoryPath . "/" . $Directory[$i]));
      echo "</div>";

      // 3.5.1.6 Heath
      echo "<br><div class = 'homeHeath'><br></div>";
    }
  }

  // 3.5.2 List Files and File Sizes
  // 3.5.2.1 File Name
  function supportedFileTypes($suffix, $directoryi, $directoryPath) {
    $needstxt = ["csv", "php", "html", "cu", "c", "go"];

    if ($_GET) {
      echo "
      <a
        href = '" . $_SERVER['REQUEST_URI'] . "&" . $suffix . "=" .
          rawurlencode($directoryi) . "'
        target = '_blank'
        id = '" . preg_replace('/\s+/', '', $directoryi) . "FileName'
      >" .
        $directoryi .
      "</a>";
    }
    else {
      echo "
      <a
        href = '" . $_SERVER['REQUEST_URI'] . "?" . $suffix . "=" .
          rawurlencode($directoryi) . "'
        target = '_blank'
        id = '" . preg_replace('/\s+/', '', $directoryi) . "FileName'
      >" .
        $directoryi .
      "</a>";
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

      echo "<div class = 'homeFileNames'>";
        echo "<div class = 'homeFiles'>";

          // If the file can be viewed directly in the browser
          if (in_array(
            strtolower(substr($Directory[$i], -4)), $SupportedFileTypes)
          ) {
            supportedFileTypes(strtolower(substr(
              $Directory[$i], -4)), $Directory[$i], $DirectoryPath);
          }
          else if (in_array(
            strtolower(substr($Directory[$i], -3)), $SupportedFileTypes)
          ) {
            supportedFileTypes(strtolower(substr(
              $Directory[$i], -3)), $Directory[$i], $DirectoryPath);
          }
          else if (in_array(
            strtolower(substr($Directory[$i], -2)), $SupportedFileTypes)
          ) {
            supportedFileTypes(strtolower(substr(
              $Directory[$i], -2)), $Directory[$i], $DirectoryPath);
          }
          else if (in_array(
            strtolower(substr($Directory[$i], -1)), $SupportedFileTypes)
          ) {
            supportedFileTypes(strtolower(substr(
              $Directory[$i], -1)), $Directory[$i], $DirectoryPath);
          }
          else {
            echo "" . $Directory[$i];
          }
        echo "</div>";

        // 3.5.2.2 Download File
        echo "
        <div class = 'homeDownloadButtonForm'>
          <form
            action = 'Zip/download.php'
            class = 'homeDownloadButtonForm'
            method = 'post'
            enctype = 'multipart/form-data'
          >
            <input
              type = 'hidden'
              value = '" . $Directory[$i] . "'
              name = 'fileToDownload'
            />
            <input
              type = 'image'
              src = 'Art/2 - Download Arrow Icon/NanoLab Download Arrow " .
                "Icon @ 36 ppi.png'
              class = 'homeDownloadButton'
              value = 'Download'
              name = 'submit'
              id = '" . preg_replace('/\s+/', '', $Directory[$i]) .
                "DownloadButton'
            />
            <input
              type = 'hidden'
              value = '" . $CurrentPathString . "'
              name = 'currentPathString'
            />
            <input type = 'hidden' value = '' name = 'recycleBin'/>
          </form>
        </div>";

        //3.5.2.3 Rename File
        echo "
        <div class = 'homeRenameButtonForm'>
          <input
            type = 'image'
            src = 'Art/4 - Rename Cursor Icon/NanoLab Rename Cursor Icon " .
              "@ 36 ppi.png'
            id = '" . preg_replace('/\s+/', '', $Directory[$i]) .
              "CursorButton'
            class = 'homeRenameButton'
          />
          <form
            action = 'rename.php'
            class = 'homeRenameButtonForm'
            method = 'post'
            enctype = 'multipart/form-data'
          >
            <input
              type = 'hidden'
              value = '" . $Directory[$i] . "'
              name = 'oldName'
            />
            <input
              type = 'text'
              value = '" . $Directory[$i] . "'
              size = '" . strlen($Directory[$i]) . "'
              id = '" . preg_replace('/\s+/', '', $Directory[$i]) .
                "RenameTextField'
              class = 'homeRenameTextField'
              name = 'newName'
            />
            <input
              type = 'hidden'
              value = '" . $CurrentPathString . "'
              name = 'currentPathString'
            />
          </form>

          <script>
          var fileName = (typeof fileName != 'undefined' && fileName instanceof Array) ? fileName : [];
          var downloadButton = (typeof downloadButton != 'undefined' && downloadButton instanceof Array) ? downloadButton : [];
          var cursorButton = (typeof cursorButton != 'undefined' && cursorButton instanceof Array) ? cursorButton : [];
          var renameTextField = (typeof renameTextField != 'undefined' && renameTextField instanceof Array) ? renameTextField : [];
          var recycleButton = (typeof recycleButton != 'undefined' && recycleButton instanceof Array) ? recycleButton : [];

          var i = (typeof i != 'undefined') ? i : 0;

          fileName.push(document.getElementById('" . preg_replace(
            '/\s+/', '', $Directory[$i]) . "FileName'));
          downloadButton.push(document.getElementById('" . preg_replace(
            '/\s+/', '', $Directory[$i]) . "DownloadButton'));
          cursorButton.push(document.getElementById('" . preg_replace(
            '/\s+/', '', $Directory[$i]) . "CursorButton'));
          renameTextField.push(document.getElementById('" . preg_replace(
            '/\s+/', '', $Directory[$i]) . "RenameTextField'));
          recycleButton.push(document.getElementById('" . preg_replace(
            '/\s+/', '', $Directory[$i]) . "RecycleButton'));

          cursorButton[i].addEventListener('click', showFileTextBox, false);

          function showFileTextBox() {
            document.getElementById(event.target.id.replace('CursorButton', 'FileName')).style.display = 'none';
            document.getElementById(event.target.id.replace('CursorButton', 'DownloadButton')).style.display = 'none';
            document.getElementById(event.target.id).style.display = 'none';
            document.getElementById(event.target.id.replace('CursorButton', 'RecycleButton')).style.display = 'none';
            document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).style.display = 'block';
            document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).focus();
            document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).setSelectionRange(0, " .
              strlen(pathinfo($Directory[$i], PATHINFO_FILENAME)) . ");
            document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).addEventListener('focusout', hideRenameFileTextField, false);
          }

          function hideRenameFileTextField() {
            document.getElementById(event.target.id).style.display = 'none';
            document.getElementById(event.target.id.replace('RenameTextField', 'FileName')).style.display = 'block';
            document.getElementById(event.target.id.replace('RenameTextField', 'DownloadButton')).style.display = 'block';
            document.getElementById(event.target.id.replace('RenameTextField', 'CursorButton')).style.display = 'block';
            document.getElementById(event.target.id.replace('RenameTextField', 'RecycleButton')).style.display = 'block';
          }

          i++;
          </script>
        </div>";

        // 3.5.2.4 Recycle File
        echo "
        <div class = 'homeRecycleButtonForm'>
          <form
            action = 'recycle.php'
            class = 'homeRecycleButtonForm'
            method = 'post'
            enctype = 'multipart/form-data'
          >
            <input
              type = 'hidden'
              value = '" . $Directory[$i] . "'
              name = 'fileToRecycle'
            />
            <input
              type = 'image'
              src = 'Art/3 - Delete Trash Can Icon/NanoLab Delete Trash Can " .
                "Select @ 36 ppi.png'
              class = 'homeRecycleButton'
              id = '" . preg_replace('/\s+/', '', $Directory[$i]) .
                "RecycleButton'
            />
            <input
              type = 'hidden'
              value = '" . $CurrentPathString . "'
              name = 'currentPathString'
            />
          </form>
        </div>";

      echo "</div>";

      // 3.5.2.5 File Size
      echo "<div class = 'homeFileSizes'>";
      $FileSize = filesize($DirectoryPath . "/" . $Directory[$i]);
      echo "" . HumanSize($FileSize);
      echo "</div>";

      // 3.5.2.6 Heath
      echo "<br><div class = 'homeHeath'><br></div>";
    }
  }
  ?>
  </div> <!-- Directory -->
</div> <!-- Main Content -->

<!-- 4 Footer -->
<div class = "homePush"></div>
</div> <!-- Wrapper -->

<div class = "homeFooter">&copy; Corvin, Inc.</div>

</body>
</html>
