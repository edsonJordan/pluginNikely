<?php
/**
 * Plugin Name: Nikeli Fashion Compensation System
 * Plugin URI: https://gato.pe/
 * Description: Sistema de compensación para consultores de Nikeli Fashion.
 * Version: 1.0
 * Author: Gato Agencia
 * Author URI: https://gato.pe/
 */


  // Define plugin constants
  define('NIKELI_PLUGIN_DIR', plugin_dir_path(__FILE__));
  define('NIKELI_PLUGIN_URL', plugin_dir_url(__FILE__));


 require_once __DIR__ . '/vendor/autoload.php';
 require_once NIKELI_PLUGIN_DIR . 'includes/source/OrdersPointsListTable.php';

use Referidos\Referidos;
use Referidos\UserStatusManager;

use Nikeli\Nikely_init;
use Nikeli\RolesManager;
use Nikeli\DiscountManager;
use Nikeli\ProductPointsManager;
use Nikeli\SettingsPage;
use Nikeli\PointsAdminPage;
use Nikeli\OrdersPointsManager;
// 




 if (!defined('ABSPATH')) {
     exit;
 }
 



 // Hook plugin activation and deactivation
 register_activation_hook(__FILE__, ['Nikeli\Nikely_init', 'activate']);
 register_deactivation_hook(__FILE__, ['Nikeli\Nikely_init', 'deactivate']);
 


 
 // Include other plugin files and initiate classes
 Nikely_init::instance();
 

$roles_manager = new RolesManager(__FILE__);
$discount_manager = new DiscountManager();
$productPointsManager = new ProductPointsManager();
$settings_page = new SettingsPage();

$points_AdminPage = new PointsAdminPage();
$OrdersPointsManager = new OrdersPointsManager(__FILE__);


$referidos = new Referidos(__FILE__);
$status = new UserStatusManager();


// Add a new checkout field
// Agregar campos personalizados al formulario de checkout

