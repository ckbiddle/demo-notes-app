<?php

  require '../model/DBFunctions.php';
  require '../model/InitDbConnect.php';

  $validationHandle = '';

  if ( isset( $_GET['vh'] ))
  {
    $validationHandle = $_GET['vh'];
    header( 'Location: ../controller/?cmd=newpwdpg&vh=' . $validationHandle );
  }
  else
  {
    echo 'Oops! Didn&rsquo;t get a validation handle.';
  }
  
  echo '</p>';

?>