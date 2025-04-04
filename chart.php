<?php
// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection with improved error handling
$con = mysqli_connect("localhost", "root", "", "sales_inventory_db");
if (!$con) {
    die("Problem in database connection! Contact administrator: " . mysqli_connect_error());
}

// Initialize arrays to prevent undefined variable errors
$productname = array();
$qty = array();

// Function to generate unique, visually distinct colors
function generateUniqueColors($count) {
    // Color palette with distinct colors
    $baseColors = [
        "#1f77b4", // blue
        "#ff7f0e", // orange
        "#2ca02c", // green
        "#d62728", // red
        "#9467bd", // purple
        "#8c564b", // brown
        "#e377c2", // pink
        "#7f7f7f", // gray
        "#bcbd22", // olive
        "#17becf", // cyan
        "#aec7e8", // light blue
        "#ffbb78", // light orange
        "#98df8a", // light green
        "#ff9896", // light red
        "#c5b0d5", // light purple
        "#c49c94", // light brown
        "#f7b6d2", // light pink
        "#c7c7c7", // light gray
        "#dbdb8d", // light olive
        "#9edae5"  // light cyan
    ];

    // Shuffle colors to add randomness
    shuffle($baseColors);

    // Limit colors to the number of products
    return array_slice($baseColors, 0, $count);
}

// PRODUCT SALES with non-zero quantities
$sql = "SELECT Product_Name, Qty FROM product_sales WHERE Qty > 0 ORDER BY Qty DESC";
$result = mysqli_query($con, $sql);
if (!$result) {
    die("Error fetching product sales: " . mysqli_error($con));
}

// Collect product data with non-zero quantities
while ($row = mysqli_fetch_array($result)) { 
    $productname[] = htmlspecialchars($row['Product_Name']);
    $qty[] = intval($row['Qty']);
}

// Generate unique colors based on product count
$uniqueColors = generateUniqueColors(count($productname));

// STOCKS MONITORING
// Determine the selected year
$selected_year = isset($_POST['select_year']) ? $_POST['select_year'] : date("Y");

// Month mapping
$month_mapping = [
    '01' => 'January', '02' => 'February', '03' => 'March', 
    '04' => 'April', '05' => 'May', '06' => 'June', 
    '07' => 'July', '08' => 'August', '09' => 'September', 
    '10' => 'October', '11' => 'November', '12' => 'December'
];

// Fetch sales monitoring data
$sql002 = "SELECT * FROM sales_monitoring WHERE `Year` = ? ORDER BY `Month` ASC";
$stmt = mysqli_prepare($con, $sql002);
mysqli_stmt_bind_param($stmt, "s", $selected_year);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$month_arr = array();
$sales = array();
while ($row = mysqli_fetch_array($result)) { 
    $mnt = $row['Month'];
    $month_arr[] = $month_mapping[$mnt] ?? $mnt;
    $sales[] = floatval($row['Sales']);
}

// Fetch available years for dropdown
$years = array();
$sql_yr = "SELECT DISTINCT `Year` FROM sales_monitoring ORDER BY `Year`";
$result_yr = mysqli_query($con, $sql_yr);
while ($row3 = mysqli_fetch_array($result_yr)) { 
    $years[] = $row3['Year'];
}
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Analytics Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .chart-container { max-width: 800px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h2>Sales Analytics Reports</h2>
                
                <form name="year_frm" method="POST" class="mb-3">
                    <div class="d-flex justify-content-center align-items-center">
                        <label for="select_year" class="me-2">Select Year:</label>
                        <select name="select_year" id="select_year" class="form-select" style="width: 100px;" onchange="this.form.submit()">
                            <?php foreach($years as $list_year): ?>
                                <option value="<?php echo $list_year; ?>" 
                                        <?php echo ($list_year == $selected_year ? 'selected' : ''); ?>>
                                    <?php echo $list_year; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>

                <div class="row">
                    <div class="col-md-6 chart-container">
                        <h3>Total Product Sales</h3>
                        <canvas id="productSalesChart"></canvas>
                    </div>
                    <div class="col-md-6 chart-container">
                        <h3>Sales Monitoring (<?php echo $selected_year; ?>)</h3>
                        <canvas id="salesMonitoringChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script>
        // Product Sales Doughnut Chart
        const productCtx = document.getElementById('productSalesChart').getContext('2d');
        new Chart(productCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($productname); ?>,
                datasets: [{
                    label: 'Product Sales',
                    data: <?php echo json_encode($qty); ?>,
                    backgroundColor: <?php echo json_encode($uniqueColors); ?>
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Sales Monitoring Bar Chart
        const salesCtx = document.getElementById('salesMonitoringChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($month_arr); ?>,
                datasets: [{
                    label: 'Monthly Sales',
                    data: <?php echo json_encode($sales); ?>,
                    backgroundColor: [
                        "#5969ff", "#ff407b", "#25d5f2", 
                        "#ffc750", "#2ec551", "#7040fa", "#ff004e"
                    ]
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>