<form action="options.php" method="POST">
  <?php
    settings_fields( "getepay_cf7_general" );
    do_settings_sections( "getepay_cf7_general_settings" );
    submit_button();
  ?>
</form>