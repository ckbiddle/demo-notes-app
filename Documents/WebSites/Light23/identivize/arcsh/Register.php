<div class="row">
  <div class="col-md-12">
    <?php

      if ( $_SESSION['arc_registration_message'] != '' )
      {
        echo '<div class="alert alert-success">' . $_SESSION['arc_registration_message'] . '</div>';
      }
  
      if ( $_SESSION['arc_registration_error_message'] != '' )
      {
        echo '<div class="alert alert-danger">' . $_SESSION['arc_registration_error_message'] . '</div>';
      }
  
    ?>
  </div>
</div>
<div class="row pb-4">
  <div style="col-md-12">
    <p>Create a new account for the <?php echo $companyName; ?> Access Request Portal.</p>
    <form action="../../controller/" method="post">
      <input type="hidden" name="cmd" value="arc_registration" />
      <input type="hidden" name="tenant_name" value="<?php echo $tenantName; ?>" />
      <div class="row mb-2">
        <div class="col-md-2"></div>
        <div class="col-md-8" style="text-align:left">
          <label for="login_id" class="form-label">Email (This will be your Login ID):</label>
          <input type="email" class="form-control" id="login_id" name="login_id" maxlength="40" value="<?php echo $_SESSION['arc_registration_login_id']; ?>" />
        </div>
        <div class="col-md-2"></div>
      </div>
      <div class="row mb-2">
        <div class="col-md-2"></div>
        <div class="col-md-8" style="text-align:left">
          <label for="pswd1" class="form-label">Select a Password:</label>
          <input type="password" class="form-control" id="pswd1" name="pwd1" maxlength="80" value="<?php echo $_SESSION['arc_registration_pwd1']; ?>" />
        </div>
        <div class="col-md-2"></div>
      </div>
      <div class="row mb-2">
        <div class="col-md-2"></div>
        <div class="col-md-8" style="text-align:left">
          <label for="pswd2" class="form-label">Re-enter your Password:</label>
          <input type="password" class="form-control" id="pswd2" name="pwd2" maxlength="80" value="<?php echo $_SESSION['arc_registration_pwd2']; ?>" />
        </div>
        <div class="col-md-2"></div>
      </div>
      <div class="row mb-2">
        <div class="col-md-2"></div>
        <div class="col-md-8" style="text-align:left">
          <label for="first_name" class="form-label">Enter your First Name:</label>
          <input type="text" class="form-control" id="first_name" name="first_name" maxlength="40" value="<?php echo $_SESSION['arc_registration_first_name']; ?>" />
        </div>
        <div class="col-md-2"></div>
      </div>
      <div class="row mb-2">
        <div class="col-md-2"></div>
        <div class="col-md-8" style="text-align:left">
          <label for="last_name" class="form-label">Enter your Last Name:</label>
          <input type="text" class="form-control" id="last_name" name="last_name" maxlength="40" value="<?php echo $_SESSION['arc_registration_last_name']; ?>" />
        </div>
        <div class="col-md-2"></div>
      </div>
      <div class="row mb-2">
        <div class="col-md-2"></div>
        <div class="col-md-8" style="text-align:left">
          <label for="job_title" class="form-label">Enter your Job Title:</label>
          <input type="text" class="form-control" id="job_title" name="job_title" maxlength="40" value="<?php echo $_SESSION['arc_registration_job_title']; ?>" />
        </div>
        <div class="col-md-2"></div>
      </div>
      <div class="row mb-4">
        <div class="col-md-2"></div>
        <div class="col-md-8" style="text-align:left">
          <label for="company_name" class="form-label">Enter your Company Name:</label>
          <input type="text" class="form-control" id="company_name" name="company_name" maxlength="80" value="<?php echo $_SESSION['arc_registration_company_name']; ?>" />
        </div>
        <div class="col-md-2"></div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <input type="submit" class="btn btn-primary" value="Sign Up">
        </div>
      </div>
    </form>
  </div>
</div>
<div class="row">
  <div class="col-md-12" style="text-align:center">
    <p>Already have an account? <a href="./?cmd=login&sessionstatus=init">Click here</a> to log in.</p>
  </div>
</div>
