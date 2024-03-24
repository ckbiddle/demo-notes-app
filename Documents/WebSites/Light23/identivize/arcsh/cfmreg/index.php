<?php

  session_start();

  require '../../GlobalVars.php';
  require '../../controller/Functions.php';
  require '../../model/InitDbConnect.php';
  require '../../model/DBFunctions.php';

  $validationHandle = $_GET['vh'];
  $arcName = '';  // Access Request Console (ARC) name. Also used as the "tenant name" throughout the application.

  if( confirmRegistration( $conn, $validationHandle, $arcName ))
  {
    $_SESSION['arc_login_message'] =  'Thank you for confirming your registration.<br>' .
                                      'You may now log in ...';
                                  
    $_SESSION['arc_login_error_message'] = '';

    header( 'Location: ' . $appRoot . '/arc/' . $arcName );
  }

?>