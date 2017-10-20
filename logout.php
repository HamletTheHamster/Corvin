<!--
        This is a PHP file for Corvin site which each user's userhub page calls to logout, and thus end the user's current session. This
		happens automatically if they close their browser.


        Last updated: 4-29-2017

        Coded by: Joel N. Johnson
-->

<?php

session_start();																			/* start session								*/

if(session_destroy())																		/* Destroy all sessions							*/
{
	header("Location: login.php");															/* Redirect to login.php						*/
}

?>