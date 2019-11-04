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

if (isset($_POST["g-recaptcha-response"])) {
  $captcha = $_POST["g-recaptcha-response"];
  $captchaSecretKey = "6LfA2cAUAAAAAFSHkup0iMMeaN2G1EqeA9clSSEr";
  $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($captchaSecretKey) .  '&response=' . urlencode($captcha);
  $response = file_get_contents($url);
  $responseKeys = json_decode($response,true);
  // should return JSON with success as true
  if($responseKeys["success"]) {}
  else {
    $loginUser = "deviceLocked";
    echo json_encode(array('loginUser' => $loginUser));
    exit;
  }
}

// Check if username exists in database
if (in_array($username, $allUsernames)) {

  //Get user's ID (starts at 1 and autoincrements for each new user)
  $sql = "SELECT id FROM UserInfo WHERE username = '$username';";
  $userID = mysqli_fetch_row(mysqli_query($conn, $sql));

  // Check if account is not locked
  $sql = "SELECT locked FROM LoginAttempts WHERE id = '$userID[0]';";
  $locked = mysqli_fetch_row(mysqli_query($conn, $sql));
  if (($locked[0] == NULL) || (strtotime($locked[0]) < strtotime($datetime) - 300)) {

    // Check if password is correct
    $sql = "SELECT password FROM UserInfo WHERE username = '$username';";
    $hashedReferencePassword = mysqli_fetch_row(mysqli_query($conn, $sql));
    if (password_verify($_POST['password'], $hashedReferencePassword[0])) {

      // Unlock account
      $sql = "UPDATE LoginAttempts SET locked = NULL WHERE id = '$userID[0]'";
      mysqli_query($conn, $sql);

      // Clear any login offenses from this ip
      $offensePosition = 1;
      while ($offensePosition < 11) {

        $ipPosition = "ip" . $offensePosition;
        $timePosition = "time" . $offensePosition;
        $sql = "SELECT $ipPosition FROM LoginAttempts WHERE id = '$userID[0]'";
        $ipCheck = mysqli_fetch_row(mysqli_query($conn, $sql));
        if ($ipCheck[0] == $ip) {
          $sql = "UPDATE LoginAttempts SET $ipPosition = NULL, $timePosition = NULL WHERE id = '$userID[0]'";
          mysqli_query($conn, $sql);

          $offensePosition++;
        }
        else {$offensePosition++;}
      }

      // Start user's session
      $_SESSION["loginUser"] = TRUE;
      $_SESSION["userID"] = $userID[0];

      $loginUser = "true";
      echo json_encode(array('loginUser' => $loginUser));
    }
    // Else if username exists but password is incorrect
    else {

      $loginUser = "false";

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
        if ($recentOffenses < 9) {

          $offenseTimeIndex = ($recentOffenses + 1) * 2;

          // If time$offense is NULL or not within the last 24 hours (86,400s)
          if ((empty($loginAttemptsRow[$offenseTimeIndex])) ||
            (strtotime($loginAttemptsRow[$offenseTimeIndex]) < strtotime($datetime) - 86400)
          ) {

            // Update ip$offense to $ip and time$offense to $datetime
            $ipColumn = "ip" . ($recentOffenses + 1);
            $timeColumn = "time" . ($recentOffenses + 1);
            $sql = "UPDATE LoginAttempts SET $ipColumn = '$ip', $timeColumn = '$datetime' WHERE id = '$userID[0]';";
            mysqli_query($conn, $sql);

            $logged = TRUE;
          }
          else {$recentOffenses++;}
        }
        else {

          // Lock account
          $sql = "UPDATE LoginAttempts SET locked = '$datetime' WHERE id = '$userID[0]';";
          mysqli_query($conn, $sql);
          $logged = TRUE;

          $loginUser = "locked";
        }
      }

      echo json_encode(array('loginUser' => $loginUser));
    }
  }
  // Else if account is locked
  else {
    $loginUser = "locked";
    echo json_encode(array('loginUser' => $loginUser));
  }
}
// Else if username does not exist
else {

  //Check if IP is locked
  $sql = "SELECT locked FROM LoginAttemptsNoID WHERE ip = '$ip';";
  $locked = mysqli_fetch_row(mysqli_query($conn, $sql));
  if (($locked[0] == NULL) || (strtotime($locked[0]) < strtotime($datetime) - 300)) {

    // Get all IP addresses in LoginAttemptsNoID
    $sql = "SELECT ip FROM LoginAttemptsNoID;";
    $allIPsColumn = mysqli_query($conn, $sql);
    while ($allIPsRow = mysqli_fetch_array($allIPsColumn)) {
      $allIPs[] = $allIPsRow[0];
    }

    // If IP from this offense exists in all IP addresses in LoginAttemptsNoID
    if (in_array($ip, $allIPs)) {

      $loginUser = "false";

      // Get row array of ip in LoginAttemptsNoID
      $sql = "SELECT * FROM LoginAttemptsNoID WHERE ip = '$ip';";
      $loginAttemptsRow = mysqli_fetch_row(mysqli_query($conn, $sql));

      // Figure out which column to record offense in based on entries, times, and current time
      $logged = FALSE;
      $recentOffenses = 0;
      while ($logged == FALSE) {

        // If there has been < 10 invalid login attempts in the last 24 hours
        if ($recentOffenses < 9) {

          $offenseTimeIndex = $recentOffenses + 1;

          // If time$offense is NULL or not within the last 24 hours (86,400s)
          if ((empty($loginAttemptsRow[$offenseTimeIndex])) ||
            (strtotime($loginAttemptsRow[$offenseTimeIndex]) < strtotime($datetime) - 86400)
          ) {

            // Update ip$offense to $ip and time$offense to $datetime
            $timeColumn = "time" . ($recentOffenses + 1);
            $sql = "UPDATE LoginAttemptsNoID SET $timeColumn = '$datetime' WHERE ip = '$ip';";
            $success = mysqli_query($conn, $sql);

            $logged = TRUE;
          }
          else {$recentOffenses++;}
        }
        else {

          // Lock account
          $sql = "UPDATE LoginAttemptsNoID SET locked = '$datetime' WHERE ip = '$ip';";
          mysqli_query($conn, $sql);
          $logged = TRUE;

          $loginUser = "deviceLocked";
        }
      }
    }
    else {

      // Add new row to LoginAttemptsNoID for this new offending IP
      $sql = "INSERT INTO LoginAttemptsNoID (ip, time1) VALUES ('$ip', '$datetime');";
      mysqli_query($conn, $sql);
    }

    echo json_encode(array('loginUser' => $loginUser));
  }
  else {

    $loginUser = "deviceLocked";
    echo json_encode(array('loginUser' => $loginUser));
  }
}
 ?>
