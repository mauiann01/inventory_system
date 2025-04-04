<?php
include 'db_connect.php';

function checkStockLevels() {
    global $conn;
    $notifications = array();
    
    // Debug database connection
    error_log("Database connection status: " . ($conn ? "Connected" : "Not connected"));
    if ($conn) {
        error_log("Last error: " . $conn->error);
    }
    
    // Get all products
    $query = "SELECT * FROM product_list r order by name asc";
    error_log("Executing product query: " . $query);
    $product = $conn->query($query);
    
    if ($product === false) {
        error_log("Error in product query: " . $conn->error);
        return array();
    }
    
    error_log("Query successful, number of products: " . $product->num_rows);
    
    // Debug: Print all products and their stock levels
    error_log("=== All Products Stock Levels ===");
    while($row = $product->fetch_assoc()) {
        // Get stock in
        $inn_query = "SELECT sum(qty) as inn FROM inventory where `status`='completed' and type = 1 and product_id = ".$row['id'];
        error_log("Executing stock in query: " . $inn_query);
        $inn = $conn->query($inn_query);
        $inn = $inn && $inn->num_rows > 0 ? $inn->fetch_array()['inn'] : 0;
        
        // Get stock out
        $out_query = "SELECT sum(qty) as `out` FROM inventory where `status`='completed' and type = 2 and product_id = ".$row['id'];
        error_log("Executing stock out query: " . $out_query);
        $out = $conn->query($out_query);
        $out = $out && $out->num_rows > 0 ? $out->fetch_array()['out'] : 0;
        
        $available = $inn - $out;
        
        // Debug log for each product
        error_log("Product: {$row['name']}");
        error_log("ID: {$row['id']}");
        error_log("Stock In: {$inn}");
        error_log("Stock Out: {$out}");
        error_log("Available: {$available}");
        error_log("---");
        
        // Check for out of stock
        if($available <= 0) {
            error_log("Found out of stock item: {$row['name']} (Available: {$available})");
            $notifications[] = array(
                'type' => 'danger',
                'message' => $row['name'] . ' is out of stock!',
                'product_id' => $row['id'],
                'available' => $available,
                'has_order_button' => true
            );
        }
        // Check for low stock (less than 10 units)
        else if($available <= 10) {
            error_log("Found low stock item: {$row['name']} (Available: {$available})");
            $notifications[] = array(
                'type' => 'warning',
                'message' => $row['name'] . ' is running low on stock! Only ' . $available . ' units remaining.',
                'product_id' => $row['id'],
                'available' => $available,
                'has_order_button' => true
            );
        }
    }
    
    // Debug log
    error_log("=== Final Notifications ===");
    error_log("Total notifications generated: " . count($notifications));
    error_log("Notifications array: " . json_encode($notifications));
    return $notifications;
}

function displayNotifications() {
    $notifications = checkStockLevels();
    if(empty($notifications)) {
        return '';
    }
    
    $html = '<div class="notifications-container">';
    foreach($notifications as $notification) {
        $html .= '<div class="alert alert-' . $notification['type'] . ' alert-dismissible fade show" role="alert">
                    ' . $notification['message'] . '
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
    }
    $html .= '</div>';
    
    return $html;
}

// AJAX endpoint to get notifications
if(isset($_GET['action']) && $_GET['action'] == 'get_notifications') {
    header('Content-Type: application/json');
    
    try {
        error_log("=== Starting Notification Check ===");
        error_log("Received notification request");
        $notifications = checkStockLevels();
        error_log("Sending notifications: " . json_encode($notifications));
        echo json_encode($notifications);
    } catch (Exception $e) {
        error_log("Error in notifications endpoint: " . $e->getMessage());
        echo json_encode(array('error' => 'Failed to get notifications'));
    }
    exit;
}
?> 