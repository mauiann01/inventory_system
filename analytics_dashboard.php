<?php 
if(!isset($_SESSION)){
    session_start();
}
include 'db_connect.php';
if(!isset($_SESSION['login_id']))
    header('location:login.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Analytics Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/font-awesome/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'topbar.php' ?>
    <?php include 'navbar.php' ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sales Analytics Dashboard</h3>
                    </div>
                    <div class="card-body">
                        <!-- Date Range Filter -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <form method='post' action='' class="form-inline">
                                    <div class="form-group mr-2">
                                        <label class="mr-2">From:</label>
                                        <input type='date' class='form-control' name='fromDate' value='<?php if(isset($_POST['fromDate'])) echo $_POST['fromDate']; ?>' required>
                                    </div>
                                    <div class="form-group mr-2">
                                        <label class="mr-2">To:</label>
                                        <input type='date' class='form-control' name='endDate' value='<?php if(isset($_POST['endDate'])) echo $_POST['endDate']; ?>' required>
                                    </div>
                                    <button type='submit' name='but_search' class='btn btn-primary'>Filter</button>
                                </form>
                            </div>
                        </div>

                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5>Total Sales</h5>
                                        <h3>₱<?php 
                                            $sql = "SELECT SUM(total_amount) as total FROM sales_list";
                                            if(isset($_POST['but_search'])){
                                                $fromDate = $_POST['fromDate'];
                                                $endDate = $_POST['endDate'];
                                                $sql .= " WHERE date_updated BETWEEN '$fromDate' AND '$endDate'";
                                            }
                                            $result = $conn->query($sql);
                                            echo number_format($result->fetch_assoc()['total'] ?? 0, 2);
                                        ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5>Total Orders</h5>
                                        <h3><?php 
                                            $sql = "SELECT COUNT(*) as count FROM sales_list";
                                            if(isset($_POST['but_search'])){
                                                $fromDate = $_POST['fromDate'];
                                                $endDate = $_POST['endDate'];
                                                $sql .= " WHERE date_updated BETWEEN '$fromDate' AND '$endDate'";
                                            }
                                            $result = $conn->query($sql);
                                            echo $result->fetch_assoc()['count'] ?? 0;
                                        ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5>Average Order Value</h5>
                                        <h3>₱<?php 
                                            $sql = "SELECT AVG(total_amount) as avg FROM sales_list";
                                            if(isset($_POST['but_search'])){
                                                $fromDate = $_POST['fromDate'];
                                                $endDate = $_POST['endDate'];
                                                $sql .= " WHERE date_updated BETWEEN '$fromDate' AND '$endDate'";
                                            }
                                            $result = $conn->query($sql);
                                            echo number_format($result->fetch_assoc()['avg'] ?? 0, 2);
                                        ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5>Total Products Sold</h5>
                                        <h3><?php 
                                            $sql = "SELECT SUM(qty) as total FROM inventory WHERE stock_from = 'Sales'";
                                            if(isset($_POST['but_search'])){
                                                $fromDate = $_POST['fromDate'];
                                                $endDate = $_POST['endDate'];
                                                $sql .= " AND date_updated BETWEEN '$fromDate' AND '$endDate'";
                                            }
                                            $result = $conn->query($sql);
                                            echo $result->fetch_assoc()['total'] ?? 0;
                                        ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Row -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Sales Trend</h5>
                                        <canvas id="salesTrendChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Top Products</h5>
                                        <canvas id="topProductsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sales-Based Forecasting Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title">Sales Forecasting</h5>
                            </div>
                            <div class="card-body">
                                <form id="forecastForm" class="form-inline justify-content-center mb-4">
                                    <div class="form-group mr-2">
                                        <label for="forecastPeriod" class="mr-2">Forecast Period:</label>
                                        <select id="forecastPeriod" name="period" class="form-control mr-3" onchange="updateSalesForecast()">
                                            <option value="7">Next 7 Days</option>
                                            <option value="14">Next 14 Days</option>
                                            <option value="30">Next 30 Days</option>
                                        </select>
                                    </div>
                                </form>
                                <div class="row">
                                    <div class="col-md-8">
                                        <canvas id="salesForecastChart"></canvas>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>Forecast Summary</h5>
                                            </div>
                                            <div class="card-body" id="salesForecastSummary">
                                                <!-- Forecast summary will be displayed here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detailed Sales Table -->
                        <div class="card">
                            <div class="card-body">
                                <h5>Detailed Sales Report</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="salesTable">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Reference #</th>
                                                <th>Customer</th>
                                                <th>Products</th>
                                                <th>Total Amount</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT s.*, c.name as customer_name 
                                                   FROM sales_list s 
                                                   LEFT JOIN customer_list c ON s.customer_id = c.id";
                                            if(isset($_POST['but_search'])){
                                                $fromDate = $_POST['fromDate'];
                                                $endDate = $_POST['endDate'];
                                                $sql .= " WHERE s.date_updated BETWEEN '$fromDate' AND '$endDate'";
                                            }
                                            $sql .= " ORDER BY s.date_updated DESC";
                                            $result = $conn->query($sql);
                                            
                                            while($row = $result->fetch_assoc()):
                                                $products = $conn->query("SELECT i.*, p.name as product_name 
                                                                       FROM inventory i 
                                                                       JOIN product_list p ON i.product_id = p.id 
                                                                       WHERE i.form_id = ".$row['id']);
                                            ?>
                                            <tr>
                                                <td><?php echo date("M d, Y",strtotime($row['date_updated'])) ?></td>
                                                <td><?php echo $row['ref_no'] ?></td>
                                                <td><?php echo $row['customer_name'] ?? 'Guest' ?></td>
                                                <td>
                                                    <?php 
                                                    $product_list = [];
                                                    while($prod = $products->fetch_assoc()):
                                                        $product_list[] = $prod['product_name'] . " (".$prod['qty'].")";
                                                    endwhile;
                                                    echo implode(", ", $product_list);
                                                    ?>
                                                </td>
                                                <td>₱<?php echo number_format($row['total_amount'],2) ?></td>
                                                <td>
                                                    <a class="btn btn-sm btn-info" href="print_sales.php?id=<?php echo $row['id'] ?>" target="_blank">
                                                        <i class="fa fa-print"></i> Receipt
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script>
        // Initialize charts
        let salesTrendChart, topProductsChart, salesForecastChart;

        // Function to update sales forecast
        function updateSalesForecast() {
            const period = document.getElementById('forecastPeriod').value;
            
            fetch(`get_sales_data.php?period=${period}`)
                .then(response => response.json())
                .then(data => {
                    if (!data || !data.dates || !data.sales) {
                        throw new Error('Invalid data format received');
                    }

                    // Calculate 3-day moving average for forecasting
                    const movingAverages = calculateMovingAverage(data.sales, 3);
                    
                    // Generate forecast dates
                    const forecastDates = [];
                    const lastDate = new Date(data.dates[data.dates.length - 1]);
                    for (let i = 1; i <= parseInt(period); i++) {
                        const nextDate = new Date(lastDate);
                        nextDate.setDate(lastDate.getDate() + i);
                        forecastDates.push(nextDate.toISOString().split('T')[0]);
                    }

                    // Generate forecast values using the last moving average
                    const lastAverage = movingAverages[movingAverages.length - 1];
                    const forecastValues = Array(parseInt(period)).fill(lastAverage);

                    // Update the chart
                    if (salesForecastChart) {
                        salesForecastChart.destroy();
                    }

                    const ctx = document.getElementById('salesForecastChart').getContext('2d');
                    salesForecastChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: [...data.dates, ...forecastDates],
                            datasets: [
                                {
                                    label: 'Historical Sales',
                                    data: [...data.sales, ...Array(parseInt(period)).fill(null)],
                                    borderColor: 'rgb(75, 192, 192)',
                                    tension: 0.1
                                },
                                {
                                    label: 'Forecasted Sales',
                                    data: [...Array(data.sales.length).fill(null), ...forecastValues],
                                    borderColor: 'rgb(255, 99, 132)',
                                    borderDash: [5, 5],
                                    tension: 0.1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Sales Amount (₱)'
                                    },
                                    ticks: {
                                        callback: function(value) {
                                            return '₱' + value.toLocaleString();
                                        }
                                    }
                                }
                            },
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Sales Forecast'
                                }
                            }
                        }
                    });

                    // Update forecast summary
                    const lastSales = data.sales[data.sales.length - 1] || 0;
                    const growthRate = lastSales > 0 ? ((lastAverage - lastSales) / lastSales * 100).toFixed(2) : 0;
                    
                    document.getElementById('salesForecastSummary').innerHTML = `
                        <div class="forecast-summary">
                            <div class="mb-3">
                                <h6>Last Recorded Sales</h6>
                                <h3 class="text-primary">₱${lastSales.toLocaleString()}</h3>
                            </div>
                            <div class="mb-3">
                                <h6>Predicted Next Sales</h6>
                                <h3 class="text-success">₱${lastAverage.toLocaleString()}</h3>
                            </div>
                            <div>
                                <h6>Growth Rate</h6>
                                <h3 class="${growthRate >= 0 ? 'text-success' : 'text-danger'}">
                                    ${growthRate}%
                                </h3>
                            </div>
                        </div>
                    `;
                })
                .catch(error => {
                    console.error('Error loading forecast data:', error);
                    document.getElementById('salesForecastSummary').innerHTML = `
                        <div class="alert alert-danger">
                            Error loading forecast data. Please try again.
                        </div>
                    `;
                });
        }

        // Function to calculate moving average
        function calculateMovingAverage(data, windowSize) {
            const result = [];
            for (let i = 0; i < data.length; i++) {
                const start = Math.max(0, i - windowSize + 1);
                const end = i + 1;
                const window = data.slice(start, end);
                const average = window.reduce((a, b) => a + b, 0) / window.length;
                result.push(average);
            }
            return result;
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTable
            $('#salesTable').DataTable({
                "pageLength": 10,
                "order": [[0, "desc"]]
            });

            // Function to load chart data
            function loadChartData() {
                // Load sales trend data
                fetch('get_chart_data.php?type=sales_trend')
                    .then(response => response.json())
                    .then(data => {
                        salesTrendChart = new Chart(document.getElementById('salesTrendChart').getContext('2d'), {
                            type: 'line',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    label: 'Daily Sales',
                                    data: data.data,
                                    borderColor: 'rgb(75, 192, 192)',
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    tension: 0.1,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Sales Amount (₱)'
                                        },
                                        ticks: {
                                            callback: function(value) {
                                                return '₱' + value.toLocaleString();
                                            }
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    title: {
                                        display: true,
                                        text: 'Last 30 Days Sales Trend'
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => console.error('Error loading sales trend data:', error));

                // Load top products data
                fetch('get_chart_data.php?type=top_products')
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.error);
                        }

                        topProductsChart = new Chart(document.getElementById('topProductsChart').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    label: 'Number of Transactions',
                                    data: data.data,
                                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                    borderColor: 'rgb(54, 162, 235)',
                                    borderWidth: 1,
                                    yAxisID: 'y'
                                },
                                {
                                    label: 'Quantity Sold',
                                    data: data.quantities,
                                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                                    borderColor: 'rgb(255, 99, 132)',
                                    borderWidth: 1,
                                    yAxisID: 'y1'
                                }]
                            },
                            options: {
                                responsive: true,
                                interaction: {
                                    mode: 'index',
                                    intersect: false,
                                },
                                scales: {
                                    y: {
                                        type: 'linear',
                                        display: true,
                                        position: 'left',
                                        title: {
                                            display: true,
                                            text: 'Number of Transactions'
                                        }
                                    },
                                    y1: {
                                        type: 'linear',
                                        display: true,
                                        position: 'right',
                                        title: {
                                            display: true,
                                            text: 'Quantity Sold'
                                        },
                                        grid: {
                                            drawOnChartArea: false
                                        }
                                    },
                                    x: {
                                        ticks: {
                                            maxRotation: 45,
                                            minRotation: 45
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    title: {
                                        display: true,
                                        text: 'Top 10 Products by Quantity Sold'
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.dataset.label || '';
                                                if (label) {
                                                    label += ': ';
                                                }
                                                if (context.datasetIndex === 0) {
                                                    label += context.parsed.y + ' transactions';
                                                } else {
                                                    label += context.parsed.y + ' units';
                                                }
                                                return label;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error loading top products data:', error);
                        document.getElementById('topProductsChart').parentElement.innerHTML = `
                            <div class="alert alert-danger">
                                Error loading top products data. Please try again.
                            </div>
                        `;
                    });
            }

            // Load chart data when page loads
            loadChartData();

            // Load initial forecast data
            updateSalesForecast();
        });

    </script>

    <style>
        .forecast-summary {
            padding: 15px;
        }
        .forecast-summary h6 {
            color: #6c757d;
            margin-bottom: 5px;
        }
        .forecast-summary h3 {
            margin: 0;
            font-weight: bold;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .table-responsive {
            margin-top: 20px;
        }
    </style>
</body>
</html> 