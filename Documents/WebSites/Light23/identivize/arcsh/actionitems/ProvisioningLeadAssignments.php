<p>
  <span style="font-weight:bold">Provisioning Actions (<?php echo count( $provisioningLeadAssignments ); ?>)</span><br />
  Please review and complete the below requests or reassign them to another person. <b>Make sure that the person who
  approved the request as the manager is the current manager.</b>
</p>
<form action="../../controller/" method="post">
  <input type="hidden" name="cmd" value="pl_assignments_input" />         
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
    
      foreach( $provisioningLeadAssignments as $row )
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
             '      <input class="form-check-input" type="checkbox" name="pl_assign[]" value="' . $requestId . '_' . $requestLineId . '" >' .
             '    </div>' .
             '  </td>' .
             '  <td>' . $requestorName . '</td>' .
             '  <td>' . $accessName . '</td>' .
             '  <td>' . $applicationName . '</td>' .
             '  <td>' . $formattedRequestDate . '</td>' .
             '  <td>' . $businessJustification . '</td>' .
             '  <td id="pltdshowhide_' . $requestId . '_' . $requestLineId . '">' .
             '    <a href="javascript:showProvisioningLeadRequestDetails( ' . $requestId . ', ' . $requestLineId . ', &quot;' . $appRoot . '&quot; )" style="text-decoration:none">&oplus;</a>' .
             '  </td>' .
             '</tr>' .
             '<tr id="pltr_' . $requestId . '_' . $requestLineId . '" style="display:none">' .
             '  <td></td>' .
             '  <td id="pltd_' . $requestId . '_' . $requestLineId . '" colspan="6"></td>' .
             '</tr>';
        
      }
    
    ?>
  </table>
  <div style="width:100%;padding-bottom:40px;text-align:right">
    <input type="submit" name="submit_button" class="btn btn-success" value="Mark as Complete" />&nbsp;
    <input type="submit" name="submit_button" class="btn btn-danger" value="Reassign" />&nbsp;
  </div>
</form>