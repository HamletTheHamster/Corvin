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
$User = array("Lauren", "Biddle");

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

	<style type="text/css">
            #dirTree a {
                text-decoration: none;
                color: #171717;
            }

            #dirTree .File a {
                color: #999999;
            }

            #dirTree .Active > a {
                font-weight: bold;
            }
    </style>
</head>

<body>

<!-- 2 Top Bar -->
<div class = "TopBar">
    <div class = "CorvinCastle">
		<?php
			echo "<a href = '" . strtolower($User[0] . $User[1]) . ".php'><img src = 'Corvin Castle.png' alt = 'Corvin Castle' style = 'width:250px;'/></a>";
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
		echo "<input type = 'hidden' value = '" . $User[0] . "' name = 'user' />";
		echo "<input type = 'hidden' value = '" . $User[1] . "' name = 'userLastName' />";
		?>
		<div id="fileList"></div>
    </form>

    <br />

    <!-- 5.2 Download File -->
    <form action = "download.php" method = "post" enctype = "multipart/form-data">
        Type a file name to download:
        <input type = "text" name = "fileToDownload" />
        <input type = "submit" value = "Download File" name = "submit" />
		<?php
		echo "<input type = 'hidden' value = '" . $User[0] . "' name = 'user' />";
		?>
    </form>

    <br />

    <!-- 5.3 Rename File or Folder -->
    <form action = "rename.php" method = "post" enctype = "multipart/form-data">
        Rename file 
        <input type = "text" name = "oldName" />
        to 
        <input type = "text" name = "newName" />
        <input type = "submit" value = "Confirm Rename" name = "submit" />
		<?php
		echo "<input type = 'hidden' value = '" . $User[0] . "' name = 'user' />";
		echo "<input type = 'hidden' value = '" . $User[1] . "' name = 'userLastName' />";
		?>
    </form>

    <br />

	<!-- 5.4 Delete File or Folder -->
	<!-- [Doesn't truly delete, just sends to the user's hidden "recycle" directory for recovery for some grace period ~ 1 month] -->
	<form action = "recycle.php" method = "post" enctype = "multipart/form-data">
		Type a file to delete:
		<input type = "text" name = "fileToRecycle" />
		<input type = "submit" value = "Confirm Delete" name = "submit" />
		<?php
		echo "<input type = 'hidden' value = '" . $User[0] . "' name = 'userID' />";
		echo "<input type = 'hidden' value = '" . $User[1] . "' name = 'userLastName' />";
		?>
	</form>

	<br /><br />

<!-- DIRECTORY TREE -->
<?php
class DirTree																								/* Defines the class DirTree													*/
{
    protected $User;																						/* root, active, & URL_KEY are properties (variables) of the class DirTree,  	*/
    protected $Active;																						/*  which is only accessible from within the class DirTree or its children		*/																					


    public function __construct($User, $Active = null)														/* Public method (function) of the class DirTree which is automatically called  */
	{																										/*  once an instance of this class is created (and object). The function takes  */
																											/*  the root and active properties as input, and clears the active property of  */
																											/*  any value upon taking it in.												*/
		
        $this->UserRoot = realpath("/home/joel/Castle/" . $User);											/* The filepath to the user's root folder is assigned to root, which is a		*/
																											/*  property from this very class												*/

        if ($Active !== null)																				/* If active is not null														*/
		{
			if (strpos($Active, '.') == false)
			{
				echo "<p id = 'DirectoryPath'>" . $User . '/' . $Active . "</p>";
			} 
			else
			{
				$TotalLength = strlen($Active);
				$Reversed = strrev($Active);
				$ReversedPosition = strpos($Reversed, '/');
				$Display = substr($Active, 0, $TotalLength - $ReversedPosition - 1);
				echo "<p id = 'DirectoryPath'>" . $User . '/' . $Display . "</p>";
			}
            $this->Active = realpath($this->UserRoot . '/' . $Active);										/* Then set active to the root path plus the value of active					*/
        }
    }

    public function isActive($PotentiallyActiveElement)														/* Public method that takes in the value of an element							*/
	{
        return substr($this->Active, 0, 
		strlen($PotentiallyActiveElement->getPathname())) === $PotentiallyActiveElement->getPathname();		/* Returns true if the string contained in active, starting with the first		*/
																											/*  character and the same length as the string for the pathname of the element */
																											/*  which is a built in php method within the built in php class				*/
																											/*  RecursiveDirectoryIterator (I think), although php manual says it's just	*/
																											/*  in Directory Iterator														*/
    }

    public function getLink($ElementToBuildLink)															/* Public method that takes in the value of an element							*/
	{
        return '?' . ltrim(rawurldecode(urlencode(substr($ElementToBuildLink->getPathname(),				/* Returns the url. URL_KEY is the key to the first element of this array,		*/
		strlen($this->UserRoot)))), '/');																	/* which is as far as I can tell unnecessary because it's an array of only one	*/
																											/*  item.. The value of this item is the string of the path of the currently    */
    }

    protected function _get(Iterator $ItemToIterate)														/* Protected method that takes in a subclass of Iterator, which is always going	*/
																											/*  to be a new instance of RecursiveDirectoryIterator in this case, coming		*/
																											/*  in with a specific directory to recursively iterate							*/ 
	{
        $Directories = $Files = array();																	/* Defines result, dirs, and files to be arrays									*/

        foreach ($ItemToIterate as $Item)																	/* For each of the items in it, assigning the value of the current item to		*/
																											/*  entry																		*/
		{
            if ($Item->isDir())																				/* If the current item (which is an object of the RecursiveDirectoryIterator	*/
																											/*  class) is a directory														*/
			{
                $ItemData = (object)array(																	/* Then an array, which is converted to an object, containing three items is	*/
                    'Type' => 'Directory',																	/*  assigned to data. The first item is keyed 'type' and contains the value		*/
                    'Name' => $Item->getBasename(),															/*  'dir'. The second item is keyed 'name' and contains the base name of the	*/
                    'Object' => $Item);																		/*  currently being examined item in the it array, which is stored in entry. 	*/
																											/*  The third item is keyed 'object' and contains the current value of entry	*/
																											
                if ($this->isActive($Item))																	/* If the above function to determine if the item is active (selected) returns	*/
																											/*  true																		*/
				{
                    $ItemData->Children = $this->_get($ItemToIterate->getChildren());						/* Recursively call this _get function, now passing the original directory's	*/
																											/*  children into the function, and assign their values to children, a new  	*/
																											/*  property we are creating within the newly created data object				*/
                    $ItemData->Active = true;																	/* Assign the value true to active, another new property we are creating within */
																											/*  the data object																*/
                }

                $Directories[$Item->getBasename()] = $ItemData;												/* Assign all the contents of data to an index of the dirs array and key this	*/
																											/*  index with the basename of the current item being examinted in the it		*/
																											/*  array, which is stored in entry												*/
            }
            else																							/* Else, if the current item being examined in the it array, which is stored in */
																											/*  entry, is not a directory													*/
			{
                $Files[$Item->getFilename()] = (object)array(												/* Then no recursion is necessary, so an array equivalent to data for the		*/
                    'Type' => 'File',																		/*  folders is created for the current file and stored in an index of the files */
                    'Name' => $Item->getFilename(),															/*  array, and this index is keyed with the name of the file currently being	*/
                    'Object' => $Item,																		/*  examined in the it array, which is stored in entry. The first item is keyed	*/
                    'Active' => $this->isActive($Item));													/*  'type' and contains the value 'file'. The second item is keyed 'name' and	*/
																											/*  contains the name of the file currently being examined in the it array,		*/
																											/*  which is stored in entry. The third item is keyed 'object' and contains the */
																											/*  current value of entry. The fourth item is keyed 'active' and contains the	*/
																											/*  value true if the current file has been selected and is active, or false if */
																											/*  the current file has not been selected and is not active					*/
            }
        }

        uksort($Directories, 'strnatcmp');																	/* Sorts the items in the dirs and files arrays using the sorting function 		*/
        uksort($Files, 'strnatcmp');																		/*  strnatcmp, which orders things the way a human would, grouping clusters of	*/
																											/*  numbers and letters in a string together. E.g., 'img12' would come after	*/
																											/*  'img2', because it is comparing 12 vs 2 instead of 1 vs 2					*/
        return array_values(array_merge($Directories, $Files));												/* Concatenate the files array to the dirs array and return all the values of	*/
																											/*  of this merged array														*/
    }

    public function get()																					/* Public method that accepts no input and simply creates a new instance of the	*/
																											/*  RecursiveDirectoryIterator subclass to be passed into _get. The only reason */
																											/*  this has to exist is because otherwise a new instance would be created on	*/
																											/*  every recursive loop of _get												*/
	{																										
        return $this->_get(new RecursiveDirectoryIterator($this->UserRoot));								/* Return the result returned from calling _get on the specified root path		*/
    }																										

    public function outputUl($DirectoryTree = null)															/* Public method that accepts the variable dirTree with the value of null		*/
	{
        if ($DirectoryTree === null)																		/* If dirTree is in fact empty													*/
		{
            $DirectoryTree = $this->get();																	/* Then call get and assign the results of get to dirTree						*/
        }

        echo '<ul>';																						/* Begin an html unordered (bulleted) list										*/
        foreach ($DirectoryTree as $Element)																/* For each item in dirTree, which are all objects, assigning the value of each	*/
																											/*  to element																	*/
		{
            $CSSClass = array($Element->Type);																/* Define classes to be an array containing just one item, the type of the		*/
																											/*  element, either 'file' or 'dir'												*/
			if ($Element->Name == '.' || $Element->Name == '..')											/* If the element is a '.' or '..'												*/
			{
				continue;																					/* Then immediately continue to the next loop of the foreach loop				*/
			}
            else if ($Element->Type === 'Directory')														/* If the element is of type 'dir'												*/
			{
                if ($Element->Active)																		/* And if the element is also active (item in current element keyed 'active'	*/
																											/*  contains the value true) because it has been selected						*/
				{
                    $CSSClass[] = 'Active';																	/* Then add a new item to the classes array with the value 'active'				*/
                }

                echo '<li class="', implode(' ', $CSSClass), '">';											/* Create a new html list item and assign it a class of the values of the		*/
																											/*  classes array, each item separated by a space, '[dir/file] [active]'		*/
                echo '<a href="', $this->getLink($Element->Object),'">';									/* Create an html link as the list item, with the link value specified by the	*/
																											/*  result of sending the object value into the getLink method above			*/
                echo $Element->Name;																		/* Display the name of the element to be used as the link text					*/
                echo '</a>';																				/* End html link																*/
                if (sizeof($Element->Children) > 0)															/* If the element has any children												*/
				{
                    $this->outputUl($Element->Children);													/* Recursively call outputUl to list the children								*/
                }
                echo '</li>';																				/* End html list item															*/
            }
            else																							/* Else, if the element is of type 'file'										*/
			{
                if ($Element->Active)																		/* And if the element is also active (item in current element keyed 'active'	*/
																											/*  contains the value true) because it has been selected						*/
				{					
                    $CSSClass[] = 'Active';																	/* Then add a new item to the classes array with the value 'active'				*/
                }           

                echo '<li class="', implode(' ', $CSSClass), '">';											/* Create a new html list item and assign it a class of the values of the		*/
																											/*  classes array, each item separated by a space, 'file [active]'				*/
                echo '<a href="', $this->getLink($Element->Object),'">';									/* Create an html link as the list item, with the link value specified by the	*/
																											/*  result of sending the object value into the getLink method above			*/
                echo $Element->Name;																		/* Display the name of the file to be used as the link text						*/
                echo '</a>';																				/* End html link																*/
                echo '</li>';																				/* End html list item															*/
            }
        }

        echo '</ul>';																						/* End html unordered list														*/
    }
}
?>


<div id="dirTree">
<?php
	$DirectoryTree = new DirTree($User[0], urldecode($_SERVER['QUERY_STRING']) ?? null);					/* Create a new instance of the class dirTree, passing two parameters to be		*/
																											/*  used in the __construct method immediately upon creating the object. The	*/
																											/*  first parameter is assigned to root. The second parameter passed first		*/
																											/*  _GETs the current url for the page and checks to see if there is anything	*/
																											/*  assigned to the URL_KEY, which would be anything following the 'el=' in the	*/
																											/*  url. If there is nothing, then it passes the value null. If there is		*/
																											/*  something, however, it passes that value									*/
	$DirectoryTree->outputUl();																				/* From the dirTree object, call the outputUl method							*/
?>
</div>

<br /><br />

<!-- 6 Footer -->
<div class = "footer">&copy; Joel N. Johnson</div>

</body>
</html>