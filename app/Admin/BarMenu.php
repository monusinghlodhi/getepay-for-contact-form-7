<?php

namespace GetepayCF7\Admin;

use GetepayCF7\Helpers\Functions;
use WP_Admin_Bar;


class BarMenu
{
  private $helpers;

  public function __construct()
  {
    $this->helpers = Functions::get_instance();
  }

  public function register()
  {
    add_action("admin_bar_menu", array($this, "menu"), 999);
  }

  public function menu( WP_Admin_Bar $admin_bar )
  {
    if (!current_user_can('manage_options')) {
      return;
    }

    $color = ("1" == $this->helpers->general_option("getepay_cf7_mode")) ? "#F3BB1B" : "#90EE90";
    $menu_id = "getepay_cf7-ab";

    $args = array(
      array(
        'id'    => $menu_id,
        'title' => "GETEPAY CF7 Mode Status: <span style='color:{$color};'>" . strtoupper($this->helpers->get_mode()) . "</span>",
        'href'  => '#'
      ),
      array(
        'parent' => $menu_id,
        'id'     => 'getepay_cf7-ab-payments',
        'title'  => "Payments",
        'href'   => admin_url("admin.php?page=getepay-cf7&tab=payments")
      ),
      array(
        'parent' => $menu_id,
        'id'     => 'getepay_cf7-ab-general-settings',
        'title'  => "General Settings",
        'href'   => admin_url("admin.php?page=getepay-cf7&tab=general-settings")
      ),
      array(
        'parent' => $menu_id,
        'id'     => 'getepay_cf7-ab-api-settings',
        'title'  => "API Settings",
        'href'   => admin_url("admin.php?page=getepay-cf7&tab=api-settings")
      ),
      array(
        'parent' => $menu_id,
        'id'     => 'getepay_cf7-ab-email-settings',
        'title'  => "Email Settings",
        'href'   => admin_url("admin.php?page=getepay-cf7&tab=email-settings")
      ),
    );

    foreach ($args as $arg) {
      $admin_bar->add_menu($arg);
    }
  }
}