<?php
namespace Referidos;

class UserRolesChecker {
    public static function check_user_roles() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscriptions';
        $current_date = current_time('mysql');

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE DATE_ADD(start_date, INTERVAL 30 DAY) <= %s",
                $current_date
            )
        );

        foreach ($results as $row) {
            $user = new \WP_User($row->user_id);
            $user->set_role('customer');
            $wpdb->delete($table_name, array('id' => $row->id));

            // Eliminar el cupón asociado
            $coupons = get_posts([
                'post_type' => 'shop_coupon',
                'meta_key' => 'generated_for_user_id',
                'meta_value' => $row->user_id,
                'posts_per_page' => -1
            ]);

            foreach ($coupons as $coupon) {
                wp_delete_post($coupon->ID, true);
            }
        }
    }
}
