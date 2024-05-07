<?php
/**
 * Plugin Name: Nikeli Fashion Compensation System
 * Plugin URI: http://tuwebsite.com
 * Description: Sistema de compensación para consultores de Nikeli Fashion.
 * Version: 1.0
 * Author: Tu Nombre
 * Author URI: http://tuwebsite.com
 */
 


 require_once __DIR__ . '/vendor/autoload.php';

use Nikeli\Nikely_init;
use Nikeli\RolesManager;
use Nikeli\DiscountManager;
use Nikeli\ProductPointsManager;
use Nikeli\SettingsPage;


 if (!defined('ABSPATH')) {
     exit;
 }
 
 // Define plugin constants
 define('NIKELI_PLUGIN_DIR', plugin_dir_path(__FILE__));
 define('NIKELI_PLUGIN_URL', plugin_dir_url(__FILE__));


 // Hook plugin activation and deactivation
 register_activation_hook(__FILE__, ['Nikeli\Nikely_init', 'activate']);
 register_deactivation_hook(__FILE__, ['Nikeli\Nikely_init', 'deactivate']);
 
 // Include other plugin files and initiate classes
 Nikely_init::instance();
 

$roles_manager = new RolesManager(__FILE__);
$discount_manager = new DiscountManager();
$productPointsManager = new ProductPointsManager();
$settings_page = new SettingsPage();