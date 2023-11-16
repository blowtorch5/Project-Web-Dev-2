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
        <?php if (isset($_SESSION["user"]["username"])): ?>
            <p>Logged in: <?=$_SESSION["user"]["username"]?></p>
        <?php endif ?>
        <?php if (isset($_SESSION["authenticated"]) && !$_SESSION['authenticated']): ?>
            <p class="error">Incorrect login</p>
        <?php endif ?>
        <form id="login" action="authenticate.php?redirect=post.php">
            <?php if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']): ?>
                <input type="hidden" name="redirect" value="post.php?id=<?=$post['id']?>">
                <label for="username">Username:</label>
                <input id="username" name="username">
                <label for="password">Password:</label>
                <input id="pass" name="pass" type="password">
                <button type="submit">Login</button>
            <?php endif ?>
            <?php if (isset($_SESSION["authenticated"]) && $_SESSION['authenticated']): ?>
                <a href="authenticate.php?redirect=post.php?id=<?=$post['id']?>&logout=true">Log out</a>
            <?php endif ?>
        </form>
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
        <main>
            <div id="post-options">
                <?php if (isset($_SESSION['user']['level']) && $_SESSION['user']['level'] == 'admin'): ?>
                <h3><a href="edit.php">Create New Post</a></h3>
                <?php endif ?>
                <h2>Search for post</h2>
                <form method="post" action="posts.php">
                    <label>Title of Post</label>
                    <input type="text" name="title" id="title">
                </form>
            </div>
            <div id="postdiv" class="post">
                <h2><?= $post['title'] ?></h2>
                <p><?= date("M d, Y", strtotime($post['time_stamp'])) ?></p>
                <?php if (isset($post['header'])): ?>
                <p><?= $post['header'] ?></p>
                <?php endif ?>
                <p><?= $post['body'] ?></p>
                <?php if (isset($post['footer'])): ?>
                <p><?= $post['footer'] ?></p>
                <?php endif ?>
                <?php if (isset($_SESSION['user']['level']) && $_SESSION['user']['level'] == 'admin'): ?>
                <p><a href="edit.php?id=<?=$post['id']?>">Edit Post</a></p>
                <?php endif ?>
            </div>
        </main>
        <footer id="indexfooter">
            <nav>
                <ul>
                    <li><a href="index.php">Home Page</a></li>
                    <li><a href="posts.php">Posts</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <?php if(!isset($_SESSION['authenticated'])): ?>
                        <li><a href="#login">Sign In</a></li>
                    <?php else: ?>
                        <li><a href="authenticate.php?redirect=post.php?id=<?=$post['id']?>&logout=true">Log out</a></li>
                    <?php endif ?>
                </ul>
            </nav>
        </footer>
    </div>
</body>
</html>