<?php

namespace GetepayCF7\Model;

class PaymentDatabase
{
    /**
     * Create the `wp_bcf7_payment` table.
     *
     * This method creates the `wp_bcf7_payment` table in the WordPress
     * database if it does not exist. The table is used to store information
     * about payments made through the CF7 Getepay plugin.
     *
     * @since 1.0.0
     */
    public static function create_db()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "getepay_cf7_payment";

        $charsetCollate = $wpdb->get_charset_collate();

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {

            $query = "CREATE TABLE $table_name(
            id bigint (20) unsigned NOT NULL AUTO_INCREMENT,
            form_id bigint (20) NOT NULL DEFAULT 0,
            form_title varchar (60) NOT NULL DEFAULT '',
            name varchar (60) NOT NULL DEFAULT '',
            phone varchar (60) NOT NULL DEFAULT '',
            amount float (10,2) unsigned NOT NULL DEFAULT 0,
            transaction_id varchar (60) NOT NULL DEFAULT '',
            bill_url varchar (256) DEFAULT NULL,
            email varchar (60) NOT NULL DEFAULT '',
            mode varchar (60) NOT NULL DEFAULT '',
            status varchar (60) NOT NULL DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            paid_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
          ) $charsetCollate";

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($query);
        }
    }
}