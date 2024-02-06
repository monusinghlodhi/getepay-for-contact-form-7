<?php

namespace GetepayCF7\Payment;

use GetepayCF7\Helpers\Functions;
use Exception;
use WPCF7_Submission;

class FormSubmission
{
  private $helpers;

  public function __construct()
  {
    $this->helpers = Functions::get_instance();
  }

  public function register()
  {
    add_action('wpcf7_before_send_mail', array($this, 'process_data'));
    add_filter('wpcf7_load_js', '__return_false');
  }

  public function process_data($contact_form)
  {
    $id = $contact_form->id();
    $list_of_forms = $this->helpers->general_option("getepay_cf7_form_select");

    $setting_url = admin_url('admin.php?page=getepay-cf7&tab=general-settings');
    if (empty($list_of_forms)) wp_die("The payment form is not specified. Please go to the <a href='$setting_url'>General Settings options page</a> to set the form.");

    if ( in_array( $id, $list_of_forms )) {
      $submission = WPCF7_Submission::get_instance();

      if ( $submission && isset( $_POST['getepay_cf7-amount'] ) ) {
        $form_id        = $submission->get_contact_form()->id();
        $form_title     = $submission->get_contact_form()->title();
        $posted_data    = $submission->get_posted_data();
        $name           = $posted_data['getepay_cf7-name'];
        $email          = $posted_data['getepay_cf7-email'];
        $amount         = $posted_data['getepay_cf7-amount'];
        $phone          = $posted_data['getepay_cf7-phone'];
        $transaction_id = '';
        $mode           = $this->helpers->get_mode();
        $status         = 'pending';

        // Convert data to JSON
        $getepay_cf7_json_data = json_encode( $posted_data );

        $payment_id = $this->record_data($form_id, $form_title, $name, $phone, $email, $amount, $transaction_id, $mode, $status, $getepay_cf7_json_data);
        
        $description = apply_filters('getepay_cf7_form_description', "Payment for $form_title");
        $this->process_payment($name, $email, $phone, $amount, $description, $payment_id);
      }
    }
  }

  public function record_data($form_id, $form_title, $name, $phone, $email, $amount, $transaction_id, $mode, $status, $getepay_cf7_json_data)
  {
    global $wpdb;

    $table_name = $wpdb->prefix . "getepay_cf7_payment";

    $wpdb->insert(
      $table_name,
      array(
        'form_id'        => $form_id,
        'form_title'     => $form_title,
        'name'           => $name,
        'phone'          => $phone,
        'amount'         => $amount,
        'transaction_id' => $transaction_id,
        'bill_url'       => '',
        'email'          => $email,
        'mode'           => $mode,
        'status'         => $status,
        'cf7_data'       => $getepay_cf7_json_data,
        'created_at'     => date("Y-m-d H:i:s"),
        'paid_at'        => '0000-00-00 00:00:00',
      ),
    );

    return $wpdb->insert_id;
  }

  public function process_payment($name, $email, $phone, $amount, $description, $payment_id)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . "getepay_cf7_payment";
    $bill_url = add_query_arg(array('getepay_cf7-listener' => 'getepay-recipt', 'payment-id' => $payment_id), get_bloginfo('wpurl') . "/getepay-receipt-page/");

    // Get plugin settings
      $url = $this->helpers->get_api_request_url();
      $mid = $this->helpers->get_api_mid();
      $terminalId = $this->helpers->get_api_terminal_id();
      $key = $this->helpers->get_api_secret_key();
      $iv = $this->helpers->get_api_live_iv();

			$ru = add_query_arg(array('getepay_cf7-listener' => 'getepay', 'payment-id' => $payment_id), site_url("?page_id=" . $this->helpers->general_option('getepay_cf7_redirect_page') . ""));
			// $ru = "https://pay1.getepay.in:8443/getepayPortal/pg/pgPaymentResponse";
      $amt = $amount;
			$udf1 = $name;
			$udf2 = (isset($phone) ? $phone : "9999999999");
			$udf3 = $email;

			// Prepare request data
			$request = array(
				"mid" => $mid,
				"amount" => $amt,
				"merchantTransactionId" => "GETEPAYCF".$payment_id,
				"transactionDate" => date("Y-m-d H:i:s"),
				"terminalId" => $terminalId,
				"udf1" => $udf2,
				"udf2" => $udf3,
				"udf3" => $udf1,
				"udf4" => "",
				"udf5" => "",
				"udf6" => "",
				"udf7" => "",
				"udf8" => "",
				"udf9" => "",
				"udf10" => "",
				"ru" => $ru,
				"callbackUrl" => "",
				"currency" => "INR",
				"paymentMode" => "ALL",
				"bankId" => "",
				"txnType" => "single",
				"productType" => "IPG",
				"txnNote" => $description,
				"vpa" => $terminalId,
			);

			// Encrypt the request
			$json_request = json_encode($request);
			$key = base64_decode($key);
			$iv = base64_decode($iv);
			$ciphertext_raw = openssl_encrypt($json_request, "AES-256-CBC", $key, OPENSSL_RAW_DATA, $iv);
			$ciphertext = bin2hex($ciphertext_raw);
			$new_request = array(
				"mid" => $mid,
				"terminalId" => $terminalId,
				"req" => $ciphertext
			);

    // $response = wp_remote_post($this->helpers->get_url() . "/api/v3/bills", $args);
    // $apiBody = json_decode(wp_remote_retrieve_body($response));
    // $bill_url = $apiBody->url;

    $response = wp_remote_post($url, array(
      'headers' => array(
        'Content-Type' => 'application/json',
      ),
      'body' => json_encode($new_request),
    ));
  
    if (is_wp_error($response)) {
      // Handle the error as needed.
      return 'error';
    }
    // Decode and process the response
    $jsonDecode = json_decode($response['body']);

    $jsonResult = $jsonDecode->response;
    $ciphertext_raw = hex2bin($jsonResult);
    $original_plaintext = openssl_decrypt($ciphertext_raw, "AES-256-CBC", $key, $options = OPENSSL_RAW_DATA, $iv);
    $json = json_decode($original_plaintext);
    
    // Process successful response
      $paymentId = $json->paymentId;
      $payment_url = $json->paymentUrl;

    if ($payment_url) {

      $wpdb->update(
        $table_name,
        array(
          //'status' => 'completed',
          'transaction_id' => $paymentId,
          'bill_url'       => $bill_url,
        ),
        array('ID' => $payment_id)
      );

      $content = require_once GETEPAY_CF7_PLUGIN_PATH . "app/views/splash-page.php";
      $content .= '<script>window.location.replace("' . $payment_url . '");</script>';
    }

    $allowed_tags = array('div' => array(), 'p' => array(), 'script' => array());

    echo wp_kses($content, $allowed_tags);
  }
}