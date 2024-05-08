<?php 
namespace Nikeli;

class PointsAdminPage {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
    }

    public function add_admin_menu() {
        add_menu_page(
            'Gestión de Puntos de Usuarios',      // Título de la página
            'Puntos de Usuarios',                 // Título del menú
            'manage_options',                     // Capacidad requerida
            'nikeli_points_management',           // Slug del menú
            [$this, 'points_page_html']           // Función que renderiza la página
        );
    }

    public function points_page_html() {
        echo '<div class="wrap"><h1>Puntos de Usuarios</h1>';
        $users = get_users();
        echo '<table class="widefat fixed" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Puntos Totales</th>
            </tr>
        </thead>
        <tbody>';
        foreach ($users as $user) {
            $user_points = get_user_meta($user->ID, '_nikeli_points_total', true);
            echo '<tr>
                <td>' . $user->ID . '</td>
                <td>' . $user->display_name . '</td>
                <td>' . $user_points . '</td>
            </tr>';
        }
        echo '</tbody></table></div>';
    }
}
