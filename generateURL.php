<!--
		This is an external PHP function for Corvin site that takes in 

		Variables

			
	
	
		Last updated: June 5, 2017

		Coded by: Joel N. Johnson    
--> 

<?php

function generateURL($User, $CurrentPath, $Item)																	/*												*/
{
	$URL = strtolower($User[0] . $User[1]) . ".php";
	array_push($CurrentPath, $Item);
	$Query = http_build_query($CurrentPath);

	$FullURL = $URL . "?" . $Query;

    return($FullURL);								
}

?>