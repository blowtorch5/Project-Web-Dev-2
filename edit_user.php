<?php

/*******w******** 
    
    Name: Patrick Philippot
    Date: 4/18/2023
    Description: A php page that allows authenticated users to edit or make new posts.

****************/

require('connect.php');
session_start();

$edit_user = false;

if(isset($_GET['user_id'])){
    $id = filter_input(INPUT_GET, 'user_id', FILTER_SANITIZE_NUMBER_INT);

    $query = "SELECT * FROM users WHERE user_id = :user_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':user_id', $id);
    $statement->execute();

    $user = $statement->fetch();
    $edit_user = true;
}

if ($edit_user && $_POST && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['pass']) && isset($_POST['confirm']) && isset($_POST['user_level']) && $_POST['pass'] == $_POST['confirm']){
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $level = filter_input(INPUT_POST, 'user_level', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $pass = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);

    $query = "UPDATE users SET username = :username, email_address = :email, user_level = :user_level, pass = :pass WHERE user_id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':username', $username);
    $statement->bindValue(':email', $email);
    $statement->bindValue(':user_level', $level);        
    $statement->bindValue(':pass', $pass);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);

    $statement->execute();

    if ($_SESSION['user']['user_id'] == $user['user_id']){
        header("Location: authenticate.php?redirect=index.php&logout=true");
    } elseif (isset($_SESSION['user']['user_level']) && $_SESSION['user']['user_level'] == "admin"){
        header("Location: users.php");
    } else {
        header("Location: index.php");
    }

    exit;
} elseif ($_POST && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['pass']) && isset($_POST['confirm']) && isset($_POST['user_level']) && $_POST['pass'] == $_POST['confirm']){
    $username= filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $level = filter_input(INPUT_POST, 'user_level', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $pass = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $query = "INSERT INTO users (username, email_address, user_level, pass) VALUES (:username, :email, :user_level, :pass)";
    $statement = $db->prepare($query);
    $statement->bindValue(':username', $username);
    $statement->bindValue(':email', $email);
    $statement->bindValue(':user_level', $level);           
    $statement->bindValue(':pass', $pass);
    $statement->execute();

    if (isset($_SESSION["user"]["user_level"]) && $_SESSION["user"]["user_level"] == "admin"){
        header("Location: users.php");
    } else {
        header("Location: index.php");
    }

    exit;
}

$query = "SELECT DISTINCT category FROM pages";
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
    <div>
        <?php if (isset($_SESSION["user"]["username"])): ?>
            <p>Logged in: <?=$_SESSION["user"]["username"]?></p>
        <?php endif ?>
        <?php if (isset($_SESSION["authenticated"]) && $_SESSION['authenticated']): ?>
            <a href="authenticate.php?redirect=index.php&logout=true">Log out</a>
        <?php endif ?>
        <header>
            <h1>Edit User</h1>
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
            <div id="post-options">
            <?php if(isset($_SESSION['authenticated']) && $_SESSION['authenticated']): ?>
                <h2><a href="edit.php">Create New Post</a></h2>
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
        <?php if ($edit_user): ?>
            <h1>User "<?= $user['username'] ?>"</h1>
            <div id="editpost">
                <form method="post" id="userForm">
                    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                    <ul>
                        <li>
                            <label for="username">Username</label>
                            <input id="username" name="username" value="<?= $user['username'] ?>">
                        </li>
                        <li>
                            <label for="email">Email</label>
                            <input id="email" name="email" value=<?= $user['email_address']?>>
                        </li>
                        <li>
                            <label for="pass">Password</label>
                            <input id="pass" name="pass" value=<?= $user['pass']?> type="password">
                        </li>
                        <li>
                            <label for="confirm">Confirm Password</label>
                            <input id="confirm" name="confirm" value="" type="password">
                        </li>
                        <?php if (isset($_SESSION['user']['user_level']) && $_SESSION['user']['user_level'] == 'admin'): ?>
                            <li>
                            <label for="user_level">User Level</label>
                                <input id="user_level" name="user_level" value="<?=$user['user_level']?>">
                            </li>
                        <?php endif ?>
                    </ul>
                    <button type="submit">Update</button> 
                </form>
                <form action="delete.php" id="deleteForm">
                    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                    <button type="submit" id="deleteButton" value="user">Delete</button>
                </form>
            </div>
        <?php else: ?>
            <h1>New User</h1>
            <div id="createuser">
                <form method="post">
                    <ul>
                        <li>
                            <label for="username">Username</label>
                            <input id="username" name="username" value="">
                        </li>
                        <li>
                            <label for="email">Email</label>
                            <input id="email" name="email" value="">
                        </li>
                        <li>
                            <label for="pass">Password</label>
                            <input id="pass" name="pass" value="" type="password">
                        </li>
                        <li>
                            <label for="confirm">Confirm Password</label>
                            <input id="confirm" name="confirm" value=""  type="password">
                        </li>
                        <?php if (isset($_SESSION['user']['user_level']) && $_SESSION['user']['user_level'] == 'admin'): ?>
                            <li>
                            <label for="user_level">User Level</label>
                                <input id="user_level" name="user_level" value="">
                            </li>
                        <?php else: ?>
                            <li>
                                <input id="user_level" name="user_level" value="user" type="hidden">
                            </li>
                        <?php endif ?>
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
                    <?php if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']): ?> 
                        <li><a href="authenticate.php?redirect=index.php&logout=true">Log out</a></li>
                    <?php endif ?>
                </ul>
            </nav>
        </footer>
    </div>
</body>
</html>