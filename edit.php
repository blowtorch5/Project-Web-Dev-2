<?php

/*******w******** 
    
    Name: Patrick Philippot
    Date: 4/18/2023
    Description: A php page that allows authenticated users to edit or make new posts.

****************/

require('connect.php');
session_start();

$edit_post = false;

global $valid_image;
$valid_image = false;

global $has_image;
$has_image = false;

if($_GET){

    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
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

    $edit_post = true;
}

// file_upload_path() - Safely build a path String that uses slashes appropriate for our OS.
// Default upload path is an 'uploads' sub-folder in the current folder.
function file_upload_path($original_filename, $upload_subfolder_name = 'uploads') {
    $current_folder = dirname(__FILE__);
    
    // Build an array of paths segment names to be joins using OS specific slashes.
    $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
    
    // The DIRECTORY_SEPARATOR constant is OS specific.
    return join(DIRECTORY_SEPARATOR, $path_segments);
}

    // file_is_an_image() - Checks the mime-type & extension of the uploaded file for "image-ness".
function file_is_an_image($temporary_path, $new_path) {
        $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
        $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];
        
        $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
        $actual_mime_type        = getimagesize($temporary_path)['mime'];
        
        $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
        $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);

        global $valid_image;
        $valid_image = true;
        
        return $file_extension_is_valid && $mime_type_is_valid;
}
    
$image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);
$upload_error_detected = isset($_FILES['image']) && ($_FILES['image']['error'] > 0);

if ($image_upload_detected) { 
    $image_filename        = $_FILES['image']['name'];
    $temporary_image_path  = $_FILES['image']['tmp_name'];
    $new_image_path        = file_upload_path($image_filename);
    if (file_is_an_image($temporary_image_path, $new_image_path)) {
        move_uploaded_file($temporary_image_path, $new_image_path);
    }
}

if ($_POST && isset($_POST['title']) && isset($_POST['body']) && isset($_POST['id'])){
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $slug = str_replace(' ', '-', $title);
    $header = filter_input(INPUT_POST, 'header', FILTER_SANITIZE_STRING);
    $body = filter_input(INPUT_POST, 'body', FILTER_SANITIZE_STRING);
    $footer = filter_input(INPUT_POST, 'footer', FILTER_SANITIZE_STRING);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
    filter_var($category, FILTER_VALIDATE_INT);
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    filter_var($id, FILTER_VALIDATE_INT);

    if ($image_upload_detected && $valid_image){
        $path_segments = ['uploads', basename($image_filename)];
        $filename = join(DIRECTORY_SEPARATOR, $path_segments);

        $query = 'INSERT INTO images (filename, page_id) VALUES (:filename, :page_id)';
        $statement = $db->prepare($query);
        $statement->bindValue(':filename', $filename);
        $statement->bindValue(':page_id', $id);
        
        $statement->execute();

        global $has_image;
        $has_image = true;
    }

    $query = "UPDATE pages SET title = :title, header = :header, body = :body, footer = :footer, slug = :slug, has_image = :has_image, category_id = :category WHERE id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':title', $title);
    $statement->bindValue(':header', $header);         
    $statement->bindValue(':body', $body);
    $statement->bindValue(':footer', $footer);
    $statement->bindValue(':slug', $slug);
    $statement->bindValue(':has_image', $has_image);
    $statement->bindValue(':category', $category);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    
    $statement->execute();

    header("Location: post.php?id={$id}&title={$slug}");

    exit;
}

if ($_POST && !empty($_POST['title']) && !empty($_POST['body'])){
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $slug = str_replace(' ', '-', $title);
    $header = filter_input(INPUT_POST, 'header', FILTER_SANITIZE_STRING);
    $body = filter_input(INPUT_POST, 'body', FILTER_SANITIZE_STRING);
    $footer = filter_input(INPUT_POST, 'footer', FILTER_SANITIZE_STRING);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
    filter_var($category, FILTER_VALIDATE_INT);
    $time_stamp = date("Y-m-d h:i:a");

    if ($image_upload_detected && $valid_image){
        global $has_image;
        $has_image = true;
    }

    $query = "INSERT INTO pages (title, header, body, footer, slug, has_image, category_id, time_stamp) VALUES (:title, :header, :body, :footer, :slug, :has_image, :category, :time_stamp)";
    $statement = $db->prepare($query);
    $statement->bindValue(':title', $title);
    $statement->bindValue(':header', $header);         
    $statement->bindValue(':body', $body);
    $statement->bindValue(':footer', $footer);
    $statement->bindValue(':slug', $slug);
    $statement->bindValue(':has_image', $has_image);
    $statement->bindValue(':category', $category); 
    $statement->bindValue(':time_stamp', $time_stamp);
        
    $statement->execute();

    if ($has_image){
        $query = 'SELECT id FROM pages WHERE title = :title AND time_stamp = :time_stamp';
        $statement = $db->prepare($query);
        $statement->bindValue(':title', $title);
        $statement->bindValue(':time_stamp', $time_stamp);

        $statement->execute();
        $post_id = $statement->fetch();

        $path_segments = ['uploads', basename($image_filename)];
        $filename = join(DIRECTORY_SEPARATOR, $path_segments);

        $query = 'INSERT INTO images (filename, page_id) VALUES (:filename, :page_id)';
        $statement = $db->prepare($query);
        $statement->bindValue(':filename', $filename);
        $statement->bindValue(':page_id', $post_id['id']);
        
        $statement->execute();
    }

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
    <?php if (isset($_SESSION['user']['user_level']) && ($_SESSION['user']['user_level'] == 'admin' || $_SESSION['user']['user_level'] == 'owner')): ?>
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
            <?php if ($edit_post): ?>
                <h1>Post "<?= $post['title'] ?>"</h1>
                <div id="editpost">
                    <form method="post" id="postForm" enctype='multipart/form-data'>
                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                        <ul>
                            <li>
                                <label for="title">Title</label>
                                <input id="title" name="title" value="<?= $post['title'] ?>">
                            </li>
                            <li>
                                <label for="header">Introduction</label>
                                <textarea id="header" name="header"><?= $post['header']?></textarea>
                            </li>
                            <li>
                                <label for="body">Body</label>
                                <textarea id="body" name="body"><?= $post['body'] ?></textarea>
                            </li>
                            <li>
                                <label for="footer">Conclusion</label>
                                <textarea id="footer" name="footer"><?= $post['footer']?></textarea>
                            </li>
                            <li>
                                <label for='image'>Image Filename:</label>
                                <input type='file' name='image' id='image'>
                            </li>
                            <li>
                                <label for="category">Category</label>
                                <select id="category" name="category">
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
                    <?php if (isset($_SESSION['user']['user_level']) && $_SESSION['user']['user_level'] == 'owner'): ?>
                        <form action="delete.php?post=true" id="deleteForm">
                            <input type="hidden" name="id" value="<?= $post['id'] ?>">
                            <button type="submit" id="deleteButton" value="post">Delete</button>
                        </form>
                    <?php endif ?>
                </div>
                <?php else: ?>
                    <h1>New Post</h1>
                    <div id="createpost">
                        <form method="post" enctype='multipart/form-data'>
                            <ul>
                                <li>
                                    <label for="title">Title</label>
                                    <input id="title" name="title" value="">
                                </li>
                                <li>
                                    <label for="header">Introduction</label>
                                    <textarea id="header" name="header"></textarea>
                                </li>
                                <li>
                                    <label for="body">Body</label>
                                    <textarea id="body" name="body"></textarea>
                                </li>
                                <li>
                                    <label for="footer">Conclusion</label>
                                    <textarea id="footer" name="footer"></textarea>
                                </li>
                                <li>
                                    <label for='image'>Image Filename:</label>
                                    <input type='file' name='image' id='image'>
                                </li>
                                <li>
                                    <label for="category">Category</label>
                                    <select id="category" name="category">
                                        <?php foreach ($categories as $category): ?>
                                            <?php if(!$category['category'] == null): ?>
                                                <option value="<?=$category['category_id']?>"><?=$category['category']?></option>
                                            <?php endif ?>
                                        <?php endforeach ?>
                                    </select>
                                </li>
                            </ul>
                            <button type="submit" id="create">Create</button>
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