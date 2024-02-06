<form action="options.php" method="POST">
  <?php
    settings_fields( "getepay_cf7_api" );
    do_settings_sections( "getepay_cf7_live_settings" );
    do_settings_sections( "getepay_cf7_sandbox_settings" );
    submit_button();
  ?>
</form>