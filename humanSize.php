<!--
This is an external php function for Corvin that takes a value in bytes and
converts that value to the appropriate prefix unit of MB, GB, etc. It then
returns that information in a statement in the following format:

  765.65 MB

Variables

  bytes   - function input; value in bytes
  type    - array of prefixes
  i       - indexer

Coded by: Joel N. Johnson
-->

<?php

//Check if user is logged in
if (!isset($_SESSION["loginUser"]) && $_SESSION["loginUser"] != TRUE) {
  header("Location: login.php");
}

// humanSize accepts an integer number of bytes as input
function humanSize($bytes) {
  // Assign array containing prefix abbreviations
  $type = array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");

  // Initialize indexer
  $i = 0;

  // While the value is still more than should be reported with the current
  // prefix
  while($bytes >= 1000) {
    // Then divide the value by 1000
    $bytes /= 1000;

    // And increase the indexer by to indicate the value has gone to the next
    // prefix
    $i++;
  }

  // Return the value properly reported
  return(sprintf("%1.2f", $bytes) . " " . $type[$i]);
}
?>
