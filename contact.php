<?php

/*******************

    Name: Patrick Philippot
    Date: 4/18/2023
    Description: A php page that displays contact information to Philippot Farms LTD

********************/

session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Contact Us</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php if(!isset($_SESSION['authenticated'])): ?>
    <a href=authenticate.php?redirect=index.php>sign in</a>
    <?php endif ?>
    <div id="contactform">
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
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </nav>
        </footer>        
    </div>
</body>
</html>