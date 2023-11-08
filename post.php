<?php

/*******w******** 
    
    Name: Patrick Philippot
    Date: 4/18/2023
    Description: A php page that displays a single post and its respective options.

****************/

require('connect.php');

session_start();

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);;

$query = "SELECT * FROM pages WHERE id = :id";
$statement = $db->prepare($query);
$statement->bindValue(':id', $id);
$statement->execute();

$post = $statement->fetch();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>My Blog Post!</title>
</head>
<body>
    <div>
        <header>
        <h1>Philippot Farms LTD</h1>
        <nav>
            <ul id="headnavlist">
                <li><a href="index.php">Home Page</a></li>
                <li id='postsearch'><a href="posts.php">Posts</a></li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </nav>
        </header>
        <div id="postdiv">
            <h2><?= $post['title'] ?></h2>
            <p><?= date("M d, Y", strtotime($post['time_stamp'])) ?></p>
            <?php if (isset($post['header'])): ?>
            <p><?= $post['header'] ?></p>
            <?php endif ?>
            <p><?= $post['body'] ?></p>
            <?php if (isset($post['footer'])): ?>
            <p><?= $post['footer'] ?></p>
            <?php endif ?>
            <?php if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']): ?>
            <p><a href="edit.php?id=<?=$post['id']?>">Edit Post</a></p>
            <?php endif ?>
        </div>
        <footer id="indexfooter">
            <nav>
                <ul>
                    <li><a href="index.php">Home Page</a></li>
                    <li><a href="posts.php">Posts</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <?php if(!isset($_SESSION['authenticated'])): ?>
                        <li><a href="authenticate.php?redirect=index.php">Sign In</a></li>
                    <?php endif ?>
                </ul>
            </nav>
        </footer>
    </div>
</body>
</html>