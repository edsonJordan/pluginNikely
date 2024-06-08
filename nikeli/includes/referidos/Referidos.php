<?php
namespace Referidos;
use Referidos\SubscriptionDB;
use Referidos\ReferralCodeDisplay;
use Referidos\OrderCompletionHandler;
use Referidos\UserRolesChecker;
use Referidos\UserActivityChecker;

use Factory\DataOrdersPoints;

class Referidos {
    private $plugin_file;

    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;
        $this->register_hooks();
        // error_log("Referidos_Frontend initialized");
    }

    private function register_hooks() {
        register_activation_hook($this->plugin_file, [SubscriptionDB::class, 'create_table_subscriptions']);
        register_activation_hook($this->plugin_file, [SubscriptionDB::class, 'create_referrals_table']);
        register_activation_hook($this->plugin_file, [SubscriptionDB::class, 'create_product_packages_table']);
        register_activation_hook($this->plugin_file, [SubscriptionDB::class, 'create_bonus_table']);
        register_activation_hook($this->plugin_file, [SubscriptionDB::class, 'create_package_bonus_table']);
        // register_activation_hook($this->plugin_file, [DataOrdersPoints::class, 'generateRecords']);


        add_action('wp_login', [UserActivityChecker::class, 'check_user_activity_on_login'], 10, 2);
// 
        
        // add_action('woocommerce_thankyou', [ReferralCodeDisplay::class, 'mostrar_codigo_referido']);
        add_action('woocommerce_order_status_completed', [OrderCompletionHandler::class, 'handle_order_completion']);

        add_action('check_user_roles_daily', [UserRolesChecker::class, 'check_user_roles']);

    }

    
}
