<?php

  session_start();

  require '../../GlobalVars.php';
  require '../../model/DBFunctions.php';
  require '../../model/InitDbConnect.php';

  $tenantName = '';

  if ( isset( $_SESSION['arc_tenant_name'] ) && $_SESSION['arc_tenant_name'] != '' )
  {
    $tenantName = $_SESSION['arc_tenant_name'];   
  }
  else
  {
    die( '<p>Error: Page cannot be accessed without a tenant name!</p>' );
  }

  $loginId = '';

  if ( isset( $_SESSION['arc_login_id'] ) && $_SESSION['arc_login_id'] != '' )
  {
    $loginId = $_SESSION['arc_login_id'];
  }
  else
  {
    header( 'Location: ../../arc/' . $tenantName );
    die();
  }

  $selectedRole = 'mgrtd';       // mgrtd: manager todo, acotd: access owner todo, pldtd: provisioning lead todo, prvtd: provisioner todo
  
  if ( isset( $_GET['srl'] ))
  {
    $selectedRole = $_GET['srl'];
  }

  $managerActionItems = array();
  $accessOwnerActionItems = array();
  $provisioningLeadActionItems = array();
  $provisionerActionItems = array();

  if ( $selectedRole == 'mgrtd' )
  {
    if ( isset( $_SESSION['arc_selected_mgr_action_items'] ))
    {
      $managerActionItems = $_SESSION['arc_selected_mgr_action_items'];
    }
  }
  else if ( $selectedRole == 'acotd' )
  {
    if ( isset( $_SESSION['arc_selected_ao_action_items'] ))
    {
      $accessOwnerActionItems = $_SESSION['arc_selected_ao_action_items'];
    }
  }
  else if ( $selectedRole == 'pldtd' )
  {
    if ( isset( $_SESSION['arc_selected_pl_action_items'] ))
    {
      $provisioningLeadActionItems = $_SESSION['arc_selected_pl_action_items'];
    }
  }
  else if ( $selectedRole == 'prvtd' )
  {
    if ( isset( $_SESSION['arc_selected_provisioner_action_items'] ))
    {
      $provisionerActionItems = $_SESSION['arc_selected_provisioner_action_items'];
    }
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
    <script src="../../scripts/default.js?prm=<?php echo rand(); ?>"></script>
    <title>Identivize Access Request Console</title>
  </head>

  <body>

    <?php
  
      require '../Navbar.php';

    ?>
    <div class="container-fluid pt-5"> <!-- pt-5 is a bootstrap feature to say "add a large top padding" -->
      <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10 p-4" style="background-color:#FFF">
          <h4>Re-assign Access Requests to Another Person</h4>
          
          <?php

            if ( $selectedRole == 'mgrtd' )
            {
              if ( count( $managerActionItems ) > 0 )
              {
                require './ManagerInput.php';
              }
            }
            else if ( $selectedRole == 'acotd' )
            {
              if ( count( $accessOwnerActionItems ) > 0 )
              {
                require './AccessOwnerInput.php';
              }
            }
            else if ( $selectedRole == 'pldtd' )
            {
              if ( count( $provisioningLeadActionItems ) > 0 )
              {
                require './ProvisionerInput.php';
              }
            }
            else if ( $selectedRole == 'prvtd' )
            {
              if ( count( $provisionerActionItems ) > 0 )
              {
                require './RevertToProvisioningLeadCommentInput.php';
              }
            }
            
          ?>
          
        </div>
        <div class="col-md-1"></div>
      </div>
    </div>  
  </body>
</html>