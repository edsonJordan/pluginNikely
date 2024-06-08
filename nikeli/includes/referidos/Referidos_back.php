<?php
namespace Referidos;

class Referidos {
    private $plugin_file;

    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;
        register_activation_hook($this->plugin_file, [$this, 'create_subscriptions_table']);
        add_action('woocommerce_thankyou', [$this, 'mostrar_codigo_referido']);
        add_action('woocommerce_order_status_completed', [$this, 'handle_order_completion']);
        add_action('check_user_roles_daily', [$this, 'check_user_roles']);

    }

    public function create_subscriptions_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscriptions';
        
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            coupon_code varchar(255) NOT NULL,
            start_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            role_level varchar(50) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function mostrar_codigo_referido($order_id) {
        $order = wc_get_order($order_id);
        $user_id = $order->get_user_id();
        $referido_code = get_user_meta($user_id, 'referido_code', true);
        
        if (!$referido_code) {
            $referido_code = wp_generate_uuid4();
            update_user_meta($user_id, 'referido_code', $referido_code);
        }

        echo '<p>Tu código de referido: <strong>' . esc_html($referido_code) . '</strong></p>';
        error_log("Código de referido mostrado para el order ID: $order_id");
    }

  

    public function handle_order_completion($order_id) {
        $order = wc_get_order($order_id);
        $user_id = $order->get_user_id();
        $items = $order->get_items();
    
        foreach ($items as $item) {
            $product = $item->get_product();
            $product_name = $product->get_name();
            $role_level = '';
    
            switch ($product_name) {
                case 'Ejecutivo':
                    $role_level = 'ejecutivo';
                    break;
                case 'Plus':
                    $role_level = 'plus';
                    break;
                case 'Top':
                    $role_level = 'top';
                    break;
            }
    
            if ($role_level) {
                $this->change_user_role($user_id, $role_level);
                $this->create_or_update_user_coupon($user_id, $role_level);
            }
        }
    }


    private function change_user_role($user_id, $role_level) {
        $user = new \WP_User($user_id);
        $user->set_role($role_level);
    }

    private function generate_coupon_code($user_info, $role_level, $user_id) {
        $user_name = sanitize_user($user_info->user_login);
        return $user_name . '_' . $role_level . '_' . $user_id;
    }

    private function create_or_update_user_coupon($user_id, $role_level) {
        global $wpdb;
        $user_info = get_userdata($user_id);
        
        // Verificar si ya existe algún registro de suscripción para este usuario.
        $existing_record = $this->get_any_subscription_record($user_id);
    
        if ($existing_record) {
            // Eliminar los cupones existentes con el código antiguo.
            $this->delete_existing_coupons($existing_record->coupon_code);
            // Eliminar el registro de suscripción existente.
            $this->delete_subscription_record($user_id);
        }
    
        // Generar un nuevo código de cupón.
        $coupon_code = $this->generate_coupon_code($user_info, $role_level, $user_id);
    
        // Crear nuevo cupón.
        $amount = '10';
        $discount_type = 'fixed_cart';
        $coupon = array(
            'post_title' => $coupon_code,
            'post_content' => '',
            'post_excerpt' => '',
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type' => 'shop_coupon'
        );
    
        $new_coupon_id = wp_insert_post($coupon);
    
        // Configuración del cupón.
        update_post_meta($new_coupon_id, 'discount_type', $discount_type);
        update_post_meta($new_coupon_id, 'coupon_amount', $amount);
        update_post_meta($new_coupon_id, 'individual_use', 'no');
        update_post_meta($new_coupon_id, 'usage_limit', '3');
        update_post_meta($new_coupon_id, 'apply_before_tax', 'yes');
        update_post_meta($new_coupon_id, 'free_shipping', 'no');
        update_post_meta($new_coupon_id, 'generated_for_user_id', $user_id);
    
        // Crear un nuevo registro en la tabla de suscripciones.
        $this->create_subscription_record($user_id, $coupon_code, $role_level);
    }
    
    private function get_any_subscription_record($user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscriptions';
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d",
            $user_id
        ));
    }
    
    private function delete_subscription_record($user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscriptions';
        $wpdb->delete($table_name, array('user_id' => $user_id));
    }
    



    

    private function get_subscription_record($user_id, $role_level) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscriptions';
        $record = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE user_id = %d AND role_level = %s",
                $user_id,
                $role_level
            )
        );
        return $record;
    }

   private function delete_existing_coupons($coupon_code) {
    $coupons = get_posts([
        'post_type' => 'shop_coupon',
        'name'      => $coupon_code, // Usar 'name' en lugar de 'meta_query'
        'posts_per_page' => -1
    ]);

    foreach ($coupons as $coupon) {
        wp_delete_post($coupon->ID, true);
    }
}

    private function create_subscription_record($user_id, $coupon_code, $role_level) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscriptions';

        // Actualizar registro si ya existe
        $existing_record = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE user_id = %d AND role_level = %s",
                $user_id,
                $role_level
            )
        );

        if ($existing_record) {
            $wpdb->update(
                $table_name,
                array(
                    'coupon_code' => $coupon_code,
                    'start_date' => current_time('mysql'),
                ),
                array('id' => $existing_record->id)
            );
        } else {
            $wpdb->insert(
                $table_name,
                array(
                    'user_id' => $user_id,
                    'coupon_code' => $coupon_code,
                    'start_date' => current_time('mysql'),
                    'role_level' => $role_level,
                )
            );
        }
    }

    
}
