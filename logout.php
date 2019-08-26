<!--
This is a php file for Corvin which is called when the user clicks logout, thus
ending the user's current session.

Coded by: Joel N. Johnson
-->

<?php
// Start session
session_start();

// Destroy all sessions
if(session_destroy()) {
  // Redirect to index.html
	header("Location: /");
}
?>
