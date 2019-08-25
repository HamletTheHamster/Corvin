<!--
        This is a php file for NanoLab which each user's main page calls to
        handle files being uploaded to Corvin. It can handle multiple files
        being uploaded at once.

        Hierarchy

            0	Session Expiration
            1	Header
            2	Upload Function

        Variables

            _FILES      -   multi-dimensional array holding the information on
                            each file being uploaded. The optional information
                            is structured as

                $_FILES['filesToUpload']['name']    -   the original name of the
                                                        file on the client
                                                        machine
                $_FILES['userfile']['type']         -   the mime type of the
                                                        file, if the browser
                                                        provided this
                                                        information.
                                                        An example would be
                                                        "image/gif". This mime
                                                        type is however not
                                                        checked on the PHP side
                                                        and therefore don't take
                                                        its value for granted
                $_FILES['userfile']['size']         -   the size, in bytes, of
                                                        the uploaded file
                $_FILES['userfile']['tmp_name']     -   the temporary filename
                                                        of the file in which the
                                                        uploaded file was stored
                                                        on the server
                $_FILES['userfile']['error']        -   the error code
                                                        associated with this
                                                        file upload

            error       -   returns value of UPLOAD_ERR_OK (0) if there isn't an
                            error
            temp_name   -   the temporary file name of the file in which the
                            uploaded file was stored on the server
            key         -   index value of current loop through the foreach loop
            name        -   name of the file without the full path before it. To
                            output the filename without the file type extension,
                            do

                $name = basename(
                            $_FILES["filesToUpload"]["name"][$key],
                            $suffix
                        );

                            where $suffix is the file type extension, e.g. .pdf

        Last updated: January 8, 2019

        Coded by: Joel N. Johnson
-->

<!-- 0 Session Expiration -->
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

    $userPage = "home.php";
    $queryArray = explode("/", substr($currentPathString, 0, -1));
    $returnURL = $userPage . "?" . http_build_query($queryArray, '');

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

    // If session started more than 20 minutes ago
    elseif (time() - $_SESSION['Created'] > 1200)
    {
        /* Then change session ID for the current session and invalidate old
        session ID */
        session_regenerate_id(true );

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

    <!-- 2 Upload Function -->
<?php

    function returnButton($returnURLParam)
    {
        echo "<br /><br />";
        $returnPage = "home.php?" . http_build_query($returnURLParam);
        echo "<form method = 'get' action = '" . $returnPage . "' />";
        echo "<button type = 'submit'>Return</button></form>";
    }

    /* This outputs the maximum uploadable file size. To change, edit php.ini
    file. */
    /*echo ini_get('upload_max_filesize') . '<br />' .
        ini_get('post_max_size') . '<br />';*/

    // Display any errors
    ini_set('display_errors', 1);

    // And be verbose about it
    error_reporting(E_ALL);

    $freeBytes = $_POST["freeBytes"];

    $destination = "../../../../mnt/Raid1Array/Corvin/" .
        $userID . " - " .
        $user[0] .
        $user[1] . "/" .
        $currentPathString;

    if (isset($_FILES["filesToUpload"]))
    {
        /* For each item in _FILES, with the current loop's index value
        referencing the error dimension of _FILES */
        foreach ($_FILES["filesToUpload"]["error"] as $key => $error)
        {
            // If there are no errors
            if ($error == UPLOAD_ERR_OK)
            {
                /* Assign the temporary file name given by the server when it
                stored it in memory to the variable tmp_name */
                $temporaryName = $_FILES["filesToUpload"]["tmp_name"][$key];

                // Assign the file name without full path to the variable name
                $name = str_replace(
                        "'",
                        "",
                        basename($_FILES["filesToUpload"]["name"][$key])
                );

                // Assign the full destination path and name
                $destinationFullPath = $destination . $name;

                // Find size of file to upload
                $fileToUploadBytes = $_FILES["filesToUpload"]["size"][$key];

                if ($fileToUploadBytes < $freeBytes) {

                    $zip = new ZipArchive;
                    if (
                        substr($name, -4) != "docx" &&
                        substr($name, -4) != "xlsx" &&
                        substr($name, -4) != "pptx" &&
                        $zip->open($temporaryName) === true
                    )
                    {
                        $zip->extractTo($destination);
                        $zip->close();
                        echo "<meta http-equiv = 'refresh' content = '0; " . $returnURL  . "'>";
                    }

                    /*
                                $Rar = fopen($TemporaryName, "r");
                                if (!$Rar)
                                {
                                    echo "Could not open the Rar file. <br /><br />";
                                }

                                $First5Characters = fgets($Rar, 5);
                                $fclose($Rar);

                                else if (strpos($First5Characters, 'Rar') !== FALSE)
                                {
                                    $Entries = $Rar->getEntries();
                                    if ($Entries === FALSE)
                                    {
                                        die("Failed fetching entries in Rar file.");
                                    }
                                    if (empty($Entries))
                                    {
                                        die("No valid entries found in Rar file.");
                                    }
                                    $Stream = reset($Entries)->getStream();
                                    if ($Stream === FALSE)
                                    {
                                        die("Failed opening first file in Rar file.");
                                    }
                                    $List = rar_list($Rar);
                                    foreach($List as $File)
                                    {
                                        $Entry = rar_entry_get($Rar, $File);
                                        $Entry->extract($Destination);
                                    }
                                    rar_close($rar_file);
                                }
                    */

                    /* Try to move the file to specific directory. If the uploaded
                    file was moved successfully, print upload success statement */
                    elseif (
                    move_uploaded_file($temporaryName, $destinationFullPath)
                    )
                    {
                        $freeBytes -= $fileToUploadBytes;
                        echo "<meta http-equiv = 'refresh' content = '0; " . $returnURL  . "'>";
                    }

                    /* Else, if the file was unsuccessfully moved, print move upload
                    error statement */
                    else
                    {
                        echo "There was an error filing your uploaded file.";
                        returnButton($queryArray);
                    }
                }
                else {
                    echo "File is larger than your remaining available space.";
                    returnButton($queryArray);
                }
            }
            elseif ($error == UPLOAD_ERR_FORM_SIZE)
                {
                    echo "The uploaded file exceeds the MAX_FILE_SIZE directive" .
                    "that was specified in the HTML form.";
                    returnButton($returnURL);
                }
            elseif ($error == UPLOAD_ERR_PARTIAL)
                {
                    echo "The uploaded file was only partially uploaded.";
                    returnButton($returnURL);
                }
            elseif ($error == UPLOAD_ERR_NO_FILE)
                {
                    echo "The file upload was unsuccessful because no file was" .
                    "uploaded.";
                    returnButton($returnURL);
                }
            elseif ($error == UPLOAD_ERR_NO_TMP_DIR)
                {
                    echo "The file upload was unsuccessful because there was no" .
                    "temporary folder to use.";
                    returnButton($returnURL);
                }
            elseif ($error == UPLOAD_ERR_CANT_WRITE)
                {
                    echo "The file upload was unsuccessful because there was a" .
                    "failure in writing to the disk.";
                    returnButton($returnURL);
                }
            elseif ($error == UPLOAD_ERR_EXTENSION)
                {
                    echo "The file upload was unsuccessful because a PHP" .
                    "extension stopped the file upload. PHP does not provide a" .
                    "way to ascertain which extension caused the file upload to" .
                    "stop; examining the list of loaded extensions with phpinfo()" .
                    "may help.";
                    returnButton($returnURL);
                }

            /* Else, if there was any other error uploading the file, print
            unknown upload error statement */
            else
            {
                echo "There was an unknown error uploading your file" .
                "<br /><br />";
                returnButton($returnURL);
            }
        }
    }
    else
    {
        print_r($_POST);
        print_r($_FILES);
    }
?>

</body>
</html>
