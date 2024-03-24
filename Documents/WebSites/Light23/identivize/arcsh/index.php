<?php

  session_start();

  require '../GlobalVars.php';

  $tenantName = '';

  if ( isset( $_SESSION['arc_tenant_name'] ) && $_SESSION['arc_tenant_name'] != '' )
  {
    $tenantName = $_SESSION['arc_tenant_name'];   
  }
  else
  {
    die( '<p>Error: Page cannot be accessed without a tenant name!</p>' );
  }

  if ( !isset( $_SESSION['arc_login_id'] ) || $_SESSION['arc_login_id'] == '' )
  {
    header( 'Location: ../arc/' . $tenantName );  
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
    <link rel="stylesheet" href="../css/default.css?prm=<?php echo rand(); ?>">
    <title>Identivize Access Request Console</title>
  </head>

  <body>
  
      <?php
  
        require './Navbar.php';
 
        require './SearchInput.php';
      
      ?>
      
  </body>
</html>