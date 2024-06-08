<?php
namespace Nikeli;

class DiscountManager {
    public function __construct() {
        add_action('woocommerce_before_calculate_totals', [$this, 'add_points_to_cart_item']);
        add_action('woocommerce_after_calculate_totals', [$this, 'adjust_pricing']);
        add_filter('woocommerce_cart_item_name', [$this, 'display_individual_discount'], 10, 3);
        add_action('woocommerce_checkout_create_order_line_item', [$this, 'add_points_info_to_order_items'], 10, 4);
    }



    public function add_points_info_to_order_items($item, $cart_item_key, $values, $order) {
        if (isset($values['nikeli_points'])) {
            $item->add_meta_data('Puntos ganados', $values['nikeli_points']);
        }
    }
      

    public function add_points_to_cart_item($cart) {
        if (is_admin() && !defined('DOING_AJAX')) return;
        foreach ($cart->get_cart() as &$item) {
            $product_id = $item['product_id'];
            $points = get_post_meta($product_id, '_nikeli_points', true);
            $item['nikeli_points'] = intval($points) * $item['quantity'];
        }
    }

    public function adjust_pricing($cart) {
        if (is_admin() && !defined('DOING_AJAX')) return;




        $current_user = wp_get_current_user();

        $user_active = get_user_meta($current_user->ID, 'estado_linea_nikeli', true);
        
        // Si el usuario no está activo, no aplicar descuentos
        if ($user_active != '1') {
            return;
        }

        $discounts_settings = get_option('discounts_settings');
        $discount_rate = 0;
        foreach ($cart->get_cart() as $item) {
            $total_items = $item['quantity'];
            $cart_total = $cart->cart_contents_total;

            if (in_array('ejecutivo', $current_user->roles) ) {
                $discount_rate = $discounts_settings['discount_rate_ejecutivo'] / 100;
            } else if (in_array('plus', $current_user->roles) ) {
                $discount_rate = $discounts_settings['discount_rate_plus'] / 100;
            } else if (in_array('top', $current_user->roles) ) {
                $discount_rate = $discounts_settings['discount_rate_top'] / 100;
            }
            
            if ($discount_rate > 0) {
                $original_price = $item['data']->get_price();
                $discounted_price = $original_price * (1 - $discount_rate);
                $item['data']->set_price($discounted_price);
                $item['discount_amount'] = $original_price - $discounted_price;
            }
        }
    }

    public function display_individual_discount($item_name, $cart_item, $cart_item_key) {
        if (isset($cart_item['discount_amount'])) {
            $discount = $cart_item['discount_amount'];
            $item_name .= sprintf('<br><small class="woocommerce-price-suffix">Ahorro: %s</small>', wc_price($discount));
        }
        if (isset($cart_item['nikeli_points'])) {
            $points = $cart_item['nikeli_points'];
            $item_name .= sprintf('<br><small class="woocommerce-price-suffix">Puntos ganados: %d</small>', $points);
        }
        return $item_name;
    }
    

    public function apply_cart_discount($cart) {
        $total_discount = 0;
        foreach ($cart->get_cart() as $item) {
            if (isset($item['discount_amount'])) {
                $total_discount += $item['discount_amount'] * $item['quantity'];
            }
        }
        if ($total_discount > 0) {
            $cart->add_fee('Descuento Total', -$total_discount, false);
        }
    }
}
