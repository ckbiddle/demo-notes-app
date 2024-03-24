<?php

  $hostname = 'localhost';
  $dbUsername = 'identivize_admin';
  $dbPassword = 'I@mP0w3rUs3r';
  $dbname = 'identivize_db6';

  $conn = mysqli_connect( $hostname, $dbUsername, $dbPassword, $dbname ) or die ( '<p>Error: Database connection failure.<p>' );

?>