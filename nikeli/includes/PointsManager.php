<?php

namespace Nikeli;

class PointsManager {
    public function __construct() {
        add_action('woocommerce_order_status_completed', [$this, 'add_points_from_order']);
        add_action('woocommerce_order_status_refunded', [$this, 'remove_points_from_order']);
    }

    public function add_points_from_order($order_id) {
        $order = wc_get_order($order_id);
        $points = $this->calculate_points_for_order($order);

        if ($points > 0) {
            $this->update_user_points($order->get_customer_id(), $points, 'add');
        }
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
}
