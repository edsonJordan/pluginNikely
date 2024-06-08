<?php 

namespace Nikeli;

class OrdersPointsManager {

    private $plugin_file;


    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']); // Asegura que esta línea esté aquí
        $this->register_hooks();
        register_activation_hook($this->plugin_file, [$this, 'create_points_table']);
        register_activation_hook($this->plugin_file, [$this, 'create_claimed_points_table']);


        register_uninstall_hook($this->plugin_file, [self::class, 'on_uninstall']);
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
                is_special tinyint(1) NOT NULL DEFAULT 0, 
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY unique_order_user (order_id, user_id)
            ) $charset_collate;";
    
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
    

    public function create_claimed_points_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'claimed_points';
    
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();
    
            $sql = "CREATE TABLE $table_name (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                user_id bigint(20) NOT NULL,
                points_total int NOT NULL,
                conversion_rate decimal(10,2) NOT NULL,  
                status varchar(50) NOT NULL,
                claim_month date NOT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY unique_user_claim (user_id, claim_month)
            ) $charset_collate;";
    
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
    
    

    public function register_settings() {
        // Registra la sección y los campos en la página de 'orders_points_management'
        add_settings_section('nikeli_data_settings', 'Gestión de Datos', null, 'orders_points_management');
        add_settings_field('nikeli_delete_data', 'Eliminar datos al desinstalar', [$this, 'delete_data_field_html'], 'orders_points_management', 'nikeli_data_settings');
        register_setting('orders_points_management', 'nikeli_delete_data');
    }

    public function enqueue_admin_scripts() {
        $script = "
            jQuery(document).ready(function($) {
                $('a.delete').on('click', function(e) {
                    if (!confirm('¿Estás seguro de que deseas desinstalar este plugin y eliminar todos los datos asociados?')) {
                        e.preventDefault();
                    }
                });
            });
        ";
        wp_add_inline_script('jquery', $script);
    }


    public function delete_data_field_html() {
        $option = get_option('nikeli_delete_data');
        echo '<input type="checkbox" id="nikeli_delete_data" name="nikeli_delete_data" value="1" ' . checked(1, $option, false) . '/>';
        echo '<label for="nikeli_delete_data">Eliminar todos los datos almacenados por el plugin al desinstalar.</label>';
    }

    public static function on_uninstall() {
        if (get_option('nikeli_delete_data') == '1') {
            global $wpdb;
            $table_name = $wpdb->prefix . 'order_points';
            $wpdb->query("DROP TABLE IF EXISTS {$table_name}");
            delete_option('nikeli_delete_data'); // Clean up option
        }
    }

    

    public function orders_page_html() {
        // Mostrar la tabla y los ajustes
        echo '<div class="wrap"><h1>Puntos por Pedido</h1>';
        settings_errors(); // Opcional: mostrar errores/confirmaciones de ajustes
    
        // Mostrar formularios de ajustes
        echo '<form method="post" action="options.php">';
        settings_fields('orders_points_management'); // Cambia esto según donde estén registrados los ajustes
        do_settings_sections('orders_points_management'); // Asegúrate de que el slug coincida con donde están registrados los ajustes
        submit_button();
        echo '</form>';
    
        // Mostrar la tabla de puntos
        $listTable = new OrdersPointsListTable();
        $listTable->prepare_items();
        $listTable->display();
        echo '</div>';
    }

    public function register_hooks() {
        add_action('woocommerce_order_status_completed', [$this, 'handle_completed_order']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);

    }

    public function handle_completed_order($order_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'order_points';


        $order = wc_get_order($order_id);
        $user = $order->get_user();
        if (!$user) {  // Comprueba si hay un usuario asociado
            error_log("No user associated with the order ID: $order_id");
            return; // No hay usuario, termina la ejecución
        }
    
        // Verificar si ya existe un registro para este pedido
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table_name} WHERE order_id = %d", $order_id));
    
        if ($exists) {
            error_log("Registro ya existe para el pedido ID: $order_id");
            return; // Si ya existe un registro, termina la función aquí
        }
    
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
    
        // Asegurar que solo se crea un registro si los puntos totales son mayores que 0
        if ($points_total > 0) {
            $this->save_order_points($order_id, $user->ID, $user->display_name, $points_total);
        } else {
            error_log("No se creó registro para el pedido ID: $order_id porque los puntos totales son 0 o negativos.");
        }
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


