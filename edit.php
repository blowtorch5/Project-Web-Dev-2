<?php

/*******w******** 
    
    Name: Patrick Philippot
    Date: 4/18/2023
    Description: A php page that allows authenticated users to edit or make new posts.

****************/

require('connect.php');
session_start();

$edit_post = false;

if(isset($_GET['id']))
{
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $query = "SELECT * FROM pages WHERE id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id);
    $statement->execute();

    $post = $statement->fetch();
    $edit_post = true;
}
if ($_POST && isset($_POST['title']) && isset($_POST['body']) && isset($_POST['id']))
{
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $header = filter_input(INPUT_POST, 'header', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $body = filter_input(INPUT_POST, 'body', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $footer = filter_input(INPUT_POST, 'footer', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    if (isset($_POST['header']))
    {
        if(isset($_POST['footer']))
        {
            $query = "UPDATE pages SET title = :title, header = :header, body = :body, footer = :footer WHERE id = :id";
            $statement = $db->prepare($query);
            $statement->bindValue(':title', $title);
            $statement->bindValue(':header', $header);        
            $statement->bindValue(':body', $body);
            $statement->bindValue(':footer', $footer); 
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
        }
        else
        {
            $query = "UPDATE pages SET title = :title, header = :header, body = :body WHERE id = :id";
            $statement = $db->prepare($query);
            $statement->bindValue(':title', $title);
            $statement->bindValue(':header', $header);        
            $statement->bindValue(':body', $body);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
        }
    }
    else
    {
        if(isset($_POST['footer']))
        {
            $query = "UPDATE pages SET title = :title, body = :body, footer = :footer WHERE id = :id";
            $statement = $db->prepare($query);
            $statement->bindValue(':title', $title);    
            $statement->bindValue(':body', $body);
            $statement->bindValue(':footer', $footer); 
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
        }
        else
        {
            $query = "UPDATE pages SET title = :title, body = :body WHERE id = :id";
            $statement = $db->prepare($query);
            $statement->bindValue(':title', $title);        
            $statement->bindValue(':body', $body);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
        }
    }

    $failed = false;
    $statement->execute();
    header("Location: post.php?id={$id}");

    exit;
}
if ($_POST && !empty($_POST['title']) && !empty($_POST['body']))
{
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $header = filter_input(INPUT_POST, 'header', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $body = filter_input(INPUT_POST, 'body', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $footer = filter_input(INPUT_POST, 'footer', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $time_stamp = date("Y-m-d h:i:a");

    if (isset($_POST['header']))
    {
        if(isset($_POST['footer']))
        {
            $query = "INSERT INTO pages (title, header, body, footer, time_stamp) VALUES (:title, :header, :body, :footer, :time_stamp)";
            $statement = $db->prepare($query);
            $statement->bindValue(':title', $title);
            $statement->bindValue(':header', $header);         
            $statement->bindValue(':body', $body);
            $statement->bindValue(':footer', $footer); 
            $statement->bindValue(':time_stamp', $time_stamp);
        }
        else
        {
            $query = "INSERT INTO pages (title, header, body, time_stamp) VALUES (:title, :header, :body, :time_stamp)";
            $statement = $db->prepare($query);
            $statement->bindValue(':title', $title);
            $statement->bindValue(':header', $header);         
            $statement->bindValue(':body', $body); 
            $statement->bindValue(':time_stamp', $time_stamp);
        }
    }
    else
    {
        if(isset($_POST['footer']))
        {
            $query = "INSERT INTO pages (title, body, footer, time_stamp) VALUES (:title, :body, :footer, :time_stamp)";
            $statement = $db->prepare($query);
            $statement->bindValue(':title', $title);       
            $statement->bindValue(':body', $body);
            $statement->bindValue(':footer', $footer); 
            $statement->bindValue(':time_stamp', $time_stamp);
        }
        else
        {
            $query = "INSERT INTO pages (title, body, time_stamp) VALUES (:title, :body, :time_stamp)";
            $statement = $db->prepare($query);
            $statement->bindValue(':title', $title);        
            $statement->bindValue(':body', $body);
            $statement->bindValue(':time_stamp', $time_stamp);
        }
    }
    
    $statement->execute();
    header("Location: index.php");

    exit;
}
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
    <?php if (isset($_SESSION['user']['level']) && $_SESSION['user']['level'] == 'admin'): ?>
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
            <main>
                <div id="post-options">
                <?php if(isset($_SESSION['authenticated']) && $_SESSION['authenticated']): ?>
                <h3><a href="edit.php">Create New Post</a></h3>
                <?php endif ?>
                <h2>Search for post</h2>
                <form method="post" action="posts.php">
                    <label>Title of Post</label>
                    <input type="text" name="title" id="title">
                </form>
                </div>
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
                        </ul>
                        <button type="submit">Update</button> 
                    </form>
                    <form action="delete.php" id="deleteForm">
                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                        <button type="submit" id="deleteButton">Delete</button>
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
                        </ul>
                    </nav>
                </footer>
            </div>
        <?php else: ?>
            <p class="error">Please log in to admin level account.</p>
            <div id="post-options">
                <?php if(isset($_SESSION['authenticated']) && $_SESSION['authenticated']): ?>
                <h3><a href="edit.php">Create New Post</a></h3>
                <?php endif ?>
                <h2>Search for post</h2>
                <form method="post" action="posts.php">
                    <label>Title of Post</label>
                    <input type="text" name="title" id="title">
                </form>
            </div>
        <?php endif ?>
</body>
</html>