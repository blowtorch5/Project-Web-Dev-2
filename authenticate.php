 <?php

/*******************

    Name: Patrick Philippot
    Date: 4/18/2023
    Description: A php page that authenticates admin users

********************/
  require('connect.php');

  $query = "SELECT * FROM users";
  $statement = $db->prepare($query);
  $statement->execute();

  $users = $statement->fetchAll();

  $logged = false;

  session_start();

  foreach( $users as $user ){
    if ($user["username"] == $_GET["username"]){
      if ($user["password"] == $_GET["password"]){
        $_SESSION['user'] = $user;
        $_SESSION["authenticated"] = true;
        $logged = true;
        header('Location: index.php');
      }
    }
  }

  if (!$logged){
    $_SESSION['authenticated'] = false;
    header('Location: index.php');
  }
?>