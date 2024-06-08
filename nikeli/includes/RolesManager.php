<?php
namespace Nikeli;

class RolesManager {
    private $plugin_file;

    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;
        
        register_activation_hook($this->plugin_file, [$this, 'activate']);
        register_deactivation_hook($this->plugin_file, [$this, 'deactivate']);
        
        // add_action('woocommerce_order_status_completed', [$this, 'update_user_role_on_purchase']);
        // add_filter('woocommerce_coupon_is_valid', [$this, 'validate_user_coupon'], 10, 2);

        add_action('check_user_roles_daily', [$this, 'check_user_roles']);
    }

    public function activate() {
        $this->add_roles();
        $this->create_products_if_not_exists();
        if (!wp_next_scheduled('check_user_roles_daily')) {
            wp_schedule_event(time(), 'daily', 'check_user_roles_daily');
        }
    }

    public function deactivate() {
        $this->remove_roles();
        wp_clear_scheduled_hook('check_user_roles_daily');
    }

    private function add_roles() {
        add_role('ejecutivo', 'Ejecutivo', [
            'read' => true,
            'manage_options' => true,
            'view_discounts' => true,
            'purchase_clothes' => true
        ]);
        add_role('plus', 'Plus', [
            'read' => true,
            'manage_options' => true,
            'view_discounts' => true,
            'purchase_clothes' => true
        ]);
        add_role('top', 'Top', [
            'read' => true,
            'manage_options' => true,
            'view_discounts' => true,
            'purchase_clothes' => true
        ]);
    }

    private function remove_roles() {
        remove_role('ejecutivo');
        remove_role('plus');
        remove_role('top');
    }
    private function create_products_if_not_exists() {
        $products = [
            'Ejecutivo' => ['description' => 'Product for Ejecutivo role', 'price' => 100],
            'Plus' => ['description' => 'Product for Plus role', 'price' => 200],
            'Top' => ['description' => 'Product for Top role', 'price' => 300]
        ];

        foreach ($products as $name => $details) {
            if (!$this->product_exists($name)) {
                $this->create_product($name, $details['description'], $details['price']);
            }
        }
    }
    private function product_exists($name) {
        $args = [
            'post_type' => 'product',
            'post_status' => 'publish',
            'title' => $name,
            'posts_per_page' => 1,
            'fields' => 'ids'
        ];

        $products = get_posts($args);
        return !empty($products);
    }

    

    private function create_product($name, $description, $price) {
        $product = new \WC_Product_Simple();
        $product->set_name($name);
        $product->set_description($description);
        $product->set_regular_price($price);
        $product->save();
    }

    public function update_user_role_on_purchase($order_id) {
        $order = wc_get_order($order_id);
        $items = $order->get_items();

        foreach ($items as $item) {
            $product = $item->get_product();
            $product_name = $product->get_name();

            switch ($product_name) {
                case 'Ejecutivo':
                    $this->change_user_role($order->get_user_id(), 'ejecutivo');
                    break;
                case 'Plus':
                    $this->change_user_role($order->get_user_id(), 'plus');
                    break;
                case 'Top':
                    $this->change_user_role($order->get_user_id(), 'top');
                    break;
            }
        }
    }

    private function change_user_role($user_id, $role) {
        $user = new \WP_User($user_id);
        $user->set_role($role);
        $expiration_date = date('Y-m-d H:i:s', strtotime('+30 days'));
        update_user_meta($user_id, 'role_expiration_date', $expiration_date);
    }

  
/* 
    private function generate_coupon_code($user_info) {
        $user_name = sanitize_user($user_info->user_login);
        $user_id = $user_info->ID;
        return $user_name . '_' . $user_id;
    }

    public function validate_user_coupon($valid, $coupon) {
        $user_id = get_current_user_id();
        $user_info = get_userdata($user_id);
        $coupon_code = $this->generate_coupon_code($user_info);

        if ($coupon->get_code() == $coupon_code) {
            return false; // No permitir que el usuario use su propio cupón
        }

        return $valid;
    } */

    public function check_user_roles() {
        $users = get_users(['role__in' => ['ejecutivo', 'plus', 'top']]);
        $current_date = date('Y-m-d H:i:s');

        foreach ($users as $user) {
            $expiration_date = get_user_meta($user->ID, 'role_expiration_date', true);
            if ($expiration_date && $current_date > $expiration_date) {
                $user = new \WP_User($user->ID);
                $user->set_role('customer');
                delete_user_meta($user->ID, 'role_expiration_date');
            }
        }
    }
}


