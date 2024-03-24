<?php

  session_start();

  require '../GlobalVars.php';
  require '../model/DBFunctions.php';
  require '../model/InitDbConnect.php';

  if ( !isset( $_SESSION['idv_login_id'] ) || $_SESSION['idv_login_id'] == '' )
  {
    header( 'Location: ' . $appRoot . '/' );
  }

  $command = 'crwkflow';   // crwkflow for 'create workflow', rvwkflow for 'review workflow', updwkflowinput for 'update workflow input'

  if ( isset( $_GET['cmd'] ))
  {
    $command = $_GET['cmd'];
  }

  $workflows = array();

  if ( $command == 'rvwkflow' )
  {
    $workflows = getWorkflowsByTenantName( $conn, $_SESSION['idv_tenant_name'] );
  }

  // This GET parameter is added to the link buttons in /Workflow.php to ensure
  // that if the user comes to this page from those buttons, the session variables
  // that hold the values to be displayed in the input boxes, and the error message,
  // are empty.
  if ( isset( $_GET['sessionstatus'] ) && $_GET['sessionstatus'] == 'init' )
  {
    $_SESSION['wkflow_workflow_name'] = '';
    $_SESSION['wkflow_workflow_description'] = '';
    $_SESSION['wkflow_error_message'] = '';
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
    <script src="../scripts/default.js"></script>
    <title>Identivize</title>
  </head>
  <body>
    <?php
  
      require '../Navbar.php';

    ?>
    <div class="container-fluid pt-5"> <!-- pt-5 is a bootstrap feature to say "add a large top padding" -->
      <div class="row">
        <div class="col"></div>
        <div class="col">
          <?php
    
            if ( $_SESSION['wkflow_error_message'] != '' )
            {
              echo '<div class="alert alert-danger">' . $_SESSION['wkflow_error_message'] . '</div>';
            }
          
          ?>
        </div>
        <div class="col"></div>
      </div>
      <?php
      
        if ( $command == 'crwkflow' )
        {
          require './CreateWorkflow.php';
        }
        else if ( $command == 'rvwkflow' )
        {
          require './ReviewWorkflow.php';
        }
        else if ( $command == 'updwkflowinput' )
        {
          require './UpdateWorkflowInput.php';         
        }

      ?>
    </div>
  </body>
</html>