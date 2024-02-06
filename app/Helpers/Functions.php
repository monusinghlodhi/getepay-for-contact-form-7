<?php

namespace GetepayCF7\Helpers;

class Functions
{
  private static $instance = null;

  public static function get_instance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function general_option($key = '', $default = false)
  {
    $value = !empty(get_option('getepay_cf7_general_settings')[$key]) ? get_option('getepay_cf7_general_settings')[$key] : $default;
    return $value;
  }

  public function api_option($key = '', $default = false)
  {
    $value = !empty(get_option('getepay_cf7_api_options')[$key]) ? get_option('getepay_cf7_api_options')[$key] : $default;
    return $value;
  }

  public function get_mode()
  {
    $mode = ("1" == $this->general_option("getepay_cf7_mode")) ? "Test" : "Live";
    return $mode;
  }

  public function get_url()
  {
    $live    = "https://portal.getepay.in:8443/getepayPortal/pg/generateInvoice";
    $sandbox = "https://pay1.getepay.in:8443/getepayPortal/pg/generateInvoice";

    $url = ("Live" == $this->get_mode()) ? $live : $sandbox;

    return $url;
  }

  public function get_api_request_url()
  {
    $live    = $this->api_option("getepay_cf7_live_request_url");
    $sandbox = $this->api_option("getepay_cf7_sandbox_request_url");

    $api_request_url = ("Live" == $this->get_mode()) ? $live : $sandbox;

    return $api_request_url;
  }
  public function get_api_mid()
  {
    $live    = $this->api_option("getepay_cf7_live_mid");
    $sandbox = $this->api_option("getepay_cf7_sandbox_mid");

    $api_mid = ("Live" == $this->get_mode()) ? $live : $sandbox;

    return $api_mid;
  }
  public function get_api_terminal_id()
  {
    $live    = $this->api_option("getepay_cf7_live_terminal_id");
    $sandbox = $this->api_option("getepay_cf7_sandbox_terminal_id");

    $api_terminal_id = ("Live" == $this->get_mode()) ? $live : $sandbox;

    return $api_terminal_id;
  }
  public function get_api_secret_key()
  {
    $live    = $this->api_option("getepay_cf7_live_secret_key");
    $sandbox = $this->api_option("getepay_cf7_sandbox_secret_key");

    $api_secret_key = ("Live" == $this->get_mode()) ? $live : $sandbox;

    return $api_secret_key;
  }

  public function get_api_live_iv()
  {
    $live    = $this->api_option("getepay_cf7_live_iv");
    $sandbox = $this->api_option("getepay_cf7_sandbox_iv");

    $api_live_iv = ("Live" == $this->get_mode()) ? $live : $sandbox;

    return $api_live_iv;
  }

  public function get_xsignature()
  {
    $live    = $this->api_option("getepay_cf7_live_xsignature_key");
    $sandbox = $this->api_option("getepay_cf7_sandbox_xsignature_key");

    $xsignature = ("Live" == $this->get_mode()) ? $live : $sandbox;

    return $xsignature;
  }
}