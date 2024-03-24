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
        <div class="col-md-10 p-4" style="background-color:#FFF">
          <h4>Search Results</h4>
          <p>Results matching your search phrase</p>
          <form action="../../controller/" method="post">
            <input type="hidden" name="cmd" value="capture_selected_access" />

            <?php

              $searchResultIndex = 0;

              foreach( $accessRequestSearchResults as $row )
              {
                $approvals = 'None';
               
                if ( $row['manager_approval_required'] == 'Y' )
                {
                  $approvals = 'Manager';
                  
                  if ( $row['access_owner_approval_required'] == 'Y' )
                  {
                    $approvals = 'Manager and Access Owner';
                  }
                }
                else
                {
                  if ( $row['access_owner_approval_required'] == 'Y' )
                  {
                    $approvals = 'Access Owner';
                  }
                }
               
                echo '<div class="row" style="background-color:#DDF">' .
                     '  <div class="col-md-1">' .
                     '    <div class="form-check">' .
                     '      <input class="form-check-input" type="checkbox" name="search_result_index[]" value="' . $searchResultIndex . '" ' . ( isset( $row['selected'] ) && $row['selected'] === true ? 'checked' : '' ) . '>' .
                     '    </div>' .
                     '  </div>' .
                     '  <div class="col-md-11"><b>' . $row['access_name'] . '</b></div>' .
                     '</div>' .
                     '<div class="row">' .
                     '  <div class="col-md-1 pb-4"></div>' .
                     '  <div class="col-md-11 pb-4">' . $row['access_description'] . '<br>' .
                     '    Application Name: ' . $row['application_name'] . '<br>' .
                     '    Application Owner: ' . $row['access_owner_name'] . '<br>' .
                     '    Risk Rating: ' . $row['risk_rating'] . '<br>' .
                     '    Approvals required: ' . $approvals .
                     //   ' (' . $row['manager_approval_required'] . ':' . $row['access_owner_approval_required'] . ')' .
                     '  </div>' .
                     '</div>';
                     
                 $searchResultIndex++;
              }
          
            ?>
            
            <div class="row">
              <div class="col-md-12" style="text-align:center">
                <button type="submit" class="btn btn-primary">Request Selected Access</button>
              </div>
            </div>
          
          </form>

        </div>
        <div class="col-md-1"></div>
      </div>
    </div>  
  </body>
</html>