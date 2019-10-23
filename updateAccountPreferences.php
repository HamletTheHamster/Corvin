<?php

session_start();

//Check if user is logged in
if (!isset($_SESSION["loginUser"]) && $_SESSION["loginUser"] != TRUE) {
  header("Location: login.php");
}

// MySQL server connection
$conn = mysqli_connect("127.0.0.1", "joel", "Daytona675");

// Check if connected to MySQL server
if (!$conn) {
  echo("Failed to connect to database: " . mysqli_connect_error()) .
    "<br /><br />";
}

// Go into Corvin database
mysqli_query($conn, "USE Corvin;");

// Assign user's ID set in validate.php
$userID = $_SESSION["userID"];

$darkmode = $_POST['darkmode'];

if ($darkmode == 'true')
{

  $sql = "UPDATE UserInfo SET darkmode = '1' WHERE id = '$userID'";
  if (mysqli_query($conn, $sql) === TRUE) {

    /*
    $sql = "SELECT firstName FROM UserInfo WHERE id = '$userID'";
    $firstName = mysqli_fetch_row(mysqli_query($conn, $sql));

    $sql = "SELECT darkmode FROM UserInfo WHERE id = '$userID'";
    $darkmodeSetting = mysqli_fetch_row(mysqli_query($conn, $sql));

    $message = 'Darkmode for ' . $firstName[0] . ' is set to ' . $darkmodeSetting[0] . ' & _POST[darkmode] ' . $isset;
    $success = 'Enabled';
    echo json_encode(array('message'=>$message,'success'=>$success));
    */
  }
}

else if ($darkmode == 'false')  //mode is false when button is disabled
{
  $sql = "UPDATE UserInfo SET darkmode = '0' WHERE id = '$userID'";
  if (mysqli_query($conn, $sql) === TRUE) {

    /*
    $sql = "SELECT firstName FROM UserInfo WHERE id = '$userID'";
    $firstName = mysqli_fetch_row(mysqli_query($conn, $sql));

    $sql = "SELECT darkmode FROM UserInfo WHERE id = '$userID'";
    $darkmodeSetting = mysqli_fetch_row(mysqli_query($conn, $sql));

    $message = 'Darkmode for ' . $firstName[0] . ' is set to ' . $darkmodeSetting[0];
    $success = 'Disabled';
    echo json_encode(array('message'=>$message,'success'=>$success));
    */
  }
}
 ?>
