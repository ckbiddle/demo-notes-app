<?php

  echo '<table class="table">' .
       '  <tr>' .
       '    <th>Requestor</th>' .
       '    <th>Requested Date</th>' .
       '    <th>Access Name</th>' .
       '    <th>Application</th>' .
       '    <th>Requestor Note(s)</th>' .
       '    <th></th>' .
       '  </tr>';

  foreach( $items as $row )
  {
    echo '<tr>' .
         '<td>' . $row['requestor_name'] . '<br>' . $row['manager_action'] . '</td>' .
         '<td>' . $row['formatted_request_date'] . '</td>' .
         '<td>' . $row['access_name'] . '</td>' .
         '<td>' . $row['application_name'] . '</td>' .
         '<td>' . $row['business_justification'] . '</td>' .
         '<td id="cmpltdshowhide_' . $row['request_id'] . '_' . $row['request_line_id'] . '" style="text-align:left">' .
         '  <a href="javascript:showCompletedItemsRequestDetails( ' . $row['request_id'] . ', ' . $row['request_line_id'] . ', &quot;' . $appRoot . '&quot; )" style="text-decoration:none">&oplus;</a>' .
         '</td>' .
         '</tr>' .
         '<tr id="cmpltr_' . $row['request_id'] . '_' . $row['request_line_id'] . '" style="display:none">' .
         '  <td id="cmpltd_' . $row['request_id'] . '_' . $row['request_line_id'] . '" colspan="6"></td>' .
         '</tr>';
  };

  echo '</table>';

?>