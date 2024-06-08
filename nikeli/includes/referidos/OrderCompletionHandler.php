<?php

namespace Referidos;

use Referidos\CouponManager;

class OrderCompletionHandler {

    public static function handle_order_completion($order_id) {
        $order = wc_get_order($order_id);
        $user_id = $order->get_user_id();
    
        if ($user_id == 0) { // Comprueba si hay un ID de usuario asociado (0 indica no registrado)
            error_log("No registered user for order ID: $order_id");
            return; // No hay usuario registrado, termina la ejecución
        }
    
        // Obtener el valor del campo billing_id_user y billing_id_package
        $leader_id = $order->get_meta('_billing_id_user');
        $package_id = $order->get_meta('_billing_id_package');
    
        if ($leader_id) {
            // Manejar el caso donde existe leader_id
            CouponManager::create_referidos_record($leader_id, $user_id);
            if ($package_id) {
                self::verify_and_apply_package_bonus($leader_id, $package_id, $order);
            }
        }
    
        // Añadir puntos a los líderes de hasta 3 niveles si hay líderes asociados y sólo una vez por pedido
        if (self::has_leaders($user_id)) {
            $total_order_points = self::calculate_total_order_points($order);
            if ($total_order_points > 0) {
                $points_to_add = $total_order_points * 0.05; // Calcula el 5% del total de puntos del pedido
                self::distribute_points_among_leaders($user_id, $points_to_add, 3, $order->get_id()); // 3 niveles de profundidad
            }
        }
    
        self::update_user_role_on_purchase($order_id);
        // Aplicar bono de bienvenida basado en la fecha de registro del usuario
        self::apply_welcome_bonus($order, $user_id);
    }

    
    private static function has_leaders($user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'referidos';

        $leader_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id_lider FROM $table_name WHERE id_referido = %d",
            $user_id
        ));

        return !is_null($leader_id);
    }

    private static function verify_and_apply_package_bonus($leader_id, $package_id, $order) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'product_packages';
    
        // Obtener los datos del paquete y el nombre
        $package_row = $wpdb->get_row($wpdb->prepare(
            "SELECT package_data, package_name FROM $table_name WHERE id = %d",
            $package_id
        ));
    
        if ($package_row) {
            $package_data = json_decode($package_row->package_data, true);
            $package_name = $package_row->package_name;
            $order_items = $order->get_items();
            $package_products_matched = self::compare_package_with_order($package_data, $order_items);
    
            if ($package_products_matched) {
                // error_log("Agregando bono");
                // self::register_bonus($leader_id, 'bonus_sale_package_products', 0, 3);
                self::change_user_role_based_on_package_name($order->get_user_id(), $package_name);
                self::register_package_bonus($leader_id, $order->get_user_id(), $package_name);
            } else {
                // error_log("No se agregó bono por compra de paquetes");
            }
        }
    }

    public static function update_user_role_on_purchase($order_id) {
        $order = wc_get_order($order_id);
        $items = $order->get_items();

        foreach ($items as $item) {
            $product = $item->get_product();
            $product_name = $product->get_name();

            switch ($product_name) {
                case 'Ejecutivo':
                    self::change_user_role($order->get_user_id(), 'ejecutivo');
                    break;
                case 'Plus':
                    self::change_user_role($order->get_user_id(), 'plus');
                    break;
                case 'Top':
                    self::change_user_role($order->get_user_id(), 'top');
                    break;
            }           
        }
        
    }


    private static function register_package_bonus($leader_id, $referred_user_id, $package_name) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'package_bonuses';
    
        // Definir el monto del bono según el paquete
        $bonuses = [
            'Ejecutivo' => 50.00,
            'Plus' => 100.00,
            'Top' => 150.00
        ];
    
        $bonus_amount = isset($bonuses[$package_name]) ? $bonuses[$package_name] : 0;
    
        if ($bonus_amount > 0) {
            $wpdb->insert($table_name, [
                'user_id' => $leader_id,
                'referred_user_id' => $referred_user_id,
                'package_type' => $package_name,
                'status' => 'pending',
                'bonus_amount' => $bonus_amount,
                'date_awarded' => current_time('mysql')
            ]);
        }
    }


    private static function change_user_role_based_on_package_name($user_id, $package_name) {
        switch ($package_name) {
            case 'Ejecutivo':
                self::change_user_role($user_id, 'ejecutivo');
                break;
            case 'Plus':
                self::change_user_role($user_id, 'plus');
                break;
            case 'Top':
                self::change_user_role($user_id, 'top');
                break;
            default:
                // No hay acción por defecto si no es uno de los paquetes esperados
                break;
        }
    }
    private static function change_user_role($user_id, $new_role) {
        $user = new \WP_User($user_id);
        $user->set_role($new_role);
    }

    private static function compare_package_with_order($package_data, $order_items) {
        $order_products = [];

        // Crear una lista de productos del pedido con sus cantidades
        foreach ($order_items as $order_item) {
            $product_id = $order_item->get_product_id();
            $quantity = $order_item->get_quantity();

            if (isset($order_products[$product_id])) {
                $order_products[$product_id] += $quantity;
            } else {
                $order_products[$product_id] = $quantity;
            }
        }

        // Verificar que cada producto del paquete esté en la lista de productos del pedido con la cantidad correcta
        foreach ($package_data as $package_item) {
            $product_id = $package_item['product_id'];
            $quantity = $package_item['quantity'];

            if (!isset($order_products[$product_id]) || $order_products[$product_id] < $quantity) {
                return false;
            }
        }

        return true;
    }

    private static function apply_welcome_bonus($order, $user_id) {
        $current_time = current_time('timestamp');
        $user_info = get_userdata($user_id);
        $user_registered = strtotime($user_info->user_registered);
        $months_since_registration = floor(($current_time - $user_registered) / (30 * DAY_IN_SECONDS));

        if ($months_since_registration == 0 && !self::is_bonus_applied($user_id, 'welcome_bonus_month_1')) {
            // Primer mes: 100 puntos, 1 prenda gratis
            if (self::get_user_points($user_id) >= 100) {
                // Añadir prenda gratis
                self::add_free_item_to_order($order, 1);
                self::register_bonus($user_id, 'welcome_bonus_month_1', 100, 1);
            }
        } elseif ($months_since_registration == 1 && !self::is_bonus_applied($user_id, 'welcome_bonus_month_2')) {
            // Segundo mes: 150 puntos, 2 prendas gratis
            if (self::get_user_points($user_id) >= 150) {
                // Añadir prendas gratis
                self::add_free_item_to_order($order, 2);
                self::register_bonus($user_id, 'welcome_bonus_month_2', 150, 2);
            }
        } elseif ($months_since_registration == 2 && !self::is_bonus_applied($user_id, 'welcome_bonus_month_3')) {
            // Tercer mes: 200 puntos, 3 prendas gratis
            if (self::get_user_points($user_id) >= 200) {
                // Añadir prendas gratis
                self::add_free_item_to_order($order, 3);
                self::register_bonus($user_id, 'welcome_bonus_month_3', 200, 3);
            }
        }
    }

    private static function register_bonus($user_id, $bonus_type, $points_required, $free_items) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'user_bonuses';

        $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'bonus_type' => $bonus_type,
                'points_required' => $points_required,
                'free_items' => $free_items,
                'date_awarded' => current_time('mysql')
            ),
            array(
                '%d',
                '%s',
                '%d',
                '%d',
                '%s'
            )
        );
    }

    private static function get_user_points($user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'order_points';

        $points_total = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(points_total) FROM $table_name WHERE user_id = %d",
            $user_id
        ));

        return $points_total ? intval($points_total) : 0;
    }

    private static function is_bonus_applied($user_id, $bonus_type) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'user_bonuses';

        $bonus_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND bonus_type = %s",
            $user_id, $bonus_type
        ));

        return $bonus_count > 0;
    }

   
    
    private static function calculate_total_order_points($order) {
        $total_order_points = 0;
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            $product_points = get_post_meta($product->get_id(), '_nikeli_points', true);
            $total_order_points += floatval($product_points) * $item->get_quantity();
        }
        return $total_order_points;
    }
    
    private static function distribute_points_among_leaders($user_id, $points, $levels, $order_id) {
        if ($levels <= 0) return; // Termina la recursión si se alcanzan 0 niveles.
    
        // Obtiene el ID del líder directo del usuario actual
        $leader_id = self::get_leader_id($user_id);
        if ($leader_id) {
            // Registra los puntos para el líder actual
            self::register_points($leader_id, $order_id, $points);
    
            // Continúa la distribución de puntos hacia arriba en la cadena de líderes
            self::distribute_points_among_leaders($leader_id, $points, $levels - 1, $order_id);
        }
    }
    
    
    private static function register_points($leader_id, $order_id, $points) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'order_points';
        $user_data = get_userdata($leader_id);

        // Verifica si el usuario existe para obtener su nombre de usuario, de lo contrario usa 'Unknown User'
        $user_name = $user_data ? $user_data->user_login : 'Unknown User'; 

        // Inserta los puntos en la base de datos
        $wpdb->insert($table_name, [
            'order_id' => $order_id,
            'user_id' => $leader_id,
            'user_name' => $user_name,
            'points_total' => $points,
            'is_special' => 1,
        ], ['%d', '%d', '%s', '%d', '%d']);
    }

    private static function get_leader_id($user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'referidos';
    
        // Consulta para obtener el líder directo del usuario dado
        return $wpdb->get_var($wpdb->prepare(
            "SELECT id_lider FROM $table_name WHERE id_referido = %d",
            $user_id
        ));
    }

    private static function add_free_item_to_order($order, $quantity) {
        $free_product_id = 123; // ID del producto gratis (ajusta esto según sea necesario)
        $product = wc_get_product($free_product_id);

        if ($product) {
            $item = new \WC_Order_Item_Product();
            $item->set_product($product);
            $item->set_quantity($quantity);
            $item->set_subtotal(0);
            $item->set_total(0);
            $order->add_item($item);
            $order->save();
        }
    }
}
