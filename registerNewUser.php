<!--
This file fully automatically registers a new user on corvin. The file
interacts with the MySQL database, ensures proper info has been collected from
the user, inputs the user's data into a new row in the database, and creates
the user's main folder as well as recycle bin folder.

Issues: copyright Corvin, Inc. displays on the top of the page and some content
        displays off of the page with certain screen sizes

Coded by: Joel N. Johnson
-->

<!-- 1 Header -->
<head>
  <title>Home | Corvin</title>

  <link href = "one.css" type = "text/css" rel = "stylesheet"/>

  <link rel = "apple-touch-icon" sizes = "57x57"
    href = "Art/Favicon/apple-icon-57x57.png" />
  <link rel = "apple-touch-icon" sizes = "60x60"
    href = "Art/Favicon/apple-icon-60x60.png" />
  <link rel = "apple-touch-icon" sizes = "72x72"
    href = "Art/Favicon/apple-icon-72x72.png" />
  <link rel = "apple-touch-icon" sizes = "76x76"
    href = "Art/Favicon/apple-icon-76x76.png" />
  <link rel = "apple-touch-icon" sizes = "114x114"
    href = "Art/Favicon/apple-icon-114x114.png" />
  <link rel = "apple-touch-icon" sizes = "120x120"
    href = "Art/Favicon/apple-icon-120x120.png" />
  <link rel = "apple-touch-icon" sizes = "144x144"
    href = "Art/Favicon/apple-icon-144x144.png" />
  <link rel = "apple-touch-icon" sizes = "152x152"
    href = "Art/Favicon/apple-icon-152x152.png" />
  <link rel = "apple-touch-icon" sizes = "180x180"
    href = "Art/Favicon/apple-icon-180x180.png" />
  <link rel = "icon" type = "image/png" sizes = "192x192"
    href = "Art/Favicon/android-icon-192x192.png" />
  <link rel = "icon" type = "image/png" sizes = "32x32"
    href = "Art/Favicon/favicon-32x32.png" />
  <link rel = "icon" type = "image/png" sizes = "96x96"
    href = "Art/Favicon/favicon-96x96.png" />
  <link rel = "icon" type = "image/png" sizes = "16x16"
    href = "Art/Favicon/favicon-16x16.png" />
  <link rel = "manifest" href = "/manifest.json" />

  <meta name = "msapplication-TileColor" content = "#ffffff"/>
  <meta name = "msapplication-TileImage" content = "/ms-icon-144x144.png"/>
  <meta name = "theme-color" content = "#ffffff"/>

  <meta http-equiv = "refresh" content = "855"/>

  <meta name = "google" content = "notranslate"/>
</head>

<!-- 2 Top Bar -->
<body class = "registerNewUser">
<div class = 'registerNewUserContainer'>
    <div class = 'registerNewUserCenter'>
        <div class = 'registerNewUserTopBar'>
            <div class = 'registerNewUserCorvin'>
                Corvin
            </div>
        </div>

<br /><br /><br /><br /><br /><br /><br /><br />

<!-- 3 MySQL Database Setup -->
<?php

// MySQL server connection
$conn = mysqli_connect("127.0.0.1", "joel", "Daytona675");

// Check if connected to MYSQLI server
if (!$conn) {
  echo("Failed to connect to database: " . mysqli_connect_error()) .
    "<br /><br />";
}

// Create Corvin database if not already created
$sql = "CREATE DATABASE IF NOT EXISTS Corvin;";
if (!mysqli_query($conn, $sql)) {
  echo "Error creating database Corvin.<br /><br />";
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
  active BIT DEFAULT 0,
  darkmode BIT DEFAULT 1);
";

if (!mysqli_query($conn, $sql)) {
  echo "Error creating UserInfo table: " . mysqli_error($conn) . "<br /><br />";
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
  echo "Error creating LoginAttempts table: " . mysqli_error($conn) . "<br /><br />";
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
  echo "Error creating LoginAttemptsNoID table: " . mysqli_error($conn) . "<br /><br />";
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
    echo "Password needs to be at least 8 characters.";
    exit;
  }
}
else {
  echo "Passwords do not match.";
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
          accountTier,
          darkmode)
          VALUES (
          '$firstName',
          '$lastName',
          '$username',
          '$hashedPassword',
          '$email',
          '$verifyEmailHash',
          '$storageSpaceInMegabytes',
          '$accountTier',
          1)
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
            else {echo "Error: User recycle folder not created.";}
          }
          else {echo "Error: User folder not created.";}

          echo "Welcome, " . $firstName . "!<br /><br /><br />";
          echo "Your ". $accountTier . " Account has been credited with " .
            $storageSpaceInHuman . " storage space.";
          echo "<br /><br /><br />Sincerely,<br /><br />";
          echo "The Corvin Team<br />";
          echo "joel@cor.vin";

          echo "<br /><br /><br /><br />";

          echo "
          <form action = 'login.php'>
            <input class = 'registerNewUserLoginButton' type = 'submit'
              value = 'Login'>
          </form>";
        }
        else {
          echo "Error: " . $sql . "<br /><br />" . mysqli_error($conn) .
            "<br /><br />";
        }
      }
      else {
        echo "Incorrect referral code.";
        echo "<br /><br />";
        echo "
          <button class = 'registerNewUserLoginButton'
            onclick = 'window.history.back()'>
            Go back
          </button>";
      }
    }
    else {
      echo "Email already associated with another account.";
      echo "<br /><br />";
      echo "
      <button class = 'registerNewUserLoginButton'
        onclick = 'window.history.back()'>
        Go back
      </button>";
    }
  }
  else {
    echo "Invalid email address.";
    echo "<br /><br />";
    echo "
    <button class = 'registerNewUserLoginButton'
      onclick = 'window.history.back()'>
      Go back
    </button>";
  }
}
else {
  echo "Username already taken.";
  echo "<br /><br />";
  echo "
  <button class = 'registerNewUserLoginButton'
    onclick = 'window.history.back()'>
    Go back
  </button>";
}
mysqli_close($conn);
?>

<br /><br />

</div>
<div class = 'registerNewUserPush'></div>
</div>

<!-- 5 Footer -->
<div class = 'registerNewUserFooter'>&copy; Corvin, Inc.</div>
</body>
