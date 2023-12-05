<?php

/*******w******** 
    
    Name: Patrick Philippot
    Date: 4/18/2023
    Description: A php page that displays multiple posts based on search.

****************/

require('connect.php');

session_start();

$query = "SELECT * FROM users ORDER BY username ASC";
$statement = $db->prepare($query);
$statement->execute();

$users = $statement->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="styles.css" />
    <title>Posts</title>
</head>
<body>
    <?php if (isset($_SESSION['user']['user_level']) && $_SESSION['user']['user_level'] == 'owner'): ?>
        <div>
            <?php if (isset($_SESSION["user"]["username"])): ?>
                <p>Logged in: <?=$_SESSION["user"]["username"]?></p>
            <?php endif ?>
            <?php if (isset($_SESSION["authenticated"]) && !$_SESSION['authenticated']): ?>
                <p class="error">Incorrect login</p>
            <?php endif ?>
            <form id="login" action="authenticate.php">
                <?php if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']): ?>
                    <input type="hidden" name="redirect" value="posts.php">
                    <label for="username">Username:</label>
                    <input id="username" name="username">
                    <label for="password">Password:</label>
                    <input id="pass" name="pass" type="password">
                    <button type="submit">Login</button>
                <?php endif ?>
                <?php if (isset($_SESSION["authenticated"]) && $_SESSION['authenticated']): ?>
                    <a href="authenticate.php?redirect=posts.php&logout=true">Log out</a>
                <?php endif ?>
            </form>
            <header id="contactheader">
            <h1>Users</h1>
            <nav>
                <ul id="headnavlist">
                    <li><a href="index.php">Home Page</a></li>
                    <li id='postsearch'><a href="posts.php">Posts</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <?php if (isset($_SESSION['user']['user_level']) && $_SESSION['user']['user_level'] == 'owner'): ?>
                        <li><a href="users.php">Edit Users</a></li>
                    <?php elseif (isset($_SESSION['authenticated']) && $_SESSION['authenticated']): ?>
                        <li><a href="edit_user.php?user_id=<?=$_SESSION['user']['user_id']?>">Edit user</a></li>
                    <?php else: ?>
                        <li><a href="edit_user.php">Register new user</a></li>
                    <?php endif ?>
                </ul>
            </nav>
            </header>
            <main>
                <?php if(count($users) != 0): ?>
                    <div id="user-list">
                    <h1>List of users</h1>
                    <?php foreach($users as $user): ?>
                        <div class="post">
                            <h2><a href="edit_user.php?user_id=<?=$user['user_id'] ?>"><?= $user['username'] ?></a></h2>
                            <p><?= $user['email_address'] ?></p>
                        </div>
                    <?php endforeach ?>
                    </div>
                <?php endif ?>
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
                            <li><a href="authenticate.php?redirect=posts.php&logout=true">Log out</a></li>
                        <?php endif ?>
                    </ul>
                </nav>
            </footer>
        </div>
    <?php else: ?>
        <p class="error">Please log in to admin level account.</p>
    <?php endif ?>          
</body>
</html>
