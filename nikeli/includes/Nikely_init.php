<?php
namespace Nikeli;

class Nikely_init {
    protected static $instance = null;

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

 
    
    private function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        add_action('plugins_loaded', [$this, 'on_plugins_loaded']);
    }

    public function on_plugins_loaded() {
        // Load plugin components
    }

    public static function activate() {
        // Code to run during plugin activation
    }

    public static function deactivate() {
        // Code to run during plugin deactivation
    }
}

