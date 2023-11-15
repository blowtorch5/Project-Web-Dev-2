<?php

/********************

    CMS Philippot Farms LTD Project
    Name: Patrick Philippot
    Date: 4/18/2023
    Description: Index to Philippot Farms LTD website. Finally added git to github.

*********************/

require('connect.php');

session_start();

$query = "SELECT * FROM pages ORDER BY time_stamp DESC LIMIT 5";
$statement = $db->prepare($query);
$statement->execute();

$posts = $statement->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Home Page</title>
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
            <input type="hidden" name="redirect" value="index.php">
            <label for="username">Username:</label>
            <input id="username" name="username">
            <label for="password">Password:</label>
            <input id="password" name="password" type="password">
            <button type="submit">Login</button>
            <?php if (isset($_SESSION["authenticated"]) && $_SESSION['authenticated']): ?>
                <a href="authenticate.php?redirect=index.php&logout=true">Log out</a>
            <?php endif ?>
        </form>
        <header id="indexheader">
            <h1>Philippot Farms LTD</h1>
            <nav>
                <ul id="headnavlist">
                    <li><a href="index.php">Home Page</a></li>
                    <li id='postsearch'><a href="posts.php">Posts</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </nav>
        </header>
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
        <main id="indexmain">          
            <div id="introduction">
                 <!-- 
                    note that this is taken from the Dairy Farmers of Canada website 
                    https://dairyfarmersofcanada.ca/en/dairy-in-canada/news-releases/philippot-family-dairy-legacy-manitoba 
                    and describes my family farm. 
                -->
                <h1>The Philippot Family – A Dairy Legacy in Manitoba</h1>
                <img src="images/Philippot Family.jpg" alt="Philippot Family" width=500/> 
                <p>
                    Showcasing the contributions of Canadian dairy farmers in the building and growth of our country, the book was created in celebration of the 150th anniversary of Canada. The book traces the emergence of dairy farming in each of Canada’s provinces through the personal stories of a family of dairy farmers who have been farming for many generations.
                </p>
                <p>
                    “For as long as I can remember, I’ve wanted to be a dairy farmer – farming is my passion,” says Alain Philippot. “I learned from my father, and he from his father, how to take care of the land and to keep it healthy so it can be passed down from generation to generation.”<br><br>

                    Alain, along with his wife Michelle, is a third generation farmer who milks 68 cows at Philippot Farms. The dairy industry developed in St. Claude due to some misfortune in the 1930s, namely the Great Depression and a terrible drought. No crops would grow because of the drought but the grass that grew like weeds turned out to be great for feeding cows. Armed with a new barn purchased before the Depression hit the community hard, Alexis Philippot leveraged the barn and the new grass to invest in cows, and subsequently, dairy. His family has never looked back.
                </p>
                <h3>
                    “By feeding the country in a sustainable way, Canadian dairy farmers have withstood the test of time, from even before Confederation, to produce Canadian quality milk,” said Wally Smith, DFC’s President. 
                </h3>
                <p>                    
                    “I am honoured to introduce the Philippot family, whose story shows a great love for dairy farming, and a deep sense of community.”
                    To read the Philippot family and Philippot Farms full story, please visit <a href="https://dairyfarmersofcanada.ca/en/dairy-in-canada/news-releases">www.dairyfarmers.ca</a> for a PDF version of the book.
                </p>
            </div>
            <div id="posts">
                <h1>Recently Posted Blog Entries.</h1>
                <?php foreach($posts as $post): ?>
                    <div class="post">
                        <h2><a href="post.php?id=<?=$post['id'] ?>"><?= $post['title'] ?></a></h2>
                        <?php if(isset($_SESSION['user']['level']) && $_SESSION['user']['level'] == 'admin'): ?>
                        <a href="edit.php?id=<?=$post['id']?>">Edit Post</a>
                        <?php endif ?>
                        <p><?=date("M d, Y", strtotime($post['time_stamp']))?></p>
                        <?php if (isset($post['header'])): ?>
                        <p><?= $post['header'] ?></p>
                        <?php endif ?>
                        <p><?= substr($post['body'], 0, 200) ?></p>
                        <?php if (isset($post['footer'])): ?>
                        <p><?= $post['footer'] ?></p>
                        <?php endif ?>
                        <p><a href="post.php?id=<?=$post['id']?>">Read Full Post</a></p>
                    </div>
                <?php endforeach ?>
            </div>
        </main>
        <footer id="indexfooter">
            <nav>
                <ul>
                    <li><a href="index.php">Home Page</a></li>
                    <li><a href="posts.php">Posts</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <?php if (!isset($_SESSION['authenticated'])): ?>
                        <li><a href="#login">Sign In</a></li>
                    <?php endif ?>
                </ul>
            </nav>
        </footer>
    </div>
</body>
</html>