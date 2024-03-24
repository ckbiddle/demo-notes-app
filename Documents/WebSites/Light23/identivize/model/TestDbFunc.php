<?php

  require './DBFunctions.php';
  require './InitDbConnect.php';

  $managerItems = array();
  $accessOwnerItems = array();

  $managerItems = getRequestLineItemsByManagerAndManagerActionOverPastYear( $conn,
                                                                            'joe.accessowner@accessowners.com',  // Manager login ID
                                                                            'light23',
                                                                            'APPROVED'
                                                                          );
  
  $accessOwnerItems = getRequestLineItemsByAccessOwnerAndAccessOwnerActionOverPastYear( $conn,
                                                                                        'joe.accessowner@accessowners.com',  // Access owner login ID
                                                                                        'light23',
                                                                                        'APPROVED'
                                                                                      );
  
  echo '<p>Manager Items</p>';
  echo '<pre>';
  print_r( $managerItems );
  echo '</pre>';
  
  echo '<p>Access Owner Items</p>';
  echo '<pre>';
  print_r( $accessOwnerItems );
  echo '</pre>';

?>