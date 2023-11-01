 <?php

/*******************

    Name: Patrick Philippot
    Date: 4/18/2023
    Description: A php page that authenticates admin users

********************/

  session_start();

  define('ADMIN_LOGIN','wally');

  define('ADMIN_PASSWORD','mypass');

  if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])

      || ($_SERVER['PHP_AUTH_USER'] != ADMIN_LOGIN)

      || ($_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD)) {

    header('HTTP/1.1 401 Unauthorized');

    header('WWW-Authenticate: Basic realm="Our Blog"');

    exit("Access Denied: Username and password required.");

  }
  else
  {
    //this is to return to the index if the user signed in there
    if(isset($_GET['redirect']) && $_GET['redirect'] == "index.php")
    {
      header('Location: index.php');
    }

    $_SESSION['authenticated'] = true;
  }

?>