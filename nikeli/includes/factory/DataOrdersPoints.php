<?php

namespace Factory;

class DataOrdersPoints
{
    public static function generateRecords()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'order_points'; // Acceso al nombre de la tabla

        for ($i = 0; $i < 100; $i++) {
            $data = [
                'order_id' => rand(1, 1000),  // Asumiendo un rango de ID de pedido
                'user_id' => rand(1, 4),  // Usuario ID entre 1 y 4
                'user_name' => 'User' . rand(1, 4),  // Nombre de usuario generado aleatoriamente
                'points_total' => rand(10, 1000),  // Puntos totales aleatorios
                'created_at' => self::randomDateIn2024(),  // Uso de self en lugar de $this
                'updated_at' => self::randomDateIn2024()
            ];

            $format = ['%d', '%d', '%s', '%d', '%s', '%s'];
            $wpdb->insert($table_name, $data, $format);
        }
    }

    private static function randomDateIn2024()
    {
        return '2024-' . rand(1, 12) . '-' . rand(1, 28) . ' ' . rand(0, 23) . ':' . rand(0, 59) . ':' . rand(0, 59);
    }
}

