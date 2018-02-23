<!--
		This is a PHP file for NanoLab which each user's userhub page calls to handle files being uploaded to Corvin. It can handle multiple
		files being uploaded at once.

		Hierarchy

			0	Session Expiration
			1	Header
			2	Upload Function

		Variables

			_FILES		-	multi-dimensional array holding the information on each file being uploaded. The optional information is structured as

								$_FILES['filesToUpload']['name']	-	the original name of the file on the client machine
								$_FILES['userfile']['type']			-	the mime type of the file, if the browser provided this information.
																		An example would be "image/gif". This mime type is however not checked on
																		the PHP side and therefore don't take its value for granted
								$_FILES['userfile']['size']			-	the size, in bytes, of the uploaded file
								$_FILES['userfile']['tmp_name']		-	the temporary filename of the file in which the uploaded file was stored
																		on the server
								$_FILES['userfile']['error']		-	the error code associated with this file upload

			error		-	returns value of UPLOAD_ERR_OK (0) if there isn't an error
			temp_name	-	the temporary file name of the file in which the uploaded file was stored on the server
			key			-	index value of current loop through the foreach loop
			name		-	name of the file without the full path before it. To output the filename without the file type extension, do

								$name = basename($_FILES["filesToUpload"]["name"][$key], $suffix);

							where $suffix is the file type extension, such as .pdf



		Last updated: August 16, 2017

		Coded by: Joel N. Johnson
-->

<!-- 0 Session Expiration -->
<?php

	ini_set("display_errors", 1);																				/* Display any errors									*/
	error_reporting(E_ALL);																						/* And be verbose about it								*/

	session_start();

	$User = filter_input(INPUT_POST, "User", FILTER_SANITIZE_STRING);
	$UserLastName = filter_input(INPUT_POST, "UserLastName", FILTER_SANITIZE_STRING);
	$CurrentPathString = filter_input(INPUT_POST, "CurrentPathString", FILTER_SANITIZE_STRING);

	$UserPage = "Users/" . strtolower($User . $UserLastName) . ".php";

	// Session Timeout after 15 Minutes
	if (isset($_SESSION['LastActivity']) && (time() - $_SESSION['LastActivity'] > 894))							/* If last request was more than 30 minutes ago 1800	*/
	{
		header("Location: login.php");																			/* and kick the user back to the login screan			*/
	}

	$_SESSION['LastActivity'] = time();																			/* Update last activity time stamp						*/

	// Regenerate Session ID every 20 Minutes
	if (!isset($_SESSION['Created']))																			/* If session started timestamp is not set				*/
	{
    	$_SESSION['Created'] = time();																			/* Then set the session start time to now				*/
	}
	else if (time() - $_SESSION['Created'] > 1200)																/* If session started more than 30 minutes ago			*/
	{
    	session_regenerate_id(true);																			/* Then change session ID for the current session		*/
																												/*  and invalidate old session ID						*/
    	$_SESSION['Created'] = time();																			/* Update creation time									*/
	}
?>

<!DOCTYPE html>
<html>

<!-- 1 Header -->
<head>
    <title>Nano Lab</title>

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

	<meta http-equiv="refresh" content="284" />
</head>

<body>

    <!-- 2 Upload Function -->
<?php
	/*
		[This outputs the maximum uploadable file size. To change, edit php.ini file.]
		echo ini_get('upload_max_filesize') . '<br />' . ini_get('post_max_size') . '<br />';
	*/

	ini_set('display_errors', 1);																				/* Display any errors								*/
	error_reporting(E_ALL);																						/* And be verbose about it							*/

	$Destination = "../../../../mnt/Raid1Array/Corvin/" . $User . $UserLastName . "/" . $CurrentPathString;

	if (isset($_FILES["filesToUpload"]))
	{
	foreach ($_FILES["filesToUpload"]["error"] as $Key => $Error)												/* For each item in _FILES, with the current		*/
	{																											/*  loop's index value referencing the error		*/
																												/*  dimension of _FILES								*/
		if ($Error == UPLOAD_ERR_OK)																			/* If there are no errors							*/
		{
			$TemporaryName = $_FILES["filesToUpload"]["tmp_name"][$Key];										/* Assign the temporary file name given by			*/
																												/*  the server when it stored it in memory			*/
																												/*  to the variable tmp_name						*/
			$Name = str_replace("'", "", basename($_FILES["filesToUpload"]["name"][$Key]));						/* Assign the file name without full path			*/
																												/*  to the variable name							*/
			$DestinationFullPath = $Destination . $Name;														/* Assign the full destination path and name		*/

			$Zip = new ZipArchive;
			if (substr($Name, -4) != "docx" && substr($Name, -4) != "xlsx" && substr($Name, -4) != "pptx" && $Zip->open($TemporaryName) === TRUE)
			{
				$Zip->extractTo($Destination);
				$Zip->close();
				echo "" . $Name . " has been uploaded and extracted.<br /><br />";
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
			else if (move_uploaded_file($TemporaryName, $DestinationFullPath))									/* Move the file to specific directory.				*/
																												/*  [This will need to be changed to a dynamic		*/
																												/*  path once I code the ability to navigate		*/
					   																							/*	around  directories]							*/
																												/*  If the uploaded file was successfully 			*/
																												/*  filed away										*/
			{
					echo "" . $Name . " has been uploaded.<br /><br />";										/* Print success statement							*/
			}
			else																								/* Else, if the file was unsuccessfully filed		*/
																												/*  away											*/
			{
				echo "There was an error filing your uploaded file.";											/* Print filing error statement						*/
			}
		}
		else if ($Error == UPLOAD_ERR_INI_SIZE)
			{
				echo "The uploaded file exceeds the upload_max_filesize directive in php.ini.";
			}
		else if ($Error == UPLOAD_ERR_FORM_SIZE)
			{
				echo "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the 
						HTML form.";
			}
		else if ($Error == UPLOAD_ERR_PARTIAL)
			{
				echo "The uploaded file was only partially uploaded.";
			}
		else if ($Error == UPLOAD_ERR_NO_FILE)
			{
				echo "The file upload was unsuccessful because no file was uploaded.";
			}
		else if ($Error == UPLOAD_ERR_NO_TMP_DIR)
			{
				echo "The file upload was unsuccessful because there was no temporary folder to use.";
			}
		else if ($Error == UPLOAD_ERR_CANT_WRITE)
			{
				echo "The file upload was unsuccessful because there was a failure in writing to the disk.";
			}
		else if ($Error == UPLOAD_ERR_EXTENSION)
			{
				echo "The file upload was unsuccessful because a PHP extension stopped the file upload.
						PHP does not provide a way to ascertain which extension caused the file upload
						to stop; examining the list of loaded extensions with phpinfo() may help.";
			}
		else																									/* Else, if there was an error uploading the		*/
																												/*  file											*/
		{
			echo "There was an unknown error uploading your file<br /><br />";									/* Print uploading error statement					*/
		}
	}
	}
	else
	{
		print_r($_POST);
		print_r($_FILES);
	}

	$QueryArray = explode("/", substr($CurrentPathString, 0, -1));
	echo "<form method = 'get' action = '" . $UserPage . "' />";												/* Button to return to previous page				*/
	echo "<input type = 'submit' value = 'Return' />";
	foreach ($QueryArray as $Key => $QueryParameter)
	{
		echo "<input type = 'hidden' value = '" . $QueryParameter . "' name = '" . $Key . "' />";
	}
	echo "</form>";
?>

</body>
</html>
