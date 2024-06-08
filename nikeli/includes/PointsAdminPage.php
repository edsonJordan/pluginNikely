<?php 
namespace Nikeli;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}



class PointsListTable extends \WP_List_Table {
    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();
        $this->process_bulk_action();

        $data = $this->fetch_points_data();
        usort($data, [&$this, 'usort_reorder']);

        $perPage = 20;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args([
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ]);

        $this->items = array_slice($data, (($currentPage-1) * $perPage), $perPage);

        $this->_column_headers = [$columns, $hidden, $sortable];
    }

    private function fetch_points_data() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'order_points';
        $query = "SELECT user_id, user_name, SUM(points_total) as points FROM {$table_name} GROUP BY user_id";
        return $wpdb->get_results($query, ARRAY_A);
    }

    public function get_columns() {
        return [
            'user_name' => 'Usuario',
            'points'    => 'Puntos Totales'
        ];
    }

    public function get_sortable_columns() {
        return [
            'points' => ['points', false]
        ];
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            default:
                return $item[$column_name];
        }
    }
}

class PointsAdminPage {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
    }

    public function add_admin_menu() {
        add_menu_page(
            'Gestión de Puntos de Usuarios',
            'Puntos de Usuarios',
            'manage_options',
            'nikeli_points_management',
            [$this, 'points_page_html']
        );
    }

    public function points_page_html() {
        echo '<div class="wrap"><h1>Puntos de Usuarios</h1>';
        $listTable = new PointsListTable();
        $listTable->prepare_items();
        $listTable->display();
        echo '</div>';
    }
}