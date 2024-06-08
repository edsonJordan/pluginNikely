<?php

namespace Referidos;

class UserStatusManager {
    // Constructor
    public function __construct() {
        add_action('show_user_profile', [$this, 'add_custom_user_profile_fields']);
        add_action('edit_user_profile', [$this, 'add_custom_user_profile_fields']);
        add_action('personal_options_update', [$this, 'save_custom_user_profile_fields']);
        add_action('edit_user_profile_update', [$this, 'save_custom_user_profile_fields']);
        add_action('user_register', [$this, 'set_default_user_meta'], 10, 1);
    }

    // Agregar el campo al perfil del usuario
    public function add_custom_user_profile_fields($user) {
        $estado_linea_nikeli = get_user_meta($user->ID, 'estado_linea_nikeli', true) ? 'checked' : '';
        ?>
        <h3><?php _e("Estado de Línea Nikeli", "textdomain"); ?></h3>
        <table class="form-table">
            <tr>
                <th>
                    <label for="estado_linea_nikeli"><?php _e("Activo"); ?></label>
                </th>
                <td>
                    <input type="checkbox" name="estado_linea_nikeli" id="estado_linea_nikeli" <?php echo $estado_linea_nikeli; ?> value="1" />
                    <span class="description"><?php _e("Marcar si el usuario está activo en Nikeli.", "textdomain"); ?></span>
                </td>
            </tr>
        </table>
        <?php
    }

    // Guardar el valor del campo personalizado
    public function save_custom_user_profile_fields($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }
        update_user_meta($user_id, 'estado_linea_nikeli', isset($_POST['estado_linea_nikeli']) ? '1' : '0');
    }

    // Establecer el metadato por defecto cuando se crea un nuevo usuario
    public function set_default_user_meta($user_id) {
        update_user_meta($user_id, 'estado_linea_nikeli', '0');
    }
}

