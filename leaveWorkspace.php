<?php

session_start();

//Check if user is logged in
if (!isset($_SESSION["loginUser"]) && $_SESSION["loginUser"] != TRUE) {
  header("Location: login.php");
}

//MYSQLi server connection
$conn = mysqli_connect("127.0.0.1", "joel", "Daytona675");

//Check if connected to MYSQLI server
if (!$conn) {
    $message = "Failed to connect to database: " . mysqli_connect_error();
    echo json_encode(array('createWorkspace' => $message));
    exit;
}

//Go into Corvin database
mysqli_query($conn, "USE Corvin;");

// Assign user's ID, set in validate.php
$userID = $_SESSION["userID"];

if (isset($_POST["leaveWorkspaceName"])) {

  $leaveWorkspaceName = $_POST["leaveWorkspaceName"];

  // Remove workspace from user's workspaces
  $sql = "SELECT * FROM Workspaces WHERE id = '$userID';";
  $workspaces = mysqli_fetch_row(mysqli_query($conn, $sql));

  foreach ($workspaces as $key => $value) {

    if ($value == $leaveWorkspaceName && $key > 0) {

      $workspaceNumber = "workspace" . $key;
      $sql = "UPDATE Workspaces SET $workspaceNumber = NULL WHERE id = '$userID';";
      mysqli_query($conn, $sql);
      $leaveWorkspace = "true";

      echo json_encode(array('message' => $leaveWorkspace));
      exit;
    }
  }
}
