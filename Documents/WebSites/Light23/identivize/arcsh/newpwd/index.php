<?php

  session_start();

  $tenantName = '';

  if ( isset( $_SESSION['arc_tenant_name'] ) && $_SESSION['arc_tenant_name'] != '' )
  {
    $tenantName = $_SESSION['arc_tenant_name'];   
  }
  else
  {
    die( '<p>Error: Page cannot be accessed without a tenant name!</p>' );
  }

  // This page can only be accessed if the user is logged in.
  if ( !isset( $_SESSION['arc_login_id'] ) || $_SESSION['arc_login_id'] == '' )
  {
    header( 'Location: ../' );
    die();
  }

  require '../../GlobalVars.php';
  require '../../model/DBFunctions.php';
  require '../../model/InitDbConnect.php';

  $rtn = '';

  if ( isset( $_GET['rtn'] ))
  {
    $rtn = $_GET['rtn'];
  }

  // This GET parameter is added to links and buttons that drive the user to this page
  // so that if the user comes to this page from those sources, the session variables
  // that hold the values to be displayed in the input boxes, and the error message,
  // are empty.
  if ( isset( $_GET['sessionstatus'] ) && $_GET['sessionstatus'] == 'init' )
  {
    $_SESSION['arc_new_pwd_error_message'] = '';
    $_SESSION['arc_new_pwd_1'] = '';
    $_SESSION['arc_new_pwd_2'] = '';
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
    <link rel="stylesheet" href="../../css/default.css?prm=<?php echo rand(); ?>">
    <title>Identivize Access Request Console New Password</title>
  </head>

<body>
    <?php

      require '../Navbar.php';
      
    ?>

    <div class="container-fluid pt-5">
      <div class="row pb-3">
        <div class="col-md-4"></div>
        <div class="col-md-4" style="text-align:center">
          <h3>Reset Password</h3>
          <p>Enter the details below.</p>
          </p>
        </div>
        <div class="col-md-4"></div>
      </div>
      <div class="row">
        <div class="col"></div>
        <div class="col">
          <?php
    
            if ( $_SESSION['arc_new_pwd_error_message'] != '' )
            {
              echo '<div class="alert alert-danger">' . $_SESSION['arc_new_pwd_error_message'] . '</div>';
            }
          
          ?>
        </div>
        <div class="col"></div>
      </div>
      <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
          <form action="../../controller/" method="post">
            <input type="hidden" name="cmd" value="arcupdpwd" />
            <div class="row mb-2">
              <div class="col-md-6">
                <label for="new_pwd_1" class="form-label">Password:</label>
              </div>
              <div class="col-md-6">
                <input type="password" class="form-control" id="new_pwd_1" name="new_pwd_1" maxlength="80" value="<?php echo $_SESSION['arc_new_pwd_1']; ?>"/>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="new_pwd_2" class="form-label">Re&dash;Enter Password:</label>
              </div>
              <div class="col-md-6">
                <input type="password" class="form-control" id="new_pwd_2" name="new_pwd_2" maxlength="80" value="<?php echo $_SESSION['arc_new_pwd_2']; ?>"/>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6" style="text-align:right">
                <input type="submit" class="btn btn-success" value="Submit">
              </div>
              <div class="col-md-6" style="text-align:left">
                <a href="../../controller/?cmd=cancelarcupdpwd<?php echo ( $rtn != '' ? '&rtn=' . $rtn : '' ); ?>" class="btn btn-danger">Cancel</a>
              </div>
            </div>
          </form>
        </div>
        <div class="col-md-3"></div>
      </div>
    </div>
  
</body>
</html>