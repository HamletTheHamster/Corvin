<?php
/*
This file fully automatically registers a new user on corvin. The file
interacts with the MySQL database, ensures proper info has been collected from
the user, inputs the user's data into a new row in the database, and creates
the user's main folder as well as recycle bin folder.

Issues: copyright Corvin, Inc. displays on the top of the page and some content
        displays off of the page with certain screen sizes

Coded by: Joel N. Johnson
*/


// MySQL server connection
$conn = mysqli_connect("127.0.0.1", "joel", "Daytona675");

// Check if connected to MYSQLI server
if (!$conn) {

  $registerUser = "Failed to connect to database: " . mysqli_connect_error();
  echo json_encode(array('registerUser' => $registerUser));
  exit;
}

// Create Corvin database if not already created
$sql = "CREATE DATABASE IF NOT EXISTS Corvin;";
if (!mysqli_query($conn, $sql)) {

  $registerUser = "Error creating database Corvin";
  echo json_encode(array('registerUser' => $registerUser));
  exit;
}

// Go into Corvin database
mysqli_query($conn, "USE Corvin;");

// Create UserInfo table if not exists
$sql = "CREATE TABLE IF NOT EXISTS UserInfo (
  id INT(9) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  firstName VARCHAR(100) NOT NULL,
  lastName VARCHAR(100) NOT NULL,
  username VARCHAR(100) NOT NULL,
  password VARCHAR(2056) NOT NULL,
  email VARCHAR(100) NOT NULL ,
  verifyEmailHash VARCHAR(128) NOT NULL ,
  registrationDate DATETIME DEFAULT CURRENT_TIMESTAMP,
  lastActive DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  storageSpaceInMegabytes INT(255) SIGNED,
  accountTier VARCHAR(100),
  active BIT DEFAULT 0);
";

if (!mysqli_query($conn, $sql)) {

  $registerUser = "Error creating UserInfo table: " . mysqli_error($conn);
  echo json_encode(array('registerUser' => $registerUser));
  exit;
}

// Create LoginAttempts table if not exists
$sql = "CREATE TABLE IF NOT EXISTS LoginAttempts (
  id INT(9) UNSIGNED PRIMARY KEY,
  ip1 VARBINARY(16),
  time1 DATETIME DEFAULT NULL,
  ip2 VARBINARY(16),
  time2 DATETIME DEFAULT NULL,
  ip3 VARBINARY(16),
  time3 DATETIME DEFAULT NULL,
  ip4 VARBINARY(16),
  time4 DATETIME DEFAULT NULL,
  ip5 VARBINARY(16),
  time5 DATETIME DEFAULT NULL,
  ip6 VARBINARY(16),
  time6 DATETIME DEFAULT NULL,
  ip7 VARBINARY(16),
  time7 DATETIME DEFAULT NULL,
  ip8 VARBINARY(16),
  time8 DATETIME DEFAULT NULL,
  ip9 VARBINARY(16),
  time9 DATETIME DEFAULT NULL,
  ip10 VARBINARY(16),
  time10 DATETIME DEFAULT NULL,
  locked DATETIME DEFAULT NULL,
  botTest BIT DEFAULT 0);
";

if (!mysqli_query($conn, $sql)) {

  $registerUser = "Error creating LoginAttempts table: " . mysqli_error($conn);
  echo json_encode(array('registerUser' => $registerUser));
  exit;
}

// Create LoginAttemptsNoID table if not exists
$sql = "CREATE TABLE IF NOT EXISTS LoginAttemptsNoID (
  ip VARBINARY(16),
  time1 DATETIME DEFAULT NULL,
  time2 DATETIME DEFAULT NULL,
  time3 DATETIME DEFAULT NULL,
  time4 DATETIME DEFAULT NULL,
  time5 DATETIME DEFAULT NULL,
  time6 DATETIME DEFAULT NULL,
  time7 DATETIME DEFAULT NULL,
  time8 DATETIME DEFAULT NULL,
  time9 DATETIME DEFAULT NULL,
  time10 DATETIME DEFAULT NULL,
  locked DATETIME DEFAULT NULL,
  botTest BIT DEFAULT 0);
";

if (!mysqli_query($conn, $sql)) {

  $registerUser = "Error creating LoginAttemptsNoID table: " . mysqli_error($conn);
  echo json_encode(array('registerUser' => $registerUser));
  exit;
}

// Create Preferences table if not exists
$sql = "CREATE TABLE IF NOT EXISTS Preferences (
  id INT(9) UNSIGNED PRIMARY KEY,
  darkmode TINYINT DEFAULT 1);
";

if (!mysqli_query($conn, $sql)) {

  $registerUser = "Error creating Preferences table: " . mysqli_error($conn);
  echo json_encode(array('registerUser' => $registerUser));
  exit;
}

// Create Workspaces table if not exists
$sql = "CREATE TABLE IF NOT EXISTS Workspaces (
  id INT(9) UNSIGNED PRIMARY KEY,
  workspace1 VARCHAR(100));
";

if (!mysqli_query($conn, $sql)) {

  $registerUser = "Error creating Workspaces table: " . mysqli_error($conn);
  echo json_encode(array('registerUser' => $registerUser));
  exit;
}

// Create WorkspaceSettings table if not exists
$sql = "CREATE TABLE IF NOT EXISTS WorkspaceSettings (
  workspace VARCHAR(100),
  storageSpaceInMegabytes INT(255) SIGNED,
  membersCanInvite TINYINT DEFAULT 0);
";

if (!mysqli_query($conn, $sql)) {
  $registerUser = "Error creating WorkspaceSettings table: " . mysqli_error($conn);
  echo json_encode(array('registerUser' => $registerUser));
  exit;
}

// Evaluate recaptcha
$recaptchaResponse = $_POST["recaptcha"];
$recaptchaSecretKey = "6LfA2cAUAAAAAFSHkup0iMMeaN2G1EqeA9clSSEr";
$recaptchaVerify = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecretKey}&response={$recaptchaResponse}"));
if ($recaptchaVerify->success == false) {
  $registerUser = "Recaptcha Test Fail";
  echo json_encode(array('registerUser' => $registerUser));
  exit;
}
else if ($recaptchaVerify->success == true) {
}
else {
  $registerUser = "No Captcha Submitted";
  echo json_encode(array('registerUser' => $registerUser));
  exit;
}

// 4 Register User
// Read in values entered in registration form
$firstName = filter_input(INPUT_POST, "firstName", FILTER_SANITIZE_STRING);
$lastName = filter_input(INPUT_POST, "lastName", FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
$username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);

// Check if passwords match
if ($_POST["password"] == $_POST["password2"]) {

  // Check if password meets criteria
  if (strlen($_POST["password"]) > 7) {

    // Password_hash automatically uses currently recommended hashing algorithm
    // with salt
    $hashedPassword = password_hash($_POST["password"], PASSWORD_DEFAULT);
  }
  else {
    $registerUser = "Passwords must be at least 8 characters";
    echo json_encode(array('registerUser' => $registerUser));
    exit;
  }
}
else {
  $registerUser = "Passwords do not match";
  echo json_encode(array('registerUser' => $registerUser));
  exit;
}

$accountTier = filter_input(INPUT_POST, "accountTier", FILTER_SANITIZE_STRING);
$referralCode = filter_input(
  INPUT_POST, "referralCode", FILTER_SANITIZE_STRING);
$incorrectReferralCode = FALSE;

// Determine account tier
switch ($accountTier) {
  case "Free":
    $storageSpaceInMegabytes = 250; //Free 250MB
    $storageSpaceInHuman = "250MB of";
    break;
  case "Plus":
    $storageSpaceInMegabytes = 100000; //Plus 100GB
    $storageSpaceInHuman = "100GB of";
    break;
  case "Pro":
    $storageSpaceInMegabytes = 500000; //Pro 500GB
    $storageSpaceInHuman = "500GB of";
    break;
  case "Referral":
    switch ($referralCode) {
      case "1TERABYTEQVHERD6496":
        $storageSpaceInMegabytes = 1000000; //1TB referral code
        $storageSpaceInHuman = "1TB of";
        break;
      case "2TERABYTESFBMKGE7594":
        $storageSpaceInMegabytes = 2000000; //2TB referral code
        $storageSpaceInHuman = "2TB of";
        break;
      case "UNRESTRICTEDDHMH6583":
        $storageSpaceInMegabytes = -1; //Unlimited referral code
        $storageSpaceInHuman = "unlimited";
        break;
      default:
        $incorrectReferralCode = TRUE; //Incorrect referral code
      break;
    }
}

// Check if username taken
$sql = "SELECT DISTINCT username FROM UserInfo;";
$allUsernames = mysqli_fetch_array(mysqli_query($conn, $sql));
if (!in_array($username, $allUsernames)) {

  // Check if email is a valid email format
  if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

    // Check if email already used
    $sql = "SELECT DISTINCT email FROM UserInfo;";
    $allEmails = mysqli_fetch_array(mysqli_query($conn, $sql));
    if (!in_array($email, $allEmails)) {

      // Check if referral code correct
      if (!$incorrectReferralCode) {
        $verifyEmailHash = hash('sha512', rand());

        $sql = "INSERT INTO UserInfo (
          firstName,
          lastName,
          username,
          password,
          email,
          verifyEmailHash,
          storageSpaceInMegabytes,
          accountTier)
          VALUES (
          '$firstName',
          '$lastName',
          '$username',
          '$hashedPassword',
          '$email',
          '$verifyEmailHash',
          '$storageSpaceInMegabytes',
          '$accountTier')
        ";

        /*
        $subject = 'Verify your email for your Corvin account';
        $message = "
Welcome, " . $firstName . "

Your Corvin account has been created!

Please click the link to activate your account:

http://www.cor.vin/verifyEmail.php?email=" . $email . "&hash = " . $verifyEmailHash .
"
Thanks,

The Corvin Team";

        $message = wordwrap($message, 70);
        $headers = 'From: joel@cor.vin' . "\r\n";
        if(mail($email, $subject, $message, $headers)) {
          echo "mail function successfully executed.";
        }
        else {
          echo "mail function unsuccessful.";
        }
        */

        if (mysqli_query($conn, $sql)) {

          // Get userID from entry just entered into database
          $sql = "SELECT id FROM UserInfo WHERE username = '" .
            $username . "'";
          $userID = mysqli_fetch_row(mysqli_query($conn, $sql));

          // Add a new row in Preferences for this ID
          $sql = "INSERT INTO Preferences (id) VALUES ('$userID[0]')";
          mysqli_query($conn, $sql);

          // Add a new row in Workspaces for this ID
          $sql = "INSERT INTO Workspaces (id) VALUES ('$userID[0]')";
          mysqli_query($conn, $sql);

          // Add a new row in LoginAttemts for this ID
          $sql = "INSERT INTO LoginAttempts (id) VALUES ('$userID[0]')";
          mysqli_query($conn, $sql);

          // Get first and last name from database
          $sql = "SELECT firstName, lastName FROM UserInfo WHERE id = '" .
            $userID[0] . "'";
          $user = mysqli_fetch_array(mysqli_query($conn, $sql));

          $userFolderName = $userID[0] . " - " . $user[0] . $user[1];
          $sanitizedUserFolderName = filter_var(
            $userFolderName, FILTER_SANITIZE_STRING);

          $userFolderFullPath = "../../../mnt/Raid1Array/Corvin/" .
            $sanitizedUserFolderName;
          $userRecycleFolderFullPath =
            "../../../mnt/Raid1Array/Corvin/0 - Recycle/" .
            $sanitizedUserFolderName;

          // Make user's main and recycle folders
          if (mkdir($userFolderFullPath, 0777, true)) {
            chmod($userFolderFullPath, 0777);

            if (mkdir($userRecycleFolderFullPath, 0777, true)) {
              chmod($userRecycleFolderFullPath, 0777);
            }
            else {
              $registerUser = "Error: User recycle folder not created";
              echo json_encode(array('registerUser' => $registerUser));
              exit;
            }
          }
          else {
            $registerUser = "Error: User folder not created";
            echo json_encode(array('registerUser' => $registerUser));
            exit;
          }

          $registerUser = "true";
          echo json_encode(array('registerUser' => $registerUser));
          exit;
        }
        else {
          $registerUser = "Error: " . $sql . " " . mysqli_error($conn);
          echo json_encode(array('registerUser' => $registerUser));
          exit;
        }
      }
      else {
        $registerUser = "Incorrect referral code";
        echo json_encode(array('registerUser' => $registerUser));
        exit;
      }
    }
    else {
      $registerUser = "Email already associated with another account";
      echo json_encode(array('registerUser' => $registerUser));
      exit;
    }
  }
  else {
    $registerUser = "Invalid email address";
    echo json_encode(array('registerUser' => $registerUser));
    exit;
  }
}
else {
  $registerUser = "Username already taken";
  echo json_encode(array('registerUser' => $registerUser));
  exit;
}
mysqli_close($conn);
?>
