<?php

//Check if user is logged in
if (!isset($_SESSION["loginUser"]) && $_SESSION["loginUser"] != TRUE) {
  header("Location: login.php");
}

function renameRecursively($oldName, $newName) {

  $directoryContents = scandir($oldName);

  foreach ($directoryContents as $filename) {

    if ($filename != ".." && $filename != ".") {

      if (is_dir($oldName . "/" . $filename)) {

        renameRecursively($oldName . "/" . $filename, $newName . "/" . $filename);
      }
      else if (is_file($oldName . "/" . $filename)) {

        rename($oldName . "/" . $filename, $newName . "/" . $filename);
      }
    }
  }

  return TRUE;
}
