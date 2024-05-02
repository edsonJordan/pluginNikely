<?php
namespace Nikeli;

class DiscountManager {
    public function __construct() {
        add_action('woocommerce_before_calculate_totals', [$this, 'adjust_pricing'], 10, 1);
        add_filter('woocommerce_cart_item_price', [$this, 'display_individual_discount'], 10, 3);
        add_action('woocommerce_cart_calculate_fees', [$this, 'apply_cart_discount'], 20, 1);
    }

    public function adjust_pricing($cart) {
        if (is_admin() && !defined('DOING_AJAX')) return;

        $current_user = wp_get_current_user();
        $discount_rate = 0;
        
        if (in_array('ejecutivo', $current_user->roles)) {
            $discount_rate = 0.40; // 40% descuento
        } elseif (in_array('plus', $current_user->roles)) {
            $discount_rate = 0.45; // 45% descuento
        } elseif (in_array('top', $current_user->roles)) {
            $discount_rate = 0.50; // 50% descuento
        }

        foreach ($cart->get_cart() as $item) {
            $original_price = $item['data']->get_price();
            $discounted_price = $original_price * (1 - $discount_rate);
            $item['data']->set_price($discounted_price);
            $item['discount_amount'] = $original_price - $discounted_price;
        }
    }

    public function display_individual_discount($price_html, $cart_item, $cart_item_key) {
        if (!empty($cart_item['discount_amount'])) {
            $discount = $cart_item['discount_amount'];
            $price_html .= sprintf('<br><small class="woocommerce-price-suffix">Ahorro: %s</small>', wc_price($discount));
        }
        return $price_html;
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
