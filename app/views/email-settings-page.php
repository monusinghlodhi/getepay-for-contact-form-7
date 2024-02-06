<form action="options.php" method="POST">
  <?php
    settings_fields( "getepay_cf7_email" );
    do_settings_sections( "getepay_cf7_email_settings" );
    submit_button();
  ?>
</form>