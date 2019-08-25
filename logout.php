<!--
        This is a php file for Corvin which each user's main page calls when the
        user clicks logout, thus ending the user's current session. This happens
        automatically if they close their browser.

        Last updated: January 8, 2019

        Coded by: Joel N. Johnson
-->

<?php

// Start session
session_start();

// Destroy all sessions
if(session_destroy())
{
    // Redirect to index.html
	header("Location: /");
}

?>