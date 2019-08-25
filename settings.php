<!-- 0 Check If Logged In -->
<?php

    session_start();

    //Check if user is logged in
    if (!isset($_SESSION["loginUser"]) && $_SESSION["loginUser"] != TRUE) {
        header("Location: login.php");
    }

    // Display any errors
    ini_set("display_errors", 1);

    // And be verbose about it
    error_reporting(E_ALL);

    //MYSQLi server connection
    $conn = mysqli_connect("127.0.0.1", "joel", "Daytona675");

    //Check if connected to MYSQLI server
    if (!$conn) {
        echo("Failed to connect to database: " .
                mysqli_connect_error()) . "<br /><br />";
    }

    //Go into Corvin database
    mysqli_query($conn, "USE Corvin;");

    //Assign user's ID, set in validate.php
    $userID = $_SESSION["userID"];

    $sql = "SELECT firstName, lastName FROM UserInfo WHERE id = '$userID'";
    $user = mysqli_fetch_array(mysqli_query($conn, $sql));

    // Session Timeout after 854 seconds (14.2 minutes)

    // If last request was more than 854 seconds ago (14.2 minutes)
    if (
        isset($_SESSION['LastActivity']) &&
        (time() - $_SESSION['LastActivity'] > 854)
    )
    {
        // Then unset $_SESSION variable for the run-time
        session_unset();

        // Destroy session data in storage
        session_destroy();

        // And kick the user back to the login screen
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

    <title>Settings | Corvin</title>

    <link
            href = "index.css"
            type = "text/css"
            rel = "stylesheet"
    />

    <link
            rel = "apple-touch-icon"
            sizes = "57x57"
            href = "Art/Favicon/apple-icon-57x57.png"
    />
    <link
            rel = "apple-touch-icon"
            sizes = "60x60"
            href = "Art/Favicon/apple-icon-60x60.png"
    />
    <link
            rel = "apple-touch-icon"
            sizes = "72x72"
            href = "Art/Favicon/apple-icon-72x72.png"
    />
    <link
            rel = "apple-touch-icon"
            sizes = "76x76"
            href = "Art/Favicon/apple-icon-76x76.png"
    />
    <link
            rel = "apple-touch-icon"
            sizes = "114x114"
            href = "Art/Favicon/apple-icon-114x114.png"
    />
    <link
            rel = "apple-touch-icon"
            sizes = "120x120"
            href = "Art/Favicon/apple-icon-120x120.png"
    />
    <link
            rel = "apple-touch-icon"
            sizes = "144x144"
            href = "Art/Favicon/apple-icon-144x144.png"
    />
    <link
            rel = "apple-touch-icon"
            sizes = "152x152"
            href = "Art/Favicon/apple-icon-152x152.png"
    />
    <link
            rel = "apple-touch-icon"
            sizes = "180x180"
            href = "Art/Favicon/apple-icon-180x180.png"
    />
    <link
            rel = "icon"
            type = "image/png"
            sizes = "192x192"
            href = "Art/Favicon/android-icon-192x192.png"
    />
    <link
            rel = "icon"
            type = "image/png"
            sizes = "32x32"
            href = "Art/Favicon/favicon-32x32.png"
    />
    <link
            rel = "icon"
            type = "image/png"
            sizes = "96x96"
            href = "Art/Favicon/favicon-96x96.png"
    />
    <link
            rel = "icon"
            type = "image/png"
            sizes = "16x16"
            href = "Art/Favicon/favicon-16x16.png"
    />
    <link
            rel = "manifest"
            href = "/manifest.json"
    />

    <meta
            name = "msapplication-TileColor"
            content = "#ffffff"
    />
    <meta
            name = "msapplication-TileImage"
            content = "/ms-icon-144x144.png"
    />
    <meta
            name = "theme-color"
            content = "#ffffff"
    />

    <meta
            http-equiv = "refresh"
            content = "855"
    />

    <meta
            name = "google"
            content = "notranslate"
    />

</head>

<body>
<div class = "Wrapper">

    <!-- 2 Top Bar -->
    <div class = "TopBar">
        <div class = "Corvin">
            <?php
                echo "<a href = 'home.php'>" .
                    "<h class = 'CorvinHeader'>C</h>" .
                    "</a>
            ";
            ?>
        </div>
        <div class = "AccountMenuDropDown">
            <p onclick = "accountDropDownMenu()" class = "AccountButton">Account</p>
            <div id = "AccountMenuContent" class = "AccountMenuContent">
                <div class = "TopAccountMenuContent">

                    <?php
                        echo "<p id = 'AccountMenuName'>" . $user[0] . " " . $user[1] . "</p>";

                        include "humanSize.php";
                        include "folderSize.php";

                        $usedBytes = folderSize("../../../../mnt/Raid1Array/Corvin/" .
                            $userID . " - " .
                            $user[0] .
                            $user[1]);

                        $sql = "SELECT storageSpaceInMegabytes FROM UserInfo WHERE id = '" . $userID . "'";
                        $storageSpaceInMegabytes = mysqli_fetch_row(mysqli_query($conn, $sql));

                        if ($storageSpaceInMegabytes[0] == "-1") {
                            $totalBytes = disk_total_space(
                                "../../../../mnt/Raid1Array/Corvin");
                            $freeBytes = disk_free_space(
                                "../../../../mnt/Raid1Array/Corvin");

                            echo "<p id = 'DiskSpace'>" .
                                humanSize($usedBytes) .  " used of " .
                                humanSize($freeBytes) . " (Unlimited)</p>
                        ";
                        }
                        else {
                            $totalBytes = $storageSpaceInMegabytes[0] * 1000000;
                            $freeBytes = $totalBytes - $usedBytes;

                            echo "<p class = 'DiskSpace'>" .
                                humanSize($usedBytes) .  " used of " .
                                humanSize($totalBytes) . "</p>
                        ";
                        }
                    ?>

                </div><!--TopAccountMenuContent-->
                <div class = "MenuLine">
                    <hr class = "MenuLine"/>
                </div>
                <div class = "BottomAccountMenuContent">
                    <a class = "GetMoreSpaceMenuItem" href = "getMoreSpace.php">Get More Space</a>
                    <a class = "MenuItem" href = "home.php">Home</a>
                    <a class = "MenuItem" href = "help.php">Help</a>
                    <a class = "MenuItem" href = "logout.php">Log Out</a>
                </div>
            </div><!--AccountMenuContent-->
        </div><!--AccountMenuDropDown-->
    </div><!--TopBar-->

    <script>
        function accountDropDownMenu() {
            document.getElementById("AccountMenuContent").classList.toggle("Show");
        }
        /*
            window.onclick = function(event) {
                if (!document.getElementById("AccountMenuContent").contains(event.target)) {
                    if (document.getElementById("AccountMenuContent").classList.contains("show") {
                        document.getElementById("AccountMenuContent").classList.remove("show");
                    }
                }
            }
        */
    </script>

    <!-- 5 Main Content -->
    <div class = "MainContent">

            <form
                action = "getMoreSpace.php"
                method = "post"
                enctype = "multipart/form-data"
            >
                <input
                    type = "submit"
                    class = "GetMoreSpaceButton"
                    value = "Get More Space"
                    name = "submit"
                />
            </form>

        <div class = "SettingsHeader">
            <p class = "SettingsHeader">Settings</p>
        </div>

        <div class = "SettingsContent">
            <?php

            echo "<div class = 'NameAndEmail'>";
                echo "<div class = 'NameAndEditName'>";
                    echo "<div class = 'Name'>";
                        echo "<div class = 'Container'>";
                            echo "<div class = 'VerticalCenter'>";
                                echo "<p class = 'AccountInfo'>" . $user[0] . " " . $user[1] . "</p>";
                        echo "</div>";
                            echo "</div>";
                    echo "</div>";
                    echo "<div class = 'EditName'>";
                        echo "<div class = 'Container'>";
                            echo "<div class = 'VerticalCenter'>";
                                echo "<a class = 'AccountInfoEdit'>Edit</a>";
                            echo "</div>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";

                echo "<div class = 'EmailAndEditEmail'>";
                    echo "<div class = 'Email'>";
                        echo "<div class = 'Container'>";
                            echo "<div class = 'VerticalCenter'>";
                                $sql = "SELECT email FROM UserInfo WHERE id = '" . $userID . "'";
                                $email = mysqli_fetch_row(mysqli_query($conn, $sql));
                                echo "<p class = 'AccountInfo'>" . $email[0] . "</p>";
                            echo "</div>";
                        echo "</div>";
                    echo "</div>";
                    echo "<div class = 'EditEmail'>";
                        echo "<div class = 'Container'>";
                            echo "<div class = 'VerticalCenter'>";
                                echo "<a class = 'AccountInfoEdit'>Update</a>";
                            echo "</div>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";

            echo "<div class = 'UsernameAndPassword'>";
                echo "<div class = 'UsernameAndEditUsername'>";
                    echo "<div class = 'Username'>";
                        echo "<div class = 'Container'>";
                            echo "<div class = 'VerticalCenter'>";
                                $sql = "SELECT username FROM UserInfo WHERE id = '" . $userID . "'";
                                $username = mysqli_fetch_row(mysqli_query($conn, $sql));
                                echo "<p class = 'AccountInfo'>" . $username[0] . "</p>";
                            echo "</div>";
                        echo "</div>";
                    echo "</div>";
                    echo "<div class = 'EditUsername'>";
                        echo "<div class = 'Container'>";
                            echo "<div class = 'VerticalCenter'>";
                                echo "<a class = 'AccountInfoEdit'>Change Username</a>";
                            echo "</div>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";

                echo "<div class = 'ChangePassword'>";
                    echo "<div class = 'Container'>";
                        echo "<div class = 'VerticalCenter'>";
                            echo "<a id = 'AccountInfoEdit'>Change Password</a>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";

            ?>
        </div>
    </div>


    <div class = "Push"></div>
</div> <!-- Wrapper -->

<!-- 6 Footer -->
<div class = "Footer">&copy; Corvin, Inc.</div>

</body>
</html>
