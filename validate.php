<?php
/*
This is a Corvin file that is called by index.html to handle user login
validation and rerouting to the user's home page.

Variables

  conn                    - MySQL database connection
  _POST[username]         - username submitted via post data in login form
  username                - user's username
  allUsernames            - array containing every single username in the
                            database
  sql                     - used to hold query database strings
  columnData              - used to hold MySQL query result from full username
                            search
  row                     - used to convert columData to row format and store
                            in allUsernames array
  hashedReferencePassword - the hashed password in the database corresponding
                            to the entered username; used to compare entered
                            password to reference password held in database
  userID                  - user ID of the user; unique integer used to
                            identify the user and grab other info from their
                            row in the database
  _SESSION[loginUser]     - global variable that allows for a user's id to
                            be set to the session that has been started;
                            verifies that the user has logged in. Their main
                            page will be inaccessible and just reroute to
                            cor.vin until they have logged in with their
                            correct username and password and thus their
                            session id has been set.
  _SESSION[userID]        - global variable setting the user's ID for use
                            throughout their logged in session

Issues: Searching among all usernames might be able to be optimized
        Column data vs row data might be able to be optimized

Coded by: Joel N. Johnson
*/

// Display any errors
ini_set("display_errors", 1);

// And be verbose about it
error_reporting(E_ALL);

session_start();

//Check if user is logged in
if (!isset($_POST["username"]) || !isset($_POST["password"])) {
  header("Location: login.php");
}

//MYSQLi server connection
$conn = mysqli_connect("127.0.0.1", "joel", "Daytona675");

//Check if connected to MYSQLI server
if (!$conn) {
    echo("Failed to connect to database: " .
            mysqli_connect_error()) . "<br /><br />";
}

//Go into Corvin database
mysqli_query($conn, "USE Corvin;");

//Assign submitted username
$username = $_POST["username"];

//Get array of all usernames
$sql = "SELECT DISTINCT username FROM UserInfo;";
$usernameColumnData = mysqli_query($conn, $sql);
while ($usernameRow = mysqli_fetch_array($usernameColumnData)) {
  $allUsernames[] = $usernameRow[0];
}

// Get current datetime and user's ip address
$datetime = date("Y-m-d H:i:s");
$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];

// Check if username exists in database
if (in_array($username, $allUsernames)) {

  // Check if password is correct
  $sql = "SELECT password FROM UserInfo WHERE username = '" . $username . "';";
  $hashedReferencePassword = mysqli_fetch_row(mysqli_query($conn, $sql));
  if (password_verify($_POST['password'], $hashedReferencePassword[0])) {

    //Get user's ID (starts at 1 and autoincrements for each new user)
    $sql = "SELECT id FROM UserInfo WHERE username = '" . $username . "';";
    $userID = mysqli_fetch_row(mysqli_query($conn, $sql));

    //Start user's session
    $_SESSION["loginUser"] = TRUE;
    $_SESSION["userID"] = $userID[0];

    $loginUser = "true";
    echo json_encode(array('loginUser' => $loginUser));
  }
  // Else if username exists but password is incorrect
  else {

    // Set ID to ID of username which exists in UserInfo table
    $sql = "SELECT id FROM UserInfo WHERE username = '" . $username . "';";
    $userID = mysqli_fetch_row(mysqli_query($conn, $sql));

    // Get row array of userID in LoginAttempts
    $sql = "SELECT * FROM LoginAttempts WHERE id = '" . $userID[0] . "';";
    $loginAttemptsRow = mysqli_fetch_row(mysqli_query($conn, $sql));

    // Figure out which column to record offense in based on entries, times, and current time
    $logged = FALSE;
    $recentOffenses = 0;
    while ($logged == FALSE) {

      // If there has been < 10 invalid login attempts in the last 24 hours
      if ($recentOffenses < 10) {

        $offenseTimeIndex = ($recentOffenses + 1) * 2;

        // If time$offense is NULL or not within the last 24 hours (86,400s)
        if ((empty($loginAttemptsRow[$offenseTimeIndex])) ||
          (strtotime($loginAttemptsRow[$offenseTimeIndex]) < strtotime($datetime) - 86400)
        ) {

          // Update ip$offense to $ip and time$offense to $datetime
          $ipColumn = "ip" . ($recentOffenses + 1);
          $timeColumn = "time" . ($recentOffenses + 1);
          $sql = "UPDATE LoginAttempts SET $ipColumn = '$ip', $timeColumn = '$datetime' WHERE id = '$userID[0]';";
          $success = mysqli_query($conn, $sql);

          $logged = TRUE;
        }
        else {$recentOffenses++;}
      }
      else {

        // Lock account
        $logged = TRUE;
      }

    }

    $loginUser = "false";
    echo json_encode(array('loginUser' => $loginUser));
  }
}
// Else if username does not exist
else {
/*
  // Problem: there's no ID set if it's an unsuccessful login
  // '-> assign ID=0 to IP addresses that entered invalid username
  // '-> UPDATE LoginAttempts SET (IP#) = $IP WHERE IP1 = $IP

  // Get row array of userID in LoginAttempts
  $sql = "SELECT * FROM LoginAttempts WHERE id = '0';";
  $loginAttemptsRow = mysqli_fetch_row(mysqli_query($conn, $sql));

  // Figure out which column to record offense in based on entries, times, and current time
  $logged = FALSE;
  $recentOffenses = 0;
  while ($logged == FALSE) {

    // If there has been < 10 invalid login attempts in the last 24 hours
    if ($recentOffenses < 10) {

      $offenseTimeIndex = ($recentOffenses + 1) * 2;

      // If time$offense is NULL or not within the last 24 hours (86,400s)
      if ((empty($loginAttemptsRow[$offenseTimeIndex])) ||
        (strtotime($loginAttemptsRow[$offenseTimeIndex]) < strtotime($datetime) - 86400)
      ) {

        // Update ip$offense to $ip and time$offense to $datetime
        $ipColumn = "ip" . ($recentOffenses + 1);
        $timeColumn = "time" . ($recentOffenses + 1);
        $sql = "UPDATE LoginAttempts SET $ipColumn = '$ip', $timeColumn = '$datetime' WHERE id = '$userID[0]';";
        $success = mysqli_query($conn, $sql);

        $logged = TRUE;
      }
      else {$recentOffenses++;}
    }
    else {

      // Lock account
      $logged = TRUE;
    }


/*
  // Get all IPs that tried to login with no valid username (ID=0)
  $sql = "SELECT ip1 FROM LoginAttempts WHERE id = '". $userID . "';";
  $ipColumnData = mysqli_query($conn, $sql);
  while ($ipRow = mysqli_fetch_array($ipColumnData)) {
    $allIPs[] = $ipRow[0];
  }

  if (in_array($ip, $allIPs) {


  }
*/
  $loginUser = "false";
  echo json_encode(array('loginUser' => $loginUser));
}
 ?>
