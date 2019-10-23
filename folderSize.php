<!--
This is an external php function for Corvin that recursively finds the size of
all the contents of a folder.

Variables

  directory       - folder of which we want to know the size
  folderSize      - the running total size of the top level directory we really
                    care about
  directoryArray  - array containing all the immediate contents of a folder
  key             - a foreach variable that assigns each element of an array to
                    a specified variable for each loop
  filename        - the specified variable that Key is assigning each element
                    of the DirectoryArray to; the name of the file or folder
                    currently being handled by the foreach loop
  newFolderSize   - the size to add to the running total size of the top level
                    directory we really care about

Coded by: Joel N. Johnson
-->

<?php

//Check if user is logged in
if (!isset($_SESSION["loginUser"]) && $_SESSION["loginUser"] != TRUE) {
  header("Location: login.php");
}

// FolderSize accepts the path of a directory as input
function folderSize($directory) {
  $folderSize = 0;

  // Assign the contents of folder to directoryArray
  $directoryArray = scandir($directory);

  // For each item in the folder, assign the name of the item to filename
  foreach ($directoryArray as $key => $filename) {
    // If the item is not unix language for two or one directories up
    if ($filename != ".." && $filename != ".") {
      // If the item is a folder itself
      if (is_dir($directory . "/" . $filename)) {
        // Then recursively send that folder through this very function,
        // starting at the top and acting recursively through any folders
        // it contains within it, and assign the size to newFolderSize */
        $newFolderSize = folderSize($directory . "/" . $filename);

        // Increase the running total size of the top level folder we really
        // care about by the amount just found
        $folderSize = $folderSize + $newFolderSize;
      }
      else if (is_file($directory . "/" . $filename)) {
        // Then find its size and increase the running total size of the top
        // level folder we really care about by that amount
        $folderSize = $folderSize + filesize($directory . "/" . $filename);
      }
    }
  }

  // Return the folder's size, in bytes
  return $folderSize;
}
?>
