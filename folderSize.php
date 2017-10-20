<!--
        This is an external PHP function for Corvin site that recursively finds the size of all the contents of a folder.

        Variables

            Directory		-	folder of which we want to know the size
			DirectoryArray	-	array containing all the immediate contents of a folder
			Key				-	a foreach variable that assigns each element of an array to a specified variable for each
								loop
			Filename		-	the specified variable that Key is assigning each element of the DirectoryArray to; the
								name of the file or folder currently being handled by the foreach loop
			NewFolderSize	-	the size to add to the running total size of the top level directory we really care about
			FolderSize		-	the running total size of the top level directory we really care about


        Last updated: April 29, 2017

        Coded by: Joel N. Johnson
-->

<?php

function FolderSize($Directory)																/* FolderSize accepts the path of a directory	*/
																							/*  as input									*/
{
	$FolderSize = 0;																		/* Initiate FolderSize to be zero at the start	*/
	$DirectoryArray = scandir($Directory);													/* Assign the contents of folder to				*/
																							/*  DirectoryArray								*/
	foreach ($DirectoryArray as $Key => $Filename)											/* For each item in the folder, assign the name	*/
																							/*  of the item to Filename						*/
	{
		if ($Filename != ".." && $Filename != ".")											/* If the item is not unix language for two		*/
																							/*  or one directories up						*/
		{
			if (is_dir($Directory . "/" . $Filename))										/* If the item is a folder itself				*/
			{
				$NewFolderSize = folderSize($Directory . "/" . $Filename);					/* Then recursively send that folder through	*/
																							/*  this very function, starting at the top 	*/
																							/*  and acting recursively through any folders	*/
																							/*  it contains within it, and assign the size	*/
																							/*  to NewFolderSize							*/
				$FolderSize = $FolderSize + $NewFolderSize;									/* Increase the running total size of the top	*/
																							/*  level folder we really care about by the	*/
																							/*  amount just found							*/
			}

			else if (is_file($Directory . "/" . $Filename))									/* Else, if the item is not a folder itself		*/
			{
				$FolderSize = $FolderSize + filesize($Directory . "/" . $Filename);			/* Then find its size and increase the running	*/
																							/*  total size of the top level folder we		*/
																							/*  really care about by that amount			*/
			}
		}
	}

	return $FolderSize;																		/* Return the folder's size, in bytes			*/
}

?>