<?php

/*******w******** 
    
    Name: Patrick Philippot
    Date: 4/18/2023
    Description: A php page that allows authenticated users to edit or make new posts.

****************/

require('connect.php');
session_start();

$edit_post = false;

if(isset($_GET['id'])){
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $query = "SELECT * FROM pages WHERE id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id);
    $statement->execute();

    $post = $statement->fetch();
    $edit_post = true;
}

if ($_POST && isset($_POST['title']) && isset($_POST['body']) && isset($_POST['id'])){
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $header = filter_input(INPUT_POST, 'header', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $body = filter_input(INPUT_POST, 'body', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $footer = filter_input(INPUT_POST, 'footer', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    $query = "UPDATE pages SET title = :title, header = :header, body = :body, footer = :footer, category_id = :category WHERE id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':title', $title);
    $statement->bindValue(':header', $header);         
    $statement->bindValue(':body', $body);
    $statement->bindValue(':footer', $footer);
    $statement->bindValue(':category', $category);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
       
    $statement->execute();
    header("Location: post.php?id={$id}");

    exit;
}

if ($_POST && !empty($_POST['title']) && !empty($_POST['body'])){
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $header = filter_input(INPUT_POST, 'header', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $body = filter_input(INPUT_POST, 'body', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $footer = filter_input(INPUT_POST, 'footer', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
    $time_stamp = date("Y-m-d h:i:a");

    $query = "INSERT INTO pages (title, header, body, footer, category_id, time_stamp) VALUES (:title, :header, :body, :footer, :category, :time_stamp)";
    $statement = $db->prepare($query);
    $statement->bindValue(':title', $title);
    $statement->bindValue(':header', $header);         
    $statement->bindValue(':body', $body);
    $statement->bindValue(':footer', $footer);
    $statement->bindValue(':category', $category); 
    $statement->bindValue(':time_stamp', $time_stamp);
        
    $statement->execute();
    header("Location: index.php");

    exit;
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
    <title>Edit this Post!</title>
</head>
<body>
    <?php if (isset($_SESSION['user']['user_level']) && $_SESSION['user']['user_level'] == 'admin'): ?>
        <div>
            <?php if (isset($_SESSION["user"]["username"])): ?>
                <p>Logged in: <?=$_SESSION["user"]["username"]?></p>
            <?php endif ?>
            <?php if (isset($_SESSION["authenticated"]) && $_SESSION['authenticated']): ?>
                <a href="authenticate.php?redirect=index.php&logout=true">Log out</a>
            <?php endif ?>
            <header>
                <h1>Edit Post</h1>
                <nav>
                    <ul id="headnavlist">
                        <li><a href="index.php">Home Page</a></li>
                        <li id='postsearch'><a href="posts.php">Posts</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                        <?php if (isset($_SESSION['user']['user_level']) && $_SESSION['user']['user_level'] == 'admin'): ?>
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
            <?php if ($edit_post): ?>
                <h1>Post "<?= $post['title'] ?>"</h1>
                <div id="editpost">
                    <form method="post" id="postForm">
                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                        <ul>
                            <li>
                                <label for="title">Title</label>
                                <input id="title" name="title" value="<?= $post['title'] ?>">
                            </li>
                            <li>
                                <label for="header">Introduction</label>
                                <textarea id="header" name="header" value=""><?= $post['header']?></textarea>
                            </li>
                            <li>
                                <label for="body">Body</label>
                                <textarea id="body" name="body" value=""><?= $post['body'] ?></textarea>
                            </li>
                            <li>
                                <label for="footer">Conclusion</label>
                                <textarea id="footer" name="footer" value=""><?= $post['footer']?></textarea>
                            </li>
                            <li>
                                <label for="category">Category</label>
                                <select id="category" name="category">
                                    <option value="">All</option>
                                    <?php foreach ($categories as $category): ?>
                                        <?php if(!$category['category'] == null): ?>
                                            <option value="<?=$category['category_id']?>"><?=$category['category']?></option>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                </select>
                            </li>
                        </ul>
                        <button type="submit">Update</button> 
                    </form>
                    <form action="delete.php?post=true" id="deleteForm">
                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                        <button type="submit" id="deleteButton" value="post">Delete</button>
                    </form>
                </div>
                <?php else: ?>
                    <h1>New Post</h1>
                    <div id="createpost">
                        <form method="post">
                            <ul>
                                <li>
                                    <label for="title">Title</label>
                                    <input id="title" name="title" value="">
                                </li>
                                <li>
                                    <label for="header">Introduction</label>
                                    <textarea id="header" name="header" value=""></textarea>
                                </li>
                                <li>
                                    <label for="body">Body</label>
                                    <textarea id="body" name="body" value=""></textarea>
                                </li>
                                <li>
                                    <label for="footer">Conclusion</label>
                                    <textarea id="footer" name="footer" value=""></textarea>
                                </li>
                                <li>
                                    <label for="category">Category</label>
                                    <select id="category" name="category">
                                        <option value="">All</option>
                                        <?php foreach ($categories as $category): ?>
                                            <?php if(!$category['category'] == null): ?>
                                                <option value="<?=$category['category_id']?>"><?=$category['category']?></option>
                                            <?php endif ?>
                                        <?php endforeach ?>
                                    </select>
                                </li>
                                <button type="submit" id="create">Create</button>
                            </ul>
                        </form>
                    </div>
                <?php endif ?>
            </main>
                <footer id="indexfooter">
                    <nav>
                        <ul>
                            <li><a href="index.php">Home Page</a></li>
                            <li><a href="posts.php">Posts</a></li>
                            <li><a href="contact.php">Contact Us</a></li>
                            <li><a href="authenticate.php?redirect=index.php&logout=true">Log out</a></li>
                        </ul>
                    </nav>
                </footer>
            </div>
        <?php else: ?>
            <p class="error">Please log in to admin level account.</p>
        <?php endif ?>
</body>
</html>