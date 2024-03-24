<?php

  require '../../GlobalVars.php';

  session_start();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../../css/default.css?prm=<?php echo rand(); ?>">
    <title>Identivize Access Request Console</title>
  </head>
  <body>

    <?php

      require '../Navbar.php';
    
    ?>

    <div class="container-fluid pt-5"> <!-- pt-5 is a bootstrap feature to say "add a large top padding" -->
      <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
          <?php
      
            if ( $_SESSION['arc_message'] != '' )
            {
              echo '<div class="mt-4 p-5 bg-primary text-white rounded">' .
                   '  <p>' . $_SESSION['arc_message'] . '</p>' .
                   '</div>';
            }
      
          ?>
        </div>
        <div class="col-md-3"></div>
      </div>
    </div>        
  </body>
</html>