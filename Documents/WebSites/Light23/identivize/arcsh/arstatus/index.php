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

  $requestStatuses = getAccessRequestStatusesByUserId( $conn, $loginId, $tenantName );

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
          <h4>Status Tracker</h4>
          <table class="table table-striped">
            <tr>
              <th>Request ID</th>
              <th>Requested Date</th>
              <th>Current Status</th>
              <th>Request Details</th>
            </tr>
            <?php
            
              foreach( $requestStatuses as $row )
              {
                $requestId = $row['request_id'];
                $requestDate = $row['request_date'];
                $status = $row['status'];
                $accessNames = getRequestAccessNameStringByRequestId( $conn, $requestId, $tenantName );
                
                echo '<tr>' .
                     '  <td>' . $requestId . '</td>' .
                     '  <td>' . $requestDate . '</td>' .
                     '  <td>' . $status . '</td>' .
                     '  <td>' . $accessNames . '</td>' .
                     '</tr>';
                
              }
            
            ?>
          </table>
          <p><a href="../">Click here</a> to request more access.</p>
        </div>
        <div class="col-md-1"></div>
      </div>
    </div>  
  </body>
</html>