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

// Assign user's ID, set in validate.php
$userID = $_SESSION["userID"];

$sql = "SELECT firstName, lastName FROM UserInfo WHERE id = '$userID'";
$user = mysqli_fetch_array(mysqli_query($conn, $sql));

// Set Darkmode/Lightmode
$sql = "SELECT darkmode FROM UserInfo WHERE id = '$userID'";
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
<html lang = "en" class = <?php echo "'".$o."'";?>>

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

  <script>var o = <?php echo json_encode($o); ?>;</script>
</head>

<body class = <?php echo "'".$o."'";?>>
<div class = <?php echo "'".$o."Wrapper'";?>>

<!-- 2 Top Bar -->
<div class = <?php echo "'".$o."TopBar'";?>>
  <div class = <?php echo "'".$o."Corvin'";?>>
    <?php
    echo "<a href = 'home.php'>" . "<h class = '".$o."CorvinHeader'>C</h>" . "</a>";
    ?>
  </div>
  <div class = <?php echo "'".$o."AccountMenuDropDown'";?>>
    <p onclick = "accountDropDownMenu()" class = <?php echo "'".$o."AccountButton'";?>>Account</p>
    <div id = "accountMenuContent" class = <?php echo "'".$o."AccountMenuContent'";?>>
      <div class = <?php echo "'".$o."TopAccountMenuContent'";?>>
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
          $totalBytes = disk_total_space("../../../../mnt/Raid1Array/Corvin");
          $freeBytes = disk_free_space("../../../../mnt/Raid1Array/Corvin");

          echo "<p class = '".$o."DiskSpace'>" . humanSize($usedBytes) .  " used of " .
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
      <br><div class = <?php echo "'".$o."MenuHeath'";?>><br></div>
      <div class = <?php echo "'".$o."BottomAccountMenuContent'";?>>
        <a class = <?php echo "'".$o."GetMoreSpaceMenuItem'";?> href = "getMoreSpace.php">
          Get More Space</a>
        <a class = <?php echo "'".$o."MenuItem'";?> href = "settings.php">Settings</a>
        <a class = <?php echo "'".$o."MenuItem'";?> href = "help.php">Help</a>
        <a class = <?php echo "'".$o."MenuItem'";?> href = "logout.php">Log Out</a>
      </div>
    </div>
  </div>
</div>

<script>
function accountDropDownMenu() {
  document.getElementById("accountMenuContent").classList.toggle(o+"Show");
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
<div class = <?php echo "'".$o."MainContent'";?>>

  <!-- 3.1 Upload File -->
  <form action = "upload.php" method = "post" enctype = "multipart/form-data">
    <span id = "hideWhenFilesSelected">
      <button type = 'button' class = <?php echo "'".$o."CustomFileUpload'";?>>
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
      class = <?php echo "'".$o."UploadButton'";?>
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
      class = <?php echo "'".$o."NewFolderNameTextBox'";?>
      spellcheck = "false"
      autocomplete = "off"
    />
    <input
      type = "button"
      class = <?php echo "'".$o."CreateFolderButton'";?>
      id = "createFolderButton"
      value = "Create Folder"
      name = "submit"
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



  <!-- 3.3 Recently Deleted Items -->
  <form
    action = "recycleBin.php"
    method = "post"
    enctype = "multipart/form-data"
  >
    <input
      type = "submit"
      class = <?php echo "'".$o."RecentlyDeletedItems'";?>
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
  <div class = <?php echo "'".$o."DirectoryPath'";?>>
    <?php
    include "generateURL.php";

    echo "
    <a class = '".$o."DirectoryPath' href = 'home.php'>
      <p class = '".$o."DirectoryPath'>Home</p>
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
  <div class = <?php echo "'".$o."Directory'";?>>
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
      echo "<div class = '".$o."FileNames'>";
        echo "<div class = '".$o."Folders'>";
          $URL = generateURL("home.php?", $CurrentPath, $Directory[$i]);
          echo "
          <a
            href = '" . $URL . "'
            class = '".$o."Folders'
            id = '" . preg_replace('/\s+/', '', $Directory[$i]) .
            "DirectoryName'
          >" .
            $Directory[$i] .
          "</a>";
        echo "</div>";

        // 3.5.1.2 Download Folder
        echo "
        <div class = '".$o."DownloadButtonForm'>
          <form
            action = 'Zip/download.php'
            class = '".$o."DownloadButtonForm'
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
                class = '".$o."DownloadButton'
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
        <div class = '".$o."RenameButtonForm'>
          <input
            type = 'image'
            src = 'Art/4 - Rename Cursor Icon/NanoLab Rename Cursor Icon @ " .
              "36 ppi.png'
            id = '" . preg_replace('/\s+/', '', $Directory[$i]) .
              "CursorButton'
            class = '".$o."RenameButton'
          />
          <form
            action = 'rename.php'
            class = '".$o."RenameButtonForm'
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
              onfocus = 'this.select()'
              id = '" . preg_replace('/\s+/', '', $Directory[$i]) .
                "RenameTextField'
              class = '".$o."RenameTextField'
              name = 'newName'
              spellcheck = 'false'
              autocomplete = 'off'
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
        <div class = '".$o."RecycleButtonForm'>
          <form
            action = 'recycle.php'
            class = '".$o."RecycleButtonForm'
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
              class = '".$o."RecycleButton'
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
      echo "<div class = '".$o."FileSizes'>";
      echo humanSize(folderSize($DirectoryPath . "/" . $Directory[$i]));
      echo "</div>";

      // 3.5.1.6 Heath
      echo "<br><div class = '".$o."Heath'><br></div>";
    }
  }

  // 3.5.2 List Files and File Sizes
  // 3.5.2.1 File Name
  function supportedFileTypes($suffix, $directoryi, $directoryPath, $oo) {
    $needstxt = ["csv", "php", "html", "cu", "c", "go"];

    if ($_GET) {
      echo "
      <a
        href = '" . $_SERVER['REQUEST_URI'] . "&" . $suffix . "=" .
          rawurlencode($directoryi) . "'
        target = '_blank'
        class = '".$oo."Files'
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
        class = '".$oo."Files'
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
            echo "" . $Directory[$i];
          }
        echo "</div>";

        // 3.5.2.2 Download File
        echo "
        <div class = '".$o."DownloadButtonForm'>
          <form
            action = 'Zip/download.php'
            class = '".$o."DownloadButtonForm'
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
              class = '".$o."DownloadButton'
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
        <div class = '".$o."RenameButtonForm'>
          <input
            type = 'image'
            src = 'Art/4 - Rename Cursor Icon/NanoLab Rename Cursor Icon " .
              "@ 36 ppi.png'
            id = '" . preg_replace('/\s+/', '', $Directory[$i]) .
              "CursorButton'
            class = '".$o."RenameButton'
          />
          <form
            action = 'rename.php'
            class = '".$o."RenameButtonForm'
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
              class = '".$o."RenameTextField'
              name = 'newName'
              spellcheck = 'false'
              autocomplete = 'off'
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
        <div class = '".$o."RecycleButtonForm'>
          <form
            action = 'recycle.php'
            class = '".$o."RecycleButtonForm'
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
              class = '".$o."RecycleButton'
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
      echo "<div class = '".$o."FileSizes'>";
      $FileSize = filesize($DirectoryPath . "/" . $Directory[$i]);
      echo "" . HumanSize($FileSize);
      echo "</div>";

      // 3.5.2.6 Heath
      echo "<br><div class = '".$o."Heath'><br></div>";
    }
  }
  ?>
  </div> <!-- Directory -->
</div> <!-- Main Content -->

<!-- 4 Footer -->
<div class = <?php echo "'".$o."Push'";?>></div>
</div> <!-- Wrapper -->

<div class = <?php echo "'".$o."Footer'";?>>&copy; Corvin, Inc.</div>

</body>
</html>
