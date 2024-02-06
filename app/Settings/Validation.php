<?php

namespace GetepayCF7\Settings;

class Validation
{
  private $helpers;

  public function __construct()
  {
    $this->helpers = \GetepayCF7\Helpers\Functions::get_instance();
  }

  public function register()
  {
    add_action("admin_notices", array($this, "credentials_check"));
  }

  public function credentials_check()
  {
    if ("1" == $this->helpers->general_option("getepay_cf7_mode") and ((empty($this->helpers->api_option("getepay_cf7_sandbox_request_url"))) or empty($this->helpers->api_option("getepay_cf7_sandbox_mid")) or empty($this->helpers->api_option("getepay_cf7_sandbox_terminal_id")) or empty($this->helpers->api_option("getepay_cf7_sandbox_secret_key")) or empty($this->helpers->api_option("getepay_cf7_sandbox_iv")))) {
      echo __(sprintf(
        '<div class="notice notice-warning">
              <p><strong>Getepay for Contact Form 7 -</strong>Getepay Sandbox Credentials is not set. Enter your Getepay Request Url, Getepay MID, Getepay Terminal Id, Getepay Key and Getepay IV in order to use Getepay service. <a href="' . get_admin_url() . 'admin.php?page=getepay-cf7&tab=api-settings">Set Credential</a></p>
          </div>',
      ), GETEPAY_CF7_TEXT_DOMAIN);
    } elseif ("0" == $this->helpers->general_option("getepay_cf7_mode") and (empty($this->helpers->api_option("getepay_cf7_live_request_url")) or empty($this->helpers->api_option("getepay_cf7_live_mid")) or empty($this->helpers->api_option("getepay_cf7_live_terminal_id")) or empty($this->helpers->api_option("getepay_cf7_live_secret_key")) or empty($this->helpers->api_option("getepay_cf7_live_iv")))) {
      echo __(sprintf(
        '<div class="notice notice-warning">
              <p><strong>Getepay for Contact Form 7 -</strong>Getepay Live Credentials is not set. Enter your Getepay Request Url, Getepay MID, Getepay Terminal Id, Getepay Key and Getepay IV in order to use Getepay service. <a href="' . get_admin_url() . 'admin.php?page=getepay-cf7&tab=api-settings">Set Credential</a></p>
          </div>',
      ), GETEPAY_CF7_TEXT_DOMAIN);
    }
  }
}