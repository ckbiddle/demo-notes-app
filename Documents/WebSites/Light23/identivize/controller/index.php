<?php

  session_start();

  require '../GlobalVars.php';
  require '../model/InitDbConnect.php';
  require '../model/DBFunctions.php';
  require './Functions.php';

  $cmd = '';

  if ( isset( $_GET['cmd'] ))
  {
    $cmd = $_GET['cmd'];
  }
  else if ( isset( $_POST['cmd'] ))
  {
    $cmd = $_POST['cmd'];
  }

  if ( $cmd == 'login' )
  {
    $loginId = $_POST['login_id'];
    $passwd = $_POST['pwd'];
    $errorMsg = '';
    $tenantName = '';
    $userName = '';
    
    // Capture the post parameters in a session so they can be re-displayed in
    // the login form fields if we need to return to it because of an input error.
    $_SESSION['login_login_id'] = $loginId;
    $_SESSION['login_pwd'] = $passwd;

    // This login is for admin level 2
    // 1 is superuser, 2 is manager (workflow creator), 3 is staff (app access requestor)
    if ( validateLogin( $conn, $loginId, $passwd, 2, $tenantName, $userName, $errorMsg ))
    {
      // This flags that the user has now logged in. When we go back to the
      // main page, it will read this to know that the login form no longer
      // needs to be displayed, but to move on to the create/edit workflow page.
      $_SESSION['idv_login_id'] = $loginId;
      
      // The tenant name is the same as the access request console name. It is used
      // throughout the application as part of the composite primary key to retrieve
      // the workflow data relevant to the user.
      $_SESSION['idv_tenant_name'] = $tenantName;
      
      $_SESSION['idv_login_name'] = $userName;
      
      // The session values accessed by the login and password fields
      // on the login form can now be set to an empty string, since there is no
      // need to return to the login form.
      $_SESSION['login_login_id'] = '';
      $_SESSION['login_pwd'] = '';
      
      header( 'Location: ../' );
    }
    else
    {
      $_SESSION['login_error_message'] = $errorMsg;
      header( 'Location: ../' );
    }
  }
  else if ( $cmd == 'logout' )
  {
    session_destroy();
    header( 'Location: ../' );
  }
  else if ( $cmd == 'arclogout' )
  {
    $tenantName = '';
    
    if ( isset( $_SESSION['arc_tenant_name'] ) && $_SESSION['arc_tenant_name'] != '' )
    {
      $tenantName = $_SESSION['arc_tenant_name'];
    }
    else
    {
      die( '<p>Error: Tenant name not provided!</p>' );
    }
    
    session_destroy();
    
    header( 'Location: ../arc/' . $tenantName );
  }
  else if ( $cmd == 'register' )
  {
    $loginId = $_POST['login_id'];
    $passwd1 = $_POST['pwd'];
    $passwd2 = $_POST['repeat_pwd'];
    $lastName = $_POST['last_name'];
    $firstName = $_POST['first_name'];
    $companyName = $_POST['company_name'];
    $arcName = $_POST['arc_name'];  // tenant_name in database
    $emailDomains = $_POST['email_domains'];
    
    // Capture the post parameters in a session so they can be re-displayed in the
    // registration form fields if we need to return to it because of an error.
    $_SESSION['reg_login_id'] = $loginId;
    $_SESSION['reg_pwd_1'] = $passwd1;
    $_SESSION['reg_pwd_2'] = $passwd2;
    $_SESSION['reg_last_name'] = $lastName;
    $_SESSION['reg_first_name'] = $firstName;
    $_SESSION['reg_company_name'] = $companyName;
    $_SESSION['reg_arc_name'] = $arcName;
    $_SESSION['reg_email_domains'] = $emailDomains;
    
    $validationHandle = '';
    $adminLevel = 2;   // 1 is superuser, 2 is manager (workflow creator), 3 is staff (app access requestor)
    $errorMessage = '';

    if ( validateRegistrationInfo( $loginId, $passwd1, $passwd2, $lastName, $firstName, $companyName, $arcName, $emailDomains, $errorMessage ))
    {
      if ( createUser( $conn, $loginId, $passwd1, $lastName, $firstName, 'NA', $companyName, $arcName, $adminLevel, $emailDomains, $validationHandle, $errorMessage ))
      {
        if ( sendEmailVerification( $loginId, // email address
                                    $firstName,
                                    $validationHandle,
                                    $appRoot,
                                    $hostingDomain,
                                    $senderEmail
                                  )
           )
        {
          $_SESSION['message'] = 'Thank you for your registration! An email has been sent to you with a ' .
                                 'link to click on that will confirm your registration.<br><br>' .
                                 'Please close this window and check your emails (check your spam folder as ' .
                                 'well, if not arrived), and click on the link in the email to continue.<br><br>' .
                                 'The email may take a couple of minutes to arrive, so please be patient.';
                                 
          // Session values accessed by the input fields should now be set to an empty string, since we have
          // successfully registered and no longer need to return to the registration input screen.
          $_SESSION['reg_login_id'] = '';
          $_SESSION['reg_pwd_1'] = '';
          $_SESSION['reg_pwd_2'] = '';
          $_SESSION['reg_last_name'] = '';
          $_SESSION['reg_first_name'] = '';
          $_SESSION['reg_company_name'] = '';
          $_SESSION['reg_arc_name'] = '';
          $_SESSION['reg_email_domains'] = '';
          $_SESSION['reg_error_message'] = '';
 
          header( 'Location: ../msg' );
        }
      }
      else
      {
        $_SESSION['reg_error_message'] = $errorMessage;
        header( 'Location: ../register' );
      }
    }
    else
    {
      $_SESSION['reg_error_message'] = $errorMessage;
      header( 'Location: ../register' );
    }
  }
  else if ( $cmd == 'crwkflow' )
  {
    $tenantName = $_POST['tenant_name'];
    $workflowName = $_POST['workflow_name'];
    $workflowDescription = $_POST['workflow_description'];
    $_SESSION['wkflow_workflow_name'] = $workflowName;
    $_SESSION['wkflow_workflow_description'] = $workflowDescription;
    
    $targetFile = '../uploads/' . basename( $_FILES['workflow_data']['name'] );
    $errorMessage = '';
    $workflowData = array();

    if ( $workflowName != '' )
    {
      if ( $workflowDescription != '' )
      {
        if ( $_FILES['workflow_data']['name'] != '' )
        {
          if ( move_uploaded_file( $_FILES['workflow_data']['tmp_name'], $targetFile ))
          {
            // parseAndLoadFile() is in Functions.php. Last field is the mode: create or update
            if ( parseAndLoadFile( $conn, $tenantName, $workflowName, $workflowDescription, $targetFile, $errorMessage, 'create' ))
            {
              unlink( $targetFile );
              
              $_SESSION['message'] = 'Data successfully parsed and loaded. ' .
                                     '<a href="../" style="color:#FFF;font-weight:bold">Click here</a> to return to the home page.';
                                     
              $_SESSION['wkflow_workflow_name'] = '';
              $_SESSION['wkflow_workflow_description'] = '';
              $_SESSION['wkflow_error_message'] = '';
              
              header( 'Location: ../msg' );
            }
            else
            {
              $_SESSION['wkflow_error_message'] = $errorMessage;
              header( 'Location: ../workflow/?cmd=crwkflow' );
            }
          }
          else
          {
            $_SESSION['wkflow_error_message'] = $errorMessage;
            header( 'Location: ../workflow/?cmd=crwkflow' );
          }
        }
        else
        {
          $_SESSION['wkflow_error_message'] = 'Error: Please specify a file to upload.';
          header( 'Location: ../workflow/?cmd=crwkflow' );
        }
      }
      else
      {
        $_SESSION['wkflow_error_message'] = 'Error: Workflow Description is required.';
        header( 'Location: ../workflow/?cmd=crwkflow' );
      }
    }
    else
    {
      $_SESSION['wkflow_error_message'] = 'Error: Workflow Name is required.';
      header( 'Location: ../workflow/?cmd=crwkflow' );
    }
  }
  else if ( $cmd == 'rvwkflow' )
  {
    $workflowName = '';
    
    if ( isset( $_POST['workflow_name'] ))
    {
      $workflowName = $_POST['workflow_name'];
      $_SESSION['rvwkflow_selected_workflow'] = $workflowName;
      $_SESSION['wkflow_error_message'] = '';
      header( 'Location: ../workflow?cmd=rvwkflow' );      
    }
    else
    {
      $_SESSION['wkflow_error_message'] = 'Error: No workflow name specified.';
      header( 'Location: ../workflow?cmd=rvwkflow' );      
    }
  }
  else if ( $cmd == 'dnldwkflow' )
  {
    $tenantName = $_POST['tenant_name'];
    $workflowName = $_POST['workflow_name'];
    
    // Replace any spaces in the workflow name with underscores.
    // Spaces in file names are not good.
    $workflowFileName = str_replace( " ", "_", $workflowName ) . '.csv';
    
    $filePath = '../downloads/' . $workflowFileName;
    
    $workflowDetails = getWorkflowDetailsByWorkflowName( $conn, $tenantName, $workflowName );
    
    if ( sizeof( $workflowDetails ) > 0 )
    {
      $file = fopen( $filePath, "w" );
      
      $headers = array( 'Access Name',
                        'Access Description',
                        'Application Name',
                        'Access Source',
                        'Risk Rating',
                        'Access Owner Name',
                        'Access Owner Email',
                        'Manager Approval Required?',
                        'Access Owner Approval Required?',
                        'Primary Provisioning Lead Name',
                        'Primary Provisioning Lead Email',
                        'Secondary Provisioning Lead Name',
                        'Secondary Provisioning Lead Email'
                      );

      fputcsv( $file, $headers );
      
      foreach( $workflowDetails as $row )
      {
        fputcsv( $file, $row );
      }

      fclose( $file );

      if ( file_exists( $filePath ))
      {
        downloadFile( $filePath );
        
        // Now that the client has downloaded the file,
        // let's get rid of it on the server.
        unlink( $filePath );
        
        exit;
      }
      else
      {
        // File not found
        echo 'File not found.';
      }
    }
    else
    {
      echo 'No data found.';     
    }
  }
  else if ( $cmd == 'updwkflowinput' )
  {
    $tenantName = $_POST['tenant_name'];
    $workflowName = $_POST['workflow_name'];
    $workflow = getWorkflowByWorkflowName( $conn, $tenantName, $workflowName );
    $workflowDescription = $workflow['workflow_description'];
    
    $_SESSION['wkflow_workflow_name'] = $workflowName;
    $_SESSION['wkflow_workflow_description'] = $workflowDescription;
    
    header( 'Location: ../workflow/?cmd=updwkflowinput' );
  }
  else if ( $cmd == 'cancelupdwkflowinput' )
  {
    $_SESSION['wkflow_workflow_name'] = '';
    $_SESSION['wkflow_workflow_description'] = '';
    $_SESSION['wkflow_error_message'] = '';

    // Go back to the "review workflow" screen, the screen that
    // called up the "update workflow" input screen
    header( 'Location: ../workflow/?cmd=rvwkflow' );
  }
  else if ( $cmd == 'updwkflow' )
  {
    $tenantName = $_POST['tenant_name'];
    $workflowName = '';
    $workflowDescription = '';
    $targetFile = '';
    $errorMessage = '';
    
    if ( isset( $_POST['workflow_name'] ) && $_POST['workflow_name'] != '' )
    {
      $workflowName = $_POST['workflow_name'];
    }

    if ( isset( $_POST['workflow_description'] ) && $_POST['workflow_description'] != '' )
    {
      $workflowDescription = $_POST['workflow_description'];
    }
    
    $_SESSION['wkflow_workflow_name'] = $workflowName;
    $_SESSION['wkflow_workflow_description'] = $workflowDescription;
    
    $workflowFileName = '';
    $fileUploadOK = false;
    
    if ( $_FILES['workflow_data']['name'] != '' )
    {
      $workflowFileName = $_FILES['workflow_data']['name'];
      $targetFile = '../uploads/' . basename( $workflowFileName );

      if ( move_uploaded_file( $_FILES['workflow_data']['tmp_name'], $targetFile ))
      {
        $fileUploadOK = true;
      }
      else
      {
        $_SESSION['wkflow_error_message'] = 'Error: Failed to upload workflow file';
        header( 'Location: ../workflow/?cmd=updwkflowinput' );
        die();
      }
    }
    else
    {
      $fileUploadOK = true;
    }

    if ( $fileUploadOK )
    {
      if ( $workflowName != '' )
      {
        if ( $workflowDescription != '' )
        {
          // parseAndLoadFile() is in Functions.php. Last field is the mode: create or update
          if ( parseAndLoadFile( $conn, $tenantName, $workflowName, $workflowDescription, $targetFile, $errorMessage, 'update' ))
          {
            unlink( $targetFile );
            
            $_SESSION['wkflow_workflow_name'] = '';
            $_SESSION['wkflow_workflow_description'] = '';
            $_SESSION['wkflow_error_message'] = '';
            
            header( 'Location: ../workflow/?cmd=rvwkflow' );

          }
          else
          {
            $_SESSION['wkflow_error_message'] = $errorMessage;
            header( 'Location: ../workflow/?cmd=updwkflowinput' );
          }
        }
        else
        {
          $_SESSION['wkflow_error_message'] = 'Error: Workflow Description is required.';
          header( 'Location: ../workflow/?cmd=updwkflowinput' );
        }
      }
      else
      {
        // Should never hit this error because workflow name is a hidden text field on the
        // source input screen that should always be populated. Putting this condition in
        // anyway, just in case.
        $_SESSION['wkflow_error_message'] = 'Error: Workflow Name is required.';
        header( 'Location: ../workflow/?cmd=updwkflowinput' );
      }
    }
  }
  else if ( $cmd == 'deleteworkflow' )
  {
    $tenantName = $_POST['tenant_name'];
    $workflowName = $_POST['workflow_name'];
    $errorMessage = '';

    if ( deleteWorkflowDetails( $conn, $tenantName, $workflowName, $errorMessage ))
    {
      if ( deleteWorkflowMaster( $conn, $tenantName, $workflowName, $errorMessage ))
      {
        $_SESSION['rvwkflow_selected_workflow'] = '';
        $_SESSION['wkflow_workflow_name'] = '';
        $_SESSION['wkflow_workflow_description'] = '';
      }
      else
      {
        $_SESSION['wkflow_error_message'] = $errorMessage;
      }
    }
    else
    {
      $_SESSION['wkflow_error_message'] = $errorMessage;
    }

    header( 'Location: ../workflow/?cmd=rvwkflow' );
  }
  else if ( $cmd == 'pwdreset' )
  {
    $userId = $_POST['login_id'];
    $_SESSION['pwdreset_login_id'] = $userId;
    $_SESSION['pwdreset_error_message'] = '';
    
    $errorMessage = '';
    $userDetails = getUserDetails( $conn, $userId, $errorMessage );

    if ( sizeof( $userDetails ) > 0 )
    {
      if ( sendPasswordResetVerification( $userId,
                                          $userDetails['first_name'],
                                          $userDetails['validation_handle'],
                                          $appRoot,
                                          $hostingDomain,
                                          $senderEmail
                                        )
         )
      {
        $_SESSION['message'] = 'Thank you ' . $userDetails['first_name'] . '. An email has been sent to you with a ' .
                               'link to click on that will log you into Identivize where you can reset your password.<br><br>' .
                               'Please close this window and check your emails (check your spam folder as well, ' .
                               'if not arrived), and click on the link in the email to continue.<br><br>' .
                               'The email may take a couple of minutes to arrive, so please be patient.';
                               
        header( 'Location: ../msg' );
        $_SESSION['pwdreset_login_id'] = '';
      }
      else
      {
        $_SESSION['pwdreset_error_message'] = 'Error: Failed to send password reset verification email.';
        header( 'Location: ../promptpwdreset' );
      }
    }
    else
    {
      $_SESSION['pwdreset_error_message'] = 'Error: We did not find an account with that email address.';
      header( 'Location: ../promptpwdreset' );
    }
  }
  else if ( $cmd == 'newpwdpg' )
  {
    $validationHandle = '';

    if ( isset( $_GET['vh'] ))
    {
      $validationHandle = $_GET['vh'];
      
      $userInfo = getUserInfoByValidationHandle( $conn, $validationHandle );

      $_SESSION['idv_login_id'] = $userInfo['user_id'];
      
      header( 'Location: ../newpwd' );
    }
  }
  else if ( $cmd == 'arcnewpwdpg' )
  {
    $validationHandle = '';

    if ( isset( $_GET['vh'] ))
    {
      $validationHandle = $_GET['vh'];
      
      $userInfo = getUserInfoByValidationHandle( $conn, $validationHandle );

      $_SESSION['arc_login_id'] = $userInfo['user_id'];
      $_SESSION['arc_tenant_name'] = $userInfo['tenant_name'];
      
      // header( 'Location: ../newpwd' );
      header( 'Location: ../arcsh/newpwd' );
    }
  }
  else if ( $cmd == 'updpwd' )
  {
    $newPwd1 = $_POST['new_pwd_1'];
    $newPwd2 = $_POST['new_pwd_2'];
    
    $_SESSION['new_pwd_1'] = $newPwd1;
    $_SESSION['new_pwd_2'] = $newPwd2;
    
    $errorMessage = '';
    
    if ( isset( $_SESSION['idv_login_id'] ) && $_SESSION['idv_login_id'] != '' )
    {
      if ( checkPasswordInput( $newPwd1, $newPwd2, $errorMessage ))
      {
        if ( updateUserPasswd( $conn, $_SESSION['idv_login_id'], $newPwd1, $errorMessage ))
        {
          $_SESSION['message'] = 'Password successfully updated! ' .
                                 '<a href="../" style="color:#FFF">Click here</a> to access the home page.';
          $_SESSION['new_pwd_1'] = '';
          $_SESSION['new_pwd_2'] = '';
          $_SESSION['new_pwd_error_message'] = '';
  
          header( 'Location: ../msg' );
        }
        else
        {
          $_SESSION['new_pwd_error_message'] = $errorMessage;
          header( 'Location: ../newpwd' ); 
        }
      }
      else
      {
        $_SESSION['new_pwd_error_message'] = $errorMessage;
        header( 'Location: ../newpwd' ); 
      }
    }
    else
    {
      header( 'Location: ../' );
    }
  }
  else if ( $cmd == 'arcupdpwd' )
  {
    $newPwd1 = $_POST['new_pwd_1'];
    $newPwd2 = $_POST['new_pwd_2'];
    
    $_SESSION['arc_new_pwd_1'] = $newPwd1;
    $_SESSION['arc_new_pwd_2'] = $newPwd2;
    
    $errorMessage = '';
    $tenantName = '';
    
    if ( isset( $_SESSION['arc_tenant_name'] ) && $_SESSION['arc_tenant_name'] != '' )
    {
      $tenantName = $_SESSION['arc_tenant_name'];
     
      if ( isset( $_SESSION['arc_login_id'] ) && $_SESSION['arc_login_id'] != '' )
      {
        if ( checkPasswordInput( $newPwd1, $newPwd2, $errorMessage ))
        {
          if ( updateUserPasswd( $conn, $_SESSION['arc_login_id'], $newPwd1, $errorMessage ))
          {
            $_SESSION['arc_message'] = 'Password successfully updated! ' .
                                       '<a href="../" style="color:#FFF">Click here</a> to access the home page.';
            $_SESSION['arc_new_pwd_1'] = '';
            $_SESSION['arc_new_pwd_2'] = '';
            $_SESSION['arc_new_pwd_error_message'] = '';
    
            header( 'Location: ../arcsh/msg' );
          }
          else
          {
            $_SESSION['arc_new_pwd_error_message'] = $errorMessage;
            header( 'Location: ../arcsh/newpwd' ); 
          }
        }
        else
        {
          $_SESSION['arc_new_pwd_error_message'] = $errorMessage;
          header( 'Location: ../arcsh/newpwd' ); 
        }
      }
      else
      {
        header( 'Location: ../arc/' . $tenantName );
      }
    }
    else
    {
      die ( '<p>Error: Tenant name not provided!</p>' );     
    }
  }
  else if ( $cmd == 'cancelupdpwd' )
  {
    $_SESSION['new_pwd_1'] = '';
    $_SESSION['new_pwd_2'] = '';
    $_SESSION['new_pwd_error_message'] = '';
    
    if ( isset( $_GET['rtn'] ))
    {
      header( 'Location: ' . $_GET['rtn'] );
    }
    else
    {
      header( 'Location: ../' );
    }
   
  }
  else if ( $cmd == 'cancelarcupdpwd' )
  {
    $_SESSION['arc_new_pwd_1'] = '';
    $_SESSION['arc_new_pwd_2'] = '';
    $_SESSION['arc_new_pwd_error_message'] = '';
    
    if ( isset( $_GET['rtn'] ))
    {
      header( 'Location: ' . $_GET['rtn'] );
    }
    else
    {
      header( 'Location: ../arcsh' );
    }
  }
  else if ( $cmd == 'arc_login' )
  {
    $loginId = $_POST['login_id'];
    $passwd = $_POST['pwd'];
    $errorMsg = '';
    $loginTenantName = $_POST['tenant_name'];
    $registeredTenantName = '';
    $userName = '';

    // Capture the post parameters in a session so they can be re-displayed in
    // the login form fields if we need to return to it because of an input error.
    $_SESSION['arc_login_login_id'] = $loginId;
    $_SESSION['arc_login_pwd'] = $passwd;

    // Access Request Console login is set for login level of staff (3).
    // 1 is superuser, 2 is manager (workflow creator), 3 is staff (app access requestor)
    if ( validateLogin( $conn, $loginId, $passwd, 3, $registeredTenantName, $userName, $errorMsg ))
    {
      if ( $loginTenantName == $registeredTenantName )
      {
        // This flags that the user has now logged in. When we go back to the
        // main page, it will read this to know that the login form no longer
        // needs to be displayed, but to move on to the access request search page.
        $_SESSION['arc_login_id'] = $loginId;
        
        // The tenant name is the same as the access request console name. It is used
        // throughout the application as part of the composite primary key to retrieve
        // the workflow data relevant to the user.
        $_SESSION['arc_tenant_name'] = $registeredTenantName;
        
        $_SESSION['arc_login_name'] = $userName;
        
        $_SESSION['arc_company_name'] = getTenantCompanyName( $conn, $registeredTenantName );
        
        // The session values accessed by the login and password fields
        // on the login form can now be set to an empty string, since there is no
        // need to return to the login form.
        $_SESSION['arc_login_login_id'] = '';
        $_SESSION['arc_login_pwd'] = '';
        
        header( 'Location: ../arcsh' );
      }
      else
      {
        die( '<p>Error: Invalid tenant: ' . $loginTenantName . '</p>' );
       
        $_SESSION['arc_login_error_message'] = 'Error: Invalid tenant.';  
        header( 'Location: ../arc/' . $loginTenantName );
      }
    }
    else
    {
      $_SESSION['arc_login_error_message'] = $errorMsg;
      header( 'Location: ../arc/' . $loginTenantName );
    }
  }
  else if ( $cmd == 'search_access_records' )
  {
    $_SESSION['arc_search_ar_search_string'] = '';
    
    if ( isset( $_POST['search_string'] ) && $_POST['search_string'] != '' )
    {
      $_SESSION['arc_search_ar_search_string'] = $_POST['search_string'];
    }

    $errorMessage = '';
    
    if ( isset( $_SESSION['arc_tenant_name'] ) && $_SESSION['arc_tenant_name'] != '' )
    {
      if ( $_SESSION['arc_search_ar_search_string'] != '' )
      {
        if ( isset( $_SESSION['arc_login_id'] ) && $_SESSION['arc_login_id'] != '' )
        {
          if ( insertSearchHistory( $conn, $_SESSION['arc_search_ar_search_string'], $_SESSION['arc_tenant_name'], $_SESSION['arc_login_id'], $errorMessage ))
          {
            $accessRequestSearchResults = searchWorkflowDetails( $conn, $_SESSION['arc_search_ar_search_string'], $_SESSION['arc_tenant_name'] );
            
            if ( count( $accessRequestSearchResults ) > 0 )
            {
              $_SESSION['arc_ar_search_results'] = $accessRequestSearchResults;
  
              // Re-initialize session values that are no longer needed.
              $_SESSION['arc_selected_access'] = array();
              $_SESSION['arc_search_ar_search_string'] = '';
              $_SESSION['arc_search_ar_error_message'] = '';
            
              header( 'Location: ../arcsh/arsearchresults' );
            }
            else
            {
              $_SESSION['arc_search_ar_error_message'] = 'No results found.';
              header( 'Location: ../arcsh/' );
            }
          }
          else
          {
            $_SESSION['arc_search_ar_error_message'] = $errorMessage;
            header( 'Location: ../arcsh/' );
          }
        }
        else
        {
          header( 'Location: ../arc/' . $_SESSION['arc_tenant_name'] );
        }
      }
      else
      {
        $_SESSION['arc_search_ar_error_message'] = 'Please enter a search phrase.';
        header( 'Location: ../arcsh/' );
      }
    }
    else
    {
      echo '<p>Error: No tenant name!</p>';
    }
  }
  else if ( $cmd == 'capture_selected_access' )
  {
    $_SESSION['arc_selected_access'] = array();

    // Clear the "selected" flag for all search results
    for ( $inx = 0; $inx < count( $_SESSION['arc_ar_search_results'] ); $inx++ )
    {
      $_SESSION['arc_ar_search_results'][$inx]['selected'] = false;
    }
   
    if ( isset( $_POST['search_result_index'] ))
    {
      $inx = 0;
      
      foreach( $_POST['search_result_index'] as $resultIndex )
      {
        // Flag this record as selected so that if the user returns
        // to the result page, the record will show as selected.
        $_SESSION['arc_ar_search_results'][$resultIndex]['selected'] = true;
        $_SESSION['arc_selected_access'][$inx] = $resultIndex;
        
        $inx++;
      }
    }
    
    header( 'Location: ../arcsh/cfmselectedaccess' );
  }
  else if ( $cmd == 'submit_access_request' )
  {
    $mgrName = '';
    $_SESSION['arc_ar_mgr_name'] = '';

    if ( isset( $_POST['mgrname'] ) && $_POST['mgrname'] != '' )
    {
      $mgrName = $_POST['mgrname'];
      $_SESSION['arc_ar_mgr_name'] = $mgrName;
    }
    
    $mgrEmail = '';
    $_SESSION['arc_ar_mgr_email'] = '';
    
    if ( isset( $_POST['mgremail'] ) && $_POST['mgremail'] != '' )
    {
      $mgrEmail = $_POST['mgremail'];
      $_SESSION['arc_ar_mgr_email'] = $mgrEmail;
    }
    
    $justification = '';
    $_SESSION['arc_ar_justification'] = '';

    if ( isset( $_POST['justification'] ) && $_POST['justification'] != '' )
    {
      $justification = $_POST['justification'];
      $_SESSION['arc_ar_justification'] = $justification;
    }

    // Passed in as output parameter to insertRequestHeader(). Primary key for
    // request_header record.
    $requestId = 0;
    
    $errorMessage = '';

    if ( isset( $_SESSION['arc_tenant_name'] ) && $_SESSION['arc_tenant_name'] != '' )
    {
      if ( isset( $_SESSION['arc_login_id'] ) && $_SESSION['arc_login_id'] != '' )
      {
        if ( $mgrName != '' )
        {
          if ( $mgrEmail != '' )
          {
            if ( $justification != '' )
            {
              if ( isset( $_SESSION['arc_ar_search_results'] ) && count( $_SESSION['arc_ar_search_results'] ) > 0 )
              {
                if ( isset( $_SESSION['arc_selected_access'] ) && count( $_SESSION['arc_selected_access'] ) > 0 )
                {                   
                  $requestHeaderData = array(
                    'tenant_name' => $_SESSION['arc_tenant_name'],
                    'user_id' => $_SESSION['arc_login_id'],
                    'manager_name' => $mgrName,
                    'manager_email' => $mgrEmail,
                    'business_justification' => $justification,
                    'request_status' => 'Submitted'   // Submitted, Approved, Complete
                  );  
              
                  if ( insertRequestHeader( $conn, $requestHeaderData, $requestId, $errorMessage ))
                  {
                    if ( insertRequestDetails( $conn,
                                               $requestId,
                                               $_SESSION['arc_tenant_name'],
                                               $_SESSION['arc_ar_search_results'],
                                               $_SESSION['arc_selected_access'],
                                               $mgrName,
                                               $mgrEmail,
                                               $errorMessage
                                             )
                       )
                    {
                      $_SESSION['arc_ar_message'] = '';
                      $_SESSION['arc_ar_error_message'] = '';
                      $_SESSION['arc_ar_mgr_name'] = '';
                      $_SESSION['arc_ar_mgr_email'] = '';
                      $_SESSION['arc_ar_justification'] = '';
                     
                      // echo '<p>Successfully inserted request information.</p>';
                      header( 'Location: ../arcsh/requestedaccess/?request_id=' . $requestId );
                    }
                    else
                    {
                      $_SESSION['arc_ar_error_message'] = $errorMessage;
                      header( 'Location: ../arcsh/cfmselectedaccess' );
                    }
                  }
                  else
                  {
                    $_SESSION['arc_ar_error_message'] = $errorMessage;
                    header( 'Location: ../arcsh/cfmselectedaccess' );
                  }
                }
                else
                {
                  $_SESSION['arc_ar_error_message'] = 'Error: No access items requested.';
                  header( 'Location: ../arcsh/cfmselectedaccess' );
                }
              }
              else
              {
                $_SESSION['arc_ar_error_message'] = 'Error: Missing access request search results.';
                header( 'Location: ../arcsh/cfmselectedaccess' );
              }
            }
            else
            {
              $_SESSION['arc_ar_error_message'] = 'Error: Justification is required.';
              header( 'Location: ../arcsh/cfmselectedaccess' );
            }
          }
          else
          {
            $_SESSION['arc_ar_error_message'] = 'Error: Manager email is required.';
            header( 'Location: ../arcsh/cfmselectedaccess' );
          }
        }
        else
        {
          $_SESSION['arc_ar_error_message'] = 'Error: Manager name is required.';
          header( 'Location: ../arcsh/cfmselectedaccess' );
        }
      }
      else
      {
        $_SESSION['arc_ar_error_message'] = 'Error: No login provided.';
        header( 'Location: ../arcsh/cfmselectedaccess' );
      }
    }
    else
    {
      $_SESSION['arc_ar_error_message'] = 'Error: Tenant name not provided.';
      header( 'Location: ../arcsh/cfmselectedaccess' );
    }
  }
  else if ( $cmd == 'arc_registration' )
  {
    $tenantName = $_POST['tenant_name'];  // don't need to validate, not from user input
    $loginId = $_POST['login_id'];
    $pwd1 = $_POST['pwd1'];
    $pwd2 = $_POST['pwd2'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $jobTitle = $_POST['job_title'];
    $companyName = $_POST['company_name'];
    
    // Capture the post parameters in a session so they can be re-displayed in the
    // registration form fields if we need to return to it because of an error.
    $_SESSION['arc_registration_login_id'] = $loginId;
    $_SESSION['arc_registration_pwd1'] = $pwd1;
    $_SESSION['arc_registration_pwd2'] = $pwd2;
    $_SESSION['arc_registration_last_name'] = $lastName;
    $_SESSION['arc_registration_first_name'] = $firstName;
    $_SESSION['arc_registration_job_title'] = $jobTitle;
    $_SESSION['arc_registration_company_name'] = $companyName;
    
    $validationHandle = '';
    $adminLevel = 3;   // 1 is superuser, 2 is manager (workflow creator), 3 is staff (app access requestor)
    $errorMessage = '';

    if ( validateArcRegistrationInfo( $loginId, $pwd1, $pwd2, $lastName, $firstName, $jobTitle, $companyName, $errorMessage ))
    {
      // The empty string being passed in is the string containing valid email domains, which an ARC user does not need to supply.
      if ( createUser( $conn, $loginId, $pwd1, $lastName, $firstName, $jobTitle, $companyName, $tenantName, $adminLevel, '', $validationHandle, $errorMessage ))
      {
        if ( sendArcEmailVerification( $loginId, $firstName, $validationHandle, $appRoot, $hostingDomain, $senderEmail ))
        {
          $_SESSION['arc_message'] = 'Thank you for your registration! An email has been sent to you with a ' .
                                     'link to click on that will confirm your registration.<br><br>' .
                                     'Please close this window and check your emails (check your spam folder as ' .
                                     'well, if not arrived), and click on the link in the email to continue.<br><br>' .
                                     'The email may take a couple of minutes to arrive, so please be patient.';
                                 
          // Session values accessed by the input fields should now be set to an empty string, since we have
          // successfully registered and no longer need to return to the registration input screen.
          $_SESSION['arc_registration_login_id'] = '';
          $_SESSION['arc_registration_pwd1'] = '';
          $_SESSION['arc_registration_pwd2'] = '';
          $_SESSION['arc_registration_last_name'] = '';
          $_SESSION['arc_registration_first_name'] = '';
          $_SESSION['arc_registration_job_title'] = '';
          $_SESSION['arc_registration_company_name'] = '';
          $_SESSION['arc_registration_error_message'] = '';
          $_SESSION['arc_registration_message'] = '';
 
          header( 'Location: ../arcsh/msg' );
        }
        else
        {
          $_SESSION['arc_registration_error_message'] = $errorMessage;
          header( 'Location: ../arc/' . $tenantName . '/?cmd=register' );
        }
      }
      else
      {
        $_SESSION['arc_registration_error_message'] = $errorMessage;
        header( 'Location: ../arc/' . $tenantName . '/?cmd=register' );
      }
    }
    else
    {
      $_SESSION['arc_registration_error_message'] = $errorMessage;
      header( 'Location: ../arc/' . $tenantName . '/?cmd=register' );
    }
  }
  else if ( $cmd == 'arc_pwdreset' )
  {
    $userId = $_POST['login_id'];
    $tenantName = $_POST['tenant_name'];
    
    $_SESSION['arc_pwdreset_login_id'] = $userId;
    $_SESSION['arc_pwdreset_error_message'] = '';
    
    $errorMessage = '';
    $userDetails = getUserDetails( $conn, $userId, $errorMessage );

    if ( count( $userDetails ) > 0 )
    {
      if ( sendArcPasswordResetVerification( $userId,
                                             $userDetails['first_name'],
                                             $userDetails['validation_handle'],
                                             $appRoot,
                                             $hostingDomain,
                                             $senderEmail
                                            )
         )
      {
        $_SESSION['arc_message'] = 'Thank you ' . $userDetails['first_name'] . '. An email has been sent to you with a ' .
                                   'link to click on that will log you into the Identivize Access Request Console where you ' .
                                   'can reset your password.<br><br>' .
                                   'Please close this window and check your emails (check your spam folder as well, ' .
                                   'if not arrived), and click on the link in the email to continue.<br><br>' .
                                   'The email may take a couple of minutes to arrive, so please be patient.';
                               
        $_SESSION['arc_pwdreset_login_id'] = '';
        $_SESSION['arc_pwdreset_error_message'] = '';

        header( 'Location: ../arcsh/msg' );
      }
      else
      {
        $_SESSION['arc_pwdreset_error_message'] = 'Error: Failed to send password reset verification email.';
        header( 'Location: ../arc/' . $tenantName . '/?cmd=promptpwdreset' );
      }
    }
    else
    {
      $_SESSION['arc_pwdreset_error_message'] = 'Error: We did not find an account with that email address.';
      header( 'Location: ../arc/' . $tenantName . '/?cmd=promptpwdreset' );
    }
  }
  else if ( $cmd == 'mgr_approvals' )
  {
    $submitButtonCommand = $_POST['submit_button'];
    $mgrAction = '';
    $updateOK = true;
    
    if ( !isset( $_SESSION['arc_mgr_processed_action_items'] ))
    {
      $_SESSION['arc_mgr_processed_action_items'] = array();
    }
    
    switch( $submitButtonCommand )
    {
      case 'Approve':
        $mgrAction = 'APPROVED';
        break;
      case 'Reject':
        $mgrAction = 'REJECTED';
        break;
      case 'Reassign':
        $mgrAction = 'REASSIGN';
        break;
      default:
        $mgrAction = 'NA';
        break;
    }

    if ( isset( $_POST['mgr_appr'] ))  // if any checkboxes have been checked
    {
      $_SESSION['arc_selected_mgr_action_items'] = $_POST['mgr_appr'];
     
      if ( $mgrAction == 'APPROVED' || $mgrAction == 'REJECTED' )
      {
        foreach( $_POST['mgr_appr'] as $row )
        {
          $values = explode( '_', $row );
          
          $requestId = $values[0];
          $requestLineId = $values[1];
  
          if ( updateManagerAction( $conn, $requestId, $requestLineId, $mgrAction, $errorMessage ))
          {
            $_SESSION['arc_mgr_processed_action_items'][] = getRequestLineItemByRequestLineId( $conn, $requestId, $requestLineId );
          }
          else
          {
            $updateOK = false;
            echo '<p>Error: ' . $errorMessage . '</p>';
            break;
          }
        }
        
        if ( $updateOK )
        {
          header( 'Location: ../arcsh/actionitems/?dmd=lst&srl=mgrtd' );
        }
      }
      else if ( $mgrAction == 'REASSIGN' )
      {
        header( 'Location: ../arcsh/actionitemsreassignment/?srl=mgrtd' );
      }
    }
    else
    {
      // If no checkboxes have been checked, just send the user back to the action items page.
      header( 'Location: ../arcsh/actionitems/?dmd=lst&srl=mgrtd' );
    }
  }
  else if ( $cmd == 'ao_approvals' )
  {
    $submitButtonCommand = $_POST['submit_button'];
    $aoAction = '';
    $updateOK = true;
    
    if ( !isset( $_SESSION['arc_ao_processed_action_items'] ))
    {
      $_SESSION['arc_ao_processed_action_items'] = array();
    }
    
    switch( $submitButtonCommand )
    {
      case 'Approve':
        $aoAction = 'APPROVED';
        break;
      case 'Reject':
        $aoAction = 'REJECTED';
        break;
      case 'Reassign':
        $aoAction = 'REASSIGN';
        break;
      default:
        $aoAction = 'NA';
        break;
    }

    if ( isset( $_POST['ao_appr'] ))  // if any checkboxes have been checked
    {
      $_SESSION['arc_selected_ao_action_items'] = $_POST['ao_appr'];
      
      if ( $aoAction == 'APPROVED' || $aoAction == 'REJECTED' )
      {
        $inx = 0;
       
        foreach( $_POST['ao_appr'] as $row )
        {
          $values = explode( '_', $row );
          
          $requestId = $values[0];
          $requestLineId = $values[1];
          
          if ( updateAccessOwnerAction( $conn, $requestId, $requestLineId, $aoAction, $errorMessage ))
          {
            $_SESSION['arc_ao_processed_action_items'][] = getRequestLineItemByRequestLineId( $conn, $requestId, $requestLineId );
          }
          else
          {
            $updateOK = false;
            echo '<p>Error: ' . $errorMessage . '</p>';
            break;
          }
          
          $inx++;
        }
        
        if ( $updateOK )
        {
          header( 'Location: ../arcsh/actionitems/?dmd=lst&srl=acotd' );
        }
      }
      else if ( $aoAction == 'REASSIGN' )
      {
        header( 'Location: ../arcsh/actionitemsreassignment/?srl=acotd' );
      }
    }
    else
    {
      // If no checkboxes have been checked, just send the user back to the action items page.
      header( 'Location: ../arcsh/actionitems/?dmd=lst&srl=acotd' );
    }
  }
  else if ( $cmd == 'pl_assignments_input' )
  {
    if ( isset( $_SESSION['arc_login_id'] ) && $_SESSION['arc_login_id'] != '' )
    {
      if ( isset( $_POST['pl_assign'] ))  // if any checkboxes have been checked
      {
        $_SESSION['arc_selected_pl_action_items'] = $_POST['pl_assign'];
        
        $submitButtonCommand = $_POST['submit_button'];
  
        /*
        if ( $submitButtonCommand == 'Assign to Self' )
        {
          $userDetailsErrorMessage = '';
          $userDetails = array();
          
          $userDetails = getUserDetails( $conn, $_SESSION['arc_login_id'], $userDetailsErrorMessage );
          
          if ( $userDetailsErrorMessage == '' && count( $userDetails ) > 0 )
          {
            $loginFullName = $userDetails['first_name'] . ' ' . $userDetails['last_name'];
            $errorMessage = '';
            $updateSuccess = true;
         
            foreach( $_SESSION['arc_selected_pl_action_items'] as $row )
            {
              $keyValues = explode( '_', $row );
              $requestId = $keyValues[0];
              $requestLineId = $keyValues[1];
              
              if ( !updateRequestLineItemProvisioner( $conn,
                                                      $requestId,
                                                      $requestLineId,
                                                      $loginFullName,
                                                      $_SESSION['arc_login_id'],
                                                      $_SESSION['arc_login_id'],
                                                      'Assigned to self',
                                                      $errorMessage
                                                    )        
                 )
              {
                $updateSuccess = false;
                break;
              }
            }
            
            if ( $updateSuccess )
            {
              // Clear the array that stores the selections.
              $_SESSION['arc_selected_pl_action_items'] = array();
              header( 'Location: ../arcsh/actionitems/?dmd=lst&srl=pldtd' );
            }
            else
            {
              echo '<p>' . $errorMessage . '</p>';
            }
          }
          else
          {
            echo '<p>' . $userDetailsErrorMessage . '</p>';           
          }
        }
        */
        
        // Here the provisioner lead is directly marking the provisioning
        // task as complete, as if he/she did the provisioning him/herself.
        // We still want to set the provisioner name, email, etc. to the
        // the name of the provisioner lead so that we have a record of
        // which provisioner lead performed the task.
        if ( $submitButtonCommand == 'Mark as Complete' )
        {
          $userDetails = array();
          $errorMessage = '';
          $updateSuccess = true;
          
          $userDetails = getUserDetails( $conn, $_SESSION['arc_login_id'], $errorMessage );
          
          if ( count( $userDetails ) > 0 && $errorMessage == '' )
          {
            $loginFullName = $userDetails['first_name'] . ' ' . $userDetails['last_name'];
           
            foreach( $_SESSION['arc_selected_pl_action_items'] as $row )
            {
              $keyValues = explode( '_', $row );
              $requestId = $keyValues[0];
              $requestLineId = $keyValues[1];
              
              if ( updateRequestLineItemProvisioner( $conn,
                                                     $requestId,
                                                     $requestLineId,
                                                     $loginFullName,
                                                     $_SESSION['arc_login_id'],
                                                     $_SESSION['arc_login_id'],
                                                     'Assigned to self and marked as complete.',
                                                     $errorMessage
                                                   )
                 )
              {
                if ( !markProvisioningComplete( $conn,
                                                $requestId,
                                                $requestLineId,
                                                $errorMessage
                                              )
                   )
                {
                  $updateSuccess = false;
                  break;
                }
              }
              else
              {
                $updateSuccess = false;
                break;
              }
            }
            
            if ( $updateSuccess )
            {
              // Clear the array that stores the selections.
              $_SESSION['arc_selected_pl_action_items'] = array();
              header( 'Location: ../arcsh/actionitems/?dmd=lst&srl=pldtd' );
            }
            else
            {
              echo '<p>' . $errorMessage . '</p>';
            }
          }
          else
          {
            echo '<p>' . $errorMessage . '</p>';
          }
        }
        else if ( $submitButtonCommand == 'Reassign' )
        {
          header( 'Location: ../arcsh/actionitemsreassignment/?srl=pldtd' );
        }
      }
      else
      {
        header( 'Location: ../arcsh/actionitems/?dmd=lst&srl=pldtd' );
      }
    }
    else
    {
      echo '<p>This command requires a login!</p>';
    }
  }
  else if ( $cmd == 'provisioner_tasks_input' )
  {
    if ( isset( $_SESSION['arc_login_id'] ) && $_SESSION['arc_login_id'] != '' )
    {
      if ( isset( $_POST['provisioner_tasks'] ))  // if any checkboxes have been checked
      {
        $_SESSION['arc_selected_provisioner_action_items'] = $_POST['provisioner_tasks'];

        $submitButtonCommand = $_POST['submit_button'];
  
        if ( $submitButtonCommand == 'Mark as Complete' )
        {
          $errorMessage = '';
          $updateSuccess = true;
         
          foreach( $_SESSION['arc_selected_provisioner_action_items'] as $row )
          {
            $keyValues = explode( '_', $row );
            $requestId = $keyValues[0];
            $requestLineId = $keyValues[1];
            
            if ( !markProvisioningComplete( $conn,
                                            $requestId,
                                            $requestLineId,
                                            $errorMessage
                                 )        
               )
            {
              $updateSuccess = false;
              break;
            }
          }
          
          if ( $updateSuccess )
          {
            // Clear the array that stores the selections.
            $_SESSION['arc_selected_provisioner_action_items'] = array();
            header( 'Location: ../arcsh/actionitems/?dmd=lst&srl=prvtd' );
          }
          else
          {
            echo '<p>' . $errorMessage . '</p>'; 
          }
        }
        else if ( $submitButtonCommand == 'Revert to Provisioning Lead' )
        {
          // echo 'Revert to Provisioning Lead selected.';
          header( 'Location: ../arcsh/actionitemsreassignment/?srl=prvtd' );
        }
      }
      else
      {
        header( 'Location: ../arcsh/actionitems/?dmd=lst&srl=prvtd' );
      }
    }
    else
    {
      echo '<p>This command requires a login!</p>';
    }
  }
  else if ( $cmd == 'get_req_appr_dtl' )
  {
    $requestId = $_GET['reqid'];
    $requestLineId = $_GET['reqlineid'];
    
    $requestLineItem = getRequestLineItemByRequestLineId( $conn,
                                                          $requestId,
                                                          $requestLineId
                                                        );
    
    // Chris Biddle, 01/02/2024
    // This is an AJAX response that is going to update the contents of an HTML element. A better practice
    // would be to return the array as JSON and have the front end do the HTML formatting, but I'm taking
    // a bit of a shortcut and embedding the HTML here in the interest of time.
    
    echo '<table>' .
         '<tr><td style="padding-right:10px">Request Identifier:</td><td>' . $requestId . '-' . $requestLineId . '</td></tr>' .
         '<tr><td style="padding-right:10px">Requestor:</td><td>' . $requestLineItem['requestor_name'] . ' (' . $requestLineItem['requestor_email'] . ')</td></tr>' .
         '<tr><td style="padding-right:10px">Job Title:</td><td>' . $requestLineItem['requestor_job_title'] . '</td></tr>' .
         '<tr><td style="padding-right:10px">Company Name:</td><td>' . $requestLineItem['requestor_company_name'] . '</td></tr>' .
         '<tr><td style="padding-right:10px">Access Name:</td><td>' . $requestLineItem['access_name'] . '</td></tr>' .
         '<tr><td style="padding-right:10px">Access Description:</td><td>' . $requestLineItem['access_description'] . '</td></tr>' .
         '<tr><td style="padding-right:10px">Access Owner:</td><td>' . $requestLineItem['access_owner_name'] . ' (' . $requestLineItem['access_owner_email'] . ')</td></tr>' .
         '<tr><td style="padding-right:10px">Access Source:</td><td>' . $requestLineItem['access_source'] . '</td></tr>' .
         '<tr><td style="padding-right:10px">Application Name:</td><td>' . $requestLineItem['application_name'] . '</td></tr>' .
         '<tr><td style="padding-right:10px">Risk Rating:</td><td>' . $requestLineItem['risk_rating'] . '</td></tr>' .
         '<tr><td style="padding-right:10px">Access Requested On:</td><td>' . $requestLineItem['formatted_request_date'] . '</td></tr>' .
         '<tr><td style="padding-right:10px">Requestor Notes:</td><td>' . $requestLineItem['business_justification'] . '</td></tr>' .
         '<tr><td style="padding-right:10px">Manager:</td><td>' . $requestLineItem['manager_name'] . ' (' . $requestLineItem['manager_email'] . ')</td></tr>' .
         '</table>';
         
  }
  else if ( $cmd == 'mgr_approval_reassignment' )
  {
    if ( isset( $_SESSION['arc_login_id'] ) && $_SESSION['arc_login_id'] != '' )
    {
      $assigneeName = '';
      $assigneeEmail = '';
      $assigneeComment = '';
      $selectedMgrActionItems = array();
      $updateSuccess = true;
      $errorMessage = '';
      
      if ( isset( $_POST['assignee_name'] ) && $_POST['assignee_name'] != '' )
      {
        $assigneeName = $_POST['assignee_name'];
      }
  
      if ( isset( $_POST['assignee_email'] ) && $_POST['assignee_email'] != '' )
      {
        $assigneeEmail = $_POST['assignee_email'];
      }
  
      if ( isset( $_POST['comment'] ) && $_POST['comment'] != '' )
      {
        $assigneeComment = $_POST['comment'];
      }
  
      if ( isset( $_SESSION['arc_selected_mgr_action_items'] ))
      {
        $selectedMgrActionItems = $_SESSION['arc_selected_mgr_action_items'];
      }
  
      if ( count( $selectedMgrActionItems ) > 0 )
      {
        foreach( $selectedMgrActionItems as $row )
        {
          $keyValues = explode( '_', $row );
          $requestId = $keyValues[0];
          $requestLineId = $keyValues[1];
          
          if ( !updateRequestLineItemManager( $conn,
                                              $requestId,
                                              $requestLineId,
                                              $assigneeName,
                                              $assigneeEmail,
                                              $assigneeComment,
                                              $errorMessage
                                            )        
             )
          {
            $updateSuccess = false;
            break;
          }
        }
        
        if ( $updateSuccess )
        {
          // Now that we have successfully reassigned the action items to
          // a new manager, we can clear the array that stores the selections.
          $_SESSION['arc_selected_mgr_action_items'] = array();
          header( 'Location: ../arcsh/actionitems/?dmd=lst&srl=mgrtd' );
        }
        else
        {
          echo '<p>' . $errorMessage . '</p>'; 
        }
      }
      else
      {
        echo '<p>No items to update!</p>';
      }
    }
    else
    {
      echo '<p>This command requires a login!</p>';
    }
  }
  else if ( $cmd == 'ao_approval_reassignment' )
  {
    if ( isset( $_SESSION['arc_login_id'] ) && $_SESSION['arc_login_id'] != '' )
    {
      $assigneeName = '';
      $assigneeEmail = '';
      $assigneeComment = '';
      $selectedAccessOwnerActionItems = array();
      $updateSuccess = true;
      $errorMessage = '';
      
      if ( isset( $_POST['assignee_name'] ) && $_POST['assignee_name'] != '' )
      {
        $assigneeName = $_POST['assignee_name'];
      }
  
      if ( isset( $_POST['assignee_email'] ) && $_POST['assignee_email'] != '' )
      {
        $assigneeEmail = $_POST['assignee_email'];
      }
  
      if ( isset( $_POST['comment'] ) && $_POST['comment'] != '' )
      {
        $assigneeComment = $_POST['comment'];
      }
  
      if ( isset( $_SESSION['arc_selected_ao_action_items'] ))
      {
        $selectedAccessOwnerActionItems = $_SESSION['arc_selected_ao_action_items'];
      }
  
      if ( count( $selectedAccessOwnerActionItems ) > 0 )
      {
        foreach( $selectedAccessOwnerActionItems as $row )
        {
          $keyValues = explode( '_', $row );
          $requestId = $keyValues[0];
          $requestLineId = $keyValues[1];
          
          if ( !updateRequestLineItemAccessOwner( $conn,
                                                  $requestId,
                                                  $requestLineId,
                                                  $assigneeName,
                                                  $assigneeEmail,
                                                  $assigneeComment,
                                                  $errorMessage
                                                )        
             )
          {
            $updateSuccess = false;
            break;
          }
        }
        
        if ( $updateSuccess )
        {
          // Clear the selected items from the session
          $_SESSION['arc_selected_ao_action_items'] = array();
          header( 'Location: ../arcsh/actionitems/?dmd=lst&srl=acotd' );
        }
        else
        {
          echo '<p>' . $errorMessage . '</p>'; 
        }
      }
      else
      {
        echo '<p>No items to update!</p>';
      }
    }
    else
    {
      echo '<p>This command requires a login!</p>';
    }
  }
  else if ( $cmd == 'pl_provisioner_assignment' )
  {
    if ( isset( $_SESSION['arc_login_id'] ) && $_SESSION['arc_login_id'] != '' )
    {
      $assigneeName = '';
      $assigneeEmail = '';
      $assigneeComment = '';
      $selectedProvisionerActionItems = array();
      $updateSuccess = true;
      $errorMessage = '';
    
      if ( isset( $_POST['assignee_name'] ) && $_POST['assignee_name'] != '' )
      {
        $assigneeName = $_POST['assignee_name'];
      }
  
      if ( isset( $_POST['assignee_email'] ) && $_POST['assignee_email'] != '' )
      {
        $assigneeEmail = $_POST['assignee_email'];
      }
  
      if ( isset( $_POST['comment'] ) && $_POST['comment'] != '' )
      {
        $assigneeComment = $_POST['comment'];
      }
  
      if ( isset( $_SESSION['arc_selected_pl_action_items'] ))
      {
        $selectedProvisionerActionItems = $_SESSION['arc_selected_pl_action_items'];
      }
  
      if ( count( $selectedProvisionerActionItems ) > 0 )
      {
        foreach( $selectedProvisionerActionItems as $row )
        {
          $keyValues = explode( '_', $row );
          $requestId = $keyValues[0];
          $requestLineId = $keyValues[1];
          
          if ( !updateRequestLineItemProvisioner( $conn,
                                                  $requestId,
                                                  $requestLineId,
                                                  $assigneeName,
                                                  $assigneeEmail,
                                                  $_SESSION['arc_login_id'],
                                                  $assigneeComment,
                                                  $errorMessage
                                                )
             )
          {
            $updateSuccess = false;
            break;
          }
        }
        
        if ( $updateSuccess )
        {
          // Clear the selected items from the session
          $_SESSION['arc_selected_pl_action_items'] = array();
          header( 'Location: ../arcsh/actionitems/?dmd=lst&srl=pldtd' );
        }
        else
        {
          echo '<p>' . $errorMessage . '</p>'; 
        }
      }
      else
      {
        echo '<p>No items to update!</p>';
      }
    }
    else
    {
      echo '<p>This command requires a login!</p>';
    }
  }
  else if ( $cmd == 'provisioner_revert_to_lead' )
  {
    if ( isset( $_SESSION['arc_login_id'] ) && $_SESSION['arc_login_id'] != '' )
    {
      $comment = '';
      $updateSuccess = true;
      $errorMessage = '';
  
      if ( isset( $_POST['comment'] ) && $_POST['comment'] != '' )
      {
        $comment = $_POST['comment'];
      }
  
      if ( isset( $_SESSION['arc_selected_pl_action_items'] ))
      {
        $selectedProvisionerActionItems = $_SESSION['arc_selected_provisioner_action_items'];
      }
  
      if ( count( $selectedProvisionerActionItems ) > 0 )
      {
        foreach( $selectedProvisionerActionItems as $row )
        {
          $keyValues = explode( '_', $row );
          $requestId = $keyValues[0];
          $requestLineId = $keyValues[1];
  
          if ( !revertProvisionerToProvisioningLead( $conn,
                                                     $requestId,
                                                     $requestLineId,
                                                     $comment,
                                                     $errorMessage
                                                   )
             )
          {
            $updateSuccess = false;
            break;
          }
        }
          
        if ( $updateSuccess )
        {
          // Clear the selected items from the session
          $_SESSION['arc_selected_provisioner_action_items'] = array();
          header( 'Location: ../arcsh/actionitems/?dmd=lst&srl=prvtd' );
        }
        else
        {
          echo '<p>' . $errorMessage . '</p>'; 
        }
      }
      else
      {
        echo '<p>No items to update!</p>';
      }
    }
    else
    {
      echo '<p>This command requires a login!</p>';
    }
  }
  else
  {
    echo '<p>Command not recognized.</p>';
  }

?>