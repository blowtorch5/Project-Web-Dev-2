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

  if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {

    header('HTTP/1.1 401 Unauthorized');

    header('WWW-Authenticate: Basic realm="Our Blog"');

    exit("Access Denied: Username and password required.");

  }
  else
  {
    //this is to return to the index if the user signed in there
    foreach( $users as $user ){
      if($user["username"] == $_SERVER["PHP_AUTH_USER"]){
        if($user["password"] == $_SERVER["PHP_AUTH_PW"]){
          $_SESSION['level'] = $user['level'];
          $_SESSION["authenticated"] = true;
        }
      }
    }

    if($_SESSION["authenticated"] == false){
      header('HTTP/1.1 401 Unauthorized');

      header('WWW-Authenticate: Basic realm="Our Blog"');
  
      exit("Access Denied: Username and password required.");
    }

    if(isset($_GET['redirect']) && $_GET['redirect'] == "index.php")
    {
      header('Location: index.php');
    }
  }

?>