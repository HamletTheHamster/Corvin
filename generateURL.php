<!--
        This is an external PHP function for Corvin site that builds the full
        url for the next page which the user is being directed to.

        Variables

            currentPath -   string containing the current path, basically
                            everything after the ternary operator in the current
                            url
            item        -   string containing the name of the folder or item
                            that the user clicked on to take them to another
                            page

        Last updated: August 6, 2019

        Coded by: Joel N. Johnson
-->

<?php

function generateURL($base, $currentPath, $item)
{
    /* Append the name of the folder that was clicked on to the end of the
    current path string */
	array_push($currentPath, $item);

    /* Convert the current path string array into an http query string with each
    item prefaced by its array key value plus an "=" and separated by an "&",
    each space in each item replaced by a "+", and each special character in
    each item replaced by its ascii analog */
	$query = http_build_query($currentPath);

    /* Append the basic url with the query string separated by the ternary
    operator, "?" */
	$fullURL = $base . $query;

    // Return the fully built url
    return($fullURL);
}

?>
