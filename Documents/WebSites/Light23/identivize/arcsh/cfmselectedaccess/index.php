<?php

  session_start();

  require '../../GlobalVars.php';

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
  }

  $accessRequestSearchResults = array();

  if ( isset( $_SESSION['arc_ar_search_results'] ) && sizeof( $_SESSION['arc_ar_search_results'] ) > 0 )
  {
    $accessRequestSearchResults = $_SESSION['arc_ar_search_results'];
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
    <title>Identivize Access Request Console</title>
  </head>

  <body>

    <?php
  
      require '../Navbar.php';

    ?>
    <div class="container-fluid pt-5"> <!-- pt-5 is a bootstrap feature to say "add a large top padding" -->
      <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10 p-4 rounded" style="background-color:#FFF">
          <h4>Access Requested</h4>
          <hr />
          <?php
    
            if ( isset( $_SESSION['arc_ar_message'] ) && $_SESSION['arc_ar_message'] != '' )
            {
              echo '<div class="alert alert-success">' . $_SESSION['arc_ar_message'] . '</div>';
            }
        
            if ( isset( $_SESSION['arc_ar_error_message'] ) && $_SESSION['arc_ar_error_message'] != '' )
            {
              echo '<div class="alert alert-danger">' . $_SESSION['arc_ar_error_message'] . '</div>';
            }
        
          ?>
          <form action="../../controller/" method="post">
            <input type="hidden" name="cmd" value="submit_access_request" />

            <?php

              $inx = 1;

              foreach( $_SESSION['arc_selected_access'] as $index )
              {
                echo '<div class="row">' .
                     '  <div class="col-md-12 pb-4">' .
                     '    <b>' . $inx . '. ' . $accessRequestSearchResults[$index]['access_name'] . '</b><br>' .
                          $accessRequestSearchResults[$index]['access_description'] .
                     '  </div>' .
                     '</div>';
                     
                 $inx++;
              }
          
            ?>

            <div class="row mb-2">
              <div class="col-md-8">
                <label for="justification" class="pb-2">
                Your request will be routed to your manager and other approvers along with the business justification. You will
                receive the requested access after it is fully approved.
                </label>
              </div>
              <div class="col-md-4"></div>
            </div>
            <div class="row">
              <div class="col-md-4 pb-3">
                <label for="mgrname" class="form-label">Your Manager&rsquo;s Name:</label>
                <input type="text" class="form-control" id="mgrname" name="mgrname" value="<?php echo $_SESSION['arc_ar_mgr_name']; ?>" maxlength="250" />
              </div>
              <div class="col-md-4 pb-3">
                <label for="mgrname" class="form-label">Your Manager&rsquo;s Email Address:</label>
                <input type="email" class="form-control" id="mgremail" name="mgremail" value="<?php echo $_SESSION['arc_ar_mgr_email']; ?>" maxlength="40" />
              </div>
              <div class="col-md-4"></div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <p>Why do you need this access? Enter a Business Justification below.</p>
              </div>
            </div>
            <div class="row">
              <div class="col-md-8">
                <textarea class="form-control" rows="4" id="justification" name="justification" maxlength="250" ><?php echo $_SESSION['arc_ar_justification']; ?></textarea>
              </div>            
              <div class="col-md-4" style="text-align:center;padding-top:40px">
                <button type="submit" class="btn btn-primary">Submit Access Request</button>
              </div>
            </div>           
          
          </form>

        </div>
        <div class="col-md-1"></div>
      </div>
    </div>  
  </body>
</html>