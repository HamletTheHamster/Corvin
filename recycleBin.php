<!--
This is the user's recycle bin for Corvin.

Hierarchy

  0 Check If Logged In
  1 Header
  2 Top Bar
  3 Main Content
    3.1 Current Directory Banner
    3.2 Items in Directory
      3.2.1 List Folders and Folder Sizes
        3.2.1.1 Folder Name
        3.2.1.2 Download Folder
        3.2.1.3 Folder Size
        3.2.1.4 Heath
      3.2.2 List Files and File Sizes
        3.2.2.1 File Name
        3.2.2.2 Download File
        3.2.2.3 File Size
        3.2.2.4 Heath
  4 Footer

Issues: Want items to be arranged in the order they were deleted, with the most
        recent at the top

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

// Check if connected to MYSQLI server
if (!$conn) {
  echo("Failed to connect to database: " .   mysqli_connect_error()) .
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
elseif (time() - $_SESSION['Created'] > 1200) {
  // Then change session ID for the current session and invalidate old session
  // ID
  session_regenerate_id(true);

  // Update creation time
  $_SESSION['Created'] = time();
}
?>

<!DOCTYPE html>
<html lang = "en" class = "recycleBin">

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

<body class = "recycleBin">
<div class = "recycleBinWrapper">

  <!-- 2 Top Bar -->
  <div class = "recycleBinTopBar">
    <div class = "recycleBinCorvin">
      <?php
      echo "
      <a href = 'home.php'>
        <h class = 'recycleBinCorvinHeader'>C</h>
      </a>";
      ?>
    </div>
    <div class = "recycleBinAccountMenuDropDown">
      <p onclick = "accountDropDownMenu()" class = "recycleBinAccountButton">Account</p>
      <div id = "AccountMenuContent" class = "recycleBinAccountMenuContent">
        <div class = "recycleBinTopAccountMenuContent">
          <?php
          echo "<p class = 'recycleBinAccountMenuName'>" . $user[0] . " " . $user[1] .
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

            echo "<p class = 'recycleBinDiskSpace'>" . humanSize($usedBytes) .
              " used of " . humanSize($freeBytes) . " (Unlimited)</p>";
          }
          else {
            $totalBytes = $storageSpaceInMegabytes[0] * 1000000;
            $freeBytes = $totalBytes - $usedBytes;

            echo "<p class = 'recycleBinDiskSpace'>" . humanSize($usedBytes) .
              " used of " . humanSize($totalBytes) . "</p>";
          }
          ?>
        </div>
        <div class = "recycleBinMenuLine">
          <hr class = "recycleBinMenuLine"/>
        </div>
        <div class = "recycleBinBottomAccountMenuContent">
          <a class = "recycleBinGetMoreSpaceMenuItem" href = "getMoreSpace.php">
            Get More Space
          </a>
          <a class = "recycleBinMenuItem" href = "settings.php">Settings</a>
          <a class = "recycleBinMenuItem" href = "help.php">Help</a>
          <a class = "recycleBinMenuItem" href = "logout.php">Log Out</a>
        </div>
      </div>
    </div>
  </div>

  <script>
  function accountDropDownMenu() {
    document.getElementById("AccountMenuContent").classList.toggle("recycleBinShow");
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
  <div class = "recycleBinMainContent">

    <!-- 3.1 Back to Home -->
    <form action = "home.php" method = "post" enctype = "multipart/form-data">
      <input type = "submit" class = "recycleBinRecentlyDeletedItems"
        value = "Back To Home" name = "submit" />
    </form>

    <br /><br />

    <!-- 3.2 Current directory -->
    <div class = "recycleBinDirectoryPath">
      <?php
      include "generateURL.php";

      parse_str($_SERVER['QUERY_STRING'], $CurrentPath);
      $CurrentPathString = implode("/", $CurrentPath) . "/";

      echo "
      <a class = 'recycleBinDirectoryPath' href = 'recycleBin.php'>
        <p class = 'recycleBinDirectoryPath'>Recycle</p>
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
            "recycleBin.php?", $DirectoryPath, $DirectoryPathFolder);
          array_push($DirectoryPath, $DirectoryPathFolder);
          $i++;
          echo "
          <p class = 'recycleBinDirectoryPath'>/</p>
          <a class = 'recycleBinDirectoryPath' href = '" . $DirectoryPathFolderURL . "'>
            <p class = 'recycleBinDirectoryPath'>" . $DirectoryPathFolder . "</p>
          </a>";
        }
      }
      ?>
      <br /><br />
    </div>

    <br /><br />

    <!-- 3.3 Items in directory -->
    <div class = "recycleBinDirectory">
      <?php
      $ReturnPathString = filter_input(
        INPUT_POST, "ReturnPathString", FILTER_SANITIZE_STRING);

      if ($ReturnPathString == null) {
        $DirectoryPath = "../../../../mnt/Raid1Array/Corvin/0 - Recycle/" .
          $userID . " - " . $user[0] . $user[1] . "/" .
          implode("/", $CurrentPath);
      }
      else {
        $DirectoryPath = "../../../../mnt/Raid1Array/Corvin/0 - Recycle/" .
          $userID . " - " . $user[0] . $user[1] . "/" . $ReturnPathString;
      }

      $Directory = scandir($DirectoryPath);
      usort($Directory, "strnatcmp");
      $NumItems = count($Directory);

      // 3.3.1 List Folders and Folder Sizes
      // 3.3.1.1 Folder Name
      for ($i = 2; $i < $NumItems; $i++) {
        if (is_dir($DirectoryPath . "/" . $Directory[$i])) {
          echo "<div class = 'recycleBinFileNames'>";
            echo "<div class = 'recycleBinFolders'>";
              $URL = generateURL(
                "recycleBin.php?", $CurrentPath, $Directory[$i]);
              echo "
              <a href = '" . $URL . "' id = '" . preg_replace(
                '/\s+/', '', $Directory[$i]) . "DirectoryName'>" .
                  $Directory[$i] .
              "</a>";
            echo "</div>";

            // 3.3.1.2 Download Folder
            echo "
            <div class = 'recycleBinDownloadButtonForm'>
              <form
                action = 'Zip/download.php'
                class = 'recycleBinDownloadButtonForm'
                method = 'post'
                enctype = 'multipart/form-data'
              >
                <input type = 'hidden' value = '" . $Directory[$i] . "'
                  name = 'fileToDownload' />
                <input
                  type = 'image'
                  src = 'Art/2 - Download Arrow Icon/NanoLab Download Arrow " .
                    "Icon @ 36 ppi.png'
                  class = 'recycleBinDownloadButton'
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
                <input
                  type = 'hidden'
                  value = '0 - Recycle'
                  name = 'recycleBin'
                />
              </form>
            </div>";

          echo "</div>";

          // 3.3.1.3 Folder Sizes
          echo "<div class = 'recycleBinFileSizes'>";
            echo humanSize(folderSize($DirectoryPath . "/" . $Directory[$i]));
          echo "</div>";
          echo "<br><div class = 'recycleBinHeath'><br></div>";
        }
      }

      // 3.3.2 List Files and File Sizes
      // 3.3.2.1 File Name
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
              "../../../../../../../../../var/www/html/ViewInBrowser/" .
              $suffix . ".txt")
            ) {
              echo "copy successful";
              echo "
              <meta
                http-equiv = 'refresh'
                content = '0; url=ViewInBrowser/" . $suffix . ".txt'
              >";
            }
            else {
              echo "copy unsuccessful";
              echo "<meta http-equiv = 'refresh' content = '2' >";
            }
          }
          else {
            if (copy(
              $directoryPath . "/" . $fileToView,
              "../../../../../../../../var/www/html/ViewInBrowser/" .
              $suffix . "." . $suffix)
            ) {
              echo "copy successful";
              echo "
              <meta
                http-equiv = 'refresh'
                content = '0; url=ViewInBrowser/" . $suffix . "." . $suffix .
              "'>";
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

          echo "<div class = 'recycleBinFileNames'>";
            echo "<div class = 'recycleBinFiles'>";

              //if the file can be viewed directly in the browser
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
                supportedFileTypes(strtolower(substr($Directory[$i], -2)), $Directory[$i], $DirectoryPath);
              }
              else if (in_array(
                strtolower(substr($Directory[$i], -1)), $SupportedFileTypes)
              ) {
                supportedFileTypes(strtolower(substr($Directory[$i], -1)), $Directory[$i], $DirectoryPath);
              }
              else {
                echo "" . $Directory[$i];
              }
            echo "</div>";

            // 3.3.2.2 Download File
            echo "
            <div class = 'recycleBinDownloadButtonForm'>
              <form
                action = 'Zip/download.php'
                class = 'recycleBinDownloadButtonForm'
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
                  class = 'recycleBinDownloadButton'
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
                <input
                  type = 'hidden'
                  value = '0 - Recycle'
                  name = 'recycleBin'
                />
              </form>
            </div>";

          echo "</div>";

          // 3.3.2.3 File Size
          echo "<div class = 'recycleBinFileSizes'>";
          $FileSize = filesize($DirectoryPath . "/" . $Directory[$i]);
          echo "" . HumanSize($FileSize);
          echo "</div>";

          // 3.3.2.4 Heath
          echo "<br><div class = 'recycleBinHeath'><br></div>";
        }
      }
      ?>
    </div>
  </div>

  <div class = "recycleBinPush"></div>
</div>

<!-- 4 Footer -->
<div class = "recycleBinFooter">&copy; Corvin, Inc.</div>

</body>
</html>
