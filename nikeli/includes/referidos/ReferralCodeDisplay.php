<?php
// Referidos_Handler.php


namespace Referidos;

class ReferralCodeDisplay {
    public static function mostrar_codigo_referido($order_id) {
        $order = wc_get_order($order_id);
        $user_id = $order->get_user_id();
        $referido_code = get_user_meta($user_id, 'referido_code', true);
        
        if (!$referido_code) {
            $referido_code = wp_generate_uuid4();
            update_user_meta($user_id, 'referido_code', $referido_code);
        }

        echo '<p>Tu código de referido: <strong>' . esc_html($referido_code) . '</strong></p>';
    }
}
