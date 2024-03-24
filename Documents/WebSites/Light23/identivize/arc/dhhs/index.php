<?php

  if ( basename( __DIR__ ) == 'arcsh' )
  {
    die( '<h1>You cannot access this file directly!</h1>' );
  }

  session_start();

  require '../../arcsh/InitSessionVars.php';
  require '../../GlobalVars.php';
  require '../../model/DBFunctions.php';
  require '../../model/InitDbConnect.php';

  $tenantName = basename( __DIR__ );

  $errorMessage = '';
  $companyName = '';
  
  $managerDetails = getManagerDetailsByTenantName( $conn, $tenantName, $errorMessage );

  if ( sizeof( $managerDetails ) > 0 )
  {
    $companyName = $managerDetails['company_name'];
  }
  
  // This GET parameter is added to links and buttons that drive the user to this page
  // so that if the user comes to this page from those sources, the session variables
  // that hold the values to be displayed in the input boxes are empty.
  if ( isset( $_GET['sessionstatus'] ) && $_GET['sessionstatus'] == 'init' )
  {
    // Clear session values associated with the login page
    $_SESSION['arc_login_login_id'] = '';
    $_SESSION['arc_login_pwd'] = '';
    $_SESSION['arc_login_message'] = '';
    $_SESSION['arc_login_error_message'] = '';
    
    // Clear session values associated with the registration page
    $_SESSION['arc_registration_login_id'] = '';
    $_SESSION['arc_registration_pwd1'] = '';
    $_SESSION['arc_registration_pwd2'] = '';
    $_SESSION['arc_registration_first_name'] = '';
    $_SESSION['arc_registration_last_name'] = '';
    $_SESSION['arc_registration_job_title'] = '';
    $_SESSION['arc_registration_company_name'] = '';
    $_SESSION['arc_registration_message'] = '';
    $_SESSION['arc_registration_error_message'] = '';
    
    // Clear session values associated with the password reset page
    $_SESSION['arc_pwdreset_login_id'] = '';
    $_SESSION['arc_pwdreset_error_message'] = '';
    $_SESSION['arc_pwdreset_message'] = '';
  }

  $cmd = 'login';
  
  if ( isset( $_GET['cmd'] ) && $_GET['cmd'] != '' )
  {
    $cmd = $_GET['cmd'];
  }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../../css/default.css?prm=<?php echo rand(); ?>">
    <title>Identivize Access Request Console</title>
  </head>
  <body>
    <div class="container-fluid pt-5"> <!-- pt-5 is a bootstrap feature to say "add a large top padding" -->
      <div class="row">
        <div class="col-md-6" style="text-align:center">
          <h3 style="font-weight:bold">
          Request all the access you<br />
          need with a few simple clicks.<br />
          Welcome to simplicity and<br />
          efficiency, redefined.
          </h3>
          <img src="../../images/desk_office_applications.png" style="width:50%;padding-top:10px" />
        </div>
        <div class="col-md-5" style="text-align:center">
          <div class="p-3 rounded" style="width:100%;height:100%;background-color:#FFF">
            <h2 class="tenant_header"><?php echo $companyName; ?></h2>
            <?php
 
              if ( $cmd == 'login' )
              {
                require '../../arcsh/Login.php';
              }
              else if ( $cmd == 'register' )
              {
                require '../../arcsh/Register.php';
              }
              else if ( $cmd == 'promptpwdreset' )
              {
                require '../../arcsh/PromptPwdReset.php';
              }
            
            ?>
          </div>
        </div>
        <div class="col-md-1"></div>
      </div>
    </div>
  </body>
</html>