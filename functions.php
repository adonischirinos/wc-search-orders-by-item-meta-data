<?php

/*
    Plugin Name: WC Search Orders by Custom Item Meta Data
    Description: Search and filter orders by metadata created in WooCommerce orders page.
    Version: 1.0
    Author: Adonis Chirinos
    Author URI: https://github.com/adonischirinos
*/

defined('ABSPATH') || exit;

$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';

// Check if WooCommerce is active.
if (in_array( $plugin_path, wp_get_active_and_valid_plugins() ) || in_array( $plugin_path, wp_get_active_network_plugins())) 
{
    add_filter('woocommerce_shop_order_search_results', 'wc_filter_orders_by_item_meta_data', 99, 3);

    function wc_filter_orders_by_item_meta_data($order_ids, $term, $search_fields) {
        // Check if not empty.
        if (!empty($term)) {
            global $wpdb;
            // SQL statement for search custom item meta data to retrieve POST ids.
            $sql_query_item_meta = $wpdb->prepare(
                "SELECT DISTINCT order_id
                FROM {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta
                INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items
                ON order_item_meta.order_item_id = order_items.order_item_id
                WHERE order_item_meta.meta_value LIKE %s",
                '%' . $wpdb->esc_like($term) . '%'
            );
    
            $filtered_order_ids = $wpdb->get_col($sql_query_item_meta);
            
            if (!empty($filtered_order_ids)) {
                // Merge $order_ids found by default search result with filtered order ids.
                $order_ids = array_unique(array_merge($filtered_order_ids, $order_ids));
            }
        }
        return $order_ids;
    }
}

