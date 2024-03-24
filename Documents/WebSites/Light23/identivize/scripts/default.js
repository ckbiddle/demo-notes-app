// JavaScript Document

function showManagerApprovalRequestDetails( pRequestId,
                                            pRequestLineId,
                                            pAppRoot
                                          )
{
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    document.getElementById( 'mgrtr_' + pRequestId + '_' + pRequestLineId ).style.display = '';
    document.getElementById( 'mgrtd_' + pRequestId + '_' + pRequestLineId ).innerHTML = this.responseText;
    document.getElementById( 'mgrtdshowhide_' + pRequestId + '_' + pRequestLineId ).innerHTML =
      '<a href="javascript:hideManagerApprovalRequestDetails( ' + pRequestId + ', ' + pRequestLineId + ', &quot;' + pAppRoot + '&quot; )" style="text-decoration:none" >&ominus;</a>';
  }
  xhttp.open( 'GET', pAppRoot + '/controller/?cmd=get_req_appr_dtl&reqid=' + pRequestId + '&reqlineid=' + pRequestLineId );
  xhttp.send();
}

function hideManagerApprovalRequestDetails( pRequestId,
                                            pRequestLineId,
                                            pAppRoot
                                          )
{
    document.getElementById( 'mgrtd_' + pRequestId + '_' + pRequestLineId ).innerHTML = '';
    document.getElementById( 'mgrtr_' + pRequestId + '_' + pRequestLineId ).style.display = 'none';
    document.getElementById( 'mgrtdshowhide_' + pRequestId + '_' + pRequestLineId ).innerHTML =
      '<a href="javascript:showManagerApprovalRequestDetails( ' + pRequestId + ', ' + pRequestLineId + ', &quot;' + pAppRoot + '&quot; )" style="text-decoration:none" >&oplus;</a>';
}

function showAccessOwnerApprovalRequestDetails( pRequestId,
                                                pRequestLineId,
                                                pAppRoot
                                              )
{
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    document.getElementById( 'aotr_' + pRequestId + '_' + pRequestLineId ).style.display = '';
    document.getElementById( 'aotd_' + pRequestId + '_' + pRequestLineId ).innerHTML = this.responseText;
    document.getElementById( 'aotdshowhide_' + pRequestId + '_' + pRequestLineId ).innerHTML =
      '<a href="javascript:hideAccessOwnerApprovalRequestDetails( ' + pRequestId + ', ' + pRequestLineId + ', &quot;' + pAppRoot + '&quot; )" style="text-decoration:none" >&ominus;</a>';
  }
  xhttp.open( 'GET', pAppRoot + '/controller/?cmd=get_req_appr_dtl&reqid=' + pRequestId + '&reqlineid=' + pRequestLineId );
  xhttp.send();
}

function hideAccessOwnerApprovalRequestDetails( pRequestId,
                                                pRequestLineId,
                                                pAppRoot
                                              )
{
    document.getElementById( 'aotd_' + pRequestId + '_' + pRequestLineId ).innerHTML = '';
    document.getElementById( 'aotr_' + pRequestId + '_' + pRequestLineId ).style.display = 'none';
    document.getElementById( 'aotdshowhide_' + pRequestId + '_' + pRequestLineId ).innerHTML =
      '<a href="javascript:showAccessOwnerApprovalRequestDetails( ' + pRequestId + ', ' + pRequestLineId + ', &quot;' + pAppRoot + '&quot; )" style="text-decoration:none" >&oplus;</a>';
}

function showProvisioningLeadRequestDetails( pRequestId,
                                             pRequestLineId,
                                             pAppRoot
                                           )
{
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    document.getElementById( 'pltr_' + pRequestId + '_' + pRequestLineId ).style.display = '';
    document.getElementById( 'pltd_' + pRequestId + '_' + pRequestLineId ).innerHTML = this.responseText;
    document.getElementById( 'pltdshowhide_' + pRequestId + '_' + pRequestLineId ).innerHTML =
      '<a href="javascript:hideProvisioningLeadRequestDetails( ' + pRequestId + ', ' + pRequestLineId + ', &quot;' + pAppRoot + '&quot; )" style="text-decoration:none" >&ominus;</a>';
  }
  xhttp.open( 'GET', pAppRoot + '/controller/?cmd=get_req_appr_dtl&reqid=' + pRequestId + '&reqlineid=' + pRequestLineId );
  xhttp.send();
}

function hideProvisioningLeadRequestDetails( pRequestId,
                                             pRequestLineId,
                                             pAppRoot
                                           )
{
    document.getElementById( 'pltd_' + pRequestId + '_' + pRequestLineId ).innerHTML = '';
    document.getElementById( 'pltr_' + pRequestId + '_' + pRequestLineId ).style.display = 'none';
    document.getElementById( 'pltdshowhide_' + pRequestId + '_' + pRequestLineId ).innerHTML =
      '<a href="javascript:showProvisioningLeadRequestDetails( ' + pRequestId + ', ' + pRequestLineId + ', &quot;' + pAppRoot + '&quot; )" style="text-decoration:none" >&oplus;</a>';
}

function showProvisionerTaskDetails( pRequestId,
                                     pRequestLineId,
                                     pAppRoot
                                   )
{
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    document.getElementById( 'prvtr_' + pRequestId + '_' + pRequestLineId ).style.display = '';
    document.getElementById( 'prvtd_' + pRequestId + '_' + pRequestLineId ).innerHTML = this.responseText;
    document.getElementById( 'prvtdshowhide_' + pRequestId + '_' + pRequestLineId ).innerHTML =
      '<a href="javascript:hideProvisionerTaskDetails( ' + pRequestId + ', ' + pRequestLineId + ', &quot;' + pAppRoot + '&quot; )" style="text-decoration:none" >&ominus;</a>';
  }
  xhttp.open( 'GET', pAppRoot + '/controller/?cmd=get_req_appr_dtl&reqid=' + pRequestId + '&reqlineid=' + pRequestLineId );
  xhttp.send();
}

function hideProvisionerTaskDetails( pRequestId,
                                     pRequestLineId,
                                     pAppRoot
                                   )
{
    document.getElementById( 'prvtd_' + pRequestId + '_' + pRequestLineId ).innerHTML = '';
    document.getElementById( 'prvtr_' + pRequestId + '_' + pRequestLineId ).style.display = 'none';
    document.getElementById( 'prvtdshowhide_' + pRequestId + '_' + pRequestLineId ).innerHTML =
      '<a href="javascript:showProvisionerTaskDetails( ' + pRequestId + ', ' + pRequestLineId + ', &quot;' + pAppRoot + '&quot; )" style="text-decoration:none" >&oplus;</a>';
}

function showCompletedItemsRequestDetails( pRequestId,
                                           pRequestLineId,
                                           pAppRoot
                                         )
{
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    document.getElementById( 'cmpltr_' + pRequestId + '_' + pRequestLineId ).style.display = '';
    document.getElementById( 'cmpltd_' + pRequestId + '_' + pRequestLineId ).innerHTML = this.responseText;
    document.getElementById( 'cmpltdshowhide_' + pRequestId + '_' + pRequestLineId ).innerHTML =
      '<a href="javascript:hideCompletedItemsRequestDetails( ' + pRequestId + ', ' + pRequestLineId + ', &quot;' + pAppRoot + '&quot; )" style="text-decoration:none" >&ominus;</a>';
  }
  xhttp.open( 'GET', pAppRoot + '/controller/?cmd=get_req_appr_dtl&reqid=' + pRequestId + '&reqlineid=' + pRequestLineId );
  xhttp.send();
}

function hideCompletedItemsRequestDetails( pRequestId,
                                           pRequestLineId,
                                           pAppRoot
                                         )
{
  document.getElementById( 'cmpltd_' + pRequestId + '_' + pRequestLineId ).innerHTML = '';
  document.getElementById( 'cmpltr_' + pRequestId + '_' + pRequestLineId ).style.display = 'none';
  document.getElementById( 'cmpltdshowhide_' + pRequestId + '_' + pRequestLineId ).innerHTML =
    '<a href="javascript:showCompletedItemsRequestDetails( ' + pRequestId + ', ' + pRequestLineId + ', &quot;' + pAppRoot + '&quot; )" style="text-decoration:none" >&oplus;</a>';
}

function showCsvInfo()
{
  document.getElementById( 'csv_info' ).style.display = '';
  document.getElementById( 'workflow_data_label' ).innerHTML =
    'Access Details <a href="javascript:hideCsvInfo()" style="text-decoration:none" >(Hide more info)</a>:';
}

function hideCsvInfo()
{
  document.getElementById( 'csv_info' ).style.display = 'none';
  document.getElementById( 'workflow_data_label' ).innerHTML =
    'Access Details <a href="javascript:showCsvInfo()" style="text-decoration:none" >(Show more info)</a>:';
}

/*
function showHello()
{
  alert( 'Hello from default.js!' );
}
*/