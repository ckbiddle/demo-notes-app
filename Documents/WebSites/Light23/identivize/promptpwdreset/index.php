<?php

  session_start();

  require '../GlobalVars.php';
  
  // This GET parameter is added to links and buttons that drive the user to this page
  // so that if the user comes to this page from those sources, the session variables
  // that hold the values to be displayed in the input boxes, and the error message,
  // are empty.
  if ( isset( $_GET['sessionstatus'] ) && $_GET['sessionstatus'] == 'init' )
  {
    $_SESSION['pwdreset_error_message'] = '';
    $_SESSION['pwdreset_login_id'] = '';
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
    <title>Identivize Password Reset</title>
  </head>

  <body>
  
    <?php

      require '../Navbar.php';
      
    ?>

    <div class="container-fluid pt-5">
      <div class="row pb-3">
        <div class="col-md-4"></div>
        <div class="col-md-4" style="text-align:center">
          <h3>Forgot your password?</h3>
          <p>
            Enter your email address and click Submit. You will receive an email with a link to reset your password.
          </p>
        </div>
        <div class="col-md-4"></div>
      </div>
      <div class="row">
        <div class="col"></div>
        <div class="col">
          <?php
    
            if ( $_SESSION['pwdreset_error_message'] != '' )
            {
              echo '<div class="alert alert-danger">' . $_SESSION['pwdreset_error_message'] . '</div>';
            }
          
          ?>
        </div>
        <div class="col"></div>
      </div>
      <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
          <form action="../controller/" method="post">
            <input type="hidden" name="cmd" value="pwdreset" />
            <div class="row mb-3">
              <div class="col-md-2"></div>
              <div class="col-md-4">
                <label for="login_id" class="form-label">Email Address:</label>
              </div>
              <div class="col-md-4">
                <input type="email" class="form-control" id="login_id" name="login_id" maxlength="40" value="<?php echo $_SESSION['pwdreset_login_id']; ?>"/>
              </div>
              <div class="col-md-2"></div>
            </div>
            <div class="row">
              <div class="col-md-12" style="text-align:center">
                <input type="submit" class="btn btn-success" value="Submit">
              </div>
            </div>
          </form>
        </div>
        <div class="col-md-3"></div>
      </div>
    </div>
  </body>

</html>