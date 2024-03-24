<?php

  if ( basename( $_SERVER['PHP_SELF'] ) === basename( __FILE__ ))
  {
    die( '<p>You cannot access this page directly!</p>' );
  }

  $appRoot = '/identivize';
  // $appRoot = '';
  
  $hostingDomain = 'light23.com';
  $senderEmail = 'contact@light23.com';

?>