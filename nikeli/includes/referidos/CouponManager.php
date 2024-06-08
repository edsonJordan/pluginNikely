<?php 


namespace Referidos;

class CouponManager {
    public static function change_user_role($user_id, $role_level) {
        $user = new \WP_User($user_id);
        $user->set_role($role_level);
    }

    public static function create_or_update_user_coupon($user_id, $role_level) {
        global $wpdb;
        $user_info = get_userdata($user_id);
        
        // Verificar si ya existe algún registro de suscripción para este usuario.
        $existing_record = self::get_any_subscription_record($user_id);
    
        if ($existing_record) {
            // Eliminar los cupones existentes con el código antiguo.
            self::delete_existing_coupons($existing_record->coupon_code);
            // Eliminar el registro de suscripción existente.
            self::delete_subscription_record($user_id);
        }
    
        // Generar un nuevo código de cupón.
        $coupon_code = self::generate_coupon_code($user_info, $role_level, $user_id);
    
        // Crear nuevo cupón.
        $amount = '0';
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
        self::create_subscription_record($user_id, $coupon_code, $role_level);
    }



    private static function get_any_subscription_record($user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscriptions';
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d",
            $user_id
        ));
    }

    private static function delete_existing_coupons($coupon_code) {
        $coupons = get_posts([
            'post_type' => 'shop_coupon',
            'name'      => $coupon_code,
            'posts_per_page' => -1
        ]);

        foreach ($coupons as $coupon) {
            wp_delete_post($coupon->ID, true);
        }
    }

    private static function delete_subscription_record($user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscriptions';
        $wpdb->delete($table_name, array('user_id' => $user_id));
    }

    private static function generate_coupon_code($user_info, $role_level, $user_id) {
        $user_name = sanitize_user($user_info->user_login);
        return $user_name . '_' . $role_level . '_' . $user_id;
    }

    private static function create_subscription_record($user_id, $coupon_code, $role_level) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscriptions';

        $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'coupon_code' => $coupon_code,
                'start_date' => current_time('mysql'),
                'role_level' => $role_level,
                'status' => true
            )
        );
    }



    public static function create_referidos_record($leader_id, $referido_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'referidos';
    
        // Verificar si ya existe un registro con el id_referido proporcionado
        $existing_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id_referido FROM $table_name WHERE id_referido = %d",
            $referido_id
        ));
    
        if ($existing_id) {
            error_log('Intento de duplicar registro en referidos: ' . $leader_id . ' intentó referir nuevamente a ' . $referido_id);
            return; // Finaliza la función si ya existe el registro
        }
    
        // Insertar el nuevo registro si no existe previamente
        $insertion = $wpdb->insert(
            $table_name,
            [
                'id_lider' => $leader_id,
                'id_referido' => $referido_id
            ],
            [
                '%d', 
                '%d'  
            ]
        );
    
        error_log('Se creó registro en referidos: ' . $leader_id . ' refirió a ' . $referido_id);
    
        if (false === $insertion) {
            // Log error o manejo de errores aquí
            error_log('Error al insertar en la tabla referidos: ' . $wpdb->last_error);
        }
    }
    
}
