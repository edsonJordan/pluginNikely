<?php

namespace Nikeli;

class PointsManager {
    public function __construct() {
        add_action('woocommerce_order_status_completed', [$this, 'add_points_from_order']);
        add_action('woocommerce_order_status_refunded', [$this, 'remove_points_from_order']);
    }

    
    public function remove_points_from_order($order_id) {
        $order = wc_get_order($order_id);
        $points = $this->calculate_points_for_order($order);

        if ($points > 0) {
            $this->update_user_points($order->get_customer_id(), $points, 'subtract');
        }
    }

    private function calculate_points_for_order($order) {
        $points = 0;
        foreach ($order->get_items() as $item) {
            $product_points = get_post_meta($item->get_product_id(), '_nikeli_points', true);
            $points += intval($product_points) * $item->get_quantity();
        }
        return $points;
    }

    private function update_user_points($user_id, $points, $action = 'add') {
        $current_points = (int) get_user_meta($user_id, '_nikeli_points_total', true);

        if ($action === 'add') {
            $new_total = $current_points + $points;
        } elseif ($action === 'subtract') {
            $new_total = $current_points - $points;
        }

        update_user_meta($user_id, '_nikeli_points_total', $new_total);
    }


    public function add_points_from_order($order_id) {
        $order = wc_get_order($order_id);
        $points = $this->calculate_points_for_order($order);

        // Agregar lógica adicional basada en las condiciones del negocio
        if ($this->meets_special_conditions($order)) {
            $points += $this->calculate_additional_points($order);
        }

        $this->update_user_points($order->get_customer_id(), $points, 'add');
    }


    

    private function meets_special_conditions($order) {
        // Lógica para determinar condiciones especiales (ej. compra de paquetes, rangos, etc.)
        return true; // Simplificado para el ejemplo
    }

    private function calculate_additional_points($order) {
        // Lógica para calcular puntos adicionales basados en promociones o escalas
        return 50; // Valor de ejemplo
    }
}
