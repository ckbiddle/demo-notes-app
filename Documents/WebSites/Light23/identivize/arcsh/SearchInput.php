<?php

  if ( basename( $_SERVER['PHP_SELF'] ) === basename( __FILE__ ))
  {
    die( '<p>You cannot access this page directly!</p>' );
  }

?>

<div class="container-fluid pt-5"> <!-- pt-5 is a bootstrap feature to say "add a large top padding" -->
  <div class="row" style="padding-bottom:20px">
    <div class="col-md-4"></div>
    <div class="col-md-4" style="text-align:center">
      <h4>Search for Access</h4>
      <p>
      You can enter a partial name of the access or a key word from the description
      to perform a search.
      </p>
      <form action="../controller/" method="post">
        <input type="hidden" name="cmd" value="search_access_records" />
        <div class="row mb-3">
          <div class="col-md-12">
            <input type="text" class="form-control" id="search_string" name="search_string" value="<?php echo $_SESSION['arc_search_ar_search_string']; ?>" />
          </div>
        </div>
        <div class="row mb-4">
          <div class="col-md-3"></div>
          <div class="col-md-6" style="text-align:center">
            <input type="submit" class="btn btn-success" value="Search">
          </div>
          <div class="col-md-3"></div>
        </div>
      </form>
      <?php
      
        if ( isset( $_SESSION['arc_search_ar_error_message'] ) && $_SESSION['arc_search_ar_error_message'] != '' )
        {
          echo '<div class="alert alert-secondary">' . $_SESSION['arc_search_ar_error_message'] . '</div>';
        }
        
      ?>
    </div>
    <div class="col-md-4"></div>
  </div>
</div>