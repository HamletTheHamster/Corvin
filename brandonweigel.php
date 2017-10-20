 <!--
		This is the main page for Corvin site.

		Hierarchy

			0	Check If Logged In
			1	Header
			2	Top Bar
			3	Space on Drive
			4	Logout
			5	Main Content
				5.1		Upload File [OR FOLDER]
				5.2		Download File [OR FOLDER]
				5.3		Rename File or Folder
				5.4		Delete File or Folder
				5.5		Current Directory
				5.6		Files in Directory
					5.5.1	List Folders and Folder Sizes
					5.5.2	List Files and File Sizes
			6	Footer
	 
		Last updated: April 28, 2017

		Coded by: Joel N. Johnson
 -->

<!-- 0 Check If Logged In -->
<?php
session_start();
$User = array("Brandon", "Weigel");

if ($_SESSION["loginUser"] !== strtolower($User[0] . $User[1]))
{
	header("Location: login.php");
}

// Session Timeout after 15 Minutes
if (isset($_SESSION['LastActivity']) && (time() - $_SESSION['LastActivity'] > 854))		// If last request was more than 30 minutes ago 1800
{
    session_unset();																	// Then unset $_SESSION variable for the run-time
    session_destroy();																	// and destroy session data in storage
	header("Location: login.php");														// and kick the user back to the login screan
}

$_SESSION['LastActivity'] = time();														// Update last activity time stamp

// Regenerate Session ID every 20 Minutes
if (!isset($_SESSION['Created']))														// If session started timestamp is not set
{
    $_SESSION['Created'] = time();														// Then set the session start time to now
}
else if (time() - $_SESSION['Created'] > 1200)											// If session started more than 30 minutes ago
{
    session_regenerate_id(true);														// Then change session ID for the current session 
																						//  and invalidate old session ID
    $_SESSION['Created'] = time();														// Update creation time
}
?>

<!DOCTYPE html>
<html>

<!-- 1 Header -->
<head>
    <title>Corvin Castle</title>

    <link href = "index.css" type = "text/css" rel = "stylesheet" />
	<link rel="apple-touch-icon" sizes="57x57" href="/Images/Favicon/apple-icon-57x57.png" />
	<link rel="apple-touch-icon" sizes="60x60" href="/Images/Favicon/apple-icon-60x60.png" />
	<link rel="apple-touch-icon" sizes="72x72" href="/Images/Favicon/apple-icon-72x72.png" />
	<link rel="apple-touch-icon" sizes="76x76" href="/Images/Favicon/apple-icon-76x76.png" />
	<link rel="apple-touch-icon" sizes="114x114" href="/Images/Favicon/apple-icon-114x114.png" />
	<link rel="apple-touch-icon" sizes="120x120" href="/Images/Favicon/apple-icon-120x120.png" />
	<link rel="apple-touch-icon" sizes="144x144" href="/Images/Favicon/apple-icon-144x144.png" />
	<link rel="apple-touch-icon" sizes="152x152" href="/Images/Favicon/apple-icon-152x152.png" />
	<link rel="apple-touch-icon" sizes="180x180" href="/Images/Favicon/apple-icon-180x180.png" />
	<link rel="icon" type="image/png" sizes="192x192" href="/Images/Favicon/android-icon-192x192.png" />
	<link rel="icon" type="image/png" sizes="32x32" href="/Images/Favicon/favicon-32x32.png" />
	<link rel="icon" type="image/png" sizes="96x96" href="/Images/Favicon/favicon-96x96.png" />
	<link rel="icon" type="image/png" sizes="16x16" href="/Images/Favicon/favicon-16x16.png" />
	<link rel="manifest" href="/manifest.json" />
	<meta name="msapplication-TileColor" content="#ffffff" />
	<meta name="msapplication-TileImage" content="/ms-icon-144x144.png" />
	<meta name="theme-color" content="#ffffff" />

	<meta http-equiv="refresh" content="855" />
</head>

<body>

<!-- 2 Top Bar -->
<div class = "TopBar">
    <div class = "CorvinCastle">
	<?php
        echo "<a href = '" . strtolower($User[0] . $User[1]) . ".php'>";
		echo "<img src = 'Corvin Castle.png' alt = 'Corvin Castle' style = 'width:250px;'/>";
		echo "</a>";
    ?>
	</div>
    <div class = "User">
		<?php
		echo "<p class = 'User'>" . $User[0] . "</p>";
		?>
    </div>
</div>

<br /><br />

<!-- 3 Space on drive -->
<div class = "DiskSpace">

	<?php

		include "humanSize.php";

        $FreeBytes = disk_free_space("/home/joel/Castle");
        $TotalBytes = disk_total_space("/home/joel/Castle");

        echo "<p class = \"DiskSpace\">" . HumanSize($FreeBytes) . " free of " . HumanSize($TotalBytes) . "</p>";

    ?>

</div>

<br />

<!-- 4 Logout -->
<div id = "Logout">
	<form action = "logout.php" method = "post" enctype = "multipart/form-data">
		<input type = "submit" value = "Logout" name = "submit" />
	</form>
</div>

<br /><br /><br />

<!-- 5 Main Content -->
<div id = "MainContent">

    <!-- 5.1 Upload File -->
	<script>

    	function updateList()
    	{
    		var input = document.getElementById('filesToUpload');
    		var output = document.getElementById('fileList');

    		if (input.files.length > 1)
    		{
    			output.innerHTML += '<ul>';

    			for (var i = 0; i < input.files.length; ++i)
    			{
    				output.innerHTML += '<li style = "list-style-type: none; padding-left: 2%;">' + (i + 1) + ". " + input.files.item(i).name + '</li>';
    			}

    			output.innerHTML += '</ul>';
    		}
    	}

	</script>

    <form action = "upload.php" method = "post" enctype = "multipart/form-data">
        Select files to upload:
		<input type = "file" name = "filesToUpload[]" id = "filesToUpload" multiple = "multiple" onchange = "javascript:updateList()" />
        <input type = "submit" value = "Upload Files" name = "submit" />
		<?php
		echo "<input type = 'hidden' value = '" . $User[0] . "' name = 'User' />";
		echo "<input type = 'hidden' value = '" . $User[1] . "' name = 'UserLastName' />";
		parse_str($_SERVER['QUERY_STRING'], $CurrentPath);
		$CurrentPathString = implode("/", $CurrentPath) . "/";
		echo "<input type = 'hidden' value = '" . $CurrentPathString . "' name = 'CurrentPathString' />";
		?>
		<div id="fileList"></div>
    </form>

    <br />

    <!-- 5.2 Download File -->
    <form action = "zip/download.php" method = "post" enctype = "multipart/form-data">
        Type a file or folder to download:
        <input type = "text" name = "FileToDownload" />
        <input type = "submit" value = "Download File" name = "submit" />
		<?php
		echo "<input type = 'hidden' value = '" . $User[0] . "' name = 'User' />";
		echo "<input type = 'hidden' value = '" . $CurrentPathString . "' name = 'CurrentPathString' />";
		?>
    </form>

    <br />

    <!-- 5.3 Rename File or Folder -->
    <form action = "rename.php" method = "post" enctype = "multipart/form-data">
        Rename file or folder
        <input type = "text" name = "oldName" />
        to 
        <input type = "text" name = "newName" />
        <input type = "submit" value = "Confirm Rename" name = "submit" />
		<?php
		echo "<input type = 'hidden' value = '" . $User[0] . "' name = 'User' />";
		echo "<input type = 'hidden' value = '" . $User[1] . "' name = 'UserLastName' />";
		echo "<input type = 'hidden' value = '" . $CurrentPathString . "' name = 'CurrentPathString' />";
		?>
    </form>

    <br />

	<!-- 5.4 Delete File or Folder -->
	<!-- [Doesn't truly delete, just sends to the user's hidden "recycle" directory for recovery for some grace period ~ 1 month] -->
	<form action = "recycle.php" method = "post" enctype = "multipart/form-data">
		Type a file or folder to delete:
		<input type = "text" name = "fileToRecycle" />
		<input type = "submit" value = "Confirm Delete" name = "submit" />
		<?php
			echo "<input type = 'hidden' value = '" . $User[0] . "' name = 'User' />";
			echo "<input type = 'hidden' value = '" . $User[1] . "' name = 'UserLastName' />";
			echo "<input type = 'hidden' value = '" . $CurrentPathString . "' name = 'CurrentPathString' />";
		?>
	</form>

	<br />

	<!-- 5.5 Create New Folder -->
	<form action = "newFolder.php" method = "post" enctype = "multipart/form-data">
		Create a new folder named:
		<input type = "text" name = "FolderName" />
		<input type = "submit" value = "Create Folder" name = "submit" />
		<?php
			echo "<input type = 'hidden' value = '" . $User[0] . "' name = 'User' />";
			echo "<input type = 'hidden' value = '" . $User[1] . "' name = 'UserLastName' />";
			echo "<input type = 'hidden' value = '" . $CurrentPathString . "' name = 'CurrentPathString' />";
		?>
	</form>

	<br /><br />

	<!-- 5.6 Current directory -->
    <div id = "DirectoryPath">
    <?php
		echo "<a id = 'DirectoryPath' href = '" . strtolower($User[0] . $User[1]) . ".php'><p id = 'DirectoryPath'>" . $User[0] . " </p></a>";
		
		parse_str($_SERVER['QUERY_STRING'], $CurrentPath);

		/*$i = 0;
		foreach ($CurrentPath as $DirectoryPathFolder)
		{
			echo "<p id = 'DirectoryPath'> / </p><p id = 'DirectoryPath'>Blah</p>";
			$DirectoryPathFolderURL = generateURL($User, $DirectoryPathFolder, null);
			echo "blahblah";
			$i = ++$i;
			echo "/ <a id = 'DirectoryPath' href = '" . $DirectoryPathFolderURL . "'><p id = 'DirectoryPath'>" . $DirectoryPathFolder . "</p></a>";
		}*/
    ?>
    </div>

    <br /><br />

	<!-- 5.7 Files in directory -->
    <div id = "Directory">

            <?php
				$ReturnPathString = filter_input(INPUT_POST, "ReturnPathString", FILTER_SANITIZE_STRING);

                if ($ReturnPathString == null)
				{
					$DirectoryPath = "/home/joel/Castle/" . $User[0] . "/" . implode("/", $CurrentPath) . "/";
                }
				else
				{
					$DirectoryPath = "/home/joel/Castle/" . $User[0] . "/" . $ReturnPathString . "/";
				}

				$Directory = scandir($DirectoryPath);
				usort($Directory, "strnatcmp");
                $NumItems = count($Directory);

				/* 5.6.1 List Folders and Folder Sizes */
				include "folderSize.php";
				include "generateURL.php";

                for ($i = 2; $i < $NumItems; $i++)
                {
                    if(is_dir($DirectoryPath . "/" . $Directory[$i]))
                    {
                        echo "<div id = 'FileNames'>";
						$URL = generateURL($User, $CurrentPath, $Directory[$i]);
                        echo "<a href = '" . $URL . "'>" . $Directory[$i] . "</a>";
                        echo "</div>";

                        echo "<div id = 'FileSizes'>";
						echo "" . humanSize(FolderSize($DirectoryPath . "/" . $Directory[$i]));
                        echo "</div>";
                        echo "<br><div id = 'Heath'><br></div>";
                    }
                }

				/* 5.6.2 List Files and File Sizes */
                for ($i = 2; $i < $NumItems; $i++)
                {
                    if(is_file($DirectoryPath . "/" . $Directory[$i]))
                    {
                        echo "<div id = 'FileNames'>";
                        echo $Directory[$i];
                        echo "</div>";

                        echo "<div id = 'FileSizes'>";
                        $FileSize = filesize($DirectoryPath . "/" . $Directory[$i]);
                        echo "" . HumanSize($FileSize);
						echo "</div>";

                        echo "<br><div id = 'Heath'><br></div>";
                    }
                }

            ?>

        </div>

    </div>

<br /><br />

<!-- 6 Footer -->
<div class = "footer">&copy; Joel N. Johnson</div>

</body>
</html>