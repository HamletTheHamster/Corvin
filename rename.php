<!--
        This is a php file for NanoLab which is called by each user's main page
        to handle renaming files in the current directory.

        Hierarchy

            0	Expire Session
            1	Header
            2	Rename Function

        Variables

            user                -   first name of user, inputted through hidden
                                    html form
            userLastName        -   last name of user, inputted through hidden
                                    html form
            currentDirectory    -   location of user's root folder
            oldName             -   file to be renamed's current name
            newName             -   file to be renamed's desired new name
            oldNameFullPath     -   location of the file with its old name
            newNameFullPath     -   location of the file with its new name


        Last updated: January 8 2019

        Coded by: Joel N. Johnson
-->

<!-- 0 Expire Session -->
<?php
    // Display any errors
    ini_set("display_errors", 1);

    // And be verbose about it
    error_reporting(E_ALL);

    session_start();

    //MYSQLi server connection
    $conn = mysqli_connect("127.0.0.1", "joel", "Daytona675");

    //Check if connected to MYSQLI server
    if (!$conn) {
        echo("Failed to connect to database: " .
                mysqli_connect_error()) . "<br /><br />";
    }

    //Go into Corvin database
    mysqli_query($conn, "USE Corvin;");

    //Assign user's ID passed from validate.php
    $userID = $_SESSION["userID"];

    $sql = "SELECT firstName, lastName FROM UserInfo WHERE id = '$userID'";
    $user = mysqli_fetch_array(mysqli_query($conn, $sql));

    $currentPathString = filter_input(
            INPUT_POST,
            "currentPathString",
            FILTER_SANITIZE_STRING
    );

    $queryArray = explode("/", substr($currentPathString, 0, -1));
    $returnURL = "home.php?" . http_build_query($queryArray, '');

    // Session Timeout after 15 Minutes

    // If last request was more than 894 seconds ago (14.9 minutes)
    if (
            isset($_SESSION['LastActivity']) &&
            (time() - $_SESSION['LastActivity'] > 894)
    )
    {
        // Kick the user back to the login screen
        header("Location: login.php");
    }

    // Update last activity time stamp
    $_SESSION['LastActivity'] = time();

    // Regenerate Session ID every 20 Minutes

    // If session started timestamp is not set
    if (!isset($_SESSION['Created']))
    {
        // Then set the session start time to now
        $_SESSION['Created'] = time();
    }

    // If session started more than 30 minutes ago
    elseif (time() - $_SESSION['Created'] > 1200)
    {
        /* Then change session ID for the current session and invalidate old
        session ID */
        session_regenerate_id(true);

        // Update creation time
        $_SESSION['Created'] = time();
    }
?>

<!DOCTYPE html>
<html lang = "en">

<!-- 1 Header -->
<head>
    <title>Corvin</title>

    <link href = "index.css" type = "text/css" rel = "stylesheet" />

    <link rel = "apple-touch-icon" sizes = "57x57"
          href = "/Art/Favicon/apple-icon-57x57.png" />
    <link rel = "apple-touch-icon" sizes = "60x60"
          href = "/Art/Favicon/apple-icon-60x60.png" />
    <link rel = "apple-touch-icon" sizes = "72x72"
          href = "/Art/Favicon/apple-icon-72x72.png" />
    <link rel = "apple-touch-icon" sizes = "76x76"
          href = "/Art/Favicon/apple-icon-76x76.png" />
    <link rel = "apple-touch-icon" sizes = "114x114"
          href = "/Art/Favicon/apple-icon-114x114.png" />
    <link rel = "apple-touch-icon" sizes = "120x120"
          href = "/Art/Favicon/apple-icon-120x120.png" />
    <link rel = "apple-touch-icon" sizes = "144x144"
          href = "/Art/Favicon/apple-icon-144x144.png" />
    <link rel = "apple-touch-icon" sizes = "152x152"
          href = "/Art/Favicon/apple-icon-152x152.png" />
    <link rel = "apple-touch-icon" sizes = "180x180"
          href = "/Art/Favicon/apple-icon-180x180.png" />
    <link rel = "icon" type = "image/png" sizes = "192x192"
          href = "/Art/Favicon/android-icon-192x192.png" />
    <link rel = "icon" type = "image/png" sizes = "32x32"
          href = "/Art/Favicon/favicon-32x32.png" />
    <link rel = "icon" type = "image/png" sizes = "96x96"
          href = "/Art/Favicon/favicon-96x96.png" />
    <link rel = "icon" type = "image/png" sizes = "16x16"
          href = "/Art/Favicon/favicon-16x16.png" />
    <link rel = "manifest" href = "/manifest.json" />

    <meta name = "msapplication-TileColor" content = "#ffffff" />
    <meta name = "msapplication-TileImage" content = "/ms-icon-144x144.png" />
    <meta name = "theme-color" content = "#ffffff" />

    <meta http-equiv = "refresh" content = "284" />
</head>

<body>

<!-- 2 Rename Function -->
<?php

    function returnButton($returnURLParam)
    {
        echo "<br /><br />";
        echo "<form method = 'get' action = '" . $returnURLParam . "' />";
        echo "<input type = 'submit' value = 'Return' /></form>";
    }

    // Display any errors
    ini_set("display_errors", 1);

    // And be verbose about it
    error_reporting(E_ALL);

    // Assign path of current directory
    $currentDirectory = "../../../mnt/Raid1Array/Corvin/" .
        $userID . " - " .
        $user[0] .
        $user[1] . "/" .
        $currentPathString;

    // Assign the old name to a variable
    $oldName = addslashes(
            trim(filter_input(
                INPUT_POST,
                "oldName",
                FILTER_SANITIZE_STRING), "./"
            )
    );

    // Assign the new name to a variable
    $newName = addslashes(
        trim(filter_input(
            INPUT_POST,
            "newName",
            FILTER_SANITIZE_STRING),
            "./"
        )
    );

    // Assign full path and old name to a variable
    $oldNameFullPath = $currentDirectory . $oldName;

    // Assign full path and new name to a variable
    $newNameFullPath = $currentDirectory . $newName;

    // If the old path and file name is readable
    if (is_readable($oldNameFullPath))
    {
        /* Then try to rename the file to the new name and if that was
        successful */
        if (rename($oldNameFullPath, $newNameFullPath))
        {
            // Then refresh the page
            echo "<meta http-equiv = 'refresh' content = '0; " . $returnURL . "'>";
        }

        // Else, if the rename failed then print rename failure statement
        else
        {
            echo "There was a problem renaming the file.";
            returnButton($returnURL);
        }
    }

    // Else, if the old path and/or file name is not readable
    else
    {
        // Then print read failure statement
        echo "There was a problem reading the file or file name.";
        returnButton($returnURL);
    }
?>

</body>
</html>
