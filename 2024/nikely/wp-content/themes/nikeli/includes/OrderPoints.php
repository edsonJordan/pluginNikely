<?php

class OrderPoints {
    private static $table_name;

    public static function initialize() {
        global $wpdb;
        self::$table_name = $wpdb->prefix . 'order_points';
        date_default_timezone_set('America/Lima'); // Establece la zona horaria de Lima, Perú.
    }

    public static function getChartData($timeFrame = '90days') {
        global $wpdb;
        $dateFormat = "%Y-%m-%d"; // Formato por defecto para agrupar por día.
        
        if ($timeFrame === 'today' || $timeFrame === 'yesterday') {
            $dateFormat = "%Y-%m-%d %H:00"; // Incluir horas para hoy y ayer.
            $specificDay = $timeFrame === 'today' ? 'CURDATE()' : 'DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
            $whereClause = "WHERE created_at >= $specificDay AND created_at < DATE_ADD($specificDay, INTERVAL 1 DAY)";
        } else {
            // Incluye desde el inicio de hace 90 días hasta el final del día actual.
            $whereClause = "WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 90 DAY) AND created_at < DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
        }

        $query = "SELECT DATE_FORMAT(created_at, '$dateFormat') AS formatted_date, SUM(points_total) AS points_sum
                  FROM " . self::$table_name . "
                  $whereClause
                  GROUP BY DATE_FORMAT(created_at, '$dateFormat')
                  ORDER BY formatted_date ASC";

        $results = $wpdb->get_results($query, OBJECT);
        $data = ['dates' => [], 'points' => []];
        foreach ($results as $row) {
            $data['dates'][] = $row->formatted_date;
            $data['points'][] = $row->points_sum;
        }

        return $data;
    }
}

?>
