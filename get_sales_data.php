<?php
include 'db_connect.php';

// Get the forecast period from the request, default to 7 days if not specified
$period = isset($_GET['period']) ? intval($_GET['period']) : 7;

// Calculate the date range for the sales data query
$end_date = date('Y-m-d');
$start_date = date('Y-m-d', strtotime("-$period days"));

// Query to get daily sales data
$sql = "SELECT 
            DATE(date_updated) as date,
            SUM(total_amount) as total_sales
        FROM sales_list 
        WHERE date_updated BETWEEN '$start_date' AND '$end_date'
        GROUP BY DATE(date_updated)
        ORDER BY date ASC";

$result = $conn->query($sql);

// Initialize arrays for dates and sales
$dates = [];
$sales = [];

// Fill arrays with zero sales for all dates in the range
$current_date = strtotime($start_date);
$end_timestamp = strtotime($end_date);

while ($current_date <= $end_timestamp) {
    $date = date('Y-m-d', $current_date);
    $dates[] = $date;
    $sales[$date] = 0;
    $current_date = strtotime('+1 day', $current_date);
}

// Populate sales data from query results
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $date = $row['date'];
        $sales[$date] = floatval($row['total_sales']);
    }
}

// Prepare the response data
$response = [
    'dates' => $dates,
    'sales' => array_values($sales)
];

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($response);
?> 