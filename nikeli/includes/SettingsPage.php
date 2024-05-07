<?php

namespace Nikeli;

class SettingsPage {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'settings_init']);
    }

    public function add_admin_menu() {
        add_menu_page(
            'Gestión de Descuentos',      // Título de la página
            'Descuentos',                 // Título del menú
            'manage_options',             // Capacidad necesaria para ver esta página
            'discount_management',        // Slug del menú
            [$this, 'settings_page_html'] // Función para mostrar el contenido de la página
        );
    }

    public function settings_page_html() { 
        ?>
        <form action="options.php" method="post">
            <h1>Gestión de Descuentos por Rol</h1>
            <?php
            settings_fields('discountSettings');
            do_settings_sections('discount_management');
            submit_button('Guardar Cambios');
            ?>
        </form>
        <?php
    }

    public function settings_init() { 
        register_setting('discountSettings', 'discounts_settings');

        add_settings_section(
            'nikeli_discounts_section', 
            __('Descuentos por rol de usuario', 'nikeli'), 
            [$this, 'settings_section_callback'], 
            'discount_management'
        );

        $roles = ['ejecutivo', 'plus', 'top'];
        foreach ($roles as $role) {
            add_settings_field(
                'discount_rate_' . $role, 
                __('Descuento para ' . ucfirst($role), 'nikeli'), 
                [$this, 'settings_field_callback'], 
                'discount_management', 
                'nikeli_discounts_section',
                [
                    'label_for' => 'discount_rate_' . $role,
                    'class' => 'nikeli_row',
                    'nikeli_custom_data' => 'custom',
                    'role' => $role
                ]
            );
        }
    }

    public function settings_section_callback() { 
        echo __('Ajuste los porcentajes de descuento aplicables a cada rol de usuario.', 'nikeli');
    }

    public function settings_field_callback($args) { 
        $options = get_option('discounts_settings');
        ?>
        <input type="number" name="discounts_settings[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo $options[$args['label_for']] ?? ''; ?>" min="0" max="100" step="0.01"> %
        <?php
    }
}

