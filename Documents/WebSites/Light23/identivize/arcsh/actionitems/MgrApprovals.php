<p>
  <span style="font-weight:bold">Manager Approval (<?php echo count( $managerApprovals ); ?>)</span><br />
  Please review and approve or decline the below requests at your earliest convenience. You received
  these requests because you were listed as the manager of the below requestors. Timely processing
  of these requests ensures that your team members have the necessary access to perform their duties
  effectively and maintain productivity.
</p>
<form action="../../controller/" method="post">
  <input type="hidden" name="cmd" value="mgr_approvals" />         
  <!-- <table class="table table-striped"> -->
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
    
      foreach( $managerApprovals as $row )
      {
        $requestId = $row['request_id'];
        $requestLineId = $row['request_line_id'];
        $requestorName = $row['requestor_name'];
        $accessName = $row['access_name'];
        $applicationName = $row['application_name'];
     // $requestDate = $row['request_date'];
        $formattedRequestDate = $row['formatted_request_date'];
        $businessJustification = $row['business_justification'];

        echo '<tr>' .
             '  <td>' .
             '    <div class="form-check">' .
             '      <input class="form-check-input" type="checkbox" name="mgr_appr[]" value="' . $requestId . '_' . $requestLineId . '" >' .
             '    </div>' .
             '  </td>' .
             '  <td>' . $requestorName . '</td>' .
             '  <td>' . $accessName . '</td>' .
             '  <td>' . $applicationName . '</td>' .
          // '  <td>' . $requestDate . '</td>' .
             '  <td>' . $formattedRequestDate . '</td>' .
             '  <td>' . $businessJustification . '</td>' .
             '  <td id="mgrtdshowhide_' . $requestId . '_' . $requestLineId . '" style="text-align:left">' .
             '    <a href="javascript:showManagerApprovalRequestDetails( ' . $requestId . ', ' . $requestLineId . ', &quot;' . $appRoot . '&quot; )" style="text-decoration:none">&oplus;</a>' .
             '  </td>' .
             '</tr>' .
             '<tr id="mgrtr_' . $requestId . '_' . $requestLineId . '" style="display:none">' .
             '  <td></td>' .
             '  <td id="mgrtd_' . $requestId . '_' . $requestLineId . '" colspan="6"></td>' .
             '</tr>';
      }
    
    ?>
  </table>
  <div style="width:100%;padding-bottom:40px;text-align:right">
    <input type="submit" name="submit_button" class="btn btn-success" value="Approve" />&nbsp;
    <input type="submit" name="submit_button" class="btn btn-danger" value="Reject" />&nbsp;
    <input type="submit" name="submit_button" class="btn btn-primary" value="Reassign" />
  </div>
</form>