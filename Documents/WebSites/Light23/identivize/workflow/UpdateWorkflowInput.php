<?php

  if ( basename( $_SERVER['PHP_SELF'] ) === basename( __FILE__ ))
  {
    die( '<p>You cannot access this page directly!</p>' );
  }
  
?>

<div class="row">
  <div class="col-md-4"></div>
  <div class="col-md-4">
    <form action="../controller/" method="post" enctype="multipart/form-data" >
      <input type="hidden" name="cmd" value="updwkflow" />
      <input type="hidden" name="tenant_name" value="<?php echo $_SESSION['idv_tenant_name']; ?>" />
      <input type="hidden" name="workflow_name" value="<?php echo $_SESSION['wkflow_workflow_name']; ?>" />
      <div class="mb-2">
        Name of Workflow: <?php echo $_SESSION['wkflow_workflow_name']; ?>
      </div>
      <div class="mb-2">
        <label for="workflow_description" class="form-label">Description of Workflow:</label>
        <textarea class="form-control" id="workflow_description" name="workflow_description" maxlength="1000" /><?php echo $_SESSION['wkflow_workflow_description']; ?></textarea>
      </div>
      <div class="mb-3">
        <label for="workflow_data" class="form-label">Access Details:</label>
        <input type="file" class="form-control" name="workflow_data" id="workflow_data">
      </div>
      <div class="row">
        <div class="col-md-6">
          <div style="text-align:right">
            <input type="submit" class="btn btn-success" value="Submit">
          </div>
        </div>
        <div class="col-md-6">
          <div style="text-align:left">
            <a href="../controller/?cmd=cancelupdwkflowinput" class="btn btn-danger">Cancel</a>
          </div>
        </div>
    </form>
  </div>
  <div class="col-md-4"></div>
</div>        
