<?php

  function closeDbConn( $pConn )
  {
    mysqli_close( $pConn );
  }

  function createUser( $pConn,
                       $pLoginId,
                       $pPasswd,
                       $pLastName,
                       $pFirstName,
                       $pJobTitle,
                       $pCompanyName,
                       $pTenantName,   // Access Request Console Name
                       $pAdminLevel,
                       $pEmailDomains,
                       &$pValidationHandle,
                       &$pErrorMsg
                     )
  {
    $rCode = false;
    $noDupes = false;
    $noTenant = false;

    // Make sure the user_id is unique
    
    $numRecs = 0;
    
    if ( $stmt1 = mysqli_prepare( $pConn, 'select count(*) num_recs
                                           from users
                                           where user_id = ?'
                                 )
       )
    {
      mysqli_stmt_bind_param( $stmt1, "s", $pLoginId );
      mysqli_stmt_execute( $stmt1 );
      mysqli_stmt_bind_result( $stmt1, $numRecs );

      while ( mysqli_stmt_fetch( $stmt1 ))
      {
        if ( $numRecs == 0 )
        {
          $noDupes = true;
        }
        else
        {
          $pErrorMsg = 'Error: Account with that email address already exists.';
        }
      }
      
      mysqli_stmt_close( $stmt1 );
    }

    $numRecs = 0;
    
    if ( $stmt2 = mysqli_prepare( $pConn, 'select count(*) num_recs
                                           from users
                                           where tenant_name = ?'
                                )
       )
    {
      mysqli_stmt_bind_param( $stmt2, "s", $pTenantName );
      mysqli_stmt_execute( $stmt2 );
      mysqli_stmt_bind_result( $stmt2, $numRecs );

      while ( mysqli_stmt_fetch( $stmt2 ))
      {
        if ( $numRecs == 0 )
        {
          $noTenant = true;
        }
        else
        {
          // If the user being created is a manager, then we need to make sure the tenant_name
          // doesn't already exist, as the manager himself/herself is supposed to designate
          // the tenant name.
          if ( $pAdminLevel == 2 )
          {
            $pErrorMsg = 'Error: Account with that access request console name already exists.';
          }
          else // If not a manager, then OK to use the tenant name
          {
            $noTenant = true;
          }
        }
      }
      
      mysqli_stmt_close( $stmt2 );
    }

    if ( $noDupes && $noTenant )
    {
      $encryptedPasswd = password_hash( $pPasswd, PASSWORD_DEFAULT );
      $pValidationHandle = uniqid();

      if ( $stmt3 = mysqli_prepare( $pConn, 'insert into users
                                             ( user_id,
                                               passwd,
                                               last_name,
                                               first_name,
                                               job_title,
                                               company_name,
                                               tenant_name,
                                               admin_level,
                                               validation_handle,
                                               valid_email_domain
                                             )
                                             values ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )'
                                  )
         )
      {
        mysqli_stmt_bind_param( $stmt3, "ssssssssss", 
                                $pLoginId,
                                $encryptedPasswd,
                                $pLastName,
                                $pFirstName,
                                $pJobTitle,
                                $pCompanyName,
                                $pTenantName,
                                $pAdminLevel,
                                $pValidationHandle,
                                $pEmailDomains
                              );
  
        if ( mysqli_stmt_execute( $stmt3 ))
        {
          $rCode = true;
        }
        else
        {
          $pErrorMsg = 'Error: Data entry failure. Please try again later. (CD101)';
        }
        
        mysqli_stmt_close( $stmt3 );
      }
      else
      {
        $pErrorMsg = 'Error: Data entry failure. Please try again later. (CD102)';
      }
    }

    return $rCode;
  }

  function validateLogin( $pConn,
                          $pLoginId,
                          $pPasswd,
                          $pAdminLevel,
                          &$pTenantName,
                          &$pUserName,
                          &$pErrorMsg
                        )
  {
    $rCode = false;

    if ( trim( $pLoginId ) != '' )
    {
      if ( trim( $pPasswd ) != '' )
      {
        $sql = 'select passwd,
                  tenant_name,
                  concat( concat( first_name, " " ), last_name ) user_name
                from users
                where user_id = ? ';
                
        $adminClause = '';

        // 1 is superuser, 2 is manager (workflow creator), 3 is staff (app access requestor)

        if ( $pAdminLevel == 1 ) // superuser
        {
          // if logging in as a superuser (1), your admin_level
          // can only be superuser (1)
          $adminClause = 'and admin_level = 1 ';
        }
        else if ( $pAdminLevel == 2 ) // manager
        {
          // if logging in as a manager (2), your admin_level has to be
          // at least manager (2) or superuser (1)
          $adminClause = 'and admin_level in ( 2, 1 ) ';
        }
        else if ( $pAdminLevel == 3 ) // staff
        {
          // if logging in as a staffer (2), your admin_level has to be
          // at least staff (3), manager (2) or superuser (1)
          $adminClause = 'and admin_level in ( 3, 2, 1 ) ';
        }

        $sql .= $adminClause;
        
        $endClause = 'and validated = "Y"';
        
        $sql .= $endClause;
 
        if ( $stmt = mysqli_prepare( $pConn, $sql )
           )
        {
          mysqli_stmt_bind_param( $stmt, 's', $pLoginId );
          mysqli_stmt_execute( $stmt );
          mysqli_stmt_bind_result( $stmt, $encryptedPasswd, $pTenantName, $pUserName );
    
          if ( mysqli_stmt_fetch( $stmt ))
          {
            if ( password_verify( $pPasswd, $encryptedPasswd ))
            {
              $rCode = true;
            }
            else
            {
              $pErrorMsg = 'Error: Invalid password.';        
            }
          }
          else
          {
            $pErrorMsg = 'Error: Invalid login ID.';        
          }
          
          mysqli_stmt_close( $stmt );
        }
      }
      else
      {
        $pErrorMsg = 'Error: Password is required.';        
      }
    }
    else
    {
      $pErrorMsg = 'Error: Login ID is required.';        
    }
    
    return $rCode;
  }

  function confirmRegistration( $pConn,
                                $pValidationHandle,
                                &$pTenantName
                              )
  {
    $rCode = false;
   
    if ( $stmt1 = mysqli_prepare( $pConn, 'update users set validated = "Y" where validation_handle = ?' ))
    {
      mysqli_stmt_bind_param( $stmt1, "s", 
                              $pValidationHandle
                            );

      if ( mysqli_stmt_execute( $stmt1 ))
      {
        mysqli_stmt_close( $stmt1 );
        
        if ( $stmt2 = mysqli_prepare( $pConn, 'select tenant_name from users where validation_handle = ?' ))
        {
          mysqli_stmt_bind_param( $stmt2, "s", 
                                  $pValidationHandle
                                );

          if ( mysqli_stmt_execute( $stmt2 ))
          {
            mysqli_stmt_bind_result( $stmt2, $pTenantName );
            mysqli_stmt_fetch( $stmt2 );
            mysqli_stmt_close( $stmt2 );
       
            $rCode = true;
          }
        }
      }
    }

    return $rCode;
  }

  function getUserDetails( $pConn,
                           $pLoginId,
                           &$pErrorMsg
                         )
  {
    $userDetails = array();
   
    if ( $stmt = mysqli_prepare( $pConn, 
                                 'select last_name,
                                    first_name,
                                    company_name,
                                    tenant_name,
                                    admin_level,
                                    validation_handle
                                  from users
                                  where user_id = ?'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, 's', $pLoginId );
      mysqli_stmt_execute( $stmt );
      mysqli_stmt_bind_result( $stmt,
                               $lastName,
                               $firstName,
                               $companyName,
                               $tenantName,
                               $adminLevel,
                               $validationHandle
                             );

      if ( mysqli_stmt_fetch( $stmt ))
      {
        $userDetails['last_name'] = $lastName;
        $userDetails['first_name'] = $firstName;
        $userDetails['company_name'] = $companyName;
        $userDetails['tenant_name'] = $tenantName;
        $userDetails['admin_level'] = $adminLevel;
        $userDetails['validation_handle'] = $validationHandle;
      }
      else
      {
        $pErrorMsg = 'Error: Unable to retrieve user details (CD701)';           
      }
    }
    else
    {
      $pErrorMsg = 'Error: Unable to retrieve user details (CD702)';           
    }
        
    return $userDetails;    
  }

  function getManagerDetailsByTenantName( $pConn,
                                          $pTenantName,  // Same as Access Request Console (ARC) name.
                                          &$pErrorMsg
                                        )
  {
    $userDetails = array();

    // There should only be one record returned, since each
    // "tenant" should only have one manager (i.e. admin_level = 2)

    if ( $stmt = mysqli_prepare( $pConn, 
                                 'select user_id,
                                    last_name,
                                    first_name,
                                    company_name,
                                    admin_level,
                                    validation_handle
                                  from users
                                  where admin_level = 2
                                  and tenant_name = ?'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, 's', $pTenantName );
      mysqli_stmt_execute( $stmt );
      mysqli_stmt_bind_result( $stmt,
                               $userId,
                               $lastName,
                               $firstName,
                               $companyName,
                               $adminLevel,
                               $validationHandle
                             );

      if ( mysqli_stmt_fetch( $stmt ))
      {
        $userDetails['user_id'] = $userId;
        $userDetails['last_name'] = $lastName;
        $userDetails['first_name'] = $firstName;
        $userDetails['company_name'] = $companyName;
        $userDetails['admin_level'] = $adminLevel;
        $userDetails['validation_handle'] = $validationHandle;
      }
      else
      {
        $pErrorMsg = 'Error: Unable to retrieve user details';           
      }
    }
    else
    {
      $pErrorMsg = 'Error: Unable to retrieve user details';           
    }
        
    return $userDetails;    
  }

  function insertWorkflowMaster( $pConn,
                                 $pTenantName,
                                 $pWorkflowName,
                                 $pWorkflowDescription,
                                 &$pErrorMessage
                               )
  {
    $rCode = false;
   
    if ( $stmt = mysqli_prepare( $pConn, 'insert into workflows
                                          (
                                            tenant_name,
                                            workflow_name,
                                            workflow_description
                                          )
                                          values
                                          (
                                            ?,
                                            ?,
                                            ?
                                          )'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, "sss", 
                              $pTenantName,
                              $pWorkflowName,
                              $pWorkflowDescription
                            );

      mysqli_stmt_execute( $stmt );
      mysqli_stmt_close( $stmt );
      
      $rCode = true;
    }
    else
    {
      $pErrorMessage = 'Error: Failed to insert workflow master data';
    }
    
    return $rCode;
  }

  function updateWorkflowMaster( $pConn,
                                 $pTenantName,
                                 $pWorkflowName,
                                 $pWorkflowDescription,
                                 &$pErrorMessage
                               )
  {
    $rCode = false;
   
    if ( $stmt = mysqli_prepare( $pConn, 'update workflows
                                          set workflow_description = ?
                                          where tenant_name = ?
                                          and workflow_name = ?'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, "sss", 
                              $pWorkflowDescription,
                              $pTenantName,
                              $pWorkflowName
                            );

      mysqli_stmt_execute( $stmt );
      mysqli_stmt_close( $stmt );
      
      $rCode = true;
    }
    else
    {
      $pErrorMessage = 'Error: Failed to update workflow master data';
    }
    
    return $rCode;
  }
  
  function deleteWorkflowMaster( $pConn,
                                 $pTenantName,
                                 $pWorkflowName,
                                 &$pErrorMessage
                               )
  {
    $rCode = false;
   
    if ( $stmt = mysqli_prepare( $pConn, 'delete from workflows
                                          where tenant_name = ?
                                          and workflow_name = ?'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, "ss", 
                              $pTenantName,
                              $pWorkflowName
                            );

      mysqli_stmt_execute( $stmt );
      mysqli_stmt_close( $stmt );
      
      $rCode = true;
    }
    else
    {
      $pErrorMessage = 'Error: Failed to delete workflow master record';
    }
    
    return $rCode;
  }

  function insertWorkflowDetails( $pConn,
                                  $pTenantName,
                                  $pWorkflowName,
                                  $pData,
                                  &$pErrorMessage
                                )
  {
    $rCode = false;
    $insertFailure = false;

    $firstRecord = true;    

    foreach( $pData as $row )
    {
      if ( $firstRecord )
      {
        // There is sometimes a byte order marker (﻿) at the beginning of the
        // CSV file. If there is, we need to remove it.
        
        $bom = pack( 'CCC', 0xEF, 0xBB, 0xBF );

        if ( strncmp( $row[0], $bom, 3 ) === 0 )
        {
          $row[0] = substr( $row[0], 3 );
        }
        
      }

      $secondaryProvisioningLeadName = ( $row[11] != 'NA' ) ? $row[11] : NULL;
      $secondaryProvisioningLeadEmail = ( $row[12] != 'NA' ) ? $row[12] : NULL;

      if ( $stmt = mysqli_prepare( $pConn, 'insert into workflow_details
                                            ( tenant_name,
                                              workflow_name,
                                              access_name,
                                              access_description,
                                              application_name,
                                              access_source,
                                              risk_rating,
                                              access_owner_name,
                                              access_owner_email,
                                              manager_approval_required,
                                              access_owner_approval_required,
                                              provisioning_lead_name_1,
                                              provisioning_lead_email_1,
                                              provisioning_lead_name_2,
                                              provisioning_lead_email_2
                                            )
                                            values
                                            ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )'
                                 )
         )
      {
        mysqli_stmt_bind_param( $stmt, "sssssssssssssss", 
                                $pTenantName,
                                $pWorkflowName,
                                $row[0],
                                $row[1],
                                $row[2],
                                $row[3],
                                $row[4],
                                $row[5],
                                $row[6],
                                $row[7],
                                $row[8],
                                $row[9],
                                $row[10],
                                $secondaryProvisioningLeadName,
                                $secondaryProvisioningLeadEmail
                              );
  
        mysqli_stmt_execute( $stmt );
        mysqli_stmt_close( $stmt );
      }
      else
      {
        $insertFailure = true;
        $pErrorMessage = 'Error: Failed to insert workflow detail record.';
        break;
      }
    }

    $deleteErrorMessage = '';
    $deleteStatus = false;

    if( $insertFailure )
    {
      // If there was a failure inserting any of the records,
      // we need to delete whatever did get inserted.
      $deleteStatus = deleteWorkflowDetails( $pConn, $pTenantName, $pWorkflowName, $deleteErrorMessage );
      
      if ( !$deleteStatus )
      {
        $pErrorMessage = ( $pErrorMessage . '. ' . $deleteErrorMessage );
      }
    }
    else
    {
      $rCode = true;
    }

    return $rCode;
  }

  function deleteWorkflowDetails( $pConn,
                                  $pTenantName,
                                  $pWorkflowName,
                                  &$pErrorMessage
                                )
  {
    $rCode = false;
   
    if ( $stmt = mysqli_prepare( $pConn, 'delete from workflow_details
                                          where tenant_name = ?
                                          and workflow_name = ?'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, "ss",
                              $pTenantName,
                              $pWorkflowName
                            );

      mysqli_stmt_execute( $stmt );
      mysqli_stmt_close( $stmt );
      
      $rCode = true;
    }
    else
    {
      $pErrorMessage = 'Error: Failed to delete workflow details';
    }
    
    return $rCode;
  }

  function getUserInfoByValidationHandle( $pConn,
                                          $pValidationHandle
                                        )
  {
    $userInfo = array();
   
    if ( $stmt = mysqli_prepare( $pConn, 
                                 'select user_id,
                                    last_name,
                                    first_name,
                                    job_title,
                                    company_name,
                                    tenant_name,
                                    admin_level,
                                    validated,
                                    valid_email_domain
                                  from users
                                  where validation_handle = ?'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, 's', $pValidationHandle );
      mysqli_stmt_execute( $stmt );
      mysqli_stmt_bind_result( $stmt,
                               $userId,
                               $lastName,
                               $firstName,
                               $jobTitle,
                               $companyName,
                               $tenantName,
                               $adminLevel,
                               $validated,
                               $validEmailDomain
                             );
      
      if ( mysqli_stmt_fetch( $stmt ))
      {
        $userInfo['user_id'] = $userId;
        $userInfo['last_name'] = $lastName;
        $userInfo['first_name'] = $firstName;
        $userInfo['job_title'] = $jobTitle;
        $userInfo['company_name'] = $companyName;
        $userInfo['tenant_name'] = $tenantName;
        $userInfo['admin_level'] = $adminLevel;
        $userInfo['validated'] = $validated;
        $userInfo['valid_email_domain'] = $validEmailDomain;
      }
    }
    
    return $userInfo;
  }

  function updateUserPasswd( $pConn,
                             $pUserId,
                             $pNewPwd,
                             &$pErrorMessage
                           )
  {
    $rCode = false;
    
    $encryptedPasswd = password_hash( $pNewPwd, PASSWORD_DEFAULT );

    if ( $stmt = mysqli_prepare( $pConn, 'update users set passwd = ? where user_id = ?' ))
    {
      mysqli_stmt_bind_param( $stmt, "ss",
                              $encryptedPasswd,
                              $pUserId
                            );

      if ( mysqli_stmt_execute( $stmt ))
      {
        $rCode = true;
      }
      else
      {
        $pErrorMessage = 'Error: Failed to update password. Please try again later. (CD103)';       
      }
    }
    else
    {
      $pErrorMessage = 'Error: Failed to update password. Please try again later. (CD104)';       
    }
    
    return $rCode;
  }

  // This replaces getWorkflowsByUserId( $pConn, $pUserId )
  function getWorkflowsByTenantName( $pConn,
                                     $pTenantName
                                   )
  {
    $workflows = array();
    
    if ( $stmt = mysqli_prepare( $pConn,
                                 'select workflow_name,
                                    workflow_description
                                  from workflows
                                  where tenant_name = ?
                                  order by workflow_name asc'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, "s", $pTenantName );
      mysqli_stmt_execute( $stmt );
      
      mysqli_stmt_bind_result( $stmt,
                               $workflowName,
                               $workflowDescription
                             );

      $inx = 0;

      while ( mysqli_stmt_fetch( $stmt ))
      {
        $workflows[$inx]['workflow_name'] = $workflowName;
        $workflows[$inx]['workflow_description'] = $workflowDescription;

        $inx++;
      }
      
      mysqli_stmt_close( $stmt );
    }

    return $workflows;
  }
  
  function getWorkflowByWorkflowName( $pConn,
                                      $pTenantName,
                                      $pWorkflowName
                                    )
  {
    $workflow = array();
    
    if ( $stmt = mysqli_prepare( $pConn,
                                 'select workflow_description
                                  from workflows
                                  where tenant_name = ?
                                  and workflow_name = ?'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, "ss", $pTenantName, $pWorkflowName );
      mysqli_stmt_execute( $stmt );
      
      mysqli_stmt_bind_result( $stmt,
                               $workflowDescription
                             );

      // Expecting only on record, so we don't need
      // to put the fetch statement in a loop.
      if ( mysqli_stmt_fetch( $stmt ))
      {
        $workflow['workflow_description'] = $workflowDescription;
      }
      
      mysqli_stmt_close( $stmt );
    }

    return $workflow;
  }

  function getWorkflowDetailsByWorkflowName( $pConn,
                                             $pTenantName,
                                             $pWorkflowName
                                           )
  {
    $workflowDetails = array();
    
    if ( $stmt = mysqli_prepare( $pConn,
                                 'select access_name,
                                    access_description,
                                    application_name,
                                    access_source,
                                    risk_rating,
                                    access_owner_name,
                                    access_owner_email,
                                    manager_approval_required,
                                    access_owner_approval_required,
                                    provisioning_lead_name_1,
                                    provisioning_lead_email_1,
                                    ifnull( provisioning_lead_name_2, "NA" ),
                                    ifnull( provisioning_lead_email_2, "NA" )
                                  from workflow_details
                                  where tenant_name = ?
                                  and workflow_name = ?
                                  order by access_name asc'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, "ss", $pTenantName, $pWorkflowName );
      mysqli_stmt_execute( $stmt );
      
      mysqli_stmt_bind_result( $stmt,
                               $accessName,
                               $accessDescription,
                               $applicationName,
                               $accessSource,
                               $riskRating,
                               $accessOwnerName,
                               $accessOwnerEmail,
                               $managerApprovalRequired,
                               $accessOwnerApprovalRequired,
                               $provisioningLeadName1,
                               $provisioningLeadEmail1,
                               $provisioningLeadName2,
                               $provisioningLeadEmail2
                             );

      $inx = 0;

      while ( mysqli_stmt_fetch( $stmt ))
      {
        $workflowDetails[$inx]['access_name'] = $accessName;
        $workflowDetails[$inx]['access_description'] = $accessDescription;
        $workflowDetails[$inx]['application_name'] = $applicationName;
        $workflowDetails[$inx]['access_source'] = $accessSource;
        $workflowDetails[$inx]['risk_rating'] = $riskRating;
        $workflowDetails[$inx]['access_owner_name'] = $accessOwnerName;
        $workflowDetails[$inx]['access_owner_email'] = $accessOwnerEmail;
        $workflowDetails[$inx]['manager_approval_required'] = $managerApprovalRequired;
        $workflowDetails[$inx]['access_owner_approval_required'] = $accessOwnerApprovalRequired;
        $workflowDetails[$inx]['provisioning_lead_name_1'] = $provisioningLeadName1;
        $workflowDetails[$inx]['provisioning_lead_email_1'] = $provisioningLeadEmail1;
        $workflowDetails[$inx]['provisioning_lead_name_2'] = $provisioningLeadName2;
        $workflowDetails[$inx]['provisioning_lead_email_2'] = $provisioningLeadEmail2;

        $inx++;
      }
      
      mysqli_stmt_close( $stmt );
    }

    return $workflowDetails;
  }
  
  function searchWorkflowDetails( $pConn,
                                  $pSearchString,
                                  $pTenantName
                                )
  {
    $workflowDetails = array();
    
    // Prepare the search string to be passed into a LIKE
    // clause. Note that mysql queries using LIKE are NOT
    // case sensitive by default, so we don't need to worry
    // about doing any uppercasing tricks.
    $preparedSearchString = '%' . $pSearchString . '%';
    
    if ( $stmt = mysqli_prepare( $pConn,
                                 'select workflow_name,
                                    access_name,
                                    access_description,
                                    application_name,
                                    access_source,
                                    risk_rating,
                                    access_owner_name,
                                    access_owner_email,
                                    manager_approval_required,
                                    access_owner_approval_required,
                                    provisioning_lead_name_1,
                                    provisioning_lead_email_1,
                                    provisioning_lead_name_2,
                                    provisioning_lead_email_2
                                  from workflow_details
                                  where tenant_name = ?
                                  and ( access_name like ? or access_description like ? )
                                  order by access_name asc'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, "sss",
                              $pTenantName,
                              $preparedSearchString,
                              $preparedSearchString
                            );

      mysqli_stmt_execute( $stmt );
      
      mysqli_stmt_bind_result( $stmt,
                               $workflowName,
                               $accessName,
                               $accessDescription,
                               $applicationName,
                               $accessSource,
                               $riskRating,
                               $accessOwnerName,
                               $accessOwnerEmail,
                               $managerApprovalRequired,
                               $accessOwnerApprovalRequired,
                               $provisioningLeadName1,
                               $provisioningLeadEmail1,
                               $provisioningLeadName2,
                               $provisioningLeadEmail2
                             );

      $inx = 0;

      while ( mysqli_stmt_fetch( $stmt ))
      {
        $workflowDetails[$inx]['workflow_name'] = $workflowName;
        $workflowDetails[$inx]['access_name'] = $accessName;
        $workflowDetails[$inx]['access_description'] = $accessDescription;
        $workflowDetails[$inx]['application_name'] = $applicationName;
        $workflowDetails[$inx]['access_source'] = $accessSource;
        $workflowDetails[$inx]['risk_rating'] = $riskRating;
        $workflowDetails[$inx]['access_owner_name'] = $accessOwnerName;
        $workflowDetails[$inx]['access_owner_email'] = $accessOwnerEmail;
        $workflowDetails[$inx]['manager_approval_required'] = $managerApprovalRequired;
        $workflowDetails[$inx]['access_owner_approval_required'] = $accessOwnerApprovalRequired;
        $workflowDetails[$inx]['provisioning_lead_name_1'] = $provisioningLeadName1;
        $workflowDetails[$inx]['provisioning_lead_email_1'] = $provisioningLeadEmail1;
        $workflowDetails[$inx]['provisioning_lead_name_2'] = $provisioningLeadName2;
        $workflowDetails[$inx]['provisioning_lead_email_2'] = $provisioningLeadEmail2;

        $inx++;
      }
      
      mysqli_stmt_close( $stmt );
    }

    return $workflowDetails;
  }

  function insertRequestHeader( $pConn,
                                $pData,
                                &$pRequestId,
                                &$pErrorMessage
                              )
  {
    $rCode = false;
   
    if ( $stmt = mysqli_prepare( $pConn, 'insert into request_header
                                          (
                                            request_date,
                                            tenant_name,
                                            user_id,
                                            manager_name,
                                            manager_email,
                                            business_justification,
                                            request_status
                                          )
                                          values
                                          ( now(), ?, ?, ?, ?, ?, ? )'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, "ssssss", 
                              $pData['tenant_name'],
                              $pData['user_id'],
                              $pData['manager_name'],
                              $pData['manager_email'],
                              $pData['business_justification'],
                              $pData['request_status']
                            );

      mysqli_stmt_execute( $stmt );
      mysqli_stmt_close( $stmt );
      
      $pRequestId = mysqli_insert_id( $pConn );
      
      $rCode = true;
    }
    else
    {
      $pErrorMessage = 'Error: Failed to insert request header data';
    }
    
    return $rCode;
  }
  
  function insertRequestDetails( $pConn,
                                 $pRequestId,
                                 $pTenantName,
                                 $pAccessSearchResults,
                                 $pSelectedAccess, // Index of user selected access in $pAccessSearchResults
                                 $pManagerName,
                                 $pManagerEmail,
                                 $pErrorMessage
                               )
  {
    $rCode = true;

    $inx = 1;

    foreach( $pSelectedAccess as $selectedAccessIndex )
    {
      if ( $stmt = mysqli_prepare( $pConn, 'insert into request_line_items
                                            ( request_id,
                                              request_line_id,
                                              tenant_name,
                                              workflow_name,
                                              access_name,
                                              access_description,
                                              application_name,
                                              access_source,
                                              risk_rating,
                                              manager_name,
                                              manager_email,
                                              access_owner_name,
                                              access_owner_email,
                                              manager_approval_required,
                                              access_owner_approval_required,
                                              provisioning_lead_name_1,
                                              provisioning_lead_email_1,
                                              provisioning_lead_name_2,
                                              provisioning_lead_email_2
                                            )
                                            values
                                            ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )'
                                 )
         )
      {
        mysqli_stmt_bind_param( $stmt, "iisssssssssssssssss", 
                                $pRequestId,
                                $inx,
                                $pTenantName,
                                $pAccessSearchResults[$selectedAccessIndex]['workflow_name'],
                                $pAccessSearchResults[$selectedAccessIndex]['access_name'],
                                $pAccessSearchResults[$selectedAccessIndex]['access_description'],
                                $pAccessSearchResults[$selectedAccessIndex]['application_name'],
                                $pAccessSearchResults[$selectedAccessIndex]['access_source'],
                                $pAccessSearchResults[$selectedAccessIndex]['risk_rating'],
                                $pManagerName,
                                $pManagerEmail,
                                $pAccessSearchResults[$selectedAccessIndex]['access_owner_name'],
                                $pAccessSearchResults[$selectedAccessIndex]['access_owner_email'],
                                $pAccessSearchResults[$selectedAccessIndex]['manager_approval_required'],
                                $pAccessSearchResults[$selectedAccessIndex]['access_owner_approval_required'],
                                $pAccessSearchResults[$selectedAccessIndex]['provisioning_lead_name_1'],
                                $pAccessSearchResults[$selectedAccessIndex]['provisioning_lead_email_1'],
                                $pAccessSearchResults[$selectedAccessIndex]['provisioning_lead_name_2'],
                                $pAccessSearchResults[$selectedAccessIndex]['provisioning_lead_email_2']
                              );
  
        mysqli_stmt_execute( $stmt );
        mysqli_stmt_close( $stmt );
      }
      else
      {
        $pErrorMessage = 'Error: Failed to insert request line item record';
        break;
      }
      
      $inx++;
    }

    return $rCode;
  }
  
  function getRequestLineItemsByRequestId( $pConn,
                                           $pRequestId,
                                           $pTenantName
                                         )
  {
    $requestLineItems = array();

    if ( $stmt = mysqli_prepare( $pConn, 
                                 'select request_line_id,
                                    tenant_name,
                                    workflow_name,
                                    access_name,
                                    access_description,
                                    application_name,
                                    access_source,
                                    risk_rating,
                                    access_owner_name,
                                    access_owner_email,
                                    manager_approval_required,
                                    access_owner_approval_required
                                  from request_line_items
                                  where request_id = ?
                                  and tenant_name = ?
                                  order by request_line_id asc'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, 'is', $pRequestId, $pTenantName );
      mysqli_stmt_execute( $stmt );
      mysqli_stmt_bind_result( $stmt,
                               $requestLineId,
                               $tenantName,
                               $workflowName,
                               $accessName,
                               $accessDescription,
                               $applicationName,
                               $accessSource,
                               $riskRating,
                               $accessOwnerName,
                               $accessOwnerEmail,
                               $managerApprovalRequired,
                               $accessOwnerApprovalRequired
                             );

      $inx = 0;

      while ( mysqli_stmt_fetch( $stmt ))
      {
        $requestLineItems[$inx]['request_line_id'] = $requestLineId;
        $requestLineItems[$inx]['tenant_name'] = $tenantName;
        $requestLineItems[$inx]['workflow_name'] = $workflowName;
        $requestLineItems[$inx]['access_name'] = $accessName;
        $requestLineItems[$inx]['access_description'] = $accessDescription;
        $requestLineItems[$inx]['application_name'] = $applicationName;
        $requestLineItems[$inx]['access_source'] = $accessSource;
        $requestLineItems[$inx]['risk_rating'] = $riskRating;
        $requestLineItems[$inx]['access_owner_name'] = $accessOwnerName;
        $requestLineItems[$inx]['access_owner_email'] = $requestLineId;
        $requestLineItems[$inx]['manager_approval_required'] = $managerApprovalRequired;
        $requestLineItems[$inx]['access_owner_approval_required'] = $accessOwnerApprovalRequired;
        
        $inx++;
      }
    }
        
    return $requestLineItems;    
  }
  
  function getAccessRequestStatusesByUserId( $pConn,
                                             $pUserId,
                                             $pTenantName
                                           )
  {
    $requestStatuses = array();

    if ( $stmt = mysqli_prepare( $pConn, 
                                 'select rh.request_id,
                                    rh.request_date,
                                    ( select count(*)
                                      from request_line_items rli
                                      where rli.request_id = rh.request_id
                                    ) as request_details,
                                    ( select count(*)
                                      from request_line_items rli
                                      where rli.request_id = rh.request_id
                                      and rli.manager_approval_required = "Y"
                                    ) as mgr_approval_required,
                                    ( select count(*)
                                      from request_line_items rli
                                      where rli.request_id = rh.request_id
                                      and rli.manager_action = "APPROVED"
                                    ) as mgr_approved,
                                    ( select count(*)
                                      from request_line_items rli
                                      where rli.request_id = rh.request_id
                                      and rli.access_owner_approval_required = "Y"
                                    ) as ao_approval_required,
                                    ( select count(*)
                                      from request_line_items rli
                                      where rli.request_id = rh.request_id
                                      and rli.access_owner_action = "APPROVED"
                                    ) as ao_approved,
                                    ( select count(*)
                                      from request_line_items rli
                                      where rli.request_id = rh.request_id
                                      and rli.provisioning_status = "COMPLETE"
                                    ) as provisioning_complete    
                                  from request_header rh
                                  where rh.user_id = ?
                                    and rh.tenant_name = ?
                                  order by rh.request_date desc'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, 'ss', $pUserId, $pTenantName );
      mysqli_stmt_execute( $stmt );
      mysqli_stmt_bind_result( $stmt,
                               $requestId,
                               $requestDate,
                               $requestDetails,
                               $mgrApprovalRequired,
                               $mgrApproved,
                               $aoApprovalRequired,
                               $aoApproved,
                               $provisioningComplete
                             );

      $inx = 0;

      while ( mysqli_stmt_fetch( $stmt ))
      {
        $status = 'Submitted';
       
        if ( $mgrApprovalRequired == $mgrApproved &&
             $aoApprovalRequired == $aoApproved
           )
        {
          $status = 'Approved';
          
          if ( $provisioningComplete == $requestDetails )
          {
            $status = 'Complete';
          }
        }
        
        $requestStatuses[$inx]['request_id'] = $requestId;
        $requestStatuses[$inx]['request_date'] = $requestDate; 
        $requestStatuses[$inx]['status'] = $status;
        
        $inx++;
      }
    }
        
    return $requestStatuses;    
  }
  
  function getRequestAccessNameStringByRequestId( $pConn,
                                                  $pRequestId,
                                                  $pTenantName
                                                )
  {
    $accessNames = '';

    if ( $stmt = mysqli_prepare( $pConn, 
                                 'select request_line_id,
                                    access_name
                                  from request_line_items
                                  where request_id = ?
                                  and tenant_name = ?
                                  order by request_line_id asc'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, 'is', $pRequestId, $pTenantName );
      mysqli_stmt_execute( $stmt );
      mysqli_stmt_bind_result( $stmt,
                               $requestLineId,
                               $accessName
                             );

      $inx = 0;

      while ( mysqli_stmt_fetch( $stmt ))
      {
        if ( $inx == 0 )
        {
          $accessNames .= $accessName;
        }
        else
        {
          $accessNames .= ( ', ' . $accessName );
        }
        
        $inx++;
      }
    }
        
    return $accessNames;    
  }
  
  function getTenantCompanyName( $pConn,
                                 $pTenantName
                               )
  {
    $companyName = '';

    if ( $stmt = mysqli_prepare( $pConn, 
                                 'select company_name
                                  from users
                                  where tenant_name = ?
                                  and admin_level = 2'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, 's', $pTenantName );
      mysqli_stmt_execute( $stmt );
      mysqli_stmt_bind_result( $stmt,
                               $bindCompanyName
                             );

      if ( mysqli_stmt_fetch( $stmt ))
      {
        $companyName = $bindCompanyName;
      }
    }
        
    return $companyName;    
  }
  
  function insertSearchHistory( $pConn,
                                $pSearchString,
                                $pTenantName,
                                $pUserId,
                                &$pErrorMessage
                              )
  {
    $rCode = false;
   
    if ( $stmt = mysqli_prepare( $pConn, 'insert into search_history
                                          (
                                            search_string,
                                            search_date,
                                            tenant_name,
                                            user_id
                                          )
                                          values
                                          ( ?, now(), ?, ? )'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, "sss", 
                              $pSearchString,
                              $pTenantName,
                              $pUserId
                            );

      mysqli_stmt_execute( $stmt );
      mysqli_stmt_close( $stmt );
      
      $pRequestId = mysqli_insert_id( $pConn );
      
      $rCode = true;
    }
    else
    {
      $pErrorMessage = 'Error: Failed to insert search history';
    }
    
    return $rCode;
  }

  function getRequestLineItemsByAccessOwnerAndAccessOwnerAction( $pConn,
                                                                 $pLoginId,  // Access owner login ID
                                                                 $pTenantName,
                                                                 $pAccessOwnerAction
                                                               )
  {
    $requestLineItems = array();

    if ( $stmt = mysqli_prepare( $pConn, 
                                 'select rh.request_date,
                                  date_format( rh.request_date, "%d-%b-%Y %H:%i" ) formatted_request_date,
                                  rh.request_id,
                                  us.user_id requestor_email,
                                  us.job_title,
                                  concat( us.first_name, concat( " " , us.last_name )) requestor_name,
                                  rli.manager_email,
                                  rli.manager_name,
                                  rh.business_justification,
                                  rli.request_line_id,
                                  rli.workflow_name,
                                  rli.access_name,
                                  rli.access_description,
                                  rli.application_name,
                                  rli.access_source,
                                  rli.risk_rating
                                from request_line_items rli,
                                  request_header rh,
                                  users us
                                where rli.request_id = rh.request_id
                                and rh.user_id = us.user_id
                                and rli.access_owner_approval_required = "Y"
                                and rli.access_owner_email = ?
                                and rli.tenant_name = ?
                                and rli.access_owner_action = ?
                                order by rh.request_date asc,
                                  rh.request_id asc,
                                  rli.request_line_id asc'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, 'sss', $pLoginId, $pTenantName, $pAccessOwnerAction );
      mysqli_stmt_execute( $stmt );
      mysqli_stmt_bind_result( $stmt,
                               $requestDate,
                               $formattedRequestDate,
                               $requestId,
                               $requestorEmail,
                               $requestorJobTitle,
                               $requestorName,
                               $managerEmail,
                               $managerName,
                               $businessJustification,
                               $requestLineId,
                               $workflowName,
                               $accessName,
                               $accessDescription,
                               $applicationName,
                               $accessSource,
                               $riskRating
                             );

      $inx = 0;

      while ( mysqli_stmt_fetch( $stmt ))
      {
        $requestLineItems[$inx]['request_date'] = $requestDate;
        $requestLineItems[$inx]['formatted_request_date'] = $formattedRequestDate;
        $requestLineItems[$inx]['request_id'] = $requestId;
        $requestLineItems[$inx]['requestor_email'] = $requestorEmail;
        $requestLineItems[$inx]['requestor_job_title'] = $requestorJobTitle;
        $requestLineItems[$inx]['requestor_name'] = $requestorName;
        $requestLineItems[$inx]['manager_email'] = $managerEmail;
        $requestLineItems[$inx]['manager_name'] = $managerName;
        $requestLineItems[$inx]['business_justification'] = $businessJustification;
        $requestLineItems[$inx]['request_line_id'] = $requestLineId;
        $requestLineItems[$inx]['workflow_name'] = $workflowName;
        $requestLineItems[$inx]['access_name'] = $accessName;
        $requestLineItems[$inx]['access_description'] = $accessDescription;
        $requestLineItems[$inx]['application_name'] = $applicationName;
        $requestLineItems[$inx]['access_source'] = $accessSource;
        $requestLineItems[$inx]['risk_rating'] = $riskRating;
        
        $inx++;
      }
    }
        
    return $requestLineItems;    
  }

  function getRequestLineItemsByAccessOwnerAndAccessOwnerActionOverPastYear( $pConn,
                                                                             $pLoginId,  // Access owner login ID
                                                                             $pTenantName,
                                                                             $pAccessOwnerAction
                                                                           )
  {
    $requestLineItems = array();

    if ( $stmt = mysqli_prepare( $pConn, 
                                 'select rh.request_date,
                                  date_format( rh.request_date, "%d-%b-%Y %H:%i" ) formatted_request_date,
                                  rh.request_id,
                                  us.user_id requestor_email,
                                  us.job_title,
                                  concat( us.first_name, concat( " " , us.last_name )) requestor_name,
                                  rli.manager_email,
                                  rli.manager_name,
                                  rh.business_justification,
                                  rli.request_line_id,
                                  rli.workflow_name,
                                  rli.access_name,
                                  rli.access_description,
                                  rli.application_name,
                                  rli.access_source,
                                  rli.risk_rating
                                from request_line_items rli,
                                  request_header rh,
                                  users us
                                where rli.request_id = rh.request_id
                                and rh.user_id = us.user_id
                                and rli.access_owner_approval_required = "Y"
                                and rli.access_owner_email = ?
                                and rli.tenant_name = ?
                                and rli.access_owner_action = ?
                                and rli.access_owner_action_datetime >= date_sub( now(), interval 1 year )
                                order by rh.request_date asc,
                                  rh.request_id asc,
                                  rli.request_line_id asc'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, 'sss', $pLoginId, $pTenantName, $pAccessOwnerAction );
      mysqli_stmt_execute( $stmt );
      mysqli_stmt_bind_result( $stmt,
                               $requestDate,
                               $formattedRequestDate,
                               $requestId,
                               $requestorEmail,
                               $requestorJobTitle,
                               $requestorName,
                               $managerEmail,
                               $managerName,
                               $businessJustification,
                               $requestLineId,
                               $workflowName,
                               $accessName,
                               $accessDescription,
                               $applicationName,
                               $accessSource,
                               $riskRating
                             );

      $inx = 0;

      while ( mysqli_stmt_fetch( $stmt ))
      {
        $requestLineItems[$inx]['request_date'] = $requestDate;
        $requestLineItems[$inx]['formatted_request_date'] = $formattedRequestDate;
        $requestLineItems[$inx]['request_id'] = $requestId;
        $requestLineItems[$inx]['requestor_email'] = $requestorEmail;
        $requestLineItems[$inx]['requestor_job_title'] = $requestorJobTitle;
        $requestLineItems[$inx]['requestor_name'] = $requestorName;
        $requestLineItems[$inx]['manager_email'] = $managerEmail;
        $requestLineItems[$inx]['manager_name'] = $managerName;
        $requestLineItems[$inx]['business_justification'] = $businessJustification;
        $requestLineItems[$inx]['request_line_id'] = $requestLineId;
        $requestLineItems[$inx]['workflow_name'] = $workflowName;
        $requestLineItems[$inx]['access_name'] = $accessName;
        $requestLineItems[$inx]['access_description'] = $accessDescription;
        $requestLineItems[$inx]['application_name'] = $applicationName;
        $requestLineItems[$inx]['access_source'] = $accessSource;
        $requestLineItems[$inx]['risk_rating'] = $riskRating;
        
        $inx++;
      }
    }
        
    return $requestLineItems;    
  }

  function getRequestLineItemsByManagerAndManagerAction( $pConn,
                                                         $pLoginId,  // Manager login ID
                                                         $pTenantName,
                                                         $pManagerAction
                                                       )
  {
    $requestLineItems = array();

    if ( $stmt = mysqli_prepare( $pConn, 
                                 'select rh.request_date,
                                    date_format( rh.request_date, "%d-%b-%Y %H:%i" ) formatted_request_date,
                                    rh.request_id,
                                    us.user_id requestor_email,
                                    us.job_title,
                                    concat( us.first_name, concat( " " , us.last_name )) requestor_name,
                                    rh.business_justification,
                                    rli.request_line_id,
                                    rli.workflow_name,
                                    rli.access_name,
                                    rli.access_description,
                                    rli.access_owner_name,
                                    rli.access_owner_email,
                                    rli.application_name,
                                    rli.access_source,
                                    rli.risk_rating
                                  from request_line_items rli,
                                    request_header rh,
                                    users us
                                  where rli.request_id = rh.request_id
                                  and rh.user_id = us.user_id
                                  and rli.manager_approval_required = "Y"
                                  and rli.manager_email = ?
                                  and rh.tenant_name = ?
                                  and rli.manager_action = ?
                                  order by rh.request_date asc,
                                    rh.request_id asc,
                                    rli.request_line_id asc'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, 'sss', $pLoginId, $pTenantName, $pManagerAction );
      mysqli_stmt_execute( $stmt );
      mysqli_stmt_bind_result( $stmt,
                               $requestDate,
                               $formattedRequestDate,
                               $requestId,
                               $requestorEmail,
                               $requestorJobTitle,
                               $requestorName,
                               $businessJustification,
                               $requestLineId,
                               $workflowName,
                               $accessName,
                               $accessDescription,
                               $accessOwnerName,
                               $accessOwnerEmail,
                               $applicationName,
                               $accessSource,
                               $riskRating
                             );

      $inx = 0;

      while ( mysqli_stmt_fetch( $stmt ))
      {
        $requestLineItems[$inx]['request_date'] = $requestDate;
        $requestLineItems[$inx]['formatted_request_date'] = $formattedRequestDate;
        $requestLineItems[$inx]['request_id'] = $requestId;
        $requestLineItems[$inx]['requestor_email'] = $requestorEmail;
        $requestLineItems[$inx]['requestor_job_title'] = $requestorJobTitle;
        $requestLineItems[$inx]['requestor_name'] = $requestorName;
        $requestLineItems[$inx]['business_justification'] = $businessJustification;
        $requestLineItems[$inx]['request_line_id'] = $requestLineId;
        $requestLineItems[$inx]['workflow_name'] = $workflowName;
        $requestLineItems[$inx]['access_name'] = $accessName;
        $requestLineItems[$inx]['access_description'] = $accessDescription;
        $requestLineItems[$inx]['access_owner_name'] = $accessOwnerName;
        $requestLineItems[$inx]['access_owner_email'] = $accessOwnerEmail;
        $requestLineItems[$inx]['application_name'] = $applicationName;
        $requestLineItems[$inx]['access_source'] = $accessSource;
        $requestLineItems[$inx]['risk_rating'] = $riskRating;
        
        $inx++;
      }
    }
        
    return $requestLineItems;    
  }

  function getRequestLineItemsByManagerAndManagerActionOverPastYear( $pConn,
                                                                     $pLoginId,  // Manager login ID
                                                                     $pTenantName,
                                                                     $pManagerAction
                                                                   )
  {
    $requestLineItems = array();

    if ( $stmt = mysqli_prepare( $pConn, 
                                 'select rh.request_date,
                                    date_format( rh.request_date, "%d-%b-%Y %H:%i" ) formatted_request_date,
                                    rh.request_id,
                                    us.user_id requestor_email,
                                    us.job_title,
                                    concat( us.first_name, concat( " " , us.last_name )) requestor_name,
                                    rh.business_justification,
                                    rli.request_line_id,
                                    rli.workflow_name,
                                    rli.access_name,
                                    rli.access_description,
                                    rli.access_owner_name,
                                    rli.access_owner_email,
                                    rli.application_name,
                                    rli.access_source,
                                    rli.risk_rating
                                  from request_line_items rli,
                                    request_header rh,
                                    users us
                                  where rli.request_id = rh.request_id
                                  and rh.user_id = us.user_id
                                  and rli.manager_approval_required = "Y"
                                  and rli.manager_email = ?
                                  and rh.tenant_name = ?
                                  and rli.manager_action = ?
                                  and rli.manager_action_datetime >= date_sub( now(), interval 1 year )
                                  order by rh.request_date asc,
                                    rh.request_id asc,
                                    rli.request_line_id asc'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, 'sss', $pLoginId, $pTenantName, $pManagerAction );
      mysqli_stmt_execute( $stmt );
      mysqli_stmt_bind_result( $stmt,
                               $requestDate,
                               $formattedRequestDate,
                               $requestId,
                               $requestorEmail,
                               $requestorJobTitle,
                               $requestorName,
                               $businessJustification,
                               $requestLineId,
                               $workflowName,
                               $accessName,
                               $accessDescription,
                               $accessOwnerName,
                               $accessOwnerEmail,
                               $applicationName,
                               $accessSource,
                               $riskRating
                             );

      $inx = 0;

      while ( mysqli_stmt_fetch( $stmt ))
      {
        $requestLineItems[$inx]['request_date'] = $requestDate;
        $requestLineItems[$inx]['formatted_request_date'] = $formattedRequestDate;
        $requestLineItems[$inx]['request_id'] = $requestId;
        $requestLineItems[$inx]['requestor_email'] = $requestorEmail;
        $requestLineItems[$inx]['requestor_job_title'] = $requestorJobTitle;
        $requestLineItems[$inx]['requestor_name'] = $requestorName;
        $requestLineItems[$inx]['business_justification'] = $businessJustification;
        $requestLineItems[$inx]['request_line_id'] = $requestLineId;
        $requestLineItems[$inx]['workflow_name'] = $workflowName;
        $requestLineItems[$inx]['access_name'] = $accessName;
        $requestLineItems[$inx]['access_description'] = $accessDescription;
        $requestLineItems[$inx]['access_owner_name'] = $accessOwnerName;
        $requestLineItems[$inx]['access_owner_email'] = $accessOwnerEmail;
        $requestLineItems[$inx]['application_name'] = $applicationName;
        $requestLineItems[$inx]['access_source'] = $accessSource;
        $requestLineItems[$inx]['risk_rating'] = $riskRating;
        
        $inx++;
      }
    }
        
    return $requestLineItems;    
  }

  /*
   * Chris Biddle, 01/30/2024
   * Commented out and replaced with getApprovedPendingRequestLineItemsByProvisioner(). In a meeting
   * with Vishu this morning, we decided to merge provisioning lead and provisioner actions into one
   * card on the action items dashboard.
   
  function getManagerAndAoApprovedRequestLineItemsByProvisioningLead( $pConn,
                                                                      $pLoginId,  // Provisioning Lead login ID
                                                                      $pTenantName,
                                                                      $pProvisioningStatus
                                                                    )
  {
   
    $requestLineItems = array();

    if ( $stmt = mysqli_prepare( $pConn, 
                                 'select rh.request_date,
                                    date_format( rh.request_date, "%d-%b-%Y %H:%i" ) formatted_request_date,
                                    rh.request_id,
                                    us.user_id requestor_email,
                                    us.job_title,
                                    concat( us.first_name, concat( " " , us.last_name )) requestor_name,
                                    rh.business_justification,
                                    rli.request_line_id,
                                    rli.workflow_name,
                                    rli.access_name,
                                    rli.access_description,
                                    rli.access_owner_name,
                                    rli.access_owner_email,
                                    rli.application_name,
                                    rli.access_source,
                                    rli.risk_rating
                                  from request_line_items rli,
                                    request_header rh,
                                    users us
                                  where rli.request_id = rh.request_id
                                    and rh.user_id = us.user_id
                                    and
                                      (
                                        ( rli.manager_approval_required = "Y"
                                          and rli.manager_action = "APPROVED"
                                        )
                                        or rli.manager_approval_required = "N"
                                      )
                                    and
                                      (
                                        ( rli.access_owner_approval_required = "Y"
                                          and rli.access_owner_action = "APPROVED"
                                        )
                                        or rli.access_owner_approval_required = "N"
                                      )
                                    and rh.tenant_name = ?
                                    and
                                      ( rli.provisioning_lead_email_1 = ?
                                        or rli.provisioning_lead_email_2 = ?
                                      )
                                    and rli.provisioning_status = ?
                                  order by rh.request_date asc,
                                    rh.request_id asc,
                                    rli.request_line_id asc'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, 'ssss', $pTenantName, $pLoginId, $pLoginId, $pProvisioningStatus );
      mysqli_stmt_execute( $stmt );
      mysqli_stmt_bind_result( $stmt,
                               $requestDate,
                               $formattedRequestDate,
                               $requestId,
                               $requestorEmail,
                               $requestorJobTitle,
                               $requestorName,
                               $businessJustification,
                               $requestLineId,
                               $workflowName,
                               $accessName,
                               $accessDescription,
                               $accessOwnerName,
                               $accessOwnerEmail,
                               $applicationName,
                               $accessSource,
                               $riskRating
                             );

      $inx = 0;

      while ( mysqli_stmt_fetch( $stmt ))
      {
        $requestLineItems[$inx]['request_date'] = $requestDate;
        $requestLineItems[$inx]['formatted_request_date'] = $formattedRequestDate;
        $requestLineItems[$inx]['request_id'] = $requestId;
        $requestLineItems[$inx]['requestor_email'] = $requestorEmail;
        $requestLineItems[$inx]['requestor_job_title'] = $requestorJobTitle;
        $requestLineItems[$inx]['requestor_name'] = $requestorName;
        $requestLineItems[$inx]['business_justification'] = $businessJustification;
        $requestLineItems[$inx]['request_line_id'] = $requestLineId;
        $requestLineItems[$inx]['workflow_name'] = $workflowName;
        $requestLineItems[$inx]['access_name'] = $accessName;
        $requestLineItems[$inx]['access_description'] = $accessDescription;
        $requestLineItems[$inx]['access_owner_name'] = $accessOwnerName;
        $requestLineItems[$inx]['access_owner_email'] = $accessOwnerEmail;
        $requestLineItems[$inx]['application_name'] = $applicationName;
        $requestLineItems[$inx]['access_source'] = $accessSource;
        $requestLineItems[$inx]['risk_rating'] = $riskRating;
        
        $inx++;
      }
    }
        
    return $requestLineItems;    
  }
  */

  function getApprovedPendingRequestLineItemsByProvisioner( $pConn,
                                                            $pLoginId,  // Provisioner or Provisioning Lead
                                                            $pTenantName
                                                          )
  {
   
    $requestLineItems = array();

    if ( $stmt = mysqli_prepare( $pConn, 
                                 'select rh.request_date,
                                    date_format( rh.request_date, "%d-%b-%Y %H:%i" ) formatted_request_date,
                                    rh.request_id,
                                    us.user_id requestor_email,
                                    us.job_title,
                                    concat( us.first_name, concat( " " , us.last_name )) requestor_name,
                                    rh.business_justification,
                                    rli.request_line_id,
                                    rli.workflow_name,
                                    rli.access_name,
                                    rli.access_description,
                                    rli.access_owner_name,
                                    rli.access_owner_email,
                                    rli.application_name,
                                    rli.access_source,
                                    rli.risk_rating
                                  from request_line_items rli,
                                    request_header rh,
                                    users us
                                  where rli.request_id = rh.request_id
                                    and rh.user_id = us.user_id
                                    and
                                      (
                                        ( rli.manager_approval_required = "Y"
                                          and rli.manager_action = "APPROVED"
                                        )
                                        or rli.manager_approval_required = "N"
                                      )
                                    and
                                      (
                                        ( rli.access_owner_approval_required = "Y"
                                          and rli.access_owner_action = "APPROVED"
                                        )
                                        or rli.access_owner_approval_required = "N"
                                      )
                                    and rh.tenant_name = ?
                                    and
                                      ( 
                                        ( rli.provisioning_status = "PENDING"
                                          and
                                          ( rli.provisioning_lead_email_1 = ?
                                            or rli.provisioning_lead_email_2 = ?
                                          )
                                        )
                                        or
                                        ( rli.provisioning_status = "ASSIGNED"
                                          and rli.provisioner_email = ?
                                        )
                                      )
                                  order by rh.request_date asc,
                                    rh.request_id asc,
                                    rli.request_line_id asc'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, 'ssss', $pTenantName, $pLoginId, $pLoginId, $pLoginId );
      mysqli_stmt_execute( $stmt );
      mysqli_stmt_bind_result( $stmt,
                               $requestDate,
                               $formattedRequestDate,
                               $requestId,
                               $requestorEmail,
                               $requestorJobTitle,
                               $requestorName,
                               $businessJustification,
                               $requestLineId,
                               $workflowName,
                               $accessName,
                               $accessDescription,
                               $accessOwnerName,
                               $accessOwnerEmail,
                               $applicationName,
                               $accessSource,
                               $riskRating
                             );

      $inx = 0;

      while ( mysqli_stmt_fetch( $stmt ))
      {
        $requestLineItems[$inx]['request_date'] = $requestDate;
        $requestLineItems[$inx]['formatted_request_date'] = $formattedRequestDate;
        $requestLineItems[$inx]['request_id'] = $requestId;
        $requestLineItems[$inx]['requestor_email'] = $requestorEmail;
        $requestLineItems[$inx]['requestor_job_title'] = $requestorJobTitle;
        $requestLineItems[$inx]['requestor_name'] = $requestorName;
        $requestLineItems[$inx]['business_justification'] = $businessJustification;
        $requestLineItems[$inx]['request_line_id'] = $requestLineId;
        $requestLineItems[$inx]['workflow_name'] = $workflowName;
        $requestLineItems[$inx]['access_name'] = $accessName;
        $requestLineItems[$inx]['access_description'] = $accessDescription;
        $requestLineItems[$inx]['access_owner_name'] = $accessOwnerName;
        $requestLineItems[$inx]['access_owner_email'] = $accessOwnerEmail;
        $requestLineItems[$inx]['application_name'] = $applicationName;
        $requestLineItems[$inx]['access_source'] = $accessSource;
        $requestLineItems[$inx]['risk_rating'] = $riskRating;
        
        $inx++;
      }
    }
        
    return $requestLineItems;    
  }

  function getRequestLineItemsByProvisionerAndProvisioningStatus( $pConn,
                                                                  $pLoginId,  // Provisioner ID
                                                                  $pTenantName,
                                                                  $pProvisioningStatus
                                                               )
  {
    $requestLineItems = array();

    if ( $stmt = mysqli_prepare( $pConn, 
                                 'select rh.request_date,
                                  date_format( rh.request_date, "%d-%b-%Y %H:%i" ) formatted_request_date,
                                  rh.request_id,
                                  us.user_id requestor_email,
                                  us.job_title,
                                  concat( us.first_name, concat( " " , us.last_name )) requestor_name,
                                  rli.manager_email,
                                  rli.manager_name,
                                  rh.business_justification,
                                  rli.request_line_id,
                                  rli.workflow_name,
                                  rli.access_name,
                                  rli.access_description,
                                  rli.application_name,
                                  rli.access_source,
                                  rli.risk_rating
                                from request_line_items rli,
                                  request_header rh,
                                  users us
                                where rli.request_id = rh.request_id
                                and rh.user_id = us.user_id
                                and rli.provisioner_email = ?
                                and rli.tenant_name = ?
                                and rli.provisioning_status = ?
                                order by rh.request_date asc,
                                  rh.request_id asc,
                                  rli.request_line_id asc'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, 'sss', $pLoginId, $pTenantName, $pProvisioningStatus );
      mysqli_stmt_execute( $stmt );
      mysqli_stmt_bind_result( $stmt,
                               $requestDate,
                               $formattedRequestDate,
                               $requestId,
                               $requestorEmail,
                               $requestorJobTitle,
                               $requestorName,
                               $managerEmail,
                               $managerName,
                               $businessJustification,
                               $requestLineId,
                               $workflowName,
                               $accessName,
                               $accessDescription,
                               $applicationName,
                               $accessSource,
                               $riskRating
                             );

      $inx = 0;

      while ( mysqli_stmt_fetch( $stmt ))
      {
        $requestLineItems[$inx]['request_date'] = $requestDate;
        $requestLineItems[$inx]['formatted_request_date'] = $formattedRequestDate;
        $requestLineItems[$inx]['request_id'] = $requestId;
        $requestLineItems[$inx]['requestor_email'] = $requestorEmail;
        $requestLineItems[$inx]['requestor_job_title'] = $requestorJobTitle;
        $requestLineItems[$inx]['requestor_name'] = $requestorName;
        $requestLineItems[$inx]['manager_email'] = $managerEmail;
        $requestLineItems[$inx]['manager_name'] = $managerName;
        $requestLineItems[$inx]['business_justification'] = $businessJustification;
        $requestLineItems[$inx]['request_line_id'] = $requestLineId;
        $requestLineItems[$inx]['workflow_name'] = $workflowName;
        $requestLineItems[$inx]['access_name'] = $accessName;
        $requestLineItems[$inx]['access_description'] = $accessDescription;
        $requestLineItems[$inx]['application_name'] = $applicationName;
        $requestLineItems[$inx]['access_source'] = $accessSource;
        $requestLineItems[$inx]['risk_rating'] = $riskRating;
        
        $inx++;
      }
    }
        
    return $requestLineItems;    
  }

  function getRequestLineItemByRequestLineId( $pConn,
                                              $pRequestId,
                                              $pRequestLineId
                                             )
  {
    $requestLineItem = array();

    if ( $stmt = mysqli_prepare( $pConn, 
                                 'select rh.request_date,
                                    date_format( rh.request_date, "%d-%b-%Y %H:%i" ) formatted_request_date,
                                    rh.request_id,
                                    us.user_id requestor_email,
                                    us.job_title,
                                    us.company_name,
                                    concat( us.first_name, concat( " " , us.last_name )) requestor_name,
                                    rli.manager_name,
                                    rli.manager_email,
                                    rh.business_justification,
                                    rli.request_line_id,
                                    rli.workflow_name,
                                    rli.access_name,
                                    rli.access_description,
                                    rli.access_owner_name,
                                    rli.access_owner_email,
                                    rli.application_name,
                                    rli.access_source,
                                    rli.risk_rating,
                                    rli.manager_action,
                                    rli.access_owner_action
                                  from request_line_items rli,
                                    request_header rh,
                                    users us
                                  where rli.request_id = rh.request_id
                                  and rh.user_id = us.user_id
                                  and rli.request_id = ?
                                  and rli.request_line_id = ?'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, 'ii', $pRequestId, $pRequestLineId );
      mysqli_stmt_execute( $stmt );
      mysqli_stmt_bind_result( $stmt,
                               $requestDate,
                               $formattedRequestDate,
                               $requestId,
                               $requestorEmail,
                               $requestorJobTitle,
                               $requestorCompanyName,
                               $requestorName,
                               $managerName,
                               $managerEmail,
                               $businessJustification,
                               $requestLineId,
                               $workflowName,
                               $accessName,
                               $accessDescription,
                               $accessOwnerName,
                               $accessOwnerEmail,
                               $applicationName,
                               $accessSource,
                               $riskRating,
                               $managerAction,
                               $accessOwnerAction
                             );

      while ( mysqli_stmt_fetch( $stmt ))
      {
        $requestLineItem['request_date'] = $requestDate;
        $requestLineItem['formatted_request_date'] = $formattedRequestDate;
        $requestLineItem['request_id'] = $requestId;
        $requestLineItem['requestor_email'] = $requestorEmail;
        $requestLineItem['requestor_job_title'] = $requestorJobTitle;
        $requestLineItem['requestor_company_name'] = $requestorCompanyName;
        $requestLineItem['requestor_name'] = $requestorName;
        $requestLineItem['manager_name'] = $managerName;
        $requestLineItem['manager_email'] = $managerEmail;
        $requestLineItem['business_justification'] = $businessJustification;
        $requestLineItem['request_line_id'] = $requestLineId;
        $requestLineItem['workflow_name'] = $workflowName;
        $requestLineItem['access_name'] = $accessName;
        $requestLineItem['access_description'] = $accessDescription;
        $requestLineItem['access_owner_name'] = $accessOwnerName;
        $requestLineItem['access_owner_email'] = $accessOwnerEmail;
        $requestLineItem['application_name'] = $applicationName;
        $requestLineItem['access_source'] = $accessSource;
        $requestLineItem['risk_rating'] = $riskRating;
        $requestLineItem['manager_action'] = $managerAction;
        $requestLineItem['access_owner_action'] = $accessOwnerAction;
      }
    }
        
    return $requestLineItem;    
  }


  function updateManagerAction( $pConn,
                                $pRequestId,
                                $pRequestLineId,
                                $pAction,
                                &$pErrorMessage
                              )
  {
    $rCode = false;
    
    if ( $stmt = mysqli_prepare( $pConn, 'update request_line_items
                                          set manager_action = ?,
                                            manager_action_datetime = now()
                                          where request_id = ?
                                          and request_line_id = ?'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, "sii",
                              $pAction,
                              $pRequestId,
                              $pRequestLineId
                            );

      if ( mysqli_stmt_execute( $stmt ))
      {
        $rCode = true;
      }
      else
      {
        $pErrorMessage = 'Error: Failed to update manager action. Please try again later. (CD203)';       
      }
    }
    else
    {
      $pErrorMessage = 'Error: Failed to update manager action. Please try again later. (CD204)';       
    }
    
    return $rCode;
  }

  function updateAccessOwnerAction( $pConn,
                                    $pRequestId,
                                    $pRequestLineId,
                                    $pAction,
                                    &$pErrorMessage
                                  )
  {
    $rCode = false;
    
    if ( $stmt = mysqli_prepare( $pConn, 'update request_line_items
                                          set access_owner_action = ?,
                                            access_owner_action_datetime = now()
                                          where request_id = ?
                                          and request_line_id = ?'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, "sii",
                              $pAction,
                              $pRequestId,
                              $pRequestLineId
                            );

      if ( mysqli_stmt_execute( $stmt ))
      {
        $rCode = true;
      }
      else
      {
        $pErrorMessage = 'Error: Failed to update access owner action. Please try again later. (CD205)';       
      }
    }
    else
    {
      $pErrorMessage = 'Error: Failed to update access owner action. Please try again later. (CD206)';       
    }
    
    return $rCode;
  }
  
  function updateRequestLineItemManager( $pConn,
                                         $pRequestId,
                                         $pRequestLineId,
                                         $pManagerName,
                                         $pManagerEmail,
                                         $pManagerReassignmentNotes,
                                         &$pErrorMessage
                                       )
  {
    $rCode = false;
    
    if ( $stmt = mysqli_prepare( $pConn, 'update request_line_items set
                                            manager_name = ?,
                                            manager_email = ?,
                                            manager_reassignment_notes = ?,
                                            manager_reassignment_datetime = now()
                                          where request_id = ?
                                          and request_line_id = ?'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, "sssii",
                              $pManagerName,
                              $pManagerEmail,
                              $pManagerReassignmentNotes,
                              $pRequestId,
                              $pRequestLineId
                            );

      if ( mysqli_stmt_execute( $stmt ))
      {
        $rCode = true;
      }
      else
      {
        $pErrorMessage = 'Error: Failed to update manager information. Please try again later. (CD305)';       
      }
    }
    else
    {
      $pErrorMessage = 'Error: Failed to update manager information. Please try again later. (CD306)';       
    }
    
    return $rCode;
  }

  function updateRequestLineItemAccessOwner( $pConn,
                                             $pRequestId,
                                             $pRequestLineId,
                                             $pAccessOwnerName,
                                             $pAccessOwnerEmail,
                                             $pAccessOwnerReassignmentNotes,
                                             &$pErrorMessage
                                           )
  {
    $rCode = false;
    
    if ( $stmt = mysqli_prepare( $pConn, 'update request_line_items set
                                            access_owner_name = ?,
                                            access_owner_email = ?,
                                            access_owner_reassignment_notes = ?,
                                            access_owner_reassignment_datetime = now()
                                          where request_id = ?
                                          and request_line_id = ?'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, "sssii",
                              $pAccessOwnerName,
                              $pAccessOwnerEmail,
                              $pAccessOwnerReassignmentNotes,
                              $pRequestId,
                              $pRequestLineId
                            );

      if ( mysqli_stmt_execute( $stmt ))
      {
        $rCode = true;
      }
      else
      {
        $pErrorMessage = 'Error: Failed to update access owner information. Please try again later. (CD307)';       
      }
    }
    else
    {
      $pErrorMessage = 'Error: Failed to update access owner information. Please try again later. (CD308)';       
    }
    
    return $rCode;
  }
  
  function updateRequestLineItemProvisioner( $pConn,
                                             $pRequestId,
                                             $pRequestLineId,
                                             $pProvisionerName,
                                             $pProvisionerEmail,
                                             $pProvisioningAssignedBy,
                                             $pProvisioningLeadComment,
                                             &$pErrorMessage
                                           )
  {
    $rCode = false;
    
    if ( $stmt = mysqli_prepare( $pConn, 'update request_line_items
                                          set provisioner_name = ?,
                                            provisioner_email = ?,
                                            provisioning_assigned_by = ?,
                                            provisioning_assigned_date = now(),
                                            provisioning_lead_comment = ?,
                                            provisioner_comment = NULL,
                                            provisioning_status = "ASSIGNED"
                                          where request_id = ?
                                            and request_line_id = ?'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt, "ssssii",
                              $pProvisionerName,
                              $pProvisionerEmail,
                              $pProvisioningAssignedBy,
                              $pProvisioningLeadComment,
                              $pRequestId,
                              $pRequestLineId
                            );

      if ( mysqli_stmt_execute( $stmt ))
      {
        $rCode = true;
      }
      else
      {
        $pErrorMessage = 'Error: Failed to update provisioner information. Please try again later. (CD401)';       
      }
    }
    else
    {
      $pErrorMessage = 'Error: Failed to update access owner information. Please try again later. (CD402)';       
    }
    
    return $rCode;
  }
  
  function revertProvisionerToProvisioningLead( $pConn,
                                                $pRequestId,
                                                $pRequestLineId,
                                                $pProvisionerComment,
                                                &$pErrorMessage
                                              )
  {
    $rCode = false;

    /*
    $sql = 'update request_line_items
            set provisioner_name = NULL,
              provisioner_email = NULL,
              provisioning_assigned_by = NULL,
              provisioning_assigned_date = NULL,
              provisioning_lead_comment = NULL,
              provisioning_status = "PENDING",
              provisioner_comment = ?
            where request_id = ?
              and request_line_id = ?';
    */

    $sql = 'update request_line_items
            set provisioning_status = "PENDING",
              provisioner_comment = ?
            where request_id = ?
              and request_line_id = ?';

    if ( $stmt = mysqli_prepare( $pConn, $sql ))
    {
      mysqli_stmt_bind_param( $stmt,
                              "sii",
                              $pProvisionerComment,
                              $pRequestId,
                              $pRequestLineId
                            );

      if ( mysqli_stmt_execute( $stmt ))
      {
        $rCode = true;
      }
      else
      {
        $pErrorMessage = 'Error: Failed to update provisioner information. Please try again later. (CD501)';       
      }
    }
    else
    {
      $pErrorMessage = 'Error: Failed to update access owner information. Please try again later. (CD502)';       
    }
    
    return $rCode;
  }
  
  function markProvisioningComplete( $pConn,
                                     $pRequestId,
                                     $pRequestLineId,
                                     &$pErrorMessage
                                   )
  {
    $rCode = false;
    
    if ( $stmt = mysqli_prepare( $pConn, 'update request_line_items
                                          set provisioning_status = "COMPLETE",
                                            provisioning_completed_date = now(),
                                            provisioner_comment = NULL
                                          where request_id = ?
                                            and request_line_id = ?'
                               )
       )
    {
      mysqli_stmt_bind_param( $stmt,
                              "ii",
                              $pRequestId,
                              $pRequestLineId
                            );

      if ( mysqli_stmt_execute( $stmt ))
      {
        $rCode = true;
      }
      else
      {
        $pErrorMessage = 'Error: Failed to update provisioner information. Please try again later. (CD601)';       
      }
    }
    else
    {
      $pErrorMessage = 'Error: Failed to update access owner information. Please try again later. (CD602)';       
    }
    
    return $rCode;
  }

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

?>