<?php

namespace GetepayCF7\Payment;

class ProcessRedirect
{

  private $helpers;

  public function __construct()
  {
    $this->helpers = \GetepayCF7\Helpers\Functions::get_instance();
  }
  
  public function register()
  {
    add_shortcode("getepay_cf7_payment_confirmation", array($this, "redirect_callback"));
  }

  public function redirect_callback()
  { ?>
<style>
	.invoice-box {
		max-width: 800px;
		margin: auto;
		padding: 30px;
		border: 1px solid #eee;
		box-shadow: 0 0 10px rgba(0, 0, 0, .15);
		font-size: 16px;
		line-height: 24px;
		font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
		color: #555;
	}
	
	.invoice-box table {
		width: 100%;
		line-height: inherit;
		text-align: left;
	}
	
	.invoice-box table td {
		padding: 5px;
		vertical-align: top;
	}
	
	.invoice-box table tr td:nth-child(2) {
		text-align: right;
	}
	
	.invoice-box table tr.top table td {
		padding-bottom: 20px;
	}
	
	.invoice-box table tr.top table td.title {
		font-size: 45px;
		line-height: 45px;
		color: #333;
	}
	
	.invoice-box table tr.information table td {
		padding-bottom: 40px;
	}
	
	.invoice-box table tr.heading td {
		background: #eee;
		border-bottom: 1px solid #ddd;
		font-weight: bold;
	}
	
	.invoice-box table tr.details td {
		padding-bottom: 20px;
	}
	
	.invoice-box table tr.item td{
		border-bottom: 1px solid #eee;
	}
	
	.invoice-box table tr.item.last td {
		border-bottom: none;
	}
	
	.invoice-box table tr.total td:nth-child(2) {
		border-top: 2px solid #eee;
		font-weight: bold;
	}
	
	@media only screen and (max-width: 600px) {
		.invoice-box table tr.top table td {
			width: 100%;
			display: block;
			text-align: center;
		}
		
		.invoice-box table tr.information table td {
			width: 100%;
			display: block;
			text-align: center;
		}
	}
	
	/** RTL **/
	.rtl {
		direction: rtl;
		font-family: Tahoma, "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
	}
	
	.rtl table {
		text-align: right;
	}
	
	.rtl table tr td:nth-child(2) {
		text-align: left;
	}

	@media print {
	body {
	font-size: 12pt; /* Adjust font size for print */
	}

	.invoice-box {
	border: 1px solid #000; /* Add border for better separation */
	box-shadow: none; /* Remove box shadow for better print clarity */
	padding: 10px; /* Adjust padding for better spacing */
	max-width: 100%; /* Make sure the content fits within the printed page */
	font-size: 14pt; /* Adjust font size for print */
	}

	/* Add more print-specific styles as needed */
	}
</style>
    <?php
    global $wp;
    if ((empty($_GET) and (isset($_GET['getepay_cf7-listener']) and "getepay" != $_GET['getepay_cf7-listener'])) or (isset($_GET['post']) and isset($_GET['action']))) return;

    if (isset($_GET['payment-id']) and "getepay" == $_GET['getepay_cf7-listener']) {
      $payment_id = $_GET['payment-id'];
      
      global $wpdb;

      $table_name = $wpdb->prefix . "getepay_cf7_payment";

      $data = $wpdb->get_results($wpdb->prepare("SELECT name, email, transaction_id, bill_url, status FROM {$table_name} WHERE id= %d", array($payment_id)));

      $data_array = get_object_vars($data[0]);

      $name   = $data_array['name'];
      $email  = $data_array['email'];
       
      // Decrypt the response data
      $keyy = $this->helpers->get_api_secret_key();
			$ivv = $this->helpers->get_api_live_iv();
			$key = base64_decode($keyy);
			$iv = base64_decode($ivv);
			$ciphertext_raw = hex2bin($_POST['response']);
			$original_plaintext = openssl_decrypt($ciphertext_raw, "AES-256-CBC", $key, $options = OPENSSL_RAW_DATA, $iv);

			$json = json_decode(json_decode($original_plaintext, true), true);

      // $getepay_cf7_data = $wpdb->get_results($wpdb->prepare("SELECT cf7_data FROM {$table_name} WHERE id= %d", array($payment_id)));

      // // Retrieve JSON data from the array
      // $jsonData = $getepay_cf7_data[0]->cf7_data;

      // // Decode the JSON data
      // $form_data = json_decode($jsonData, true);

      // if ( $form_data ) {

      //   // Dynamically create variables based on the keys in the JSON data
      //   foreach ($form_data as $key => $value) {
      //     // Use variable variables to create variables dynamically
      //     ${$key} = $value;
      //   }

      //   // Construct the email message dynamically with labels and variable values
      //   $message = '';
      //   foreach ($form_data as $key => $value) {
      //     // Convert the key to a human-readable label (replace underscores with spaces and capitalize)
      //     $label = ucwords(str_replace('_', ' ', $key));

      //     // Append the label and variable value to the message
      //     $message .= "$label: $value\n";
      //   }

      //   // Example: Sending an email using the dynamically created variables
      //   $subject = 'New Contact Form Submission';

      //   // Assuming you have a function to send emails in your WordPress setup
      //   wp_mail($email, $subject, $message);
        
      // }

      // echo '<pre>';
      // print_r($getepaycf7_data); die;
			$txnAmount = $json["txnAmount"];
      $status = strtolower($json["txnStatus"]);
      $getepayTxnId = $json['getepayTxnId'];
      $getepaytxnDate = $json['txnDate'];
      // $bill_url       = $this->helpers->get_url() . "/bills/" . $getepayTxnId;
			$bill_url = add_query_arg(array('getepay_cf7-listener' => 'getepay-recipt', 'payment-id' => $payment_id), get_bloginfo('wpurl') . "/getepay-receipt-page/");

      if ("success" == $status) {

        $wpdb->update(
          $table_name,
          array(
            'status' => 'completed',
            'transaction_id' => $getepayTxnId,
            'paid_at' => $getepaytxnDate,
            'bill_url' => $bill_url
          ),
          array('ID' => $payment_id)
        );

        $transactions = array(
          'customer_email' => $email,
          'customer_name' => $name,
          'txn_id' => $getepayTxnId,
          'txn_status' => $status,
          'txn_date' => $getepaytxnDate,
          'txn_amount' => $txnAmount
      ); 
    
     do_action('bcf7_payment_success', $transactions);
?>
        <div class="invoice-box">
            <table cellpadding="0" cellspacing="0">
                <tr class="top">
                    <td colspan="2">
                        <table>
                            <tr>
                            <center><h2>Thank you for your payment!</h2></center>
                            </tr>
                            <tr>
                                <!-- <td class="title"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'includes/images/gelogo.png'; ?>" style="width:100%; max-width:300px;"></td> -->
                                <td class="title"><img src="https://pay1.getepay.in:8443/getePaymentPages/resources/img/cardtype/getepay.png" style="width:100%; max-width:95px;"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                <center><h4>Payment Details</h4></center>
                </tr>
                <!-- <tr class="heading">
                    <td><strong> Payment Details </strong></td>
                    <td><strong> Value </strong></td>
                </tr> -->
                <tr class="item">
                    <td> Name</td>
                    <td> <?php echo esc_html($name); ?> </td>
                </tr>
                <tr class="item">
                    <td> Email</td>
                    <td> <?php echo esc_html($email); ?> </td>
                </tr>
                <tr class="item">
                    <td> Status</td>
                    <td> <?php
                    //echo $getepaytxnStatus; exit;
                        if($json["txnStatus"] == "SUCCESS") {
                            echo esc_attr("Success ✅");
                        } elseif($json["txnStatus"] == "FAILED") {
                            echo esc_attr("Fail 🚫");
                        } elseif($json["txnStatus"] == "PENDING") {
                            echo esc_attr("Pending 🚫");
                        }
                    ?> </td>
                </tr>

                <tr class="item">
                    <td> Getepay Txn Id</td>
                    <td> # <?php echo esc_attr( $getepayTxnId ); ?> </td>
                </tr>

                <tr class="item">
                    <td> Transaction Date</td>
                    <td> <?php echo date("F j, Y"); ?> </td>
                </tr>
                <tr class="item last">
                    <td> Amount</td>
                    <td> <?php echo esc_attr( $txnAmount ); ?> </td>
                </tr>
            </table>
        </div>

      <?php
      } elseif ("pending" == $status) {
        // $bill_url = $this->helpers->get_url() . '/bills/' . ($_GET['getepay']['id']);
        $wpdb->update(
          $table_name,
          array(
            'status' => 'pending',
            'transaction_id' => $getepayTxnId,
            'paid_at' => $getepaytxnDate,
            'bill_url' => $bill_url
          ),
          array('ID' => $payment_id)
        );

        $transactions = array(
          'customer_email' => $email,
          'customer_name' => $name,
          'txn_id' => $getepayTxnId,
          'txn_status' => $status,
          'txn_date' => $getepaytxnDate,
          'txn_amount' => $txnAmount
      ); 
    
     do_action('bcf7_payment_success', $transactions);
      ?>
        <div class="invoice-box">
            <table cellpadding="0" cellspacing="0">
                <tr class="top">
                    <td colspan="2">
                        <table>
                            <tr>
                            <center><h2>Sorry, Your payment has been pending. Here are your transaction details</h2></center>
                            </tr>
                            <tr>
                                <!-- <td class="title"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'includes/images/gelogo.png'; ?>" style="width:100%; max-width:300px;"></td> -->
                                <td class="title"><img src="https://pay1.getepay.in:8443/getePaymentPages/resources/img/cardtype/getepay.png" style="width:100%; max-width:95px;"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                <center><h4>Payment Details</h4></center>
                </tr>
                <!-- <tr class="heading">
                    <td> Payment Details</td>
                    <td> Value</td>
                </tr> -->
                <tr class="item">
                    <td> Name</td>
                    <td> <?php echo esc_html($name); ?> </td>
                </tr>
                <tr class="item">
                    <td> Email</td>
                    <td> <?php echo esc_html($email); ?> </td>
                </tr>
                <tr class="item">
                    <td> Status</td>
                    <td> <?php
                    //echo $getepaytxnStatus; exit;
                        if($json["txnStatus"] == "SUCCESS") {
                            echo esc_attr("Success ✅");
                        } elseif($json["txnStatus"] == "FAILED") {
                            echo esc_attr("Fail 🚫");
                        } elseif($json["txnStatus"] == "PENDING") {
                            echo esc_attr("Pending 🚫");
                        }
                    ?> </td>
                </tr>

                <tr class="item">
                    <td> Getepay Txn Id</td>
                    <td> # <?php echo esc_attr( $getepayTxnId ); ?> </td>
                </tr>

                <tr class="item">
                    <td> Transaction Date</td>
                    <td> <?php echo date("F j, Y"); ?> </td>
                </tr>
                <tr class="item last">
                    <td> Amount</td>
                    <td> <?php echo esc_attr( $txnAmount ); ?> </td>
                </tr>
            </table>
        </div>
      <?php

      } else {
        $wpdb->update(
          $table_name,
          array(
            'status' => 'failed',
            'transaction_id' => $getepayTxnId,
            'paid_at' => $getepaytxnDate,
            'bill_url' => $bill_url
          ),
          array('ID' => $payment_id)
        );

        $transactions = array(
          'customer_email' => $email,
          'customer_name' => $name,
          'txn_id' => $getepayTxnId,
          'txn_status' => $status,
          'txn_date' => $getepaytxnDate,
          'txn_amount' => $txnAmount
      ); 
    
     do_action('bcf7_payment_success', $transactions);
      ?>
        <div class="invoice-box">
            <table cellpadding="0" cellspacing="0">
                <tr class="top">
                    <td colspan="2">
                        <table>
                            <tr>
                            <center><h2>Sorry, Your payment has been failed. Here are your transaction details</h2></center>
                            </tr>
                            <tr>
                                <!-- <td class="title"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'includes/images/gelogo.png'; ?>" style="width:100%; max-width:300px;"></td> -->
                                <td class="title"><img src="https://pay1.getepay.in:8443/getePaymentPages/resources/img/cardtype/getepay.png" style="width:100%; max-width:95px;"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                <center><h4>Payment Details</h4></center>
                </tr>
                <!-- <tr class="heading">
                    <td> Payment Details</td>
                    <td> Value</td>
                </tr> -->
                <tr class="item">
                    <td> Name</td>
                    <td> <?php echo esc_html($name); ?> </td>
                </tr>
                <tr class="item">
                    <td> Email</td>
                    <td> <?php echo esc_html($email); ?> </td>
                </tr>
                <tr class="item">
                    <td> Status</td>
                    <td> <?php
                    //echo $getepaytxnStatus; exit;
                        if($json["txnStatus"] == "SUCCESS") {
                            echo esc_attr("Success ✅");
                        } elseif($json["txnStatus"] == "FAILED") {
                            echo esc_attr("Fail 🚫");
                        } elseif($json["txnStatus"] == "PENDING") {
                            echo esc_attr("Pending 🚫");
                        }
                    ?> </td>
                </tr>

                <tr class="item">
                    <td> Getepay Txn Id</td>
                    <td> # <?php echo esc_attr( $getepayTxnId ); ?> </td>
                </tr>

                <tr class="item">
                    <td> Transaction Date</td>
                    <td> <?php echo date("F j, Y"); ?> </td>
                </tr>
                <tr class="item last">
                    <td> Amount</td>
                    <td> <?php echo esc_attr( $txnAmount ); ?> </td>
                </tr>
            </table>
        </div>
<?php
      }
    }
  }
}