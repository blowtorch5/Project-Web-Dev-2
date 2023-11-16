<?php

/*******************

    Name: Patrick Philippot
    Date: 4/18/2023
    Description: A php page that displays contact information to Philippot Farms LTD

********************/

require('connect.php');
session_start();

$query = "SELECT DISTINCT category FROM pages";
$statement = $db->prepare($query);
$statement->execute();

$categories = $statement->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Contact Us</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div>
        <?php if (isset($_SESSION["user"]["username"])): ?>
            <p>Logged in: <?=$_SESSION["user"]["username"]?></p>
        <?php endif ?>
        <?php if (isset($_SESSION["authenticated"]) && !$_SESSION['authenticated']): ?>
            <p class="error">Incorrect login</p>
        <?php endif ?>
        <form id="login" action="authenticate.php">
            <?php if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']): ?>
                <input type="hidden" name="redirect" value="contact.php">
                <label for="username">Username:</label>
                <input id="username" name="username">
                <label for="password">Password:</label>
                <input id="pass" name="pass" type="password">
                <button type="submit">Login</button>
            <?php endif ?>
            <?php if (isset($_SESSION["authenticated"]) && $_SESSION['authenticated']): ?>
                <a href="authenticate.php?redirect=contact.php&logout=true">Log out</a>
            <?php endif ?>
        </form>
        <header id="contactheader">
            <h1>Contact Us</h1>
            <nav>
                <ul id="headnavlist">
                    <li><a href="index.php">Home Page</a></li>
                    <li id='postsearch'><a href="posts.php">Posts</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </nav>
        </header>
        <main id="contactmain">
            <div id="post-options">
            <?php if (isset($_SESSION['user']['level']) && $_SESSION['user']['level'] == 'admin'): ?>
            <h3><a href="edit.php">Create New Post</a></h3>
            <?php endif ?>
            <h2>Search for post</h2>
            <form method="post" action="posts.php">
                <label for="title">Title of Post</label>
                <input type="text" name="title" id="title">
                <label for="category">Category</label>
                <select id="category" name="category">
                    <option value="">Select a Category</option>
                    <?php foreach ($categories as $category): ?>
                        <?php if(!$category['category'] == null): ?>
                            <option value="<?=$category['category']?>"><?=$category['category']?></option>
                        <?php endif ?>
                    <?php endforeach ?>
                </select>
                <button type="submit">Search</button>
            </form>
            </div>
           <div id="contactus">
                <h2>You can contact us by the following</h2>
                <ul> <!-- PLEASE DO NOT CONTACT ANY OF THESE, I PROMISE THESE WORK, THEY ARE MY PARENTS' ACTUAL CONTACT INFO -->
                    <li> 
                        <p>Phone at: (204) 379-2396</p>
                    </li>
                    <li>
                        <p>Email at: philippotdairy@outlook.com</p>
                    </li>
                    <li>
                        <p>Mail at: P.O. Box 550, Saint-Claude, MB, R0G 1Z0</p>
                    </li>
                </ul> 
           </div>
        </main>
        <footer id="contactfooter">
            <nav>
                <ul>
                    <li><a href="index.php">Home Page</a></li>
                    <li><a href="posts.php">Posts</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <?php if(!isset($_SESSION['authenticated'])): ?>
                        <li><a href="#login">Sign In</a></li>
                    <?php else: ?>
                        <li><a href="authenticate.php?redirect=contact.php&logout=true">Log out</a></li>
                    <?php endif ?>
                </ul>
            </nav>
        </footer>        
    </div>
</body>
</html>