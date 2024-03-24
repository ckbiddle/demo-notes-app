<?php

  if ( basename( $_SERVER['PHP_SELF'] ) === basename( __FILE__ ))
  {
    die( '<p>You cannot access this page directly!</p>' );
  }
  
?>

<div class="row">
  <div class="col-md-4 ps-5 pe-5">
    <h4>Create a New Access Request Workflow</h4>
    <p>
      A workflow is a group of access privileges to software that a member of your
      organization may need. Access privileges needed for a specific application
      could be considered a single workflow.<br /><br />
      <a href="../identivize_data_uploader.csv">Click here</a> for a template CSV file
      with some test data.
    </p>
  </div>
  <div class="col-md-4 p-4 bg-white rounded">
    <form action="../controller/" method="post" enctype="multipart/form-data" >
      <input type="hidden" name="cmd" value="crwkflow" />
      <input type="hidden" name="tenant_name" value="<?php echo $_SESSION['idv_tenant_name']; ?>" />
      <div class="mb-2">
        <label for="workflow_name" class="form-label">Name of Workflow:</label>
        <input type="text" class="form-control" id="workflow_name" name="workflow_name" maxlength="40" value="<?php echo $_SESSION['wkflow_workflow_name']; ?>" />
      </div>
      <div class="mb-2">
        <label for="workflow_description" class="form-label">Description of Workflow:</label>
        <textarea class="form-control" id="workflow_description" name="workflow_description" maxlength="1000" /><?php echo $_SESSION['wkflow_workflow_description']; ?></textarea>
      </div>
      <div class="mb-3">
        <label id="workflow_data_label" for="workflow_data" class="form-label">Access Details <a href="javascript:showCsvInfo()" style="text-decoration:none">(Show more info)</a>:</label>
        <input type="file" class="form-control" name="workflow_data" id="workflow_data">
      </div>
      <div class="mb-3" id="csv_info" style="display:none">
        <p>
          Using the file selector above, upload a CSV file with the following details of the
          access that can be requested. Note that all fields are required except for Secondary
          Provisioning Lead Name and Secondary Provisioning Lead Email:
        </p>
        <table class="table" style="font-size:small">
          <tr><td style="width:25%">Column 1:</td><td style="width:75%">Access Name</td></tr>
          <tr><td>Column 2:</td><td>Access Description</td></tr>
          <tr><td>Column 3:</td><td>Application Name</td></tr>
          <tr><td>Column 4:</td><td>Access Source</td></tr>
          <tr><td>Column 5:</td><td>Risk Rating (High, Medium, or Low)</td></tr>
          <tr><td>Column 6:</td><td>Access Owner Name</td></tr>
          <tr><td>Column 7:</td><td>Access Owner Email</td></tr>
          <tr><td>Column 8:</td><td>Manager Approval Required? (Y or N)</td></tr>
          <tr><td>Column 9:</td><td>Access Owner Approval Required? (Y or N)</td></tr>
          <tr><td>Column 10:</td><td>Primary Provisioning Lead Name</td></tr>
          <tr><td>Column 11:</td><td>Primary Provisioning Lead Email</td></tr>
          <tr><td>Column 12:</td><td>Secondary Provisioning Lead Name<br />(Enter NA if none)</td></tr>
          <tr><td>Column 13:</td><td>Secondary Provisioning Lead Email<br />(Enter NA if none)</td></tr>
        </table>
      </div>
      <div style="text-align:center">
        <input type="submit" class="btn btn-success" value="Submit">
      </div>
    </form>
  </div>
  <div class="col-md-4"></div>
</div>        
