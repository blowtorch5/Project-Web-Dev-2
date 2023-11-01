<?php

 /*******************

    Name: Patrick Philippot
    Date: 4/18/2022
    Description: A php page that deletes an entry in a database

********************/

	require("connect.php");
	require("authenticate.php");

    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $query = "DELETE FROM pages WHERE id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue('id', $id);        

    $statement->execute();

    header("Location: index.php");
?>