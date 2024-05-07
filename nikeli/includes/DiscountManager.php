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
        $discounts_settings = get_option('discounts_settings');
        $discount_rate = 0;
        $total_items = 0;
        $cart_total = $cart->cart_contents_total;

        foreach ($cart->get_cart() as $item) {
            $total_items += $item['quantity'];
        }
        
        // Comprobamos las condiciones para cada tipo de rol
        if (in_array('ejecutivo', $current_user->roles)) {
            if ($total_items >= 6 || $cart_total >= 250) {
                $discount_rate = ($total_items >= 25 && $cart_total >= 1000) ? 0.50 : 0.40;
            }
        } elseif (in_array('plus', $current_user->roles)) {
            if ($total_items >= 12 || $cart_total >= 500) {
                $discount_rate = ($total_items >= 25 && $cart_total >= 1000) ? 0.50 : 0.45;
            }
        } elseif (in_array('top', $current_user->roles)) {
            if ($total_items >= 25 || $cart_total >= 1000) {
                $discount_rate = $discounts_settings['discount_rate_top'] / 100;
            }
        }

        // Aplicamos el descuento
        if ($discount_rate > 0) {
            foreach ($cart->get_cart() as $item) {
                $original_price = $item['data']->get_price();
                $discounted_price = $original_price * (1 - $discount_rate);
                $item['data']->set_price($discounted_price);
                $item['discount_amount'] = $original_price - $discounted_price;
            }
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
