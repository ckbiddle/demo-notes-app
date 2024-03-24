<?php

  function validateRegistrationInfo( $pLoginId,
                                     $pPasswd1,
                                     $pPasswd2,
                                     $pLastName,
                                     $pFirstName,
                                     $pCompanyName,
                                     $pArcName, // access request console name
                                     $pEmailDomains,
                                     &$pErrorMessage
                                   )
  {
    $rCode = false;
    $validEmail = false;

    if ( $pEmailDomains != '' )
    {
      if ( $pLoginId != '' )
      {
        if ( $pLastName != '' )
        {
          if ( $pFirstName != '' )
          {
            if ( $pCompanyName != '' )
            {
              if ( $pArcName != '' )
              {
                if ( checkPasswordInput( $pPasswd1, $pPasswd2, $pErrorMessage ))
                {
                  $rVal = strpos( $pLoginId, '@' );
                 
                  if ( $rVal === false )
                  {
                    $pErrorMessage = 'Error: Login ID is not an email address (it does not contain an @ symbol). Please enter a valid email address.'; 
                  }
                  else
                  {
                    if ( $rVal > 0 )
                    {
                      $validEmail = true; 
                    }
                    else
                    {
                      $pErrorMessage = 'Error: Login ID is not an email address (it does not contain an @ symbol between characters). Please enter a valid email address.'; 
                    }
                  }
                  
                  if ( $validEmail )
                  {
                    $rVal = strpos( $pArcName, ' ' );
                    
                    if ( $rVal === false )
                    {
                      if ( strtolower( $pArcName ) == $pArcName )
                      {
                        $rCode = true;
                      }
                      else
                      {
                        $pErrorMessage = 'Error: Access Request Console Name must be in lower case.'; 
                      }
                    }
                    else
                    {
                      $pErrorMessage = 'Error: Access Request Console Name must not contain spaces.'; 
                    }
                  }
                } // closing bracket for checkPasswordInput()
              }
              else
              {
                $pErrorMessage = 'Error: Access Request Console Name is required.'; 
              }
            }
            else
            {
              $pErrorMessage = 'Error: Company Name is required.'; 
            }
          }
          else
          {
            $pErrorMessage = 'Error: First name is required. Please enter a first name.'; 
          }
        }
        else
        {
          $pErrorMessage = 'Error: Last name is required. Please enter a last name.'; 
        }
      }
      else
      {
        $pErrorMessage = 'Error: Your email address is required. Please enter your email address.'; 
      }
    }
    else
    {
      $pErrorMessage = 'Error: Valid email domain(s) is required. Please enter at least one valid email domain.'; 
    }
    
    return $rCode;
  }
  
  function validateArcRegistrationInfo( $pLoginId,      // ok
                                        $pPwd1,         // ok
                                        $pPwd2,         // ok
                                        $pLastName,     // ok
                                        $pFirstName,    // ok
                                        $pJobTitle,     // ok
                                        $pCompanyName,  // ok
                                        &$pErrorMessage
                                      )
  {
    $rCode = false;
    $validEmail = false;

    if ( $pLoginId != '' )
    {
      if ( $pLastName != '' )
      {
        if ( $pFirstName != '' )
        {
          if ( $pCompanyName != '' )
          {
            if ( $pJobTitle != '' )
            {
              if ( checkPasswordInput( $pPwd1, $pPwd2, $pErrorMessage ))
              {
                $rVal = strpos( $pLoginId, '@' );
               
                if ( $rVal === false )
                {
                  $pErrorMessage = 'Error: Login ID is not an email address (it does not contain an @ symbol). Please enter a valid email address.'; 
                }
                else
                {
                  if ( $rVal > 0 )
                  {
                    $rCode = true; 
                  }
                  else
                  {
                    $pErrorMessage = 'Error: Login ID is not an email address (it does not contain an @ symbol between characters). Please enter a valid email address.'; 
                  }
                }
              } // closing bracket for checkPasswordInput()
            }
            else
            {
              $pErrorMessage = 'Error: Job Title is required.'; 
            }
          }
          else
          {
            $pErrorMessage = 'Error: Company Name is required.'; 
          }
        }
        else
        {
          $pErrorMessage = 'Error: First name is required. Please enter a first name.'; 
        }
      }
      else
      {
        $pErrorMessage = 'Error: Last name is required. Please enter a last name.'; 
      }
    }
    else
    {
      $pErrorMessage = 'Error: Your email address is required. Please enter your email address.'; 
    }
    
    return $rCode;
  }

  function checkPasswordInput( $pPwd1,
                               $pPwd2,
                               &$pErrorMessage
                             )
  {
    $rCode = false;
    
    if ( $pPwd1 != '' )
    {
      if ( $pPwd2 != '' )
      {
        if ( $pPwd1 == $pPwd2 )
        {
          $rCode = true;
        }
        else
        {
          $pErrorMessage = 'Error: Passwords do not match.';
        }
      }
      else
      {
        $pErrorMessage = 'Error: Repeat password is required.';
      }
    }
    else
    {
      $pErrorMessage = 'Error: Password is required.';
    }
    
    return $rCode;
  }

  function sendEmailVerification( $pEmailAddress,
                                  $pFirstName,
                                  $pValidationHandle,
                                  $pAppRoot,
                                  $pHostingDomain,
                                  $pFromEmailAddress
                                )
  {
    $rCode = false;
   
    $emailSubject = 'Identivize registration confirmation requested';
    
    $url = 'http://' . $pHostingDomain . $pAppRoot . '/cfmreg/?vh=' . $pValidationHandle;
    
    $emailContent = "Thank you " . $pFirstName . ", for registering with Identivize! " .
                    "Please click on the following link to confirm your registration:\n\n" . $url;

    $headers = "From: $pFromEmailAddress";
    
    if ( mail( $pEmailAddress, $emailSubject, $emailContent, $headers ))
    {
      $rCode = true;
    }
   
    return $rCode;   
  }
  
  function sendArcEmailVerification( $pEmailAddress,
                                     $pFirstName,
                                     $pValidationHandle,
                                     $pAppRoot,
                                     $pHostingDomain,
                                     $pFromEmailAddress
                                   )
  {
    $rCode = false;
   
    $emailSubject = 'Identivize Access Request Console registration confirmation requested';
    
    $url = 'http://' . $pHostingDomain . $pAppRoot . '/arcsh/cfmreg/?vh=' . $pValidationHandle;
    
    $emailContent = "Thank you " . $pFirstName . ", for registering with the Identivize Access Request Console! " .
                    "Please click on the following link to confirm your registration:\n\n" . $url;

    $headers = "From: $pFromEmailAddress";
    
    if ( mail( $pEmailAddress, $emailSubject, $emailContent, $headers ))
    {
      $rCode = true;
    }
   
    return $rCode;   
  }
  
  function createArcPortal( $pArcName )
  {
    $dirPath = '../arc/' . $pArcName . '/';
    mkdir( $dirPath, 0777, true );
    copy( '../arcsh/LoginPortalIndex.php', $dirPath . 'index.php' );
  }
  
  function parseAndLoadFile( $pConn,
                             $pTenantName,
                             $pWorkflowName,
                             $pWorkflowDescription,
                             $pTargetFile,
                             &$pErrorMessage,
                             $pMode   // 'create' or 'update'
                           )
  {
    $rCode = false;
    $goodData = false;
   
    // Initialize an empty array to store the data
    $data = [];

    // Whether the mode is create or update, if the user specified an input
    // workflow CSV file, parse it into the array $data[].
    if ( $pTargetFile != '' )
    {
      // Open the CSV file for reading
      if (( $handle = fopen( $pTargetFile, 'r' )) !== false )
      {
        // Read and discard the header row.
        if (( $headerRow = fgetcsv( $handle )) !== false )
        {
          $goodData = true;
          $accessNames = array();

          $inx = 1;

          while ( $goodData && ( $row = fgetcsv( $handle )) !== false )
          {
            if ( validWorkflowData( $row, $inx, $pErrorMessage ))
            {
              $data[] = $row;
              $accessNames[] = $row[0];
            }
            else
            {
              $goodData = false;
              // echo '<p>' . $pErrorMessage . '</p>';
            }
            
            $inx++;
          }
          
          $uniqueAccessNames = array_unique( $accessNames );
          
          if ( count( $uniqueAccessNames ) < count( $accessNames ))
          {
            $pErrorMessage = 'Error: Column 1 access names must be unique.';
            $goodData = false;
          }
        }
        
        fclose( $handle ); // Close the CSV file
      }
      else
      {
        $pErrorMessage = 'Error: Failed to open data file.';       
      }
    }
    else // Input workflow file has not been specified
    {
      // In create mode, a workflow file is required. In update mode it is optional
      // because the user might just want to update the workflow description and/or
      // other master data without necessarily loading a new workflow file.
      
      if ( $pMode == 'create' )
      {
        $pErrorMessage = 'Error: A workflow file must be specified';
      }
      else  // In update mode, just set $goodData to true and move on
      {
        $goodData = true;
      }
    }

    /*
    if ( $goodData )
    {
      echo '<h3>Good Data!</h3>';
      echo '<pre>';
      print_r( $data );
      echo '</pre>';
    }
    die();
    */

    if( $goodData )
    {
      // Whether in create or update mode, a workflow name description is always required.
      if ( $pWorkflowName != '' )
      {
        if ( $pWorkflowDescription != '' )
        {
          if ( $pMode == 'create' )
          {
            if ( insertWorkflowMaster( $pConn, $pTenantName, $pWorkflowName, $pWorkflowDescription, $pErrorMessage ))
            {
              if ( insertWorkflowDetails( $pConn, $pTenantName, $pWorkflowName, $data, $pErrorMessage ))
              {
                $rCode = true;
              }
            }
          }
          else // In update mode ($pMode = 'update')
          {
            if ( updateWorkflowMaster( $pConn, $pTenantName, $pWorkflowName, $pWorkflowDescription, $pErrorMessage ))
            {
              // If new workflow data was supplied, we want to just delete all the old workflow data
              // and load the new data.
              if ( $pTargetFile != '' )
              {
                if ( deleteWorkflowDetails( $pConn, $pTenantName, $pWorkflowName, $pErrorMessage ))
                {
                  if ( insertWorkflowDetails( $pConn, $pTenantName, $pWorkflowName, $data, $pErrorMessage ))
                  {
                    $rCode = true;
                  }
                }
              }
              else
              {
                $rCode = true;
              }
            }
          }
        }
        else
        {
          $pErrorMessage = 'Error: Workflow Description is required.'; 
        }
      }
      else
      {
        $pErrorMessage = 'Error: Workflow Name is required.'; 
      }
    }
    
    return $rCode;
  }

  function validWorkflowData( $pData,
                              $pRowNumber,
                              &$pErrorMessage
                            )
  {
    $allFieldsPopulated = false;
    $validContent = false;
    $rCode = false;

    // First check to see that all fields have been populated.
    if ( count( $pData ) == 13 )
    {
      if ( $pData[0] != '' )
      {
        if ( $pData[1] != '' )
        {
          if ( $pData[2] != '' )
          {
            if ( $pData[3] != '' )
            {
              if ( $pData[4] != '' )
              {
                if ( $pData[5] != '' )
                {
                  if ( $pData[6] != '' )
                  {
                    if ( $pData[7] != '' )
                    {
                      if ( $pData[8] != '' )
                      {
                        if ( $pData[9] != '' )
                        {
                          if ( $pData[10] != '' )
                          {
                            if ( $pData[11] != '' )
                            {
                              if ( $pData[12] != '' )
                              {
                                $allFieldsPopulated = true;
                              }
                              else
                              {
                                $pErrorMessage = 'Error: At row ' . $pRowNumber . ' column 13 &ldquo;Secondary Provisioning Lead Email&rdquo; is required. If none exists, enter NA.';
                              }
                            }
                            else
                            {
                              $pErrorMessage = 'Error: At row ' . $pRowNumber . ' column 12 &ldquo;Secondary Provisioning Lead Name&rdquo; is required. If none exists, enter NA.';
                            }
                          }
                          else
                          {
                            $pErrorMessage = 'Error: At row ' . $pRowNumber . ' column 11 &ldquo;Primary Provisioning Lead Email&rdquo; is required.';
                          }
                        }
                        else
                        {
                          $pErrorMessage = 'Error: At row ' . $pRowNumber . ' column 10 &ldquo;Primary Provisioning Lead Name&rdquo; is required.';
                        }
                      }
                      else
                      {
                        $pErrorMessage = 'Error: At row ' . $pRowNumber . ' column 9 &ldquo;Access Owner Approval Required?&rdquo; is required and must be Y or N.';
                      }
                    }
                    else
                    {
                      $pErrorMessage = 'Error: At row ' . $pRowNumber . ' column 8 &ldquo;Manager Approval Required?&rdquo; is required and must be Y or N.';
                    }
                  }
                  else
                  {
                    $pErrorMessage = 'Error: At row ' . $pRowNumber . ' column 7 &ldquo;Access Owner Email&rdquo; is required and must be a valid email address.';
                  }
                }
                else
                {
                  $pErrorMessage = 'Error: At row ' . $pRowNumber . ' column 6 &ldquo;Access Owner Name&rdquo; is required.';
                }
              }
              else
              {
                $pErrorMessage = 'Error: At row ' . $pRowNumber . ' column 5 &ldquo;Risk Rating&rdquo; is required and can only be High, Medium, or Low.';
              }
            }
            else
            {
              $pErrorMessage = 'Error: At row ' . $pRowNumber . ' column 4 &ldquo;Access Source&rdquo; is required.';
            }
          }
          else
          {
            $pErrorMessage = 'Error: At row ' . $pRowNumber . ' column 3 &ldquo;Application Name&rdquo; is required.';
          }
        }
        else
        {
          $pErrorMessage = 'Error: At row ' . $pRowNumber . ' column 2 &ldquo;Access Description&rdquo; is required.';
        }
      }
      else
      {
        $pErrorMessage = 'Error: At row ' . $pRowNumber . ' column 1 &ldquo;Access Name&rdquo; is required.';
      }
    }
    else
    {
      $pErrorMessage = 'Error: At row ' . $pRowNumber . '. There should be 13 columns.';
    }

    // Now that we have verified that all fields have been populated, let's validate
    // specific fields for the proper content.
    if ( $allFieldsPopulated )
    {
      if ( $pData[4] == 'Low' || $pData[4] == 'Medium' || $pData[4] == 'High' )
      {
        $val1 = strpos( $pData[6], '@' );
               
        if ( $val1 === false )
        {
          $pErrorMessage = 'Error: At row ' . $pRowNumber . ' column 7 &ldquo;Access Owner Email&rdquo; is not a valid email address (it does not contain an @ symbol).'; 
        }
        else
        {
          if ( $pData[7] == 'Y' || $pData[7] == 'N' )
          {
            if ( $pData[8] == 'Y' || $pData[8] == 'N' )
            {
              $val2 = strpos( $pData[10], '@' );
              
              if ( $val2 === false )
              {
                $pErrorMessage = 'Error: At row ' . $pRowNumber . ' column 11 &ldquo;Primary Provisioning Lead Email&rdquo; is not a valid email address (it does not contain an @ symbol).'; 
              }
              else
              {
                if ( $pData[12] == 'NA' )
                {
                  $validContent = true;
                }
                else
                {
                  $val3 = strpos( $pData[12], '@' );
                  
                  if ( $val3 === false )
                  {
                    $pErrorMessage = 'Error: At row ' . $pRowNumber . ' column 13 &ldquo;Secondary Provisioning Lead Email&rdquo; is not a valid email address (it does not contain an @ symbol). If none exists, type NA for the value.'; 
                  }
                  else
                  {
                    $validContent = true;
                  }
                }
              }
            }
            else
            {
              $pErrorMessage = 'Error: At row ' . $pRowNumber . ' column 9 &ldquo;Access Owner Approval Required?&rdquo; must be Y or N.'; 
            }
          }
          else
          {
            $pErrorMessage = 'Error: At row ' . $pRowNumber . ' column 8 &ldquo;Manager Approval Required?&rdquo; must be Y or N.';
          }
        }
      }
      else
      {
        $pErrorMessage = 'Error: At row ' . $pRowNumber . ' column 5 &ldquo;Risk Rating&rdquo; can only be High, Medium, or Low.';
      }
    }

    if ( $allFieldsPopulated && $validContent )
    {
      $rCode = true;
    }

    return $rCode;
  }

  function sendPasswordResetVerification( $pEmailAddress,
                                          $pFirstName,
                                          $pValidationHandle,
                                          $pAppRoot,
                                          $pHostingDomain,
                                          $pFromEmailAddress
                                        )
  {
    $rCode = false;
   
    $emailSubject = 'Identivize password reset confirmation requested';
    
    $url = 'http://' . $pHostingDomain . $pAppRoot . '/cfmpwdreset/?vh=' . $pValidationHandle;
    
    $emailContent = "Hi " . $pFirstName . ". You have requested a reset of your password. " .
                    "Please click on the following link. This will log you into Identivize " .
                    "and take you to the password reset page:\n\n" . $url;

    $headers = "From: $pFromEmailAddress";
    
    if ( mail( $pEmailAddress, $emailSubject, $emailContent, $headers ))
    {
      $rCode = true;
    }
   
    return $rCode;   
  }

  function sendArcPasswordResetVerification( $pEmailAddress,
                                             $pFirstName,
                                             $pValidationHandle,
                                             $pAppRoot,
                                             $pHostingDomain,
                                             $pFromEmailAddress
                                           )
  {
    $rCode = false;
   
    $emailSubject = 'Identivize Access Request Console password reset confirmation requested';
    
    $url = 'http://' . $pHostingDomain . $pAppRoot . '/arcsh/cfmpwdreset/?vh=' . $pValidationHandle;
    
    $emailContent = "Hi " . $pFirstName . ". You have requested a reset of your password. " .
                    "Please click on the following link. This will log you into the Identivize " .
                    "Access Request Console and take you to the password reset page:\n\n" . $url;

    $headers = "From: $pFromEmailAddress";
    
    if ( mail( $pEmailAddress, $emailSubject, $emailContent, $headers ))
    {
      $rCode = true;
    }
   
    return $rCode;   
  }

  function downloadFile( $pFilePath )
  {
    // Set headers for the download
    header( 'Content-Description: File Transfer' );
    header( 'Content-Type: application/octet-stream' );
    header( 'Content-Disposition: attachment; filename=' . basename( $pFilePath ));
    header( 'Expires: 0' );
    header( 'Cache-Control: must-revalidate' );
    header( 'Pragma: public' );
    header( 'Content-Length: ' . filesize( $pFilePath ));
  
    // Read the file and output it to the browser
    readfile( $pFilePath );
  }

?>