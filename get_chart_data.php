<?php
include 'db_connect.php';

$response = array();

if(isset($_GET['type'])) {
    switch($_GET['type']) {
        case 'sales_trend':
            // Fetch sales trend data
            $trend_sql = "SELECT DATE(date_updated) as sale_date, SUM(total_amount) as daily_sales 
                        FROM sales_list 
                        GROUP BY DATE(date_updated) 
                        ORDER BY sale_date DESC 
                        LIMIT 30";
            $trend_result = $conn->query($trend_sql);
            $trend_dates = array();
            $trend_sales = array();
            
            while($row = $trend_result->fetch_assoc()) {
                $trend_dates[] = date("M d", strtotime($row['sale_date']));
                $trend_sales[] = floatval($row['daily_sales']);
            }
            
            $response['labels'] = array_reverse($trend_dates);
            $response['data'] = array_reverse($trend_sales);
            break;
            
        case 'top_products':
            // Simplified query for top products
            $products_sql = "SELECT 
                               p.name,
                               COUNT(i.id) as transaction_count,
                               SUM(i.qty) as total_qty
                           FROM inventory i 
                           INNER JOIN product_list p ON i.product_id = p.id 
                           WHERE i.type = 2 
                           GROUP BY p.id, p.name 
                           ORDER BY total_qty DESC 
                           LIMIT 10";
            
            $products_result = $conn->query($products_sql);
            
            if (!$products_result) {
                error_log("MySQL Error: " . $conn->error);
                $response['error'] = "Failed to fetch top products data";
                break;
            }
            
            $product_names = array();
            $product_sales = array();
            $product_qty = array();
            
            while($row = $products_result->fetch_assoc()) {
                $product_names[] = $row['name'];
                $product_sales[] = intval($row['transaction_count']);
                $product_qty[] = intval($row['total_qty']);
            }
            
            $response['labels'] = $product_names;
            $response['data'] = $product_sales;
            $response['quantities'] = $product_qty;
            break;
    }
}

header('Content-Type: application/json');
echo json_encode($response); 