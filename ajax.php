<?php
// Start output buffering
ob_start();

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to the user
ini_set('log_errors', 1); // Log errors to the error log

// Include database connection
require_once 'db_connect.php';

// Get action parameter
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Include admin class
include 'admin_class.php';
$crud = new Action();

if($action == 'login'){
	$login = $crud->login();
	if($login)
		echo $login;
}
if($action == 'login2'){
	$login = $crud->login2();
	if($login)
		echo $login;
}
if($action == 'logout'){
	$logout = $crud->logout();
	if($logout)
		echo $logout;
}
if($action == 'logout2'){
	$logout = $crud->logout2();
	if($logout)
		echo $logout;
}
if($action == 'save_user'){
	$save = $crud->save_user();
	if($save)
		echo $save;
}
if($action == 'delete_user'){
	$save = $crud->delete_user();
	if($save)
		echo $save;
}
if($action == 'signup'){
	$save = $crud->signup();
	if($save)
		echo $save;
}
if($action == "save_settings"){
	$save = $crud->save_settings();
	if($save)
		echo $save;
}
if($action == "save_category"){
	$save = $crud->save_category();
	if($save)
		echo $save;
}
if($action == "delete_category"){
	$save = $crud->delete_category();
	if($save)
		echo $save;
}

if($action == "save_supplier"){
	$save = $crud->save_supplier();
	if($save)
		echo $save;
}
if($action == "delete_supplier"){
	$save = $crud->delete_supplier();
	if($save)
		echo $save;
}
if($action == "save_product"){
	$save = $crud->save_product();
	if($save)
		echo $save;
}
if($action == "delete_product"){
	$save = $crud->delete_product();
	if($save)
		echo $save;
}
if($action == "save_receiving"){
	$save = $crud->save_receiving();
	if($save)
		echo $save;
}
if($action == "delete_receiving"){
	$save = $crud->delete_receiving();
	if($save)
		echo $save;
}
if($action == "save_customer"){
	$save = $crud->save_customer();
	if($save)
		echo $save;
}
if($action == "delete_customer"){
	$save = $crud->delete_customer();
	if($save)
		echo $save;
}

if($action == "update_stock"){
	$save = $crud->update_stock();
	if($save)
		echo $save;
}

if($action == "bulk_delete_products"){
	$save = $crud->bulk_delete_products();
	if($save)
		echo $save;
}

if($action == "check_low_stock"){
	$save = $crud->check_low_stock();
	if($save)
		echo $save;
}

if($action == "export_products_qr"){
	$save = $crud->export_products_qr();
	if($save)
		echo $save;
}

if($action == "get_stock_movement"){
	// Clear any previous output
	ob_clean();
	header('Content-Type: application/json');
	
	try {
		if (!$conn) {
			throw new Exception("Database connection failed");
		}

		$query = "SELECT 
			DATE(date_updated) as date,
			SUM(CASE WHEN type = 1 THEN qty ELSE 0 END) as stock_in,
			SUM(CASE WHEN type = 2 THEN qty ELSE 0 END) as stock_out
		FROM inventory 
		WHERE status = 'completed'
		GROUP BY DATE(date_updated)
		ORDER BY date DESC
		LIMIT 30";
		
		$result = $conn->query($query);
		if ($result === false) {
			throw new Exception("Query failed: " . $conn->error);
		}

		$data = [
			'labels' => [],
			'stock_in' => [],
			'stock_out' => []
		];
		
		if($result && $result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$data['labels'][] = date('M d', strtotime($row['date']));
				$data['stock_in'][] = (int)$row['stock_in'];
				$data['stock_out'][] = (int)$row['stock_out'];
			}
		} else {
			// If no data, show last 7 days with zero values
			for($i = 6; $i >= 0; $i--) {
				$date = date('M d', strtotime("-$i days"));
				$data['labels'][] = $date;
				$data['stock_in'][] = 0;
				$data['stock_out'][] = 0;
			}
		}
		echo json_encode($data);
	} catch (Exception $e) {
		error_log("Stock movement error: " . $e->getMessage());
		echo json_encode([
			'error' => true,
			'message' => 'Error fetching stock movement data: ' . $e->getMessage()
		]);
	}
	exit;
}

if($action == "get_category_sales"){
	// Clear any previous output
	ob_clean();
	header('Content-Type: application/json');
	
	try {
		if (!$conn) {
			throw new Exception("Database connection failed");
		}

		$query = "SELECT 
			c.name as category,
			COALESCE(SUM(i.qty * p.price), 0) as total_sales
		FROM category_list c
		LEFT JOIN product_list p ON p.category_id = c.id
		LEFT JOIN inventory i ON i.product_id = p.id AND i.type = 2 AND i.status = 'completed'
		GROUP BY c.id, c.name
		ORDER BY total_sales DESC";
		
		$result = $conn->query($query);
		if ($result === false) {
			throw new Exception("Query failed: " . $conn->error);
		}

		$data = [
			'labels' => [],
			'values' => []
		];
		
		if($result && $result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$data['labels'][] = $row['category'];
				$data['values'][] = (float)$row['total_sales'];
			}
		} else {
			// If no data, show a message
			$data['labels'][] = 'No Sales Data';
			$data['values'][] = 1;
		}
		echo json_encode($data);
	} catch (Exception $e) {
		error_log("Category sales error: " . $e->getMessage());
		echo json_encode([
			'error' => true,
			'message' => 'Error fetching category sales data: ' . $e->getMessage()
		]);
	}
	exit;
}

if($action == "import_products"){
	$save = $crud->import_products();
	if($save)
		echo $save;
}

if($action == "export_products"){
	$crud->export_products();
}

if($action == "download_template"){
	$crud->download_template();
}

if($action == "get_price_history"){
	$save = $crud->get_price_history();
	if($save)
		echo $save;
}

if($action == "save_variants"){
	$save = $crud->save_variants();
	if($save)
		echo $save;
}

if($action == "chk_prod_availability"){
	$save = $crud->chk_prod_availability();
	if($save)
		echo $save;
}

if($action == "save_sales"){
	$save = $crud->save_sales();
	if($save)
		echo $save;
}

if($action == "delete_sales"){
	$save = $crud->delete_sales();
	if($save)
		echo $save;
}

if($action == 'check_low_stock'){
	$low_stock_items = array();
	$qry = $conn->query("SELECT p.*, 
		COALESCE((SELECT sum(qty) FROM inventory WHERE status='completed' AND type = 1 AND product_id = p.id), 0) as inn,
		COALESCE((SELECT sum(qty) FROM inventory WHERE status='completed' AND type = 2 AND product_id = p.id), 0) as out
		FROM product_list p 
		HAVING (inn - out) <= 10 AND (inn - out) > 0 
		ORDER BY (inn - out) ASC");
	while($row = $qry->fetch_assoc()) {
		$available = $row['inn'] - $row['out'];
		$low_stock_items[] = array(
			'id' => $row['id'],
			'name' => $row['name'],
			'available' => $available,
			'unit' => isset($row['unit']) ? $row['unit'] : 'pcs'
		);
	}
	echo json_encode($low_stock_items);
}

