<?php
namespace Nikeli;


class RolesManager {
    private $plugin_file;

    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;
        
        register_activation_hook($this->plugin_file, [$this, 'activate']);
        register_deactivation_hook($this->plugin_file, [$this, 'deactivate']);
    }

    public function activate() {
        $this->add_roles();
    }

    public function deactivate() {
        $this->remove_roles();
    }

    private function add_roles() {
        add_role('ejecutivo', 'Ejecutivo', [
            'read' => true,
            'manage_options' => true,
            'view_discounts' => true,
            'purchase_clothes' => true
        ]);
        add_role('plus', 'Plus', [
            'read' => true,
            'manage_options' => true,
            'view_discounts' => true,
            'purchase_clothes' => true
        ]);
        add_role('top', 'Top', [
            'read' => true,
            'manage_options' => true,
            'view_discounts' => true,
            'purchase_clothes' => true
        ]);
    }

    private function remove_roles() {
        remove_role('ejecutivo');
        remove_role('plus');
        remove_role('top');
    }
}
