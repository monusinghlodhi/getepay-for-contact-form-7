<?php
/**
 * Plugin Name:     Getepay for Contact Form 7
 * Plugin URI:      https://github.com/monusinghlodhi/getepay-for-contact-form-7
 * Description:     This plugin seamlessly integrates Getepay with Contact Form 7
 * Author:          Monu Singh
 * Author URI:      https://getepay.in
 * Text Domain:     getepay-for-cf7
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         Getepay_For_CF7
 */

defined("WPINC") or die;

if (file_exists( dirname(__FILE__) . '/vendor/autoload.php' ) ) {
  require_once dirname(__FILE__) . '/vendor/autoload.php';
}

define("GETEPAY_CF7_PLUGIN_PATH", plugin_dir_path(__FILE__));
define("GETEPAY_CF7_PLUGIN_URL", plugin_dir_url(__FILE__));
define("GETEPAY_CF7_PLUGIN_FILE", plugin_basename(__FILE__));
define("GETEPAY_CF7_ASSETS_URL", plugins_url('/getepay-for-contact-form-7/assets/'));
define("GETEPAY_CF7_TEXT_DOMAIN", "getepay-for-cf7");
define("GETEPAY_CF7_PLUGIN_VERSION", "1.0.0");

function cf7getepay_admin_enqueue($hook){
  wp_enqueue_script('cf7getepay-sweetalert2', GETEPAY_CF7_PLUGIN_URL.'assets/js/lib/sweetalert2.js');
  wp_enqueue_script('cf7getepay-admin', GETEPAY_CF7_PLUGIN_URL.'assets/js/admin.js',array('jquery'),1.0,true);
  wp_localize_script('cf7getepay-admin', 'ajax_object_cf7rzp',
  array (
    'ajax_url' 			=> admin_url('admin-ajax.php')
  )
);

  wp_enqueue_style( 'cf7getepay-styles', GETEPAY_CF7_PLUGIN_URL.'assets/css/styles.css','',1.0);
}
add_action('admin_enqueue_scripts','cf7getepay_admin_enqueue',10);

if (class_exists('GetepayCF7\\Init')) {
  GetepayCF7\Init::register_services();
}

function getepay_cf7_activation() {
	( new \GetepayCF7\Base\Activate() )->activate();
}

register_activation_hook( __FILE__, 'getepay_cf7_activation' );

function cf7getepay_get_payment_more_info($post_id){
  global $wpdb;

  $table_name = $wpdb->prefix . "getepay_cf7_payment";
  $res = [];
  $post_id = sanitize_text_field(is_numeric($_GET['post_id'])?$_GET['post_id']:exit);

  $data = $wpdb->get_results($wpdb->prepare("SELECT form_id, form_title, name, phone, amount, transaction_id, email, mode, status, cf7_data FROM {$table_name} WHERE id= %d", array($post_id)));

  // // Retrieve JSON data from the array
  // $cf7_jsonData = $data[0]->cf7_data;

  // // Decode the JSON data
  // $form_data = json_decode($cf7_jsonData, true);

  // echo $form_data['getepay_cf7-pooja-datetime']; die;

  $data_array = get_object_vars($data[0]);

  $name   = $data_array['name'];
  $email  = $data_array['email'];
  $phone  = $data_array['phone'];
  $form_id  = $data_array['form_id'];
  // $form_title  = $data_array['form_title'];
  $amount  = $data_array['amount'];
  $transaction_id  = $data_array['transaction_id'];
  $mode  = $data_array['mode'];
  $status  = $data_array['status'];  
  $bookingdate  = $data_array['getepay_cf7-pooja-datetime'];

  $res['getepay_payment_id'] = $transaction_id;
  $res['getepay_cf7_name'] = $name;
  $res['getepay_cf7_phone'] = $phone;
  $res['getepay_cf7_email'] = $email ;
  $res['getepay_cf7_form_title'] = get_the_title($form_id);
  $res['getepay_cf7_amount'] = $amount;
  $res['getepay_cf7_payment_mode'] = $mode;
  $res['getepay_cf7_payment_status'] = $status;

  // $dt = get_the_date( 'Y-m-d H:i:s', $post_id);
  // $dt = new DateTime($dt, new DateTimeZone('UTC'));
  // $dt->setTimezone(new DateTimeZone('Asia/Kolkata'));
  // $res['created_at'] = $dt->format('F j, Y | h:i:s a');


  echo json_encode($res);
  wp_die();
}
add_action( 'wp_ajax_nopriv_cf7getepay_get_payment_more_info', 'cf7getepay_get_payment_more_info' );
add_action( 'wp_ajax_cf7getepay_get_payment_more_info', 'cf7getepay_get_payment_more_info' );