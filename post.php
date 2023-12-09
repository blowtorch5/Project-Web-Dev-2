<?php

/*******w******** 
    
    Name: Patrick Philippot
    Date: 4/18/2023
    Description: A php page that displays a single post and its respective options.

****************/

require('connect.php');

session_start();

if ($_GET){
    
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    filter_var($id, FILTER_VALIDATE_INT);
    $slug = filter_input(INPUT_GET, 'title', FILTER_SANITIZE_STRING);
    
    $query = "SELECT * FROM pages WHERE id = :id AND slug = :slug";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id);
    $statement->bindValue(':slug', $slug);
    $statement->execute();
    
    $post = $statement->fetch();
    
    if($post == null){
        header("Location: index.php");
        exit;
    }

    if($post['has_image']){
        $query = "SELECT * FROM images WHERE page_id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id);
    
        $statement->execute();
        
        $image = $statement->fetch();
    }
}


$query = "SELECT * FROM categories";
$statement = $db->prepare($query);
$statement->execute();

$categories = $statement->fetchAll();

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
        <h1>Post "<?= $post['title'] ?>"</h1>
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
                <?php if (isset($image)): ?>
                    <img src="<?= $image['filename'] ?>" alt="<?= $image['filename'] ?>" width=500>
                <?php endif ?>
                <?php if (isset($_SESSION['user']['user_level']) && ($_SESSION['user']['user_level'] == 'admin' || $_SESSION['user']['user_level'] == 'owner')): ?>
                    <p><a href="edit.php?id=<?=$post['id']?>&title=<?=$post['slug']?>">Edit Post</a></p>
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
                        <li><a href="authenticate.php?redirect=index.php&logout=true">Log out</a></li>
                    <?php endif ?>
                </ul>
            </nav>
        </footer>
    </div>
</body>
</html>