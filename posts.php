<?php

/*******w******** 
    
    Name: Patrick Philippot
    Date: 4/18/2023
    Description: A php page that displays multiple posts based on search.

****************/

require('connect.php');

session_start();

if (isset($_POST['title']) && !$_POST['title'] == '') {

    if (isset($_POST['category']) && !$_POST['category'] == '') {
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $category = filter_input(INPUT_POST,'category', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $query = "SELECT * FROM pages WHERE title LIKE :title AND category_id = :category ORDER BY time_stamp DESC";
        $statement = $db->prepare($query);
        $statement->bindValue(':title', '%' . $title . '%');
        $statement->bindValue(':category', $category);
        $statement->execute();

        $posts = $statement->fetchAll();
    } else {
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $query = "SELECT * FROM pages WHERE title LIKE :title ORDER BY time_stamp DESC";
        $statement = $db->prepare($query);
        $statement->bindValue(':title', '%' . $title . '%');
        $statement->execute();

        $posts = $statement->fetchAll();
    }
} elseif (isset($_POST['category']) && !$_POST['category'] == ''){
    $category = filter_input(INPUT_POST,'category', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $query = "SELECT * FROM pages WHERE category_id = :category ORDER BY time_stamp DESC";
    $statement = $db->prepare($query);
    $statement->bindValue(':category', $category);
    $statement->execute();

    $posts = $statement->fetchAll();
} else {
    $query = "SELECT * FROM pages ORDER BY time_stamp DESC";
    $statement = $db->prepare($query);
    $statement->execute();

    $posts = $statement->fetchAll();
}

$query = "SELECT * FROM categories";
$statement = $db->prepare($query);
$statement->execute();

$categories = $statement->fetchAll();

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
            <h1>Posts</h1>
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
            <div id="post-options">
                <?php if (isset($_SESSION['user']['user_level']) && ($_SESSION['user']['user_level'] == 'admin' || $_SESSION['user']['user_level'] == 'owner')): ?>
                    <h2><a href="edit.php">Create New Post</a></h2>
                <?php endif ?>
                <h2>Search for post</h2>
                <form method="post" action="posts.php">
                    <label for="title">Title of Post</label>
                    <input type="text" name="title" id="title">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <option value="">All</option>
                        <?php foreach ($categories as $category): ?>
                            <?php if(!$category['category'] == null): ?>
                                <option value="<?=$category['category_id']?>"><?=$category['category']?></option>
                            <?php endif ?>
                        <?php endforeach ?>
                    </select>
                    <button type="submit">Search</button>
                </form>
            </div>
            <?php if(count($posts) != 0): ?>
                <div id="post-list">
                    <h1>List of posts</h1>
                    <?php foreach($posts as $post): ?>
                        <div class="post">
                            <h2><a href="post.php?id=<?=$post['id']?>&title=<?=$post['slug']?>"><?= $post['title'] ?></a></h2>
                            <?php if (isset($_SESSION['user']['user_level']) && ($_SESSION['user']['user_level'] == 'admin' || $_SESSION['user']['user_level'] == 'owner')): ?>
                                <a href="edit.php?id=<?=$post['id']?>&title=<?=$post['slug']?>">Edit Post</a>
                            <?php endif ?>
                                <p><?=date("M d, Y ", strtotime($post['time_stamp']))?></p>
                            <?php if (isset($post['header'])): ?>
                                <p><?= $post['header'] ?></p>
                            <?php endif ?>
                                <p><?= substr($post['body'], 0, 200) ?></p>
                            <?php if (isset($post['footer'])): ?>
                                <p><?= $post['footer'] ?></p>
                            <?php endif ?>
                                <p><a href="post.php?id=<?=$post['id']?>&title=<?=$post['slug']?>">Read Full Post</a></p>
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
</body>
</html>
