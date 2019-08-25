<!--
        This is a php file for NanoLab which is called by each user's main user
        page to handle files or folders being deleted. It does not truly delete
        the files, but rather sends them to the user's hidden recycle folder
        [for later retrieval within some grace period of ~ 1 month.]

        Hierarchy

            0 Expire Session
            1 Header
            2 Recycle Function

        Variables

            currentDirectory                -   full path to the current
                                                directory containing the file or
                                                folder to be recycled
            fileToRecycle                   -   name of the file to be recycled;
                                                received from html form
            fileToRecycleFullPath           -   full path to the file, including
                                                file name and file type
                                                extension
            userRecycleDirectory            -   full path to the user's hidden
                                                recycle folder
            userRecycleDirectoryFullPath    -   full path to the recycled file
                                                in user's recycle folder,
                                                including file name and file
                                                type extension


        Last updated: January 8, 2019

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

    // Session Timeout after 14.9 Minutes

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

    // If session started more than 20 minutes ago
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

    <meta http-equiv = "refresh" content = "60" />
</head>

<body>

<!-- 2 Recycle Function -->
<?php

    function returnButton($returnURLParam)
    {
        echo "<br /><br />";
        echo "<form method = 'get' action = '" . $returnURLParam . "' />";
        echo "<input type = 'submit' value = 'Return' /></form>";
    }

    $currentDirectory = "../../../mnt/Raid1Array/Corvin/" .
        $userID . " - " .
        $user[0] .
        $user[1] . "/" .
        $currentPathString;

    // Assign file name to recycle to variable
    $fileToRecycle = filter_input(
            INPUT_POST,
            "fileToRecycle",
            FILTER_SANITIZE_STRING
    );

    // Assign user's recycle folder path
    $userRecycleDirectory = "/../../../mnt/Raid1Array/Corvin/0 - Recycle/" .
        $userID . " - " .
        $user[0] .
        $user[1] . "/";
    $fileToRecycleFullPath = $currentDirectory . $fileToRecycle;

    $i = 1;

    /* While the name of the file or folder to be recycled matches the name of a
    file or folder already in the recycle folder, append the new file/folder
    with (#) representing the number of identically named files or folders that
    reside in the recycle folder by that name */
    while (
            array_search($fileToRecycle, scandir($userRecycleDirectory)) !==
            FALSE
    )
    {
        // If there is already a single copy
        if ($i > 1)
        {
            // Take the (1) off of the end of the name and make it (2)
            $fileToRecycle = substr($fileToRecycle, 0, -3) .
                "(" . $i . ")";
        }

        // Else append the file/folder with (1)
        else
        {
            $fileToRecycle = $fileToRecycle . "(1)";
        }
        ++$i;
    }

    // If there were duplicates, rename the file to match
    rename(
            $fileToRecycleFullPath,
            $currentDirectory . $fileToRecycle
    );

    // Assign full path plus name to variable
    $fileToRecycleFullPath = $currentDirectory . $fileToRecycle;

    // Assign recycled file name full path
    $userRecycleDirectoryFullPath = $userRecycleDirectory . $fileToRecycle;

    // If the full path and file is readable
    if (is_readable($fileToRecycleFullPath))
    {
        /* Then try to move the file to user's hidden recycle folder. If this
        was successful */
        if (rename($fileToRecycleFullPath, $userRecycleDirectoryFullPath))
        {
            echo "<meta http-equiv = 'refresh' content = '0; " . $returnURL . "'>";
        }

        // Else, print recycle fail statement
        else
        {
            echo "There was a problem sending " . $fileToRecycle .
                " to your recycle folder.";
            returnButton($returnURL);
        }
    }

    // Else, if the file is unreadable
    else
    {
        // Print read fail statement
        echo "There was a problem reading " . $fileToRecycle .
            "'s name or location.";
        returnButton($returnURL);
    }
?>

</body>
</html>
