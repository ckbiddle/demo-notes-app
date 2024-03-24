<?php

  if ( basename( $_SERVER['PHP_SELF'] ) === basename( __FILE__ ))
  {
    die( '<p>You cannot access this page directly!</p>' );
  }
  
?>

<div class="container-fluid pt-5"> <!-- pt-5 is a bootstrap feature to say "add a large top padding" -->
  <div class="row" style="padding-top:75px">
    <div class="col"></div>
    <div class="col">
      <?php

        if ( $_SESSION['login_message'] != '' )
        {
          echo '<div class="alert alert-success">' . $_SESSION['login_message'] . '</div>';
        }
    
        if ( $_SESSION['login_error_message'] != '' )
        {
          echo '<div class="alert alert-danger">' . $_SESSION['login_error_message'] . '</div>';
        }
    
      ?>
    </div>
    <div class="col"></div>
  </div>
  <div class="row" style="padding-bottom:20px">
    <div class="col-md-4"></div>
    <div class="col-md-4">
      <form action="./controller/" method="post">
        <input type="hidden" name="cmd" value="login" />
        <div class="row mb-2">
          <div class="col-md-3">
            <label for="login_id" class="form-label">Login ID:</label>
          </div>
          <div class="col-md-9">
            <input type="email" class="form-control" id="login_id" name="login_id" maxlength="40" value="<?php echo $_SESSION['login_login_id']; ?>" />
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-3">
            <label for="pswd" class="form-label">Password:</label>
          </div>
          <div class="col-md-9">
            <input type="password" class="form-control" id="pswd" name="pwd" maxlength="40" value="<?php echo $_SESSION['login_pwd']; ?>" />
          </div>
        </div>
        <div class="row">
          <div class="col-md-12" style="text-align:center">
            <input type="submit" class="btn btn-success" value="Submit">
          </div>
        </div>
      </form>
    </div>
    <div class="col-md-4"></div>
  </div>
  <div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4" style="text-align:center">
      <p>Forgot your password? <a href="./promptpwdreset/?sessionstatus=init">Click here</a> to reset.</p>
    </div>
    <div class="col-md-4"></div>
  </div>
  <div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4" style="text-align:center">
      <p>Don't have a login? <a href="./register/?sessionstatus=init">Click here</a> to register.</p>
    </div>
    <div class="col-md-4"></div>
  </div>
</div>