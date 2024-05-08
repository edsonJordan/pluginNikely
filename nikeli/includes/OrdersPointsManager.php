<?php 

namespace Nikeli;

class OrdersPointsManager {

    private $plugin_file;


    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;
        register_activation_hook($this->plugin_file, [$this, 'create_points_table']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        $this->register_hooks();
    }

    public function add_admin_menu() {
        add_menu_page(
            'Gestión de Puntos por Pedido',      // Título de la página
            'Puntos por Pedido',                 // Título del menú
            'manage_options',                    // Capacidad necesaria para ver esta página
            'orders_points_management',          // Slug del menú
            [$this, 'orders_page_html']          // Función para mostrar el contenido de la página
        );
    }
    public function create_points_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'order_points';
    
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();
    
            $sql = "CREATE TABLE $table_name (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                order_id bigint(20) NOT NULL,
                user_id bigint(20) NOT NULL,
                user_name varchar(255) NOT NULL,
                points_total int NOT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;";
    
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
    

    public function orders_page_html() {
        $listTable = new OrdersPointsListTable();
        $listTable->prepare_items();
        
        echo '<div class="wrap"><h1>Puntos por Pedido</h1>';
        $listTable->display();
        echo '</div>';
    }

    public function register_hooks() {
        add_action('woocommerce_order_status_completed', [$this, 'handle_completed_order']);
    }

    public function handle_completed_order($order_id) {
        error_log("Order completed hook triggered for order ID: $order_id");
        $order = wc_get_order($order_id);
        $user = $order->get_user();
        $points_total = 0;
    
        foreach ($order->get_items() as $item_id => $item) {
            $product_id = $item->get_product_id();
            $product_points = get_post_meta($product_id, '_nikeli_points', true);
            $points_total += intval($product_points) * $item->get_quantity();
            error_log("Product ID: $product_id, Points: $product_points");
        }
    
        error_log("Total points for order $order_id: $points_total");
        $this->save_order_points($order_id, $user->ID, $user->display_name, $points_total);
    }
    

    public function save_order_points($order_id, $user_id, $user_name, $points_total) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'order_points';

        $wpdb->insert(
            $table_name,
            [
                'order_id' => $order_id,
                'user_id' => $user_id,
                'user_name' => $user_name,
                'points_total' => $points_total
            ],
            ['%d', '%d', '%s', '%d']
        );
    }


}


