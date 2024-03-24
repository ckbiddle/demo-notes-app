<?php

  $validationHandle = '';

  if ( isset( $_GET['vh'] ))
  {
    $validationHandle = $_GET['vh'];
    header( 'Location: ../../controller/?cmd=arcnewpwdpg&vh=' . $validationHandle );
  }
  else
  {
    echo '<p>Oops! Didn&rsquo;t get a validation handle.</p>';
  }

?>