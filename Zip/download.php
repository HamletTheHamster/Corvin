<!--
        This is a PHP file for NanoLab that is called by index.php to handle
        files being downloaded from NanoLab.

        Variables

			DownloadDirectory	-	string containing path of file to be
			                        downloaded
			FileToDownload		-	information about file to be downloaded,
			                        structured as

									    filter_input(
									        type of input,
									        name of variable to get,
									        FILTER_SANITIZE_STRING:
										        filter that removes tags and
										        removes or encodes special
										        characters from a string
										)

			FullPath			-	full path of file to be downloaded
			                        including file name and type



        Last updated: August 6, 2019

        Coded by: Joel N. Johnson
-->

<?php
    //Display any errors
	ini_set("display_errors", 1);

	//And be verbose about it
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

	$CurrentPathString = filter_input(
	        INPUT_POST,
            "currentPathString",
            FILTER_SANITIZE_STRING
    );

	//If this request is coming from the user's recycle bin
    // recycleBin = '0 - Recycle', else recycleBin = ''
	$recycleBin = $_POST["recycleBin"];

	//Assign path of directory holding file
	$DownloadDirectory = "/../../../mnt/Raid1Array/Corvin/" . $recycleBin . "/" .
        $userID . " - " .
        $user[0] .
        $user[1] . "/" .
        $CurrentPathString;

	//Assign name of file from input
	$FileToDownload = filter_input(
	        INPUT_POST,
            "fileToDownload",
            FILTER_SANITIZE_STRING
    );

	//Concatonate path and file name
	$FullPath = $DownloadDirectory . $FileToDownload;
	$RealPath = realpath($FullPath);
	$ZipFileName = $DownloadDirectory .
        urlencode(basename($FileToDownload)) . ".zip";

	//If the path is readable
	if (is_readable($FullPath))
	{

		if (is_dir($FullPath))
		{

			// Initialize archive object
			$zip = new ZipArchive();
			$zip->open(
			        $ZipFileName,
                    ZipArchive::CREATE |
                    ZipArchive::OVERWRITE
            );

			// Create recursive directory iterator
			$files = new RecursiveIteratorIterator(
			        new RecursiveDirectoryIterator($RealPath),
                    RecursiveIteratorIterator::LEAVES_ONLY
            );

			foreach ($files as $name => $file)
			{
				// Skip directories (they would be added automatically)
				if (!$file->isDir())
				{
					// Get real and relative path for current file
					$filePath = $file->getRealPath();
					echo "filePath: " . $filePath . "<br>";
					$relativePath = substr(
					        $filePath,
                            strlen($RealPath) + 1
                    );
					echo "relativePath: " . $relativePath . "<br>";

					// Add current file to archive
					$zip->addFile($filePath, $relativePath);
				}
			}

			// Zip archive will be created only after closing object
			$zip->close();

			//State headers
			header("Content-Description: File Transfer");
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename = '" .
                basename($ZipFileName) . "'"
            );
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, ' .
                'post-check=0, pre-check=0'
            );
			header('Pragma: public');
			header('Content-Length: ' . filesize($ZipFileName));
			ob_clean();
			flush();

			//Force download of zip folder
			readfile($ZipFileName);

			unlink($ZipFileName);
			exit;
		}
		else
		{
		    //State headers
			header("Content-Description: File Transfer");
			header("Content-Type: application/octet-stream");
			header('Content-Disposition: attachment; filename = "' .
                basename($FullPath) . '"'
            );
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, ' .
                'post-check=0, pre-check=0'
            );
			header('Pragma: public');
			header('Content-Length: ' . filesize($FullPath));
			ob_clean();
			flush();

			//Force download of file
			readfile($FullPath);
			exit;
		}
	}

	//Else, if the path is not readable
	else
	{
	    //Print fail statement
    	echo "Download failed because the file " .
            "does not exist in the current directory.";
	}
?>
