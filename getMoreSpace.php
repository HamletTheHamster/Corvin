<?php

session_start();

//Check if user is logged in
if (!isset($_SESSION["loginUser"]) && $_SESSION["loginUser"] != TRUE) {
  header("Location: login.php");
}

echo "Get more space";

?>
