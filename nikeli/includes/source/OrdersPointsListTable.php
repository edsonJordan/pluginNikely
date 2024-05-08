<?php

namespace Nikeli;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class OrdersPointsListTable extends \WP_List_Table {

    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $this->process_bulk_action();

        $data = $this->table_data();
        $totalItems = count($data);

        $perPage = 20;
        $currentPage = $this->get_pagenum();
        $data = array_slice($data, (($currentPage-1) * $perPage), $perPage);

        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ));

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

    private function table_data() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'order_points';
        
        $query = "SELECT * FROM $table_name";
        
        // Implement simple search/filter feature
        if (!empty($_REQUEST['s'])) {
            $search = esc_sql($wpdb->esc_like($_REQUEST['s']));
            $query .= " WHERE user_name LIKE '%$search%'";
        }

        // Order results
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'order_id'; //If no sort, default to order ID
        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc'; //If no order, default to asc
        $query .= ' ORDER BY ' . esc_sql($orderby) . ' ' . esc_sql($order);
        
        $results = $wpdb->get_results($query, ARRAY_A);
        return $results;
    }

    public function get_columns() {
        $columns = array(
            'order_id'     => 'ID de Pedido',
            'user_name'    => 'Usuario',
            'points_total' => 'Puntos Totales',
            'actions'      => 'Acciones'
        );
        return $columns;
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'order_id':
            case 'user_name':
            case 'points_total':
                return $item[$column_name];
            case 'actions':
                return sprintf('<a href="%s">Ver Pedido</a>', admin_url('post.php?post=' . $item['order_id'] . '&action=edit'));
            default:
                return print_r($item, true); // Show the whole array for troubleshooting purposes
        }
    }

    protected function get_sortable_columns() {
        return array(
            'points_total' => array('points_total', false),
            'order_id'     => array('order_id', false)
        );
    }

    private function get_hidden_columns() {
        return array();
    }

    public function display_tablenav($which) {
        if ($which == 'top') {
            echo '<div class="tablenav top">';
            $this->search_box('search', 'search_id');
            submit_button('Filter', 'button', 'filter_action', false, array('id' => 'post-query-submit'));
            echo '</div>';
        }
    }
}
