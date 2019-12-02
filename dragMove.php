<?php
session_start();

//Check if user is logged in
if (!isset($_SESSION["loginUser"]) && $_SESSION["loginUser"] != TRUE) {
  header("Location: login.php");
}

$directoryToMove = $_POST["directoryToMove"];
$directoryTarget = $_POST["directoryTarget"];

if (!is_readable($directoryToMove)) {

  $message = "There was a problem reading directory paths";
  echo json_encode(array('message' => $message));
  exit;
}

if (rename($directoryToMove, $directoryTarget)) {

  $message = "true";
  echo json_encode(array('message' => $message));
  exit;
}
else {

  include "renameRecursively.php";
  
  $message = renameRecursively($directoryToMove, $directoryTarget);
  echo json_encode(array('message' => $message));
  exit;
}
