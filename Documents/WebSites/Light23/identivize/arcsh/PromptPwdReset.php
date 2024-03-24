<div class="row">
  <div class="col-md-12">
    <?php

      if ( $_SESSION['arc_pwdreset_message'] != '' )
      {
        echo '<div class="alert alert-success">' . $_SESSION['arc_pwdreset_message'] . '</div>';
      }
  
      if ( $_SESSION['arc_pwdreset_error_message'] != '' )
      {
        echo '<div class="alert alert-danger">' . $_SESSION['arc_pwdreset_error_message'] . '</div>';
      }
  
    ?>
  </div>
</div>
<div class="row pb-4">
  <div style="col-md-12">
    <p>
    <b>Forgot your password?</b><br />
    Enter your email address and click Submit.
    You will receive an email with a link to
    reset your password.
    </p>
    <form action="../../controller/" method="post">
      <input type="hidden" name="cmd" value="arc_pwdreset" />
      <input type="hidden" name="tenant_name" value="<?php echo $tenantName; ?>" />
      <div class="row mb-4">
        <div class="col-md-2"></div>
        <div class="col-md-8" style="text-align:left">
          <label for="login_id" class="form-label">Email:</label>
          <input type="email" class="form-control" id="login_id" name="login_id" maxlength="40" value="<?php echo $_SESSION['arc_pwdreset_login_id']; ?>" />
        </div>
        <div class="col-md-2"></div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <input type="submit" class="btn btn-primary" value="Submit">
        </div>
      </div>
    </form>
  </div>
</div>
<div class="row">
  <div class="col-md-12" style="text-align:center">
    <p><a href="./?cmd=login&sessionstatus=init">Back to log in page</a></p>
  </div>
</div>
