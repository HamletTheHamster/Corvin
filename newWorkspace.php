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

  if (!is_numeric(substr($_POST["newWorkspaceName"], 0, 1))) {

    $newWorkspaceName = $userID . $_POST["newWorkspaceName"];
    $_SESSION["currentWorkspace"] = $newWorkspaceName;

    // Create workspace folder and recycle folder
    $workspaceFolderFullPath = "../../../mnt/Raid1Array/Corvin/000 - Workspaces/" .
      $newWorkspaceName;
    $workspaceRecycleFolderFullPath = "../../../mnt/Raid1Array/Corvin/000 - Workspaces/0 - WorkspacesRecycle/" .
      $newWorkspaceName;

    if (mkdir($workspaceFolderFullPath, 0777, true)) {
      chmod($workspaceFolderFullPath, 0777);

      if (mkdir($workspaceRecycleFolderFullPath, 0777, true)) {
        chmod($workspaceRecycleFolderFullPath, 0777);
      }
      else {
        $createWorkspace = "Error creating workspace recycle folder";
        echo json_encode(array('message' => $createWorkspace));
        exit;
      }
    }
    else {
      $createWorkspace = "Error creating workspace folder";
      echo json_encode(array('message' => $createWorkspace));
      exit;
    }

    // Add workspace to WorkspaceSettings table
    $storageSpaceInMegabytes = 250;
    $sql = "INSERT INTO WorkspaceSettings (
      workspace,
      storageSpaceInMegabytes)
      VALUES (
      '$newWorkspaceName',
      '$storageSpaceInMegabytes');
    ";
    mysqli_query($conn, $sql);

    // Add workspace to Workspaces table
    $sql = "SELECT * FROM Workspaces WHERE id = '$userID';";
    $workspaces = mysqli_fetch_row(mysqli_query($conn, $sql));

    foreach ($workspaces as $key => $value) {

      if ($value === NULL && $key > 0) {

        $workspaceNumber = "workspace" . $key;
        $sql = "UPDATE Workspaces SET $workspaceNumber = '$newWorkspaceName' WHERE id = '$userID';";
        mysqli_query($conn, $sql);

        $createWorkspace = "true";

        echo json_encode(array('message' => $createWorkspace));
        exit;
      }
    }

    $workspaceNumber = "workspace" . count($workspaces);
    $sql = "ALTER TABLE Workspaces ADD $workspaceNumber VARCHAR(100);";
    mysqli_query($conn, $sql);

    $sql = "UPDATE Workspaces SET $workspaceNumber = '$newWorkspaceName' WHERE id = '$userID';";
    mysqli_query($conn, $sql);
    $createWorkspace = "true";

    echo json_encode(array('message' => $createWorkspace));
    exit;
  }
  else {

    $message = "Workspace names cannot start with a number";
    echo json_encode(array('message' => $message));
    exit;
  }
}
else {

  $message = "no Post Set";
  echo json_encode(array('message' => $message));
  exit;
}
