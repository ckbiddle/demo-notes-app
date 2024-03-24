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

  if ( !isset( $_SESSION['arc_login_id'] ) || $_SESSION['arc_login_id'] == '' )
  {
    header( 'Location: ../../arc/' . $tenantName );
    die();
  }

  $requestId = 0;

  if ( isset( $_GET['request_id'] ) && $_GET['request_id'] != '' )
  {
    $requestId = $_GET['request_id'];
  }

  $requestLineItems = getRequestLineItemsByRequestId( $conn, $requestId, $tenantName );

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
          <p>
          Congratulations! Your request for the following access is assigned <b>Request ID <?php echo $requestId; ?></b>
          and is now routed to the appropriate approvers. You will receive an email after your request is fully approved
          by the approvers and fulfilled by the provisioning team.
          </p>
          
          <?php
          
            echo '<h4>Request ID ' . $requestId . '</h4>';
          
            foreach( $requestLineItems as $row )
            {
              echo '<p>' .
                   '<b>' . $row['request_line_id'] . '. ' . $row['access_name'] . '</b><br>' .
                   $row['access_description'] .
                   '</p>';
            }
          
          ?>
          
          <!--
          <pre>
            <?php print_r( $requestLineItems ); ?>
          </pre>
          -->

          <p><a href="../arstatus">Click here</a> to view the status of your access requests.</p>
        </div>
        <div class="col-md-1"></div>
      </div>
    </div>  
  </body>
</html>