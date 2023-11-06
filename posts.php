<?php

/*******w******** 
    
    Name: Patrick Philippot
    Date: 4/18/2023
    Description: A php page that displays multiple posts based on search.

****************/

require('connect.php');

session_start();

if (isset($_POST['title'])) {

    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $query = "SELECT * FROM pages WHERE title LIKE :title ORDER BY time_stamp DESC";
    $statement = $db->prepare($query);
    $statement->bindValue(':title', '%' . $title . '%');
    $statement->execute();

    $posts = $statement->fetchAll();
}
else
{
    $query = "SELECT * FROM pages ORDER BY time_stamp DESC";
    $statement = $db->prepare($query);
    $statement->execute();

    $posts = $statement->fetchAll();
}

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
    <div>
    <header id="contactheader">
            <h1>Posts</h1>
            <nav>
                <ul id="headnavlist">
                    <li><a href="index.php">Home Page</a></li>
                    <li id='postsearch'><a href="posts.php">Posts</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </nav>
        </header>
        <main>
            <div>
                <?php if(isset($_SESSION['authenticated']) && $_SESSION['authenticated']): ?>
                <h3><a href="edit.php">Create New Post</a></h3>
                <?php endif ?>
                <h2>Search for post</h2>
                <form method="post" action="posts.php">
                    <label>Title of Post</label>
                    <input type="text" name="title" id="title">
                </form>
            </div>
            <?php if(count($posts) != 0): ?>
            <div>
            <?php foreach($posts as $post): ?>
                <h2><a href=post.php?id=<?=$post['id'] ?>><?= $post['title'] ?></a></h2>
                <?php if(isset($_SESSION['authenticated']) && $_SESSION['authenticated']): ?>
                <a href=edit.php?id=<?=$post['id']?>>Edit Post</a>
                <?php endif ?>
                <p><?=date("M d, Y ", strtotime($post['time_stamp']))?></p>
                <?php if (isset($post['header'])): ?>
                <p><?= $post['header'] ?></p>
                <?php endif ?>
                <p><?= substr($post['body'], 0, 200) ?></p>
                <?php if (isset($post['footer'])): ?>
                <p><?= $post['footer'] ?></p>
                <?php endif ?>
                <p><a href=post.php?id=<?=$post['id']?>>Read Full Post</a></p>
            <?php endforeach ?>
            </div>
            <?php endif ?>
        </main>
        <footer id="indexfooter">
            <nav>
                <ul>
                    <li><a href="index.php">Home Page</a></li>
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
