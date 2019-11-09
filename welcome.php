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

<?php

$firstName = $_POST['firstName'];
$accountTier = $_POST['accountTier'];
$storageSpaceInHuman = $_POST['$storageSpaceInHuman'];

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
?>

<br /><br />

</div>
<div class = 'registerNewUserPush'></div>
</div>

<!-- 5 Footer -->
<div class = 'registerNewUserFooter'>&copy; Corvin, Inc.</div>
</body>
