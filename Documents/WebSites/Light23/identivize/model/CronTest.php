<?php

  // require './DBFunctions.php';
  // require './InitDbConnect.php';

  function logActivity( $pConn,
                        $pActivityCode,
                        $pLogText
                      )
  {
    if ( $stmt = mysqli_prepare( $pConn, 'insert into activity_log
                                          ( activity_code,
                                            log_text
                                          )
                                          values
                                          ( ?,
                                            ?
                                          )'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt,
                              "ss",
                              $pActivityCode,
                              $pLogText
                            );

      mysqli_stmt_execute( $stmt );
    }
  }

  $hostname = 'localhost';
  $dbUsername = 'identivize_admin';
  $dbPassword = 'I@mP0w3rUs3r';
  $dbname = 'identivize_db6';

  // $conn = mysqli_connect( $hostname, $dbUsername, $dbPassword, $dbname ) or die ( 'Error: Database connection failure.' );
  $conn = mysqli_connect( $hostname, $dbUsername, $dbPassword, $dbname );

  $message = 'Testing cron. Current date and time is ' . date('m/d/Y h:i:s a', time());

  logActivity( $conn, '1001', $message );
  
  mail( 'ckbiddle@pacbell.net', 'Cron Test', $message, 'From: contact@light23.com' );

?>