<?php

session_start();

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

if (isset($_POST["newWorkspaceName"])) {

  $newWorkspaceName = $_POST["newWorkspaceName"];

  $sql = "SELECT * FROM Workspaces WHERE id = '$userID';";
  $workspaces = mysqli_fetch_row(mysqli_query($conn, $sql));

  foreach ($workspaces as $key => $value) {

    if ($value === NULL && $key > 0) {

      $workspaceNumber = "workspace" . $key;
      $sql = "UPDATE Workspaces SET $workspaceNumber = '$newWorkspaceName' WHERE id = '$userID';";
      $createWorkspace = mysqli_query($conn, $sql);

      echo json_encode(array('createWorkspace' => $createWorkspace));
      exit;
    }
    $totalWorkspaces++;
  }

  $workspaceNumber = "workspace" . count($workspaces);
  $sql = "ALTER TABLE Workspaces ADD $workspaceNumber VARCHAR(100);";
  mysqli_query($conn, $sql);

  $sql = "UPDATE Workspaces SET $workspaceNumber = '$newWorkspaceName' WHERE id = '$userID';";
  $createWorkspace = mysqli_query($conn, $sql);

  echo json_encode(array('createWorkspace' => $createWorkspace));
  exit;
}
else if (isset($_POST["joinWorkspace"])) {

  $joinWorkspace = $_POST["joinWorkspace"];
}
else {

  $message = "no Post Set";
  echo json_encode(array('createWorkspace' => $message));
  exit;
}
