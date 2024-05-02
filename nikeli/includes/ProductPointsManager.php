<?php
namespace Nikeli;

class ProductPointsManager {
    public function __construct() {
        add_action('woocommerce_product_options_general_product_data', [$this, 'add_points_field']);
        add_action('woocommerce_process_product_meta', [$this, 'save_points_field']);
    }

    public function add_points_field() {
        echo '<div class="options_group">';
        woocommerce_wp_text_input(
            array(
                'id'                => '_nikeli_points',
                'label'             => __('Puntos de recompensa', 'nikeli'),
                'desc_tip'          => 'true',
                'description'       => __('Puntos otorgados por la compra de este producto.', 'nikeli'),
                'type'              => 'number',
                'custom_attributes' => array(
                    'step' => 'any',
                    'min'  => '0'
                )
            )
        );
        echo '</div>';
    }

    public function save_points_field($post_id) {
        $points = isset($_POST['_nikeli_points']) ? $_POST['_nikeli_points'] : '';
        update_post_meta($post_id, '_nikeli_points', sanitize_text_field($points));
    }
}
