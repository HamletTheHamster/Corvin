<!--
        This is a PHP file for NanoLab that is called by index.php to handle files being downloaded from NanoLab.

        Variables

			DownloadDirectory	-	string containing path of file to be downloaded
			FileToDownload		-	information about file to be downloaded, structured as

										filter_input(type of input, name of variable to get, FILTER_SANITIZE_STRING:
													 filter that removes tags and removes or encodes special
													 characters from a string)

			FullPath			-	full path of file to be downloaded including file name and type


        Last updated: August 16, 2017

        Coded by: Joel N. Johnson
-->

<?php
	ini_set("display_errors", 1);																			/* Display any errors							*/
	error_reporting(E_ALL);																					/* And be verbose about it						*/

	$User = filter_input(INPUT_POST, "User", FILTER_SANITIZE_STRING);
	$UserLastName = filter_input(INPUT_POST, "UserLastName", FILTER_SANITIZE_STRING);
	$CurrentPathString = filter_input(INPUT_POST, "CurrentPathString", FILTER_SANITIZE_STRING);

	$DownloadDirectory = "/../../../mnt/Raid1Array/Corvin/" . $User . $UserLastName . "/" . $CurrentPathString;/* Assign path of directory holding file		*/

	$FileToDownload = filter_input(INPUT_POST, "FileToDownload", FILTER_SANITIZE_STRING);					/* Assign name of file from input				*/
	$FullPath = $DownloadDirectory . $FileToDownload;														/* Concatonate path and file name				*/
	$RealPath = realpath($FullPath);
	$ZipFileName = $DownloadDirectory . urlencode(basename($FileToDownload)) . ".zip";

	if (is_readable($FullPath))																				/* If the path is readable						*/
	{

		if (is_dir($FullPath))
		{

			// Initialize archive object
			$zip = new ZipArchive();
			$zip->open($ZipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);

			// Create recursive directory iterator
			/* @var SplFileInfo[] $files */
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($RealPath), RecursiveIteratorIterator::LEAVES_ONLY);

			foreach ($files as $name => $file)
			{
				// Skip directories (they would be added automatically)
				if (!$file->isDir())
				{
					// Get real and relative path for current file
					$filePath = $file->getRealPath();
					echo "filePath: " . $filePath . "<br>";
					$relativePath = substr($filePath, strlen($RealPath) + 1);
					echo "relativePath: " . $relativePath . "<br>";

					// Add current file to archive
					$zip->addFile($filePath, $relativePath);
				}
			}

			// Zip archive will be created only after closing object
			$zip->close();

			header("Content-Description: File Transfer");													/* State headers								*/
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename = '" . basename($ZipFileName) . "'");
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($ZipFileName));
			ob_clean();
			flush();
			readfile($ZipFileName);																			/* Force download of zip folder					*/

			unlink($ZipFileName);
			exit;
		}
		else
		{
			header("Content-Description: File Transfer");													/* State headers								*/
			header("Content-Type: application/octet-stream");
			header('Content-Disposition: attachment; filename = "' . basename($FullPath) . '"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($FullPath));
			ob_clean();
			flush();
			readfile($FullPath);																			/* Force download of file						*/
			exit;
		}
	}

	else																									/* Else, if the path is not readable			*/
	{
    	echo "Download failed because the file does not exist in the current directory.";					/* Print fail statement							*/
	}
?>
