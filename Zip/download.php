<!--
This is a PHP file for Corvin that is called by home.php to handle
files being downloaded from Corvin.

Variables

  conn              - MySQL database connection
  userID            - user ID of the user; unique integer used to identify
                      the user and grab other info from their row in
                      the database
  _SESSION[userID]  - global variable used to maintain user's identity across
                      pages
  sql               - used to hold query database strings
  user              - array;
                        user[0] = user's first name,
                        user[1] = user's last name
  currentPathString - sanitized query string from previous page and passed via
                      post data; used to return user to the directory they
                      were in before a download error occurred
  recycleBin        - Used to determine if download request is for a file or
                      folder in the recycle bin;
                        recycleBin = '0 - recycleBin' if it is,
                        recycleBin = '' if it isn't
  downloadDirectory - string containing full path to the directory containing
                      the file or folder to be downloaded
  fileToDownload    - information about file to be downloaded, structured as

                        filter_input(
                          type of input,
                          name of variable to get,
                          FILTER_SANITIZE_STRING:
                          filter that removes tags and
                          removes or encodes special
                          characters from a string
                        )

  fullPath          - full path of file to be downloaded including file name
                      and type
  realPath          - strips and and all '../'s in fullPath
  zipFileName       - assigns url-encoded file name to zip file to download
  zip               - ZipArchive object; used for creating zip files of folder
                      to download
  files             - array containing all files in the directory to zip; each
                      element is a string containing the real path to the file
  name              - key for files array; name of file is used as index
  file              - single file in array of files; string containing real
                      path to an individual file
  filePath          - strips any and all '../'s from file
  relativePath      - relative path of file; chops filePath string down to the
                      length of realPath + 1

Issues: rework zip section, it's object oriented and some parts don't make
  sense

Coded by: Joel N. Johnson
-->

<?php
// Display any errors
ini_set("display_errors", 1);

// And be verbose about it
error_reporting(E_ALL);

session_start();

// MYSQLi server connection
$conn = mysqli_connect("127.0.0.1", "joel", "Daytona675");

// Check if connected to MYSQLI server
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

$currentPathString = filter_input(
  INPUT_POST,
  "currentPathString",
  FILTER_SANITIZE_STRING
);

// Used to handle requests from the user's recycle bin
$recycleBin = $_POST["recycleBin"];

// Assign path of directory holding file
$downloadDirectory = "/../../../mnt/Raid1Array/Corvin/" . $recycleBin . "/" .
  $userID . " - " . $user[0] . $user[1] . "/" . $currentPathString;

// Assign name of file from input
$fileToDownload = filter_input(
  INPUT_POST,
  "fileToDownload",
  FILTER_SANITIZE_STRING
);

// Concatonate path and file name
$fullPath = $downloadDirectory . $fileToDownload;
$realPath = realpath($fullPath);
$zipfilename = $downloadDirectory . urlencode(basename($fileToDownload)) .
  ".zip";

if (is_readable($fullPath)) {
  if (is_dir($fullPath)) {

  // Initialize ZipArchive object
  $zip = new ZipArchive();
  $zip->open($zipfilename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

  // Create recursive directory iterator
  $files = new RecursiveIteratorIterator(
  new RecursiveDirectoryIterator($realPath),
      RecursiveIteratorIterator::LEAVES_ONLY
    );

  foreach ($files as $name => $file) {
    // Skip directories (they would be added automatically)
    if (!$file->isDir()) {
      // Get real and relative path for current file
      $filePath = $file->getrealPath();
      echo "filePath: " . $filePath . "<br>";
      $relativePath = substr($filePath, strlen($realPath) + 1);
      echo "relativePath: " . $relativePath . "<br>";

      // Add current file to archive
      $zip->addFile($filePath, $relativePath);
    }
  }

  //IDEAS TO FIX ZIP GARBAGE DUMP ON LARGE ZIP FOLDERS
  //increase script execution time, other php.ini settings
  //close and reopen ZipArchive after n files added (only so many might be
  // allowed open at once, closing and reopening forces the thus far added filesize
  // to be compressed and added before adding more)
  //cap the maximum file size or number of files allowed in a single zip file
  // and move on to the next zip file for the rest.

  // State headers
  header("Content-Description: File Transfer");
  header("Content-Type: application/octet-stream");
  header("Content-Disposition: attachment; filename = " .
    basename($zipfilename));
  header('Content-Transfer-Encoding: binary');
  header('Expires: 0');
  header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  header('Pragma: public');
  header('Content-Length: ' . filesize($zipfilename));
  ob_clean();
  flush();

  // Force download of zip folder
  readfile($zipfilename);

  unlink($zipfilename);
  exit;
  }
  else {
  // State headers
  header("Content-Description: File Transfer");
  header("Content-Type: application/octet-stream");
  header('Content-Disposition: attachment; filename = "' .
  basename($fullPath) . '"');
  header('Content-Transfer-Encoding: binary');
  header('Expires: 0');
  header('Cache-Control: must-revalidate, ' . 'post-check=0, pre-check=0');
  header('Pragma: public');
  header('Content-Length: ' . filesize($fullPath));
  ob_clean();
  flush();

  // Force download of file
  readfile($fullPath);
  exit;
  }
}
else {
  echo "Download failed because the file does not exist in the current " .
  "directory.";
}
?>
