<?php

namespace Referidos;

class UserActivityChecker {
    // Define una variable estática para controlar la verificación en cada inicio de sesión
    public static $forceCheck = true;  // Cambia esto a false cuando no quieras forzar la verificación en cada inicio de sesión

    public static function check_user_activity_on_login($user_login, $user) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'order_points';

        // error_log("Loggin Ejecutado para: " . $user_login);
        if (in_array('administrator', (array) $user->roles)) {
            // error_log("Usuario es administrador, no se verifica actividad.");
            return;
        }

        $today = date('Y-m-d');
        $last_checked = get_user_meta($user->ID, 'last_purchase_check', true);
        
        if ($last_checked !== $today || self::$forceCheck) {
            self::check_activity_and_update_role($user, $wpdb, $table_name);
            if (!self::$forceCheck) {
                update_user_meta($user->ID, 'last_purchase_check', $today);
            }
            // error_log("Actividad verificada y rol actualizado si es necesario para: " . $user_login);
        } else {
            // error_log("Actividad ya verificada hoy para: " . $user_login);
        }
    }

    private static function check_activity_and_update_role($user, $wpdb, $table_name) {
        if (self::user_has_required_role($user->roles)) {
            if (!self::has_met_activity_requirement($user->ID, $wpdb, $table_name)) {
                // self::change_user_role_to_customer($user->ID);
                self::disable_user_status($user->ID);
            } else {
                self::activated_user_status($user->ID);
            }
        }
    }

    private static function user_has_required_role($roles) {
        $roles_to_check = ['top', 'ejecutivo', 'plus'];
        return !empty(array_intersect($roles, $roles_to_check));
    }

    private static function has_met_activity_requirement($user_id, $wpdb, $table_name) {
        $date_30_days_ago = date('Y-m-d', strtotime('-30 days'));
        $points_total = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(points_total) FROM $table_name WHERE user_id = %d AND created_at >= %s AND is_special = 0",
            $user_id, $date_30_days_ago
        ));

        // error_log("Puntos totales para el usuario $user_id desde $date_30_days_ago: $points_total");
        return ($points_total >= 150);
    }

    private static function change_user_role_to_customer($user_id) {
        $user = new \WP_User($user_id);
        $user->set_role('customer');
        // error_log("Rol cambiado a 'customer' para el usuario con ID: $user_id");
    }

    private static function disable_user_status($user_id) {
        update_user_meta($user_id, 'estado_linea_nikeli', '0');
        // error_log("Estado de línea Nikeli desactivado para el usuario con ID: $user_id");
    }
    private static function activated_user_status($user_id) {
        update_user_meta($user_id, 'estado_linea_nikeli', '1');
        // error_log("Estado de línea Nikeli activado para el usuario con ID: $user_id");
    }

    
}
