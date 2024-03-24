<?php

  if ( $cardActive )
  {
   
?>

    <div style="height:auto;padding-bottom:10px">
      <div style="width:100%;height:100%;background-color:#FFF;padding:3px;border-radius:10px">
        <a href="./?dmd=lst&srl=<?php echo $crdSrl; ?>" style="text-decoration:none">
          <div style="width:100%;height:100%;padding:10px;text-align:center">
            <h5><?php echo $crdDataLabel; ?></h5>
            <span style="font-size:40px"><?php echo count( $crdDataArray ); ?></span>
            <!--
            <p>
              <?php echo $crdDataDescription; ?>
            </p>
            -->
          </div>
        </a>
      </div>
    </div>
    
<?php

  }
  else
  {

?>

    <div style="height:auto;padding-bottom:10px">
      <div style="width:100%;height:100%;background-color:#FFF;padding:3px;border-radius:10px">
        <a href="javascript:void(0)" style="text-decoration:none;color:#333">
          <div style="width:100%;height:100%;padding:10px;text-align:center">
            <h5><?php echo $crdDataLabel; ?></h5>
            <span style="font-size:40px"><?php echo count( $crdDataArray ); ?></span>
            <!--
            <p>
              <?php echo $crdDataDescription; ?>
            </p>
            -->
          </div>
        </a>
      </div>
    </div>

<?php

  }
  
?>