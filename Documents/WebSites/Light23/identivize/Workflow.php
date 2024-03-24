<?php

  if ( basename( $_SERVER['PHP_SELF'] ) === basename( __FILE__ ))
  {
    die( '<p>You cannot access this page directly!</p>' );
  }

  if ( isset( $_SESSION['idv_login_id'] ) && $_SESSION['idv_login_id'] != '' )
  {
    $errorMessage = '';
   
    $userDetails = getUserDetails( $conn, $_SESSION['idv_login_id'], $errorMessage );
    
    if ( $errorMessage != '' )
    {
      die( $errorMessage );     
    }
  }

?>

<div class="container-fluid p-5 pt-4"> <!-- pt-5 is a bootstrap feature to say "add a large top padding" -->
  <div class="row">
    <div class="col-md-12">
      <h4>Home</h4>
      <p>
        <div style="padding-bottom:5px">The Access Request Portal for your organization is:</div>
        <a href="<?php echo ( $appRoot . '/arc/' . $userDetails['tenant_name'] ); ?>/?sessionstatus=init" style="text-decoration:none">
          https://<?php echo ( $hostingDomain . $appRoot . '/arc/' . $userDetails['tenant_name'] ); ?>
        </a>
      </p>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
      <!-- <a href="./workflow/?cmd=crwkflow&sessionstatus=init" class="btn p-3" style="background-color:#EFF6FF;color:#03C;width:100%;font-size:15px"> -->
      <a href="./workflow/?cmd=crwkflow&sessionstatus=init" class="btn p-3 shadow-sm" style="background-color:#EFF6FF;color:#03C;width:100%;font-size:15px;">
        Create a new request workflow
      </a>
    </div>
    <div class="col-md-4">
      <a href="./workflow/?cmd=rvwkflow&sessionstatus=init" class="btn p-3 shadow-sm" style="background-color:#EFF6FF;color:#03C;width:100%;font-size:15px">
        Review/Modify an existing workflow
      </a>
    </div>
    <div class="col-md-4"></div>
  </div>

  <!--
  <div class="row pb-3">
    <div class="col-md-4"></div>
    <div class="col-md-4" style="text-align:center">
      <h3>Welcome, <?php echo $userDetails['first_name'] . ' ' . $userDetails['last_name']; ?>!</h3>
      <p>
        Your Access Request Console is:<br />
        <a href="<?php echo ( $appRoot . '/arc/' . $userDetails['tenant_name'] ); ?>/?sessionstatus=init">
          https://<?php echo ( $hostingDomain . $appRoot . '/arc/' . $userDetails['tenant_name'] ); ?>
        </a>
      </p>
    </div>
    <div class="col-md-4"></div>
  </div>
  <div class="row pb-2">
    <div class="col-md-4"></div>
    <div class="col-md-4" style="text-align:center">
      <a href="./workflow/?cmd=crwkflow&sessionstatus=init" class="btn btn-success" style="width:100%">Create a new access request workflow</a>
    </div>
    <div class="col-md-4"></div>
  </div>
  <div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4" style="text-align:center">
      <a href="./workflow/?cmd=rvwkflow&sessionstatus=init" class="btn btn-success" style="width:100%">Review or Modify an existing workflow</a>
    </div>
    <div class="col-md-4"></div>
  </div>
  -->
</div>