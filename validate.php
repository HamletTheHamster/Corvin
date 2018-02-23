<!--
        This is a PHP file for NanoLab that is called by login.php to handle user login validation and rerouting to the correct user hub.

        Variables

			UserDatabase	-	[Username,				Password,				First Name,			Last Name;
								Joel's Username,		Joel's Password,		Joel,				Johnson;
								Lauren's Username,		Lauren's Password,		Lauren,				Biddle]
			Username		-	username that the user typed into the username field
			Password		-	password that the user typed into the password field
			UserMatch		-	variable that is zero until Username matches a username in UserDatabase
			n			-	indexer
			User			-	concatenated firstnamelastname of user
			_SESSION		-	global variable that allows for a user's id to be set to the session that has been started; verifies that the
							user has logged in. Their userhub page will not be accessible and just reroute to the login page unless they
							have logged in with correct username and password and thus their session id has been set.



        Last updated: August 16, 2017

        Coded by: Joel N. Johnson
-->

<?php
	/*include "oneWayFunction.php";*/

	ini_set("display_errors", 1);																			/* Display any errors							*/
	error_reporting(E_ALL);																					/* And be verbose about it						*/

	session_start();

	$UserDatabase = array(
	array("Joel",		"5Emmick5",			"Joel",			"Johnson"		),							/* Assign two dimensional array of user account */
	array("lbiddle",	"Hungry4Apples?",		"Lauren",		"Biddle"		),
	array("Test",		"TTPassword",			"Test",			"Test"			),
	array("Jeff",		"1932Ford",			"Jeff",			"Johnson"		),
	array("Brandon",	"flannEl17",			"Brandon",		"Weigel"		),
	array("Josh",		"TheHairyJ",			"Josh",			"Hensley"		));

	$Username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);								/* Assign submitted username					*/
	$Password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);								/* Assign submitted password					*/
	/*$Password = oneWayFunction($Password);*/																/* Send password through one way function		*/

	$UserMatch = 0;																							/* UserMatch is 0 until a user match is found	*/
	$n = 0;																									/* Indexer										*/
	$TotalUsers = count($UserDatabase);
	while ($UserMatch == 0 && $n <= $TotalUsers - 1)														/* While a match is still not found				*/
	{
		if ($Username == $UserDatabase[$n][0])																/* If the inserted username matches the current	*/
																											/*  username index in UserDatabase				*/
		{
			$UserMatch++;																					/* Then make UserMatch not 0					*/
		}

		else																								/* Else, if username has not matched one in		*/
																											/*  UserDatabase yet							*/
		{
			$n++;																							/* Continue stepping through UserDatabase by 	*/
																											/*  increasing n by one							*/
		}
	}

	if ($n > $TotalUsers - 1)
	{
		echo "Incorrect username.";																			/* Print incorrect username error				*/
		echo "<br /><br /><form method = 'get' action = 'login.php' />";									/* Button to return to login.php				*/
		echo "<input type = 'submit' value = 'Return' />";
		echo "</form>";
	}
	else
	{
		if ($Password == $UserDatabase[$n][1])																/* Now that the user has been identified, if	*/
																											/*  the password they entered matches their		*/
																											/*  password									*/
		{
			$User = strtolower($UserDatabase[$n][2] . $UserDatabase[$n][3]);								/* Assign firstnamelastname to User				*/
			echo "Welcome, " . $UserDatabase[$n][2] . "!";													/* Print brief welcome statement				*/
			$_SESSION["loginUser"] = $User;																	/* Start user's session, logging them in and	*/
																											/*  making their userhub page accessible for	*/
																											/*  them in their browser until they log out	*/
																											/*  or close their browser						*/
			echo "<meta http-equiv='Refresh' content='0; url = " . "Users/" . $User . ".php'>";				/* Redirect to the user's page, located at		*/
																											/*  firstnamelastname.php						*/
		}
		else																								/* Else, if their password is incorrect			*/
		{
			echo "Incorrect password.";																		/* Print incorrect username or password error	*/
			echo "<br /><br /><form method = 'get' action = 'login.php' />";								/* Button to return to login.php				*/
			echo "<input type = 'submit' value = 'Return' />";
			echo "</form>";
		}
	}
?>
