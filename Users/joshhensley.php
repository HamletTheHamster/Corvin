 <!--
		This is the user's main page for NanoLab.

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

		Last updated: December 23, 2017

		Coded by: Joel N. Johnson
 -->

<!-- 0 Check If Logged In -->
<?php
	session_start();
	$User = array("Josh", "Hensley");

	if ($_SESSION["loginUser"] !== strtolower($User[0] . $User[1]))
	{
		header("Location: ../login.php");
	}

	// Session Timeout after 15 Minutes
	if (isset($_SESSION['LastActivity']) && (time() - $_SESSION['LastActivity'] > 854))		// If last request was more than 30 minutes ago 1800
	{
    		session_unset();									// Then unset $_SESSION variable for the run-time
    		session_destroy();									// and destroy session data in storage
		header("Location: ../login.php");							// and kick the user back to the login screan
	}

	$_SESSION['LastActivity'] = time();								// Update last activity time stamp

	// Regenerate Session ID every 20 Minutes
	if (!isset($_SESSION['Created']))								// If session started timestamp is not set
	{
    	$_SESSION['Created'] = time();									// Then set the session start time to now
	}
	else if (time() - $_SESSION['Created'] > 1200)							// If session started more than 30 minutes ago
	{
    	session_regenerate_id(true);									// Then change session ID for the current session
													//  and invalidate old session ID
    	$_SESSION['Created'] = time();									// Update creation time
	}
?>

<!DOCTYPE html>
<html>

<!-- 1 Header -->
<head>

    <title>Corvin</title>

    <link href = "../index.css" type = "text/css" rel = "stylesheet" />

	<link rel="apple-touch-icon" sizes="57x57" href="../Art/Favicon/apple-icon-57x57.png" />
	<link rel="apple-touch-icon" sizes="60x60" href="../Art/Favicon/apple-icon-60x60.png" />
	<link rel="apple-touch-icon" sizes="72x72" href="../Art/Favicon/apple-icon-72x72.png" />
	<link rel="apple-touch-icon" sizes="76x76" href="../Art/Favicon/apple-icon-76x76.png" />
	<link rel="apple-touch-icon" sizes="114x114" href="../Art/Favicon/apple-icon-114x114.png" />
	<link rel="apple-touch-icon" sizes="120x120" href="../Art/Favicon/apple-icon-120x120.png" />
	<link rel="apple-touch-icon" sizes="144x144" href="../Art/Favicon/apple-icon-144x144.png" />
	<link rel="apple-touch-icon" sizes="152x152" href="../Art/Favicon/apple-icon-152x152.png" />
	<link rel="apple-touch-icon" sizes="180x180" href="../Art/Favicon/apple-icon-180x180.png" />
	<link rel="icon" type="image/png" sizes="192x192" href="../Art/Favicon/android-icon-192x192.png" />
	<link rel="icon" type="image/png" sizes="32x32" href="../Art/Favicon/favicon-32x32.png" />
	<link rel="icon" type="image/png" sizes="96x96" href="../Art/Favicon/favicon-96x96.png" />
	<link rel="icon" type="image/png" sizes="16x16" href="../Art/Favicon/favicon-16x16.png" />
	<link rel="manifest" href="/manifest.json" />

	<meta name="msapplication-TileColor" content="#ffffff" />
	<meta name="msapplication-TileImage" content="/ms-icon-144x144.png" />
	<meta name="theme-color" content="#ffffff" />

	<meta http-equiv="refresh" content="855" />

</head>

<body>

<!-- 2 Top Bar -->
<div class = "TopBar">
    <div class = "Corvin">
		<?php
	   		echo "<a href = '" . strtolower($User[0] . $User[1]) . ".php'>";
			/*echo "<img src = '../Images/Nano Lab.png' alt = 'NanoLab' style = 'width:250px;'/>";*/
			echo "<h class = 'CorvinHeader'>Corvin</h>";
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
		include "../humanSize.php";

        	$FreeBytes = disk_free_space("../../../../mnt/Raid1Array/Corvin/" . $User[0] . $User[1]);
        	$TotalBytes = disk_total_space("../../../../mnt/Raid1Array/Corvin/" . $User[0] . $User[1]);

	        echo "<p class = \"DiskSpace\">" . HumanSize($FreeBytes) . " free of " . HumanSize($TotalBytes) . "</p>";
    	?>

</div>

<br />

<!-- 4 Logout -->
<div id = "Logout">
	<form action = "../logout.php" method = "post" enctype = "multipart/form-data">
	<input type = "submit" class = "LogoutButton" value = "Logout" name = "submit" />
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

    <form action = "../upload.php" method = "post" enctype = "multipart/form-data">
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

	<!-- 5.5 Create New Folder -->
	<form action = "../newFolder.php" method = "post" enctype = "multipart/form-data">
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
    <div class = "DirectoryPath">
    <?php
		include "../generateURL.php";

		echo "<a class = 'DirectoryPath' href = '" . strtolower($User[0] . $User[1]) . ".php'><p class = 'DirectoryPath'>" . $User[0] . " </p></a>";

		parse_str($_SERVER['QUERY_STRING'], $CurrentPath);

		foreach ($CurrentPath as $Key => $Value)
		{
			if (!is_int($Key))
			{
				unset($CurrentPath[$Key]);
			}
		}

		$DirectoryPath = array("");
		$i = 0;
		foreach ($CurrentPath as $Key => $DirectoryPathFolder)
		{
			if ($DirectoryPathFolder != "")
			{
				$DirectoryPathFolderURL = generateURL($User, $DirectoryPath, $DirectoryPathFolder);
				array_push($DirectoryPath, $DirectoryPathFolder);
				$i = ++$i;
				echo "<p class = 'DirectoryPath'> / </p>" . "<a class = 'DirectoryPath' href = '" . $DirectoryPathFolderURL . "'>" . "<p class = 'DirectoryPath'>" . $DirectoryPathFolder . "</p>" . "</a>";
			}
		}
		echo "<br /><br />";
    ?>
    </div>

    <br /><br />

	<!-- 5.7 Files in directory -->
    <div id = "Directory">
            <?php
		$ReturnPathString = filter_input(INPUT_POST, "ReturnPathString", FILTER_SANITIZE_STRING);

               	if ($ReturnPathString == null)
		{
			$DirectoryPath = "../../../../mnt/Raid1Array/Corvin/" . $User[0] . $User[1] . "/" . implode("/", $CurrentPath);
           	}
		else
		{
			$DirectoryPath = "../../../../mnt/Raid1Array/Corvin/" . $User[0] . $User[1] . "/" . $ReturnPathString;
		}

		$Directory = scandir($DirectoryPath);
		usort($Directory, "strnatcmp");
           	$NumItems = count($Directory);

		/* 5.6.1 List Folders and Folder Sizes */
		include "../folderSize.php";

                for ($i = 2; $i < $NumItems; $i++)
                {
                    if (is_dir($DirectoryPath . "/" . $Directory[$i]))
                    {

			echo "
				<script>
					function copyTextToClipboard(text)
					{
						var textArea = document.createElement('textarea');

						/* To copy a string to the clipboard without a visible text input box, you must flash create a phantom text box and then immediately destroy it.*/
						/* The following lines just style the phantom text box to have minimal visual impact in case the page hangs and the user does actually see this */
						/* phantom text box.																*/
						textArea.style.position = 'fixed';
						textArea.style.top = '0';
						textArea.style.left = '0';
						textArea.style.width = '2em';
						textArea.style.height = '2em';
						textArea.style.padding = 0;
						textArea.style.border = 'none';
						textArea.style.outline = 'none';
						textArea.style.boxShadow = 'none';
						textArea.style.background = 'transparent';

						textArea.value = text;

						document.body.appendChild(textArea);

						textArea.select();

						try
						{
							var successful = document.execCommand('copy');
							var msg = successful ? 'successful' : 'unsuccessful';
							console.log('Copying text command was ' + msg);
						}
						catch (err)
						{
							console.log('Oops, unable to copy');
						}

						document.body.removeChild(textArea);
					}

					var copyText = document.querySelector('.js-copy-text');

					copyText.addEventListener('click', function(event) {copyTextToClipboard('" . $Directory[$i] . "');});
				</script>
			";

                        echo "<div id = 'FileNames'>";
				echo "<div class = 'Folders'>";
				$URL = generateURL($User, $CurrentPath, $Directory[$i]);
	                        echo "<a href = '" . $URL . "'>" . $Directory[$i] . "</a>";
				echo "</div>";

/*				echo "<button type = 'button' class = 'CopyTextButton' onclick = 'copyTextToClipboard(\"" . $Directory[$i] . "\")'>Copy Text</button>";*/ /* Copy to Clipboard Button */

				/* Download Folder */
					echo "<div class = 'DownloadButtonForm'>";
					echo "
						<form action = '../Zip/download.php' class = 'DownloadButtonForm' method = 'post' enctype = 'multipart/form-data'>
							<input type = 'hidden' value = '" . $Directory[$i] . "' name = 'FileToDownload' />
							<input type = 'image' src = '../Art/2 - Download Arrow Icon/NanoLab Download Arrow Icon @ 36 ppi.png' class = 'DownloadButton' value = 'Download' name = 'submit' />
							<input type = 'hidden' value = '" . $User[0] . "' name = 'User' />
							<input type = 'hidden' value = '" . $User[1] . "' name = 'UserLastName' />
							<input type = 'hidden' value = '" . $CurrentPathString . "' name = 'CurrentPathString' />
						</form>
					";
					echo "</div>";

				/* Rename Folder */
					echo "<div class = 'RenameButtonForm'>";

					echo "	<input type = 'image' src = '../Art/4 - Rename Cursor Icon/NanoLab Rename Cursor Icon @ 36 ppi.png' id = '" . preg_replace('/\s+/','', $Directory[$i]) . "CursorButton' class = 'RenameButton' />";
					echo "
						<form action = '../rename.php' class = 'RenameButtonForm' method = 'post' enctype = 'multipart/form-data'>
							<!--<input type = 'image' src = '../Art/4 - Rename Cursor Icon/NanoLab Rename Cursor Icon @ 36 ppi.png' id = '" . preg_replace('/\s+/','', $Directory[$i]) . "CursorButton' class = 'RenameButton' />-->
							<input type = 'hidden' value = '" . $Directory[$i] . "' name = 'oldName' />
							<input type = 'text' id = '" . preg_replace('/\s+/','', $Directory[$i]) . "RenameTextField' class = 'RenameTextField' value = '" . $Directory[$i] . "' size = '" . strlen($Directory[$i]) . "' name = 'newName' />
							<input type = 'submit' id = '" . preg_replace('/\s+/','', $Directory[$i]) . "RenameSubmitButton' class = 'RenameSubmitButton' value = 'Confirm Rename' name = 'submit' />
							<input type = 'hidden' value = '" . $User[0] . "' name = 'User' />
							<input type = 'hidden' value = '" . $User[1] . "' name = 'UserLastName' />
							<input type = 'hidden' value = '" . $CurrentPathString . "' name = 'CurrentPathString' />
						</form>
					";

					echo "<script>

						var cursorButton = (typeof cursorButton != 'undefined' && cursorButton instanceof Array) ? cursorButton : [];
						var renameTextField = (typeof renameTextField != 'undefined' && renameTextField instanceof Array) ? renameTextField : [];
						var renameSubmitButton = (typeof renameSubmitButton != 'undefined' && renameSubmitButton instanceof Array) ? renameSubmitButton : [];
						var i = (typeof i != 'undefined') ? i : 0;

						cursorButton.push(document.getElementById('" . preg_replace('/\s+/','', $Directory[$i]) . "CursorButton'));
						renameTextField.push(document.getElementById('" . preg_replace('/\s+/','', $Directory[$i]) . "RenameTextField'));
						renameSubmitButton.push(document.getElementById('" . preg_replace('/\s+/','', $Directory[$i]) . "RenameSubmitButton'));

						cursorButton[i].addEventListener('click', showTextBox, false);

						function showTextBox()
						{
							document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).style.display = 'block';
							document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).focus();
							document.getElementById(event.target.id.replace('CursorButton', 'RenameSubmitButton')).style.display = 'block';
							document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).addEventListener('focusout', textBoxFocus, false);
						}

						function textBoxFocus()
						{
							document.getElementById(event.target.id).style.display = 'none';
							document.getElementById(event.target.id.replace('RenameTextField', 'RenameSubmitButton')).style.display = 'none';
						}

						i++;

					</script>";


					echo "</div>";

				/* Recycle Folder */
					echo "<div class = 'RecycleButtonForm'>";
					echo "
						<form action = '../recycle.php' class = 'RecycleButtonForm' method = 'post' enctype = 'multipart/form-data'>
							<input type = 'hidden' value = '" . $Directory[$i] . "' name = 'fileToRecycle' />
							<input type = 'image' src = '../Art/3 - Delete Trash Can Icon/NanoLab Delete Trash Can Select @ 36 ppi.png' class = 'RecycleButton' />
							<input type = 'hidden' value = '" . $User[0] . "' name = 'User' />
							<input type = 'hidden' value = '" . $User[1] . "' name = 'UserLastName' />
							<input type = 'hidden' value = '" . $CurrentPathString . "' name = 'CurrentPathString' />
						</form>
					";
					echo "</div>";
			echo "</div>"; /* To Here */

                        echo "<div id = 'FileSizes'>";
			echo "" . humanSize(FolderSize($DirectoryPath . "/" . $Directory[$i]));
                        echo "</div>";
                        echo "<br><div id = 'Heath'><br></div>";
                    }
                }

		/* 5.6.2 List Files and File Sizes */
                for ($i = 2; $i < $NumItems; $i++)
                {
                    if (is_file($DirectoryPath . "/" . $Directory[$i]))
                    {
			$SupportedFileTypes = array("pdf", "txt", "csv", "bmp", "gif", "jpg", "jpeg", "png", "webp", "3gp", "avi", "mov", "mp4", "m4v", "m4a", "mp3", "mkv", "ogv", "ogm", "ogg", "oga", "webm", "wav");

                        echo "<div id = 'FileNames'>";
			echo"<div class = 'Files'>";

			if (in_array(strtolower(substr($Directory[$i], -3)), $SupportedFileTypes) || in_array(strtolower(substr($Directory[$i], -4)), $SupportedFileTypes))
			{

				if (strtolower(substr($Directory[$i], -3)) == "pdf")
				{
					if ($_GET)
					{
                        			echo "<a href = '" . $_SERVER['REQUEST_URI'] . "&pdf=" . rawurlencode($Directory[$i]) . "' target = '_blank'>" . $Directory[$i] . "</a>";
					}
					else
					{
						echo "<a href = '" . $_SERVER['REQUEST_URI'] . "?pdf=" . rawurlencode($Directory[$i]) . "' target = '_blank'>" . $Directory[$i] . "</a>";
					}

					if (isset($_GET['pdf']))
					{
						$pdf = rawurldecode($_REQUEST['pdf']);

						if (copy($DirectoryPath . "/" . $pdf, "../../../../../../var/www/html/ViewInBrowser/pdf.pdf"))
						{
								echo "copy successfull";
								echo "<meta http-equiv = 'refresh' content = '0; url=../ViewInBrowser/pdf.pdf'>";
						}
						else
						{
							echo "copy unsuccessfull";
							echo "<meta http-equiv = 'refresh' content = '2'>";
						}
					}
				}
				else if (strtolower(substr($Directory[$i], -3)) == "txt")
				{
					if ($_GET)
					{
						echo "<a href = '" . $_SERVER['REQUEST_URI'] . "&txt=" . rawurlencode($Directory[$i]) . "' target = '_blank'>" . $Directory[$i] . "</a>";
					}
					else
					{
						echo "<a href = '" .$_SERVER['REQUEST_URI'] . "?txt=" . rawurlencode($Directory[$i]) . "' target = '_blank'>" . $Directory[$i] . "</a>";
					}

					if (isset($_GET['txt']))
					{
						$txt = rawurldecode($_REQUEST['txt']);

						if (copy($DirectoryPath . "/" . $txt, "../../../../../../var/www/html/ViewInBrowser/txt.txt"))
						{
							echo "copy successfull";
							echo "<meta http-equiv = 'refresh' content = '0; url=../ViewInBrowser/txt.txt'>";
						}
						else
						{
							echo "copy unsuccessfull";
							echo "<meta http-equiv = 'refresh' content = '2'>";
						}
					}
				}
				else if (strtolower(substr($Directory[$i], -3)) == "csv")
				{
					if ($_GET)
					{
						echo "<a href = '" . $_SERVER['REQUEST_URI'] . "&csv=" . rawurlencode($Directory[$i]) . "' target = '_blank'>" . $Directory[$i] . "</a>";
					}
					else
					{
						echo "<a href = '" .$_SERVER['REQUEST_URI'] . "?csv=" . rawurlencode($Directory[$i]) . "' target = '_blank'>" . $Directory[$i] . "</a>";
					}

					if (isset($_GET['csv']))
					{
						$csv = rawurldecode($_REQUEST['csv']);

						if (copy($DirectoryPath . "/" . $csv, "../../../../../../var/www/html/ViewInBrowser/csv.txt"))
						{
							echo "copy successfull";
							echo "<meta http-equiv = 'refresh' content = '0; url=../ViewInBrowser/csv.txt'>";
						}
						else
						{
							echo "copy unsuccessfull";
							echo "<meta http-equiv = 'refresh' content = '2'>";
						}
					}
				}
				else if (strtolower(substr($Directory[$i], -3)) == "jpg")
				{
					if ($_GET)
					{
						echo "<a href = '" . $_SERVER['REQUEST_URI'] . "&jpg=" . rawurlencode($Directory[$i]) . "' target = '_blank'>" . $Directory[$i] . "</a>";
					}
					else
					{
						echo "<a href = '" .$_SERVER['REQUEST_URI'] . "?jpg=" . rawurlencode($Directory[$i]) . "' target = '_blank'>" . $Directory[$i] . "</a>";
					}

					if (isset($_GET['jpg']))
					{
						$jpg = rawurldecode($_REQUEST['jpg']);

						if (copy($DirectoryPath . "/" . $jpg, "../../../../../../var/www/html/ViewInBrowser/jpg.jpg"))
						{
							echo "copy successfull";
							echo "<meta http-equiv = 'refresh' content = '0; url=../ViewInBrowser/jpg.jpg'>";
						}
						else
						{
							echo "copy unsuccessfull";
							echo "<meta http-equiv = 'refresh' content = '2'>";
						}
					}
				}
				else if (strtolower(substr($Directory[$i], -3)) == "png")
				{
					if ($_GET)
					{
						echo "<a href = '" . $_SERVER['REQUEST_URI'] . "&png=" . rawurlencode($Directory[$i]) . "' target = '_blank'>" . $Directory[$i] . "</a>";
					}
					else
					{
						echo "<a href = '" .$_SERVER['REQUEST_URI'] . "?png=" . rawurlencode($Directory[$i]) . "' target = '_blank'>" . $Directory[$i] . "</a>";
					}

					if (isset($_GET['png']))
					{
						$png = rawurldecode($_REQUEST['png']);

						if (copy($DirectoryPath . "/" . $png, "../../../../../../var/www/html/ViewInBrowser/png.png"))
						{
							echo "copy successfull";
							echo "<meta http-equiv = 'refresh' content = '0; url=../ViewInBrowser/png.png'>";
						}
						else
						{
							echo "copy unsuccessfull";
							echo "<meta http-equiv = 'refresh' content = '2'>";
						}
					}
				}
				else if (strtolower(substr($Directory[$i], -3)) == "gif")
				{
					if ($_GET)
					{
						echo "<a href = '" . $_SERVER['REQUEST_URI'] . "&gif=" . rawurlencode($Directory[$i]) . "' target = '_blank'>" . $Directory[$i] . "</a>";
					}
					else
					{
						echo "<a href = '" .$_SERVER['REQUEST_URI'] . "?gif=" . rawurlencode($Directory[$i]) . "' target = '_blank'>" . $Directory[$i] . "</a>";
					}

					if (isset($_GET['gif']))
					{
						$gif = rawurldecode($_REQUEST['gif']);

						if (copy($DirectoryPath . "/" . $gif, "../../../../../../var/www/html/ViewInBrowser/gif.gif"))
						{
							echo "copy successfull";
							echo "<meta http-equiv = 'refresh' content = '0; url=../ViewInBrowser/gif.gif'>";
						}
						else
						{
							echo "copy unsuccessfull";
							echo "<meta http-equiv = 'refresh' content = '2'>";
						}
					}
				}
				else if (strtolower(substr($Directory[$i], -4)) == "jpeg")
				{
					if ($_GET)
					{
						echo "<a href = '" . $_SERVER['REQUEST_URI'] . "&jpeg=" . rawurlencode($Directory[$i]) . "' target = '_blank'>" . $Directory[$i] . "</a>";
					}
					else
					{
						echo "<a href = '" .$_SERVER['REQUEST_URI'] . "?jpeg=" . rawurlencode($Directory[$i]) . "' target = '_blank'>" . $Directory[$i] . "</a>";
					}

					if (isset($_GET['jpeg']))
					{
						$jpeg = rawurldecode($_REQUEST['jpeg']);

						if (copy($DirectoryPath . "/" . $jpeg, "../../../../../../var/www/html/ViewInBrowser/jpeg.jpeg"))
						{
							echo "copy successfull";
							echo "<meta http-equiv = 'refresh' content = '0; url=../ViewInBrowser/jpeg.jpeg'>";
						}
						else
						{
							echo "copy unsuccessfull";
							echo "<meta http-equiv = 'refresh' content = '2'>";
						}
					}
				}
				else if (strtolower(substr($Directory[$i], -3)) == "bmp")
				{
					if ($_GET)
					{
						echo "<a href = '" . $_SERVER['REQUEST_URI'] . "&bmp=" . rawurlencode($Directory[$i]) . "' target = '_blank'>" . $Directory[$i] . "</a>";
					}
					else
					{
						echo "<a href = '" .$_SERVER['REQUEST_URI'] . "?bmp=" . rawurlencode($Directory[$i]) . "' target = '_blank'>" . $Directory[$i] . "</a>";
					}

					if (isset($_GET['bmp']))
					{
						$bmp = rawurldecode($_REQUEST['bmp']);

						if (copy($DirectoryPath . "/" . $bmp, "../../../../../../var/www/html/ViewInBrowser/bmp.bmp"))
						{
							echo "copy successfull";
							echo "<meta http-equiv = 'refresh' content = '0; url=../ViewInBrowser/bmp.bmp'>";
						}
						else
						{
							echo "copy unsuccessfull";
							echo "<meta http-equiv = 'refresh' content = '2'>";
						}
					}
				}
				else
				{
					echo "" . $Directory[$i];
				}
			}
			else
			{
				echo "" . $Directory[$i];
			}
			echo "</div>";

/*			echo "<button type = 'button' class = 'CopyTextButton' onclick = 'copyTextToClipboard(\"" . $Directory[$i] . "\")'>Copy Text</button>";*/ /* Copy to Clipboard Button */

			/* Download Folder */
				echo "<div class = 'DownloadButtonForm'>";
				echo "
					<form action = '../Zip/download.php' class = 'DownloadButtonForm' method = 'post' enctype = 'multipart/form-data'>
						<input type = 'hidden' value = '" . $Directory[$i] . "' name = 'FileToDownload' />
						<input type = 'image' src = '../Art/2 - Download Arrow Icon/NanoLab Download Arrow Icon @ 36 ppi.png' class = 'DownloadButton' value = 'Download' name = 'submit' />
						<input type = 'hidden' value = '" . $User[0] . "' name = 'User' />
						<input type = 'hidden' value = '" . $User[1] . "' name = 'UserLastName' />
						<input type = 'hidden' value = '" . $CurrentPathString . "' name = 'CurrentPathString' />
					</form>
				";
				echo "</div>";

			/* Rename Folder */
				echo "<div class = 'RenameButtonForm'>";

				echo "	<input type = 'image' src = '../Art/4 - Rename Cursor Icon/NanoLab Rename Cursor Icon @ 36 ppi.png' id = '" . preg_replace('/\s+/','', $Directory[$i]) . "CursorButton' class = 'RenameButton' />";
				echo "
					<form action = '../rename.php' class = 'RenameButtonForm' method = 'post' enctype = 'multipart/form-data'>
							<!--<input type = 'image' src = '../Art/4 - Rename Cursor Icon/NanoLab Rename Cursor Icon @ 36 ppi.png' id = '" . preg_replace('/\s+/','', $Directory[$i]) . "CursorButton' class = 'RenameButton' />-->
							<input type = 'hidden' value = '" . $Directory[$i] . "' name = 'oldName' />
							<input type = 'text' id = '" . preg_replace('/\s+/','', $Directory[$i]) . "RenameTextField' class = 'RenameTextField' value = '" . $Directory[$i] . "' size = '" . strlen($Directory[$i]) . "' name = 'newName' />
							<input type = 'submit' id = '" . preg_replace('/\s+/','', $Directory[$i]) . "RenameSubmitButton' class = 'RenameSubmitButton' value = 'Confirm Rename' name = 'submit' />
							<input type = 'hidden' value = '" . $User[0] . "' name = 'User' />
							<input type = 'hidden' value = '" . $User[1] . "' name = 'UserLastName' />
							<input type = 'hidden' value = '" . $CurrentPathString . "' name = 'CurrentPathString' />
						</form>
					";

				echo "<script>

					var cursorButton = (typeof cursorButton != 'undefined' && cursorButton instanceof Array) ? cursorButton : [];
					var renameTextField = (typeof renameTextField != 'undefined' && renameTextField instanceof Array) ? renameTextField : [];
					var renameSubmitButton = (typeof renameSubmitButton != 'undefined' && renameSubmitButton instanceof Array) ? renameSubmitButton : [];
					var i = (typeof i != 'undefined') ? i : 0;

					cursorButton.push(document.getElementById('" . preg_replace('/\s+/','', $Directory[$i]) . "CursorButton'));
					renameTextField.push(document.getElementById('" . preg_replace('/\s+/','', $Directory[$i]) . "RenameTextField'));
					renameSubmitButton.push(document.getElementById('" . preg_replace('/\s+/','', $Directory[$i]) . "RenameSubmitButton'));

					cursorButton[i].addEventListener('click', showTextBox, false);

					function showTextBox()
					{
						document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).style.display = 'block';
						document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).focus();
						document.getElementById(event.target.id.replace('CursorButton', 'RenameSubmitButton')).style.display = 'block';
						document.getElementById(event.target.id.replace('CursorButton', 'RenameTextField')).addEventListener('focusout', textBoxFocus, false);
					}

					function textBoxFocus()
					{
						document.getElementById(event.target.id).style.display = 'none';
						document.getElementById(event.target.id.replace('RenameTextField', 'RenameSubmitButton')).style.display = 'none';
					}

					i++;

				</script>";

				echo "</div>";

			/* Recycle Folder */
				echo "<div class = 'RecycleButtonForm'>";
				echo "
					<form action = '../recycle.php' class = 'RecycleButtonForm' method = 'post' enctype = 'multipart/form-data'>
						<input type = 'hidden' value = '" . $Directory[$i] . "' name = 'fileToRecycle' />
						<input type = 'image' src = '../Art/3 - Delete Trash Can Icon/NanoLab Delete Trash Can Select @ 36 ppi.png' class = 'RecycleButton' />
						<input type = 'hidden' value = '" . $User[0] . "' name = 'User' />
						<input type = 'hidden' value = '" . $User[1] . "' name = 'UserLastName' />
						<input type = 'hidden' value = '" . $CurrentPathString . "' name = 'CurrentPathString' />
					</form>
				";
				echo "</div>"; /* To Here */

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
