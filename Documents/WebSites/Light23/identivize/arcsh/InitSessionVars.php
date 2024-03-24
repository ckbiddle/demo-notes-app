<?php

  if ( basename( $_SERVER['PHP_SELF'] ) === basename( __FILE__ ))
  {
    die( '<p>You cannot access this page directly!</p>' );
  }

  // echo '<p>Initializing session variables ...</p>';

  // Just for session variables that are strings
  $sessionVars = array(
    'arc_login_id',
    'arc_login_login_id',
    'arc_login_pwd',
    'arc_login_message',
    'arc_login_error_message',
    'arc_tenant_name',
    'arc_company_name',
    'arc_login_name',
    'arc_ar_mgr_name',
    'arc_ar_mgr_email',
    'arc_ar_justification',
    'arc_ar_message',
    'arc_ar_error_message',
    'arc_registration_login_id',
    'arc_registration_pwd1',
    'arc_registration_pwd2',
    'arc_registration_first_name',
    'arc_registration_last_name',
    'arc_registration_job_title',
    'arc_registration_company_name',
    'arc_registration_message',
    'arc_registration_error_message',
    'arc_pwdreset_message',
    'arc_pwdreset_error_message',
    'arc_pwdreset_login_id',
    'arc_new_pwd_error_message',
    'arc_new_pwd_1',
    'arc_new_pwd_2',
    'arc_search_ar_search_string',
    'arc_search_ar_error_message'
  );

  foreach( $sessionVars as $var )
  {
    if ( !isset( $_SESSION[$var] ))
    {
      $_SESSION[$var] = '';
    }
  }
  
  $_SESSION['arc_ar_search_results'] = array();
  $_SESSION['arc_selected_access'] = array();
  $_SESSION['arc_selected_mgr_action_items'] = array();
  $_SESSION['arc_selected_ao_action_items'] = array();
  $_SESSION['arc_selected_pl_action_items'] = array();
  $_SESSION['arc_mgr_processed_action_items'] = array();
  $_SESSION['arc_ao_processed_action_items'] = array();
  $_SESSION['arc_selected_provisioner_action_items'] = array();

?>