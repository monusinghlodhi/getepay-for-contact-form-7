<?php
/**
 * Activate class file.
 *
 * @package GetepayCF7
 */

namespace GetepayCF7\Base;

use WP_Query;

/**
 * Activate Class.
 */
class Activate {
    /**
	 * Method to activate the plugin.
	 *
	 * @return void
	 */
	public function activate() {
		\GetepayCF7\Model\PaymentDatabase::create_db();
		ob_start();
		$this->create_confirmation_page();
		$this->create_example_form();
		$this->create_getepay_rceipt_page_plugin_activate();

		ob_end_clean();
	}

	function create_getepay_rceipt_page_plugin_activate() {
		// Create a new page on activation
		$page_id = wp_insert_post(array(
			'post_title'     => 'Getepay Receipt Page',
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		));
	
		// Set the template for the newly created page
		update_post_meta($page_id, '_wp_page_template', 'getepay-receipt-page.php');
	
		// Move getepay-receipt-page.php to the theme directory
		$template_content = '<?php /* Template Name: Getepay Receipt Page */ ?>
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
	  </style>
		<?php
		if (isset($_GET["payment-id"]) && isset($_GET["getepay_cf7-listener"]) == "getepay-recipt") {
			$payment_id = $_GET["payment-id"];			
	  
			global $wpdb;
	  
			$table_name = $wpdb->prefix . "getepay_cf7_payment";
	  
			$data = $wpdb->get_results($wpdb->prepare("SELECT name, email, amount, transaction_id, bill_url, status FROM {$table_name} WHERE id= %d", array($payment_id)));
	  
			$data_array = get_object_vars($data[0]);
	  
			$name   = $data_array["name"];
			$email  = $data_array["email"];
			$amount  = $data_array["amount"];
			$trx_id = $data_array["transaction_id"];
			$bill   = $data_array["bill_url"];
			$status = $data_array["status"];
	  
			if ("completed" == $status) {
	  
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
									  <td class="title"><img src="https://pay1.getepay.in:8443/getePaymentPages/resources/img/cardtype/getepay.png" style="width:100%; max-width:95px;"></td>
								  </tr>
							  </table>
						  </td>
					  </tr>
					  <tr>
					  <center><h4>Payment Receipt</h4></center>
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
							  if($status == "completed") {
								  echo esc_attr("Success ✅");
							  } elseif($status == "failed") {
								  echo esc_attr("Fail 🚫");
							  } elseif($status == "pending") {
								  echo esc_attr("Pending 🚫");
							  }
						  ?> </td>
					  </tr>
	  
					  <tr class="item">
						  <td> Getepay Txn Id</td>
						  <td> # <?php echo esc_attr( $trx_id ); ?> </td>
					  </tr>
	  
					  <tr class="item">
						  <td> Transaction Date</td>
						  <td> <?php echo date("F j, Y"); ?> </td>
					  </tr>
					  <tr class="item last">
						  <td> Amount</td>
						  <td> <?php echo esc_attr( $amount ); ?> </td>
					  </tr>
				  </table>
				  <center><h4>Receipt No: #<?php echo $payment_id; ?></h4></center>
			  </div>
	  
			<?php
			} elseif ("pending" == $status) {
	  
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
									  <!-- <td class="title"><img src="<?php echo plugin_dir_url( __FILE__ ) . "includes/images/gelogo.png"; ?>" style="width:100%; max-width:300px;"></td> -->
									  <td class="title"><img src="https://pay1.getepay.in:8443/getePaymentPages/resources/img/cardtype/getepay.png" style="width:100%; max-width:95px;"></td>
								  </tr>
							  </table>
						  </td>
					  </tr>
					  <tr>
					  <center><h4>Payment Receipt</h4></center>
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
							  if($status == "completed") {
								  echo esc_attr("Success ✅");
							  } elseif($status == "failed") {
								  echo esc_attr("Fail 🚫");
							  } elseif($status == "pending") {
								  echo esc_attr("Pending 🚫");
							  }
						  ?> </td>
					  </tr>
	  
					  <tr class="item">
						  <td> Getepay Txn Id</td>
						  <td> # <?php echo esc_attr( $trx_id ); ?> </td>
					  </tr>
	  
					  <tr class="item">
						  <td> Transaction Date</td>
						  <td> <?php echo date("F j, Y"); ?> </td>
					  </tr>
					  <tr class="item last">
						  <td> Amount</td>
						  <td> <?php echo esc_attr( $amount ); ?> </td>
					  </tr>
				  </table>
				  <center><h4>Receipt No: #<?php echo $payment_id; ?></h4></center>
			  </div>
			<?php
	  
			} elseif ("failed" == $status) {
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
									  <td class="title"><img src="https://pay1.getepay.in:8443/getePaymentPages/resources/img/cardtype/getepay.png" style="width:100%; max-width:95px;"></td>
								  </tr>
							  </table>
						  </td>
					  </tr>
					  <tr>
					  <center><h4>Payment Receipt</h4></center>
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
							  if($status == "completed") {
								  echo esc_attr("Success ✅");
							  } elseif($status == "failed") {
								  echo esc_attr("Fail 🚫");
							  } elseif($status == "pending") {
								  echo esc_attr("Pending 🚫");
							  }
						  ?> </td>
					  </tr>
	  
					  <tr class="item">
						  <td> Getepay Txn Id</td>
						  <td> # <?php echo esc_attr( $trx_id ); ?> </td>
					  </tr>
	  
					  <tr class="item">
						  <td> Transaction Date</td>
						  <td> <?php echo date("F j, Y"); ?> </td>
					  </tr>
					  <tr class="item last">
						  <td> Amount</td>
						  <td> <?php echo esc_attr( $amount ); ?> </td>
					  </tr>
				  </table>
				  <center><h4>Receipt No: #<?php echo $payment_id; ?></h4></center>
			  </div>
	  <?php
			}
		  }
		
		?>';
		// Check if a child theme is active
		if (is_child_theme()) {
			$template_path = get_stylesheet_directory() . '/getepay-receipt-page.php';
		} else {
			// Use the parent theme directory
			$template_path = get_template_directory() . '/getepay-receipt-page.php';
		}

		// Check if the file doesn't exist before moving
		if (!file_exists($template_path)) {
			file_put_contents($template_path, $template_content);
		}
	}
	

	/**
	 * Method to create a page.
	 *
	 * @param string $page_title The title of the page.
	 *
	 * @return int|null The ID of the created page or null if page already exists.
	 */
	private function create_page($page_title) {
		$args = array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			's'              => $page_title,
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			wp_reset_postdata();
			return null;
		} else {
			$redirect_page = $this->insert_page($page_title);
			$this->save_id($redirect_page);
			return $redirect_page;
		}
	}

    /**
	 * Method to create the confirmation page.
	 *
	 * @return int|null The ID of the created page or null if page already exists.
	 */
	private function create_confirmation_page() {
		$page_title = esc_html__( 'GETEPAY CF7 Payment Confirmation', GETEPAY_CF7_TEXT_DOMAIN );
		return $this->create_page($page_title);
	}

    /**
	 * Method to create the BCF7 Example Form.
	 *
	 * @return int|null The ID of the created form or null if form already exists.
	 */
	private function create_example_form() {
		$form_title = 'GETEPAY CF7 Example Payment Form';
		$form_content = 'Payment Form Example';

		$args = array(
			'post_type'      => 'wpcf7_contact_form',
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			's'              => $form_title,
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			wp_reset_postdata();
			return null;
		} else {
			$form_id = $this->insert_form($form_title, $form_content);
			$this->add_form_meta($form_id);
			return $form_id;
		}
	}

    /**
	 * Method to insert a page and return its ID.
	 *
	 * @param string $page_title The title of the page.
	 *
	 * @return int The ID of the inserted page.
	 */
	private function insert_page($page_title) {
		$post_id = wp_insert_post(
			array(
				'post_title'     => $page_title,
				'post_name'      => sanitize_title($page_title),
				'post_content'   => '<!-- wp:shortcode -->[getepay_cf7_payment_confirmation]<!-- /wp:shortcode -->',
				'post_status'    => 'publish',
				'post_author'    => get_current_user_id(),
				'post_type'      => 'page',
				'comment_status' => 'closed',
			));

        return $post_id;
    }

    /**
     * Method to save the page ID in the options table.
     *
     * @param int $redirect_page_id The ID of the page.
     *
     * @return void
     */
    private function save_id($redirect_page_id) {
        $options = get_option('getepay_cf7_general_settings');

        if ($options) {
            if (isset($options['getepay_cf7_redirect_page']) && $options['getepay_cf7_redirect_page'] === $redirect_page_id) {
                return;
            } else {
                $options['getepay_cf7_redirect_page'] = $redirect_page_id;
                update_option('getepay_cf7_general_settings', $options);
            }
        } else {
            $options = array(
                'getepay_cf7_mode'            => '1',
                'getepay_cf7_form_select'     => '',
                'getepay_cf7_redirect_page'   => $redirect_page_id,
            );
            add_option('getepay_cf7_general_settings', $options);
        }
    }

    /**
     * Method to insert a form and return its ID.
     *
     * @param string $form_title    The title of the form.
     * @param string $form_content  The content of the form.
     *
     * @return int The ID of the inserted form.
     */
    private function insert_form($form_title, $form_content) {
        $post_data = array(
            'post_title'     => $form_title,
            'post_content'   => $form_content,
            'post_status'    => 'publish',
            'post_author'    => get_current_user_id(),
            'post_type'      => 'wpcf7_contact_form',
            'comment_status' => 'closed',
        );

        $post_id = wp_insert_post($post_data);

        return $post_id;
    }

    /**
     * Method to add meta data for the form.
     *
     * @param int $form_id The ID of the form.
     *
     * @return void
     */
    private function add_form_meta($form_id) {
        $form = '
        <label> Name
            [text* getepay_cf7-name] </label>

        <label> Your email
            [email* getepay_cf7-email] </label>

        <label> Phone
            [tel* getepay_cf7-phone] </label>

        <label> Amount (INR)
            [number* getepay_cf7-amount] </label>


        [submit "Submit"]';

        add_post_meta($form_id, '_additional_settings', 'skip_mail: on');
        add_post_meta($form_id, '_form', $form);
    }
}