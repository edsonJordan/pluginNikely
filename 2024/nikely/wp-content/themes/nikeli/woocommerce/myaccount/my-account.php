<?php
/**
 * My Account Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

do_action( 'woocommerce_account_navigation' ); ?>

<div class="woocommerce-MyAccount-content">
    <?php
        /**
         * My Account content.
         * @since 2.6.0
         */
        do_action( 'woocommerce_account_content' );
    ?>
</div>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
