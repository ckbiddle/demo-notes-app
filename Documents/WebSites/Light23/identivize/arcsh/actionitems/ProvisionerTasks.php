<p>
  <span style="font-weight:bold">Provisioner Tasks (<?php echo count( $provisionerTasks ); ?>)</span><br />
  Please review and process the below provisioning requests, or revert it back to the provisioning lead if needed.
  You received these requests because you were listed as the provisioner of these requests by the provisioning lead.
  Timely processing of these requests ensures that your team members have the necessary access to perform their
  duties effectively and to maintain productivity.
</p>
<form action="../../controller/" method="post">
  <input type="hidden" name="cmd" value="provisioner_tasks_input" />         
  <table class="table">
    <tr>
      <th></th>
      <th>Requestor</th>
      <th>Access Name</th>
      <th>Application</th>
      <th>Request Date</th>
      <th>Requestor Notes</th>
      <th></th> <!-- Show more -->
    </tr>
    <?php
    
      foreach( $provisionerTasks as $row )
      {
        $requestId = $row['request_id'];
        $requestLineId = $row['request_line_id'];
        $requestorName = $row['requestor_name'];
        $accessName = $row['access_name'];
        $applicationName = $row['application_name'];
        $formattedRequestDate = $row['formatted_request_date'];
        $businessJustification = $row['business_justification'];
        
        echo '<tr>' .
             '  <td>' .
             '    <div class="form-check">' .
             '      <input class="form-check-input" type="checkbox" name="provisioner_tasks[]" value="' . $requestId . '_' . $requestLineId . '" >' .
             '    </div>' .
             '  </td>' .
             '  <td>' . $requestorName . '</td>' .
             '  <td>' . $accessName . '</td>' .
             '  <td>' . $applicationName . '</td>' .
             '  <td>' . $formattedRequestDate . '</td>' .
             '  <td>' . $businessJustification . '</td>' .
             '  <td id="prvtdshowhide_' . $requestId . '_' . $requestLineId . '">' .
             '    <a href="javascript:showProvisionerTaskDetails( ' . $requestId . ', ' . $requestLineId . ', &quot;' . $appRoot . '&quot; )" style="text-decoration:none">&oplus;</a>' .
             '  </td>' .
             '</tr>' .
             '<tr id="prvtr_' . $requestId . '_' . $requestLineId . '" style="display:none">' .
             '  <td></td>' .
             '  <td id="prvtd_' . $requestId . '_' . $requestLineId . '" colspan="6"></td>' .
             '</tr>';
        
      }
    
    ?>
  </table>
  <div style="width:100%;padding-bottom:40px;text-align:right">
    <input type="submit" name="submit_button" class="btn btn-success" value="Mark as Complete" />&nbsp;
    <input type="submit" name="submit_button" class="btn btn-danger" value="Revert to Provisioning Lead" />&nbsp;
  </div>
</form>