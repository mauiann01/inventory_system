<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sales_inventory_db";

$month = $_GET['month'];
$year = $_GET['year'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
SELECT pl.name, SUM(i.qty) as qty
FROM inventory i
JOIN product_list pl ON i.product_id = pl.id
WHERE i.stock_from = ? AND MONTH(i.date_updated) = ? AND YEAR(i.date_updated) = ?
GROUP BY pl.name";

$stmt = $conn->prepare($sql);

// Bind parameters with correct types: "s" for string, "i" for integer
$stmt->bind_param("sii", $stockFrom, $month, $year);

// Set value for $stockFrom
$stockFrom = "Sales";

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($data);
?>
