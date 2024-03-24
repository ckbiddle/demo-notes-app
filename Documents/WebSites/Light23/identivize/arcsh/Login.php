<div class="row">
  <div class="col-md-12">
    <?php

      if ( $_SESSION['arc_login_message'] != '' )
      {
        echo '<div class="alert alert-success">' . $_SESSION['arc_login_message'] . '</div>';
      }
  
      if ( $_SESSION['arc_login_error_message'] != '' )
      {
        echo '<div class="alert alert-danger">' . $_SESSION['arc_login_error_message'] . '</div>';
      }
  
    ?>
  </div>
</div>
<div class="row pb-4">
  <div style="col-md-12">
    <p>Login to start requesting access</p>
    <form action="../../controller/" method="post">
      <input type="hidden" name="cmd" value="arc_login" />
      <input type="hidden" name="tenant_name" value="<?php echo $tenantName; ?>" />
      <div class="row mb-3">
        <div class="col-md-2"></div>
        <div class="col-md-8" style="text-align:left">
          <label for="login_id" class="form-label">Email:</label>
          <input type="email" class="form-control" id="login_id" name="login_id" maxlength="40" value="<?php echo $_SESSION['arc_login_login_id']; ?>" />
        </div>
        <div class="col-md-2"></div>
      </div>
      <div class="row mb-4">
        <div class="col-md-2"></div>
        <div class="col-md-8" style="text-align:left">
          <label for="pswd" class="form-label">Password:</label>
          <input type="password" class="form-control" id="pswd" name="pwd" maxlength="80" value="<?php echo $_SESSION['arc_login_pwd']; ?>" />
        </div>
        <div class="col-md-2"></div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <input type="submit" class="btn btn-primary" value="Log In">
        </div>
      </div>
    </form>
  </div>
</div>
<div class="row">
  <div class="col-md-12" style="text-align:center">
    <p>Forgot your password? <a href="./?cmd=promptpwdreset&sessionstatus=init">Click here to reset.</a></p>
  </div>
</div>
<div class="row">
  <div class="col-md-12" style="text-align:center">
    <p>First time visiting this portal? <a href="./?cmd=register&sessionstatus=init">Click here to register now.</a></p>
  </div>
</div>