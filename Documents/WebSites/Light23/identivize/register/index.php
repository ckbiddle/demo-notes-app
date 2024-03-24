<?php

  session_start();

  require '../GlobalVars.php';

  // This GET parameter is added to links and buttons that drive the user to this page
  // so that if the user comes to this page from those sources, the session variables
  // that hold the values to be displayed in the input boxes, and the error message,
  // are empty.
  if ( isset( $_GET['sessionstatus'] ) && $_GET['sessionstatus'] == 'init' )
  {
    $_SESSION['reg_error_message'] = '';
    $_SESSION['reg_login_id'] = '';
    $_SESSION['reg_pwd_1'] = '';
    $_SESSION['reg_pwd_2'] = '';
    $_SESSION['reg_last_name'] = '';
    $_SESSION['reg_first_name'] = '';
    $_SESSION['reg_company_name'] = '';
    $_SESSION['reg_arc_name'] = '';  
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
    <title>Identivize Registration</title>
  </head>

  <body style="background-image: url( ../images/Identivize_Background.jpg );background-size:100%;background-repeat:no-repeat" >
  
    <?php

      require '../Navbar.php';
      
    ?>

    <div class="container-fluid pt-4">
      <div class="row pb-3">
        <div class="col-md-4"></div>
        <div class="col-md-4" style="text-align:center">
          <h3>Create a New Admin Account</h3>
          <p>Already registered? <a href="<?php echo $appRoot; ?>/?sessionstatus=init">Log in</a> here.</p>
          </p>
        </div>
        <div class="col-md-4"></div>
      </div>
      <div class="row">
        <div class="col"></div>
        <div class="col">
          <?php
    
            if ( $_SESSION['reg_error_message'] != '' )
            {
              echo '<div class="alert alert-danger">' . $_SESSION['reg_error_message'] . '</div>';
            }
          
          ?>
        </div>
        <div class="col"></div>
      </div>
      <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
          <form action="../controller/" method="post">
            <input type="hidden" name="cmd" value="register" />
            <div class="row mb-2">
              <div class="col-md-6">
                <label for="login_id" class="form-label">Email Address (Will be your Login ID):</label>
              </div>
              <div class="col-md-6">
                <input type="email" class="form-control" id="login_id" name="login_id" maxlength="40" value="<?php echo $_SESSION['reg_login_id']; ?>"/>
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-md-6">
                <label for="pswd" class="form-label">Choose a Password:</label>
              </div>
              <div class="col-md-6">
                <input type="password" class="form-control" id="pswd" name="pwd" maxlength="80" value="<?php echo $_SESSION['reg_pwd_1']; ?>" />
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-md-6">
                <label for="rpt_pswd" class="form-label">Repeat Password:</label>
              </div>
              <div class="col-md-6">
                <input type="password" class="form-control" id="rpt_pswd" name="repeat_pwd" maxlength="80" value="<?php echo $_SESSION['reg_pwd_2']; ?>" />
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-md-6">
                <label for="last_name" class="form-label">Last Name:</label>
              </div>
              <div class="col-md-6">
                <input type="text" class="form-control" id="last_name" name="last_name" maxlength="40" value="<?php echo $_SESSION['reg_last_name']; ?>" />
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-md-6">
                <label for="first_name" class="form-label">First Name:</label>
              </div>
              <div class="col-md-6">
                <input type="text" class="form-control" id="first_name" name="first_name" maxlength="40" value="<?php echo $_SESSION['reg_first_name']; ?>" />
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-md-6">
                <label for="company_name" class="form-label">Company:</label>
              </div>
              <div class="col-md-6">
                <input type="text" class="form-control" id="company_name" name="company_name" maxlength="80" value="<?php echo $_SESSION['reg_company_name']; ?>" />
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-md-6">
                <label for="arc_name" class="form-label">Access Request Console Name:</label>
              </div>
              <div class="col-md-6">
                <input type="text" class="form-control" id="arc_name" name="arc_name" maxlength="40" value="<?php echo $_SESSION['reg_arc_name']; ?>" />
              </div>
            </div>
            <div class="row mb-4">
              <div class="col-md-6">
                <label for="email_domains" class="form-label">Valid Email Domain(s) (Separate using commas):</label>
              </div>
              <div class="col-md-6">
                <input type="text" class="form-control" id="email_domains" name="email_domains" maxlength="80" value="<?php echo $_SESSION['reg_email_domains']; ?>" />
              </div>
            </div>
            <div class="row">
              <div class="col-md-12" style="text-align:center">
                <input type="submit" class="btn btn-success" value="Submit">
              </div>
            </div>
          </form>
        </div>
        <div class="col-md-2"></div>
      </div>
    </div>
  </body>

</html>