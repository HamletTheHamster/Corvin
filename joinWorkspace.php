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

if (isset($_POST["joinWorkspaceName"])) {

  $joinWorkspaceName = $_POST["joinWorkspaceName"];

  // Get workspace owner id
  $idAquired = FALSE;
  $prefix = 1;
  while ($idAquired == FALSE) {

      if (is_numeric(substr($joinWorkspaceName, 0, $prefix))) {

          $prefix++;
      }
      else {
        $ownerID = substr($joinWorkspaceName, 0, $prefix - 1);
        $idAquired = TRUE;
      }
  }

  // Check if workspace exists in owner's workspace list
  $sql = "SELECT * FROM Workspaces WHERE id = '$ownerID';";
  $ownerWorkspaces = mysqli_fetch_row(mysqli_query($conn, $sql));
  $workspaceExists = FALSE;
  foreach ($ownerWorkspaces as $ownerWorkspace) {

    if ($ownerWorkspace == $joinWorkspaceName) {

      $workspaceExists = TRUE;
    }
  }
  if ($workspaceExists == FALSE) {

    // Check among all workspaces for existance of workspace
    $sql = "SELECT * FROM Workspaces;";
    $allWorkspaces = mysqli_fetch_array(mysqli_query($conn, $sql));
    foreach ($allWorkspaces as $allWorkspace) {

      if ($allWorkspace == $joinWorkspaceName) {

        $workspaceExists = TRUE;
      }
    }
  }
  if ($workspaceExists == FALSE) {

    $message = "Invalid workspace code.";
    echo json_encode(array('message' => $message));
    exit;
  }

  // Add workspace to user's workspaces
  $sql = "SELECT * FROM Workspaces WHERE id = '$userID';";
  $workspaces = mysqli_fetch_row(mysqli_query($conn, $sql));

  foreach ($workspaces as $key => $value) {

    if ($value === NULL && $key > 0) {

      $workspaceNumber = "workspace" . $key;
      $sql = "UPDATE Workspaces SET $workspaceNumber = '$joinWorkspaceName' WHERE id = '$userID';";
      mysqli_query($conn, $sql);
      $_SESSION["currentWorkspace"] = $joinWorkspaceName;
      $joinWorkspace = "true";

      echo json_encode(array('message' => $joinWorkspace));
      exit;
    }
  }

  $workspaceNumber = "workspace" . count($workspaces);
  $sql = "ALTER TABLE Workspaces ADD $workspaceNumber VARCHAR(100);";
  mysqli_query($conn, $sql);

  $sql = "UPDATE Workspaces SET $workspaceNumber = '$joinWorkspaceName' WHERE id = '$userID';";
  mysqli_query($conn, $sql);
  $_SESSION["currentWorkspace"] = $joinWorkspaceName;
  $joinWorkspace = "true";

  echo json_encode(array('message' => $joinWorkspace));
  exit;
}
else {

  $message = "no Post Set";
  echo json_encode(array('message' => $message));
  exit;
}
