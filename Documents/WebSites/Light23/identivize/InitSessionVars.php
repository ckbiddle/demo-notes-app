<?php

  if ( basename( $_SERVER['PHP_SELF'] ) === basename( __FILE__ ))
  {
    die( '<p>You cannot access this page directly!</p>' );
  }


  $sessionVars = array(
    'idv_login_id',
    'idv_tenant_name',
    'idv_login_name',
    'login_message',
    'login_error_message',
    'login_login_id',
    'login_pwd',
    'reg_error_message',
    'reg_login_id',
    'reg_pwd_1',
    'reg_pwd_2',
    'reg_last_name',
    'reg_first_name',
    'reg_company_name',
    'reg_arc_name',
    'reg_email_domains',
    'message',
    'wkflow_error_message',
    'wkflow_workflow_name',
    'wkflow_workflow_description',
    'pwdreset_error_message',
    'pwdreset_login_id',
    'new_pwd_error_message',
    'new_pwd_1',
    'new_pwd_2',
    'rvwkflow_selected_workflow'
  );

  foreach( $sessionVars as $var )
  {
    if ( !isset( $_SESSION[$var] ))
    {
      $_SESSION[$var] = '';
    }
  }

?>