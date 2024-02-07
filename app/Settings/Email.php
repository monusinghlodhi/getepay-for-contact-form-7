<?php

namespace GetepayCF7\Settings;

class Email
{
  public static $options;

  public function __construct()
  {
    self::$options = get_option("getepay_cf7_email_settings");
  }

  public function register()
  {
    add_action('admin_init', array($this, "init"));
    add_filter('pre_update_option_getepay_cf7_email_settings', array($this, 'update_email_permission'));
  }

  public function update_email_permission($new_value)
  {
    if (!isset($new_value['getepay_cf7_email_permission'])) {
      $new_value['getepay_cf7_email_permission'] = '';
    }

    return $new_value;
  }

  public function init()
  {
    register_setting(
      'getepay_cf7_email',
      'getepay_cf7_email_settings'
    );

    add_settings_section(
      'geteapy_cf7_email_section',
      'Email Settings',
      null,
      'getepay_cf7_email_settings'
    );

    add_settings_field(
      "getepay_cf7_email_permission",
      "Enable",
      array($this, 'send_email_callback'),
      "getepay_cf7_email_settings",
      "geteapy_cf7_email_section",
      array(
        "label_for" => "getepay_cf7_email_permission"
      )
    );

    add_settings_field(
      'geteapy_cf7_email_subject',
      'Email Subject',
      array($this, 'email_subject_callback'),
      'getepay_cf7_email_settings',
      'geteapy_cf7_email_section',
      array(
        'label_for' => 'geteapy_cf7_email_subject'
      )
    );

    add_settings_field(
      'geteapy_cf7_email_body',
      'Email Body',
      array($this, 'email_body_callback'),
      'getepay_cf7_email_settings',
      'geteapy_cf7_email_section',
      array(
        'label_for' => 'geteapy_cf7_email_body'
      )
    );
  }

  public function send_email_callback()
  {
    ?>
      <input type="checkbox" name="getepay_cf7_email_settings[getepay_cf7_email_permission]" id="getepay_cf7_email_permission" value="1" <?php isset(self::$options['getepay_cf7_email_permission']) ? (checked("1", self::$options['getepay_cf7_email_permission'], true)) : null ?>>
      <label for="getepay_cf7_email_permission">Send a confirmation email upon a successful transaction</label>
    <?php
  }

  public function email_subject_callback()
  {
    $default       = 'Transaction Confirmation';
    $subject_value = esc_attr(isset(self::$options['geteapy_cf7_email_subject']) ? self::$options['geteapy_cf7_email_subject'] : $default );
  ?>
    <input class="regular-text" type="text" name="getepay_cf7_email_settings[geteapy_cf7_email_subject]" id="geteapy_cf7_email_subject" value="<?php echo $subject_value ?>">
  <?php
  }

  public function email_body_callback()
  {
    $default = <<<GETEPAY_CF7_EMAIL_BODY
      <div>
        <p style="text-align: left;">Hello {customer_name},</p>
        Your payment has been {transaction_status}. Here are your transaction details:
        <p style="text-align: left;"><strong>Transaction ID:</strong> {transaction_id}</p>
        <p style="text-align: left;"><strong>Date:</strong> {transaction_date}</p>
        <p style="text-align: left;"><strong>Amount Paid:</strong> {transaction_amount}</p>
        <p style="text-align: left;">Thank you for your business.</p>
      </div>
      &nbsp;
    GETEPAY_CF7_EMAIL_BODY;

    $content = isset(self::$options['geteapy_cf7_email_body']) ? self::$options['geteapy_cf7_email_body'] : $default;

    $settings = array(
      'textarea_name' => 'getepay_cf7_email_settings[geteapy_cf7_email_body]',
      'textarea_rows' => 10,
      'teeny' => true,
      'media_buttons' => false,
      'tinymce' => array(
        'plugins' => 'wordpress,wplink,wpautoresize,wpeditimage,wpgallery,wplink,wpdialogs',
        'toolbar1' => 'formatselect,bold,italic,underline,strikethrough,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,undo,redo',
        'toolbar2' => '',
        'toolbar3' => '',
        'toolbar4' => '',
        'wp_autoresize_on' => true,
        'wp_link_text' => __('Insert/edit link'),
        'image_advtab' => true,
      ),
    );

    wp_editor($content, 'geteapy_cf7_email_body', $settings);
  }
}