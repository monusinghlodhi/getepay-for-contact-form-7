<?php

namespace GetepayCF7\Settings;

class General
{
  public static $options;

  public function __construct()
  {
    self::$options = get_option("getepay_cf7_general_settings");
  }

  public function register()
  {
    add_action('admin_init', array($this, "init"));
  }

  public function init()
  {
    register_setting("getepay_cf7_general", "getepay_cf7_general_settings");

    add_settings_section(
      "getepay_cf7_general_section",
      null,
      null,
      "getepay_cf7_general_settings"
    );

    add_settings_field(
      "getepay_cf7_mode",
      "Test Mode",
      array($this, 'mode_callback'),
      "getepay_cf7_general_settings",
      "getepay_cf7_general_section",
      array(
        "label_for" => "getepay_cf7_mode"
      )
    );

    add_settings_field(
      "getepay_cf7_form_select",
      "Payment Form",
      array($this, 'form_select_callback'),
      "getepay_cf7_general_settings",
      "getepay_cf7_general_section",
      array(
        "label_for" => "getepay_cf7_form_select"
      )
    );

    add_settings_field(
      "getepay_cf7_redirect_page",
      "Payment Confirmation / Redirect Page",
      array($this, 'redirect_page_callback'),
      "getepay_cf7_general_settings",
      "getepay_cf7_general_section",
      array(
        "label_for" => "getepay_cf7_redirect_page"
      )
    );
  }

  public function mode_callback()
  {
  ?>
    <input type="checkbox" name="getepay_cf7_general_settings[getepay_cf7_mode]" id="getepay_cf7_mode" value="1" <?php isset(self::$options['getepay_cf7_mode']) ? (checked("1", self::$options['getepay_cf7_mode'], true)) : null ?>>
    <label for="getepay_cf7_mode">Activate Test Mode</label>
  <?php
  }

  public function form_select_callback()
  {
    $cf7_forms = get_posts(array(
      'post_type' => 'wpcf7_contact_form',
      'posts_per_page' => -1
    ));
    
    $ids_titles = array();
    
    foreach ($cf7_forms as $form) {
      $ids_titles[$form->ID] = esc_html($form->post_title) . ' (ID: ' . esc_html($form->ID) . ')';
    }
    
    $selected_ids = array_map('intval', (array) (isset(self::$options['getepay_cf7_form_select']) ? self::$options['getepay_cf7_form_select'] : array()));
    
    ?>
    <select name='getepay_cf7_general_settings[getepay_cf7_form_select][]' id='getepay_cf7_form_select' multiple="multiple" style="width: 350px; height: 150px;">
      <option value="">------------</option>
      <?php foreach ($ids_titles as $id => $title) : ?>
        <?php $selected = in_array($id, $selected_ids); ?>
        <option value="<?php echo esc_attr($id); ?>" <?php selected($selected); ?>>
          <?php echo $title; ?>
        </option>
      <?php endforeach; ?>
    </select>
    <p class="description">Choose a Contact Form 7 form to use. Hold CTRL/CMD to select multiple forms. <br> <a href="<?php echo esc_url(admin_url("admin.php?page=wpcf7-new")); ?>">Click here</a> to create a new form.</p>
    <?php
  }


  public function redirect_page_callback() 
  {
    $page_query_args = array('post_type' => 'page', 'posts_per_page' => -1);
    $pages = get_posts($page_query_args);
  
    $page_ids = array_map(function($page) { return $page->ID; }, $pages);
    $page_titles = array_map(function($page) { return $page->post_title; }, $pages);
  
    $ids_titles = array_combine($page_ids, $page_titles);
    $redirect_page_id = self::$options['getepay_cf7_redirect_page'] ?? '';
  
    ?>
    <select name='getepay_cf7_general_settings[getepay_cf7_redirect_page]' id='getepay_cf7_redirect_page' style="width: 350px;">
      <option value="">-- Select a redirect page --</option>
      <?php foreach ($ids_titles as $id => $title) { ?>
        <option value="<?php echo esc_attr($id) ?>" <?php selected($id, $redirect_page_id, true); ?>>
          <?php echo esc_html($title); ?>
        </option>
      <?php } ?>
    </select>
    <p class="description">Choose a page to redirect after payment completed. Default page: <strong>GETEPAY CF7 Payment Confirmation</strong></p>
    <p class="description">If you want to use a custom redirect page, make sure to add the <code id="getepay_cf7-shortcode" style="cursor: pointer;" title="Click to copy ">[getepay_cf7_payment_confirmation]</code> shortcode inside the custom page's content.</p>
    <?php
  }
}