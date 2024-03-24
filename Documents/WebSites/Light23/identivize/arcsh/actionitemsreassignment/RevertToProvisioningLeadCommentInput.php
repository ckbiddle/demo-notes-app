<p>
  Please state a reason for reverting these action items back to the provisioning lead.
</p>
<form action="../../controller/" method="post">
  <input type="hidden" name="cmd" value="provisioner_revert_to_lead" />
  <div class="row">
    <div class="col-md-8">
     <label for="comment">Your Reason</label>
     <textarea class="form-control" rows="2" id="comment" name="comment" maxlength="400"></textarea> 
    </div>
    <div class="col-md-4" style="padding-top:60px">
      <input type="submit" class="btn btn-primary" value="Submit" />    
    </div>
  </div>
</form>

<table class="table">
  <tr>
    <th>Requestor</th>
    <th>Requested Date</th>
    <th>Access Name</th>
    <th>Application</th>
    <th>Requestor Notes</th>
  </tr>
  <?php
 
    foreach( $provisionerActionItems as $row )
    {
      $requestKeys = explode( '_', $row );
      $requestId = $requestKeys[0];
      $requestLineId = $requestKeys[1];
      $requestLineItem = getRequestLineItemByRequestLineId( $conn, $requestId, $requestLineId );
      
      echo '<tr>' . 
           '<td>' . $requestLineItem['requestor_name'] . '</td>' .
           '<td>' . $requestLineItem['request_date'] . '</td>' . 
           '<td>' . $requestLineItem['access_name'] . '</td>' . 
           '<td>' . $requestLineItem['application_name'] . '</td>' .
           '<td>' . $requestLineItem['business_justification'] . '</td>' .
           '</tr>';
    }
    
  ?>
</table>