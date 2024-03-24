<?php

  if ( basename( $_SERVER['PHP_SELF'] ) === basename( __FILE__ ))
  {
    die( '<p>You cannot access this page directly!</p>' );
  }

  $selectedWorkflow = '';
  $workflowDetails = array();

  if ( isset( $_SESSION['rvwkflow_selected_workflow'] ) && $_SESSION['rvwkflow_selected_workflow'] != '' )
  {
    $selectedWorkflow = $_SESSION['rvwkflow_selected_workflow'];
    
    $workflowMaster = getWorkflowByWorkflowName( $conn, $_SESSION['idv_tenant_name'], $selectedWorkflow );
    $workflowDetails = getWorkflowDetailsByWorkflowName( $conn, $_SESSION['idv_tenant_name'], $selectedWorkflow );
  }

?>

<?php

  if ( sizeof( $workflowDetails ) == 0 )
  {
   
?>

    <div class="row mb-3">
      <div class="col-md-2"></div>
      <div class="col-md-8">
        <h4>Modify an Existing Access Request Workflow</h4>
        <p>
          To modify the details, simply download the CSV using the
          &ldquo;Download CSV&rdquo; button, make your edits in the
          CSV, then click &ldquo;Modify This Workflow&rdquo;. There
          you can again upload the CSV file, replacing the details
          that were previously there.
        </p>      
      </div>
      <div class="col-md-2"></div>
    </div>

<?php

  }

?>

<div class="row mb-3">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <form action="../controller/" method="post" enctype="multipart/form-data" >
      <input type="hidden" name="cmd" value="rvwkflow" />
      <!-- <input type="hidden" name="tenant_name" value="<?php echo $_SESSION['idv_tenant_name']; ?>" /> -->
      <div class="row mb-2">
        <div class="col-md-8">
          <p>Select the name of the workflow you wish to review or modify:</p>
        </div>
        <div class="col-md-4">
          <select class="form-select" id="workflow_name" name="workflow_name" onchange="this.form.submit()">
            <option value="NA">(Please make a selection)</option>
            <?php
    
              foreach( $workflows as $workflow )
              {
                if ( $workflow['workflow_name'] == $selectedWorkflow )
                {
                  echo '<option value="' . $workflow['workflow_name'] . '" selected>' . $workflow['workflow_name'] . '</option>';
                }
                else
                {
                  echo '<option value="' . $workflow['workflow_name'] . '" >' . $workflow['workflow_name'] . '</option>';
                }
              }

            ?>
          </select>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-2"></div>
</div>
<?php

  if ( sizeof( $workflowDetails ) > 0 )
  {
   
?>
    <div class="row mb-2 p-3" style="background-color:#FFF">
      <div class="col-md-2">
        <b>Workflow Description:</b>
      </div>
      <div class="col-md-4">
        <?php echo $workflowMaster['workflow_description']; ?>
      </div>
      <div class="col-md-2">
        <form action="../controller/" method="post">
          <input type="hidden" name="cmd" value="dnldwkflow" />
          <input type="hidden" name="tenant_name" value="<?php echo $_SESSION['idv_tenant_name']; ?>" />
          <input type="hidden" name="workflow_name" value="<?php echo $_SESSION['rvwkflow_selected_workflow']; ?>" />
          <input type="submit" class="btn btn-primary" value="Download CSV">
        </form>
      </div>
      <div class="col-md-2">
        <form action="../controller/" method="post">
          <input type="hidden" name="cmd" value="updwkflowinput" />
          <input type="hidden" name="tenant_name" value="<?php echo $_SESSION['idv_tenant_name']; ?>" />
          <input type="hidden" name="workflow_name" value="<?php echo $_SESSION['rvwkflow_selected_workflow']; ?>" />
          <input type="submit" class="btn btn-primary" value="Modify This Workflow">
        </form>
      </div>
      <div class="col-md-2">
        <form action="../controller/" method="post">
          <input type="hidden" name="cmd" value="deleteworkflow" />
          <input type="hidden" name="tenant_name" value="<?php echo $_SESSION['idv_tenant_name']; ?>" />
          <input type="hidden" name="workflow_name" value="<?php echo $_SESSION['rvwkflow_selected_workflow']; ?>" />
          <input type="submit" class="btn btn-danger" value="Delete This Workflow" onclick="return confirm( 'Are you sure you want to delete this workflow?' )" >
        </form>
      </div>
    </div>
    </div>
    <div class="row">
      <!-- <div class="col-md-2"></div> -->
      <div class="col-md-12">
        <table class="table table-striped">
          <tr>
            <th>Access Name</th>
            <th>Description</th>
            <th>Application Name</th>
            <th>Access Source</th>
            <th>Risk Rating</th>
            <th>Access Owner Name</th>
            <th>Access Owner Email</th>
            <th>Manager Approval Required?</th>
            <th>Access Owner Approval Required?</th>
          </tr>
          <?php
          
            foreach( $workflowDetails as $row )
            {
              echo '<tr>' .
                   '<td>' . $row['access_name'] . '</td>' .
                   '<td>' . $row['access_description'] . '</td>' .
                   '<td>' . $row['application_name'] . '</td>' .
                   '<td>' . $row['access_source'] . '</td>' .
                   '<td>' . $row['risk_rating'] . '</td>' .
                   '<td>' . $row['access_owner_name'] . '</td>' .
                   '<td>' . $row['access_owner_email'] . '</td>' .
                   '<td>' . $row['manager_approval_required'] . '</td>' .
                   '<td>' . $row['access_owner_approval_required'] . '</td>' .
                   '</tr>';
            }
            
          ?>
        </table>
      </div>
      <!-- <div class="col-md-2"></div> -->
    </div>

<?php

  }
  
?>
