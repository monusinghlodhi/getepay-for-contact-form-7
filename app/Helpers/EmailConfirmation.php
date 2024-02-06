<?php
namespace GetepayCF7\Helpers;

class EmailConfirmation {

  private const EMAIL_SETTINGS_OPTION = 'getepay_cf7_email_settings';
  private const EMAIL_SUBJECT_OPTION = 'geteapy_cf7_email_subject';
  private const EMAIL_BODY_OPTION = 'geteapy_cf7_email_body';

  private array $options;

  /**
   * Email confirmation constructor.
   */
  public function __construct() {
	  if ( null != get_option( self::EMAIL_SETTINGS_OPTION )) {
		$this->options = get_option( self::EMAIL_SETTINGS_OPTION );
	  }
  }
  

  /**
   * Sends email confirmation.
   *
   * @param array $transaction Transaction details.
   */
  public function send( array $transaction ): void {
    $to = $transaction['customer_email'];
    $subject = $this->options[ self::EMAIL_SUBJECT_OPTION ];
    $body = $this->options[ self::EMAIL_BODY_OPTION ];
    $body = $this->replace_variables_in_email_body( $body, $transaction );

    wp_mail( $to, $subject, $body );
  }

  /**
   * Replaces placeholders in email body with transaction details.
   *
   * @param string $body Email body.
   * @param array $transaction Transaction details.
   *
   * @return string Modified email body.
   */
  private function replace_variables_in_email_body( string $body, array $transaction ): string {
    $date = date( 'F j, Y', strtotime( $transaction['txn_date'] ) );
    $body = str_replace( '{transaction_status}', $transaction['txn_status'], $body );
    $body = str_replace( '{customer_name}', $transaction['customer_name'], $body );
    $body = str_replace( '{transaction_id}', $transaction['txn_id'], $body );
    $body = str_replace( '{transaction_date}', $date, $body );
    $body = str_replace( '{transaction_amount}', 'INR ' . $transaction['txn_amount'], $body );
    return $body;
  }
}