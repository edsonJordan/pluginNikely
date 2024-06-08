<?php
// Referidos_Backend.php

namespace Referidos;

class SubscriptionDB {


    public static function create_table_subscriptions() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscriptions';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                user_id bigint(20) NOT NULL,
                coupon_code varchar(255) NOT NULL,
                start_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                role_level varchar(50) NOT NULL,
                status boolean NOT NULL DEFAULT false,
                PRIMARY KEY  (id)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    public static function create_referrals_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'referidos';
    
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();
        
            $sql = "CREATE TABLE $table_name (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                id_lider bigint(20) NOT NULL,
                id_referido bigint(20) NOT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP, 
                PRIMARY KEY  (id),
                UNIQUE KEY unique_lider_referido (id_lider, id_referido)  
            ) $charset_collate;";
        
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    public static function create_product_packages_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'product_packages';
    
        // Verificar si la tabla ya existe
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();
    
            $sql = "CREATE TABLE $table_name (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                user_id bigint(20) NOT NULL,
                package_name VARCHAR(255) NOT NULL UNIQUE,
                package_data json NOT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY  (id)
            ) $charset_collate;";
    
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        } else {
            // Opcionalmente, manejar el caso donde la tabla ya existe
            // Por ejemplo, actualizar estructura, añadir índices faltantes, etc.
        }
    }

    public static function create_bonus_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'user_bonuses';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();
        
            $sql = "CREATE TABLE $table_name (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                user_id bigint(20) NOT NULL,
                bonus_type varchar(50) NOT NULL,
                points_required int(11) NOT NULL,
                free_items int(11) NOT NULL,
                status varchar(20) NOT NULL DEFAULT 'pending',  
                date_awarded datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                PRIMARY KEY  (id),
                UNIQUE KEY unique_user_bonus (user_id, bonus_type)  
            ) $charset_collate;";
        
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    public static function create_package_bonus_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'package_bonuses';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();
        
            $sql = "CREATE TABLE $table_name (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                user_id bigint(20) NOT NULL,
                referred_user_id bigint(20) NOT NULL,
                package_type varchar(50) NOT NULL,
                bonus_amount decimal(10,2) NOT NULL,
                status varchar(20) NOT NULL DEFAULT 'pending',
                date_awarded datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY  (id),
                UNIQUE KEY unique_user_package_bonus (user_id, referred_user_id, package_type)
            ) $charset_collate;";
        
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }




}
