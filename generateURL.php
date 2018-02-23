<!--
		This is an external PHP function for Corvin site that takes in 

		Variables




		Last updated: August 16, 2017

		Coded by: Joel N. Johnson
-->

<?php

function generateURL($User, $CurrentPath, $Item)																	/*												*/
{
	$URL = strtolower($User[0] . $User[1]) . ".php";
/*
	if ($PersonalOrShared == "Shared")
	{
		array_push($CurrentPathShared, $Item);
	}
	else
	{
		array_push($CurrentPathPersonal, $Item);
	}
*/

	array_push($CurrentPath, $Item);
/*
	$PersonalAndShared = array(	"P" => $CurrentPathPersonal,
								"S" => $CurrentPathShared);
*/
	$Query = http_build_query($CurrentPath);

	$FullURL = $URL . "?" . $Query;

    return($FullURL);
}

?>
