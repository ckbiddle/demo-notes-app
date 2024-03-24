<?php

  session_start();

  require './GlobalVars.php';
  require './InitSessionVars.php';
  require './model/DBFunctions.php';
  require './model/InitDbConnect.php';

  $loggedIn = false;

  if ( $_SESSION['idv_login_id'] != '' )
  {
    $loggedIn = true;
  }

  $hostPage = 'Home';
  
  // This GET parameter is added to links and buttons that drive the user to this page
  // so that if the user comes to this page from those sources, the session variables
  // that hold the values to be displayed in the input boxes, and the error message,
  // are empty.
  if ( isset( $_GET['sessionstatus'] ) && $_GET['sessionstatus'] == 'init' )
  {
    $_SESSION['login_message'] = '';
    $_SESSION['login_error_message'] = '';
    $_SESSION['login_login_id'] = '';
    $_SESSION['login_pwd'] = '';
  }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="./css/default.css?prm=<?php echo rand(); ?>">
    <title>Identivize</title>
  </head>
  
  <!-- <body <?php echo ( $loggedIn ? 'style="background-color:#FFF"' : '' ); ?> > -->
  <!-- <body <?php echo ( $loggedIn ? 'style="background-image: url( ./images/Identivize_Background2.jpg );background-size:100%;background-repeat:no-repeat"' : '' ); ?> > -->
  <body style="background-image: url( ./images/Identivize_Background.jpg );background-size:100%;background-repeat:no-repeat" >  
  
    <?php

      require './Navbar.php';

      if ( $loggedIn )
      {
        require './Workflow.php';    
      }
      else
      {
        require './Login.php';    
      }
    
    ?>
  
  </body>
</html>