<?php

  if ( basename( $_SERVER['PHP_SELF'] ) === basename( __FILE__ ))
  {
    die( '<p>You cannot access this page directly!</p>' );
  }

?>

<!-- <nav class="navbar navbar-expand-sm bg-dark navbar-dark"> -->
<nav class="navbar navbar-expand-sm navbar-dark" style="background-color:#100673">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?php echo $appRoot . '/' ?>">
      <div style="float:left">
        <img src="<?php echo $appRoot . '/images/Identivize_Logo.png'; ?>" style="height:40px;padding-right:10px" />
      </div>
      <div style="float:left;padding-top:5px">
        <b>identivize</b><?php echo ( isset( $_SESSION['idv_login_name'] ) && $_SESSION['idv_login_name'] != '' ? ( '&nbsp;&nbsp;<span style="font-size:medium">Welcome, ' . $_SESSION['idv_login_name'] . '</span>' ) : '' ); ?>
      </div>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
      <ul class="navbar-nav ms-auto">  <!-- ms-auto aligns nav items to the right -->
        <?php
        
          if ( isset( $_SESSION['idv_login_id'] ) && $_SESSION['idv_login_id'] != '' )
          {
            echo '<li class="nav-item dropdown">' .
                 '  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Profile</a>' .
                 '  <ul class="dropdown-menu">' .
              // '    <li class="dropdown-item">' . $_SESSION['idv_login_name'] . '</li>' .
                 '    <li><a class="dropdown-item" href="' . $appRoot . '/newpwd/?sessionstatus=init&rtn=' . $_SERVER['REQUEST_URI'] . '">Update My Password</a></li>' .
                 '  </ul>' .
                 '</li>' .
                 '<li class="nav-item">' .
                 '  <a class="nav-link" href="' . $appRoot . '/controller/?cmd=logout">Logout</a>' .
                 '</li>';
          }
        
        ?>
      </ul>
    </div>
  </div>
</nav>