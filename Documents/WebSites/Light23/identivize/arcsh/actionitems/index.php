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

  // crd: display dashboard cards
  // lst: display request line items in a table based on selected card
  $displayMode = 'crd';
  
  if ( isset( $_GET['dmd'] ))
  {
    $displayMode = $_GET['dmd'];
  }

  // mgrtd: manager todos
  // acotd: access owner todos
  // pldtd: provisioning lead todos
  // prvtd: provisioner todos
  // mgrcp: manager completed items
  // acocp: access owner completed items
  $selectedRole = 'mgrtd';
  
  if ( isset( $_GET['srl'] ))
  {
    $selectedRole = $_GET['srl'];
  }

  $managerApprovals = array();
  $accessOwnerApprovals = array();
  $provisioningLeadAssignments = array();
  // $provisionerTasks = array();
  
  $managerApproved = array();
  $accessOwnerApproved = array();

  if ( $displayMode == 'crd' )
  {
    // To Do items
    $managerApprovals = getRequestLineItemsByManagerAndManagerAction( $conn, $loginId, $tenantName, 'PENDING' );
    $accessOwnerApprovals = getRequestLineItemsByAccessOwnerAndAccessOwnerAction( $conn, $loginId, $tenantName, 'PENDING' );
    $provisioningLeadAssignments = getApprovedPendingRequestLineItemsByProvisioner( $conn, $loginId, $tenantName );
    // $provisioningLeadAssignments = getManagerAndAoApprovedRequestLineItemsByProvisioningLead( $conn, $loginId, $tenantName, 'PENDING' );
    // $provisionerTasks = getRequestLineItemsByProvisionerAndProvisioningStatus( $conn, $loginId, $tenantName, 'ASSIGNED' );
    
    // Completed items
    $managerApproved = getRequestLineItemsByManagerAndManagerActionOverPastYear( $conn, $loginId, $tenantName, 'APPROVED' );
    $accessOwnerApproved = getRequestLineItemsByAccessOwnerAndAccessOwnerActionOverPastYear( $conn, $loginId, $tenantName, 'APPROVED' );

  }
  else if ( $displayMode == 'lst' )
  {
    if ( $selectedRole == 'mgrtd' )
    {
      $managerApprovals = getRequestLineItemsByManagerAndManagerAction( $conn, $loginId, $tenantName, 'PENDING' );
    }
    else if ( $selectedRole == 'acotd' )
    {
      $accessOwnerApprovals = getRequestLineItemsByAccessOwnerAndAccessOwnerAction( $conn, $loginId, $tenantName, 'PENDING' );
    }
    else if ( $selectedRole == 'pldtd' )
    {
      // $provisioningLeadAssignments = getManagerAndAoApprovedRequestLineItemsByProvisioningLead( $conn, $loginId, $tenantName, 'PENDING' );
      $provisioningLeadAssignments = getApprovedPendingRequestLineItemsByProvisioner( $conn, $loginId, $tenantName );
    }
    // else if ( $selectedRole == 'prvtd' )
    // {
    //   $provisionerTasks = getRequestLineItemsByProvisionerAndProvisioningStatus( $conn, $loginId, $tenantName, 'ASSIGNED' );
    // }
    else if ( $selectedRole == 'mgrcp' )  // completed manager items
    {
      $managerApproved = getRequestLineItemsByManagerAndManagerActionOverPastYear( $conn, $loginId, $tenantName, 'APPROVED' );
    }
    else if ( $selectedRole == 'acocp' )  // completed access owner items
    {
      $accessOwnerApproved = getRequestLineItemsByAccessOwnerAndAccessOwnerActionOverPastYear( $conn, $loginId, $tenantName, 'APPROVED' );
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

  <body style="background-color:#FFF">

    <?php
  
      require '../Navbar.php';

    ?>
    <div class="container-fluid p-5 pt-4"> <!-- pt-5 is a bootstrap feature to say "add a large top padding" -->
      <div class="row">

        <?php
        
        if ( $displayMode == 'crd' )
        {
         
        ?>
        
          <div class="col-md-4 p-2">
          
            <div class="p-3 rounded" style="width:100%;background-color:#EEF">            
            
              <h5 style="font-weight:bold">Pending Access Requests</h5>
  
              <?php           
      
                // $noActionItems = true;

                $crdDataLabel = 'Manager Actions';
                $crdDataArray = $managerApprovals;
                $crdDataDescription = 'Approve access requests as a manager';
                $crdSrl = 'mgrtd';

                if ( count( $managerApprovals ) > 0 )
                {
                  // $noActionItems = false;
                  $cardActive = true;
                }
                else
                {
                  $cardActive = false;
                }
                
                require './ToDoCard.php';

                $crdDataLabel = 'Access Owner Actions';
                $crdDataArray = $accessOwnerApprovals;
                $crdDataDescription = 'Approve access requests as an access owner';
                $crdSrl = 'acotd';

                if ( count( $accessOwnerApprovals ) > 0 )
                {
                  // $noActionItems = false;
                  $cardActive = true;
                }
                else
                {
                  $cardActive = false;
                }

                require './ToDoCard.php';

                if ( count( $provisioningLeadAssignments ) > 0 )
                {
                  // $noActionItems = false;
                  
                  $cardActive = true;
                  $crdDataLabel = 'Provisioning Actions';
                  $crdDataArray = $provisioningLeadAssignments;
                  $crdDataDescription = 'Assign provisioning tasks to others in your team as a provisioning lead';
                  $crdSrl = 'pldtd';

                  require './ToDoCard.php';
                }

                /*
                 * Chris Biddle, 01/30/2024
                 * Per our meeting of 01/30/2024, we are merging provisioning lead and provisioner actions
                 * into one card labeled 'Provisioning Actions', so we don't need a separate card for
                 * provisioner.
                 
                if ( count( $provisionerTasks ) > 0 )
                {
                  // $noActionItems = false;
                  
                  $cardActive = true;
                  $crdDataLabel = 'Fulfill Access Provisioning';
                  $crdDataArray = $provisionerTasks;
                  $crdDataDescription = 'Grant the requested access and fulfill access provisioning requests';
                  $crdSrl = 'prvtd';

                  require './ToDoCard.php';
                }
                */
      
                // if ( $noActionItems )
                // {
                //   echo '<p>You have no pending to do items.</p>';             
                // }
      
              ?>
        
            </div>
            
          </div>          
          
          <div class="col-md-4 p-2">
          
            <div class="p-3 rounded" style="width:100%;background-color:#EEF">            
          
              <h5 style="font-weight:bold">Completed</h5>
  
              <?php           
      
                // $noCompletedItems = true;

                $crdDataLabel = 'Manager Actions';
                $crdDataArray = $managerApproved;
                $crdDataDescription = 'Access requests you approved as a manager over the last one year';
                $crdSrl = 'mgrcp';

                if ( count( $managerApproved ) > 0 )
                {
                  // $noCompletedItems = false;
                  $cardActive = true;
                }
                else
                {
                  $cardActive = false;
                }

                require './ToDoCard.php';

                $crdDataLabel = 'Access Owner Actions';
                $crdDataArray = $accessOwnerApproved;
                $crdDataDescription = 'Access requests you approved as an access owner over the last one year';
                $crdSrl = 'acocp';

                if ( count( $accessOwnerApproved ) > 0 )
                {
                  // $noCompletedItems = false;
                  $cardActive = true;
                }
                else
                {
                  $cardActive = false;
                }
                
                require './ToDoCard.php';
      
                // if ( $noCompletedItems )
                // {
                //   echo '<p>You have no completed items over the past year.</p>';             
                // }
      
              ?>
            
            </div>
            
          </div>   
            
          <div class="col-md-4"></div>
          
        <?php
        
        }  // end if ( $displayMode == 'crd' )
        else if ( $displayMode == 'lst' )
        {
        
        ?>
              
          <div class="col-md-1"></div>
          <div class="col-md-10 p-4" style="background-color:#FFF">
            <h4>Action Items</h4>
        
        <?php
        
          if ( $selectedRole == 'mgrtd' )
          {
            if ( count( $managerApprovals ) > 0 )
            {
              require './MgrApprovals.php';
            }
            else
            {
              echo '<p>You have no pending requests to approve as a manager.</p>';
            }
            
            require './ProcessedManagerItems.php';
          }
          else if ( $selectedRole == 'acotd' )
          {
            if ( count( $accessOwnerApprovals ) > 0 )
            {
              require './AccessOwnerApprovals.php';
            }
            else
            {
              echo '<p>You have no pending requests to approve as an access owner.</p>';
            }
            
            require './ProcessedAccessOwnerItems.php';
          }
          else if ( $selectedRole == 'pldtd' )
          {
            if ( count( $provisioningLeadAssignments ) > 0 )
            {
              require './ProvisioningLeadAssignments.php';
            }
            else
            {
              echo '<p>You have no pending requests to assign as a provisioning lead.</p>';
            }
          }
          // else if ( $selectedRole == 'prvtd' )
          // {
          //   if ( count( $provisionerTasks ) > 0 )
          //   {
          //     require './ProvisionerTasks.php';
          //   }
          //   else
          //   {
          //     echo '<p>You have no pending tasks as a provisioner.</p>';
          //   }
          // }
          else if ( $selectedRole == 'mgrcp' )  // Manager items completed over the past year
          {
            if ( count( $managerApproved ) > 0 )
            {
              echo '<p>Here are the access requests you approved as a manager over the last year.</p>';
              $items = $managerApproved;
              require './ItemsTable.php';
            }
            else
            {
              echo '<p>You have no access requests you approved as a manager over the last year.</p>';
            }
          }
          else if ( $selectedRole == 'acocp' )  // Access owner items completed over the past year
          {
            if ( count( $accessOwnerApproved ) > 0 )
            {
              echo '<p>Here are the access requests you approved as an access owner over the last year.</p>';
              $items = $accessOwnerApproved;
              require './ItemsTable.php';
            }
            else
            {
              echo '<p>You have no access requests you approved as an access owner over the last year.</p>';
            }
          }
          
        ?>
        
          </div>
          <div class="col-md-1"></div>
      
        <?php
        
        }  // end if ( $displayMode == 'lst' )
        
        ?>
        
      </div> <!-- end class="row" -->
    </div> <!-- end class="container-fluid pt-5" -->
  </body>
</html>