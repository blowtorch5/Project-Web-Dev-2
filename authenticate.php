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

  session_start();

  foreach( $users as $user ){
    if ($user["username"] == $_GET["username"]){
      if ($user["password"] == $_GET["password"]){
        $_SESSION['user'] = $user;
        $_SESSION["authenticated"] = true;
        header('Location: index.php');
      }
    }
  }

  if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']){
    $_SESSION['authenticated'] = false;
    header('Location: index.php');
  }
?>