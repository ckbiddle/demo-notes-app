<p>
  Assign the below access requests to the following person:
</p>
<form action="../../controller/" method="post">
  <input type="hidden" name="cmd" value="mgr_approval_reassignment" />
  <div class="row">
    <div class="col-md-4">
      <label for="assignee_name" class="form-label">Assignee Name</label>
      <input type="text" class="form-control" id="assignee_name" name="assignee_name" maxlength="80">
    </div>
    <div class="col-md-4">
      <label for="assignee_email" class="form-label">Assignee&lsquo;s Email Address</label>
      <input type="email" class="form-control" id="assignee_email" name="assignee_email" maxlength="80">
    </div>
    <div class="col-md-4"></div>
  </div>
  <div class="row">
    <div class="col-md-8">
     <label for="comment">Your Comment</label>
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
 
    foreach( $managerActionItems as $row )
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