<!--
		This is an external PHP function for Corvin site that takes a value in bytes and converts that value 
		to the appropriate prefix unit of MB, GB, etc. It then returns that information in a statement in the
		following format:

			765.65 MB

		Variables

			Bytes	-	function input; value in bytes
			Type	-	array of prefixes
			i		-	indexer
	
	
		Last updated: April 21, 2017

		Coded by: Joel N. Johnson    
--> 

<?php

function HumanSize($Bytes)																	/* HumanSize accepts a value in	bytes as input	*/
{
	$Type = array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");						/* Assign array containing prefix abbreviations	*/
    $i = 0;																					/* Initialize indexer							*/

    while($Bytes >= 1024)																	/* While the value is still more than should be	*/
																							/*  reported with the current prefix			*/
    {
        $Bytes /= 1024;																		/* Then divide the value by 1024				*/
        $i++;																				/* And increase the indexer by to indicate the	*/
																							/*  value has gone to the next prefix			*/
    }

    return("" . sprintf("%1.2f", $Bytes) . " " . $Type[$i]);								/* return the value properly reported			*/
}

?>