<?php

  if ( isset( $_SESSION['arc_mgr_processed_action_items'] ) && count( $_SESSION['arc_mgr_processed_action_items'] ) > 0 )
  {
    echo '<p>You APPROVED (or REJECTED) the below ' . count( $_SESSION['arc_mgr_processed_action_items'] ) . ' access request(s) as the manager of the access requested:</p>';
   
    echo '<table class="table">' .
         '  <tr>' .
         '    <th>Requestor</th>' .
         '    <th>Requested Date</th>' .
         '    <th>Access Name</th>' .
         '    <th>Application</th>' .
         '    <th>Requestor Note(s)</th>' .
         '  </tr>';

    foreach( $_SESSION['arc_mgr_processed_action_items'] as $row )
    {
      echo '<tr>' .
           '<td>' . $row['requestor_name'] . '<br>' . $row['manager_action'] . '</td>' .
       //  '<td>' . $row['request_date'] . '</td>' .
           '<td>' . $row['formatted_request_date'] . '</td>' .
           '<td>' . $row['access_name'] . '</td>' .
           '<td>' . $row['application_name'] . '</td>' .
           '<td>' . $row['business_justification'] . '</td>' .
           '</tr>';
    };

    echo '</table>';
  }

?>