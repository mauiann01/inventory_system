<?php
if(!isset($_SESSION)){
    session_start();
}
include('db_connect.php');
if(!isset($_SESSION['login_id']))
    header('location:login.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Demand Forecasting</title>
    <?php include('./header.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'topbar.php' ?>
    <?php include 'navbar.php' ?>

    <main class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-1"><b>Active Demand Forecasting</b></h4>
                                    <p class="mb-0 description">Track and analyze product demand patterns to optimize inventory management and predict future requirements.</p>
                                </div>
                                <div class="print-actions">
                                    <button type="button" class="btn btn-light" onclick="printReport()">
                                        <i class="fa fa-print"></i> Generate Demand Report
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="forecast-description mb-4">
                                <div class="alert alert-info">
                                    <h5><i class="fa fa-info-circle"></i> About Active Demand Forecasting</h5>
                                    <p>This tool helps you analyze and predict product demand based on historical data. Use the filters below to generate forecasts for specific time periods and make data-driven inventory decisions.</p>
                                    <ul class="mb-0">
                                        <li>View demand patterns across different products</li>
                                        <li>Identify high and low demand items</li>
                                        <li>Plan inventory based on predicted demand</li>
                                    </ul>
                                </div>
                            </div>

                            <form id="forecastForm" class="form-inline justify-content-center mb-4">
                                <div class="form-group">
                                    <label for="month" class="mr-2">Month:</label>
                                    <select id="month" name="month" class="form-control">
                                        <?php
                                        $months = [
                                            '01' => 'January', '02' => 'February', '03' => 'March',
                                            '04' => 'April', '05' => 'May', '06' => 'June',
                                            '07' => 'July', '08' => 'August', '09' => 'September',
                                            '10' => 'October', '11' => 'November', '12' => 'December'
                                        ];
                                        foreach ($months as $key => $value) {
                                            $selected = ($key == date('m')) ? 'selected' : '';
                                            echo "<option value='$key' $selected>$value</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group mx-3">
                                    <label for="year" class="mr-2">Year:</label>
                                    <select id="year" name="year" class="form-control">
                                        <?php
                                        $current_year = date('Y');
                                        for ($i = $current_year + 1; $i >= $current_year - 2; $i--) {
                                            $selected = ($i == $current_year) ? 'selected' : '';
                                            echo "<option value='$i' $selected>$i</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-primary" onclick="generateForecast()">
                                    <i class="fa fa-chart-line"></i> Generate Active Forecast
                                </button>
                            </form>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0"><i class="fa fa-chart-bar"></i> Demand Visualization</h5>
                                        </div>
                                        <div class="card-body chart-container">
                                            <canvas id="forecastChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0"><i class="fa fa-clipboard-list"></i> Active Forecast Summary</h5>
                                        </div>
                                        <div class="card-body" id="forecastSummary">
                                            <!-- Forecast summary will be displayed here -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fa fa-analytics"></i> Active Demand Analysis</h5>
                                </div>
                                <div class="card-body">
                                    <div class="demand-levels mb-3">
                                        <h6 class="mb-2">Demand Level Indicators:</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="demand-level-item">
                                                    <span class="badge bg-success">High Demand</span>
                                                    <small class="text-muted ml-2">More than 100 units - Requires immediate attention for stock maintenance</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="demand-level-item">
                                                    <span class="badge bg-warning">Moderate Demand</span>
                                                    <small class="text-muted ml-2">51-100 units - Regular monitoring needed</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="demand-level-item">
                                                    <span class="badge bg-danger">Low Demand</span>
                                                    <small class="text-muted ml-2">50 or fewer units - Consider inventory optimization</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="forecastTable">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Product Name</th>
                                                    <th>Active Demand</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="forecastTableBody">
                                                <!-- Table content will be generated by JavaScript -->
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
    </main>

    <script>
        let forecastChart;

        function generateForecast() {
            const month = document.getElementById('month').value;
            const year = document.getElementById('year').value;
            
            fetch(`get_data.php?month=${month}&year=${year}`)
                .then(response => response.json())
                .then(data => {
                    if (!data || data.length === 0) {
                        throw new Error('No data available');
                    }

                    // Extract item names and quantities
                    const itemNames = data.map(item => item.item_name);
                    const quantities = data.map(item => item.qty);

                    // Update the forecast chart
                    if (forecastChart) {
                        forecastChart.destroy();
                    }

                    const ctx = document.getElementById('forecastChart').getContext('2d');
                    forecastChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: itemNames,
                            datasets: [{
                                label: 'Active Demand',
                                data: quantities,
                                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                borderColor: 'rgb(75, 192, 192)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Quantity'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Products'
                                    }
                                }
                            },
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Active Demand by Product'
                                },
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });

                    // Update forecast summary
                    const totalQuantity = quantities.reduce((a, b) => a + b, 0);
                    const averageQuantity = totalQuantity / quantities.length;
                    const maxQuantity = Math.max(...quantities);
                    const maxProduct = itemNames[quantities.indexOf(maxQuantity)];
                    
                    document.getElementById('forecastSummary').innerHTML = `
                        <div class="forecast-summary">
                            <div class="mb-3">
                                <h6>Total Active Demand</h6>
                                <h3 class="text-primary">${totalQuantity} units</h3>
                            </div>
                            <div class="mb-3">
                                <h6>Average Demand per Product</h6>
                                <h3 class="text-success">${averageQuantity.toFixed(2)} units</h3>
                            </div>
                            <div>
                                <h6>Top Product</h6>
                                <h4 class="text-info">${maxProduct}</h4>
                                <p class="mb-0">${maxQuantity} units</p>
                            </div>
                        </div>
                    `;

                    // Update detailed analysis table
                    const tableBody = document.getElementById('forecastTableBody');
                    tableBody.innerHTML = '';
                    
                    data.forEach(item => {
                        let status = '';
                        if (item.qty > 100) {
                            status = '<span class="badge bg-success">High Demand</span>';
                        } else if (item.qty > 50) {
                            status = '<span class="badge bg-warning">Moderate Demand</span>';
                        } else {
                            status = '<span class="badge bg-danger">Low Demand</span>';
                        }

                        tableBody.innerHTML += `
                            <tr>
                                <td>${item.item_name}</td>
                                <td>${item.qty} units</td>
                                <td>${status}</td>
                            </tr>
                        `;
                    });
                })
                .catch(error => {
                    console.error('Error loading forecast data:', error);
                    document.getElementById('forecastSummary').innerHTML = `
                        <div class="alert alert-danger">
                            Error loading forecast data. Please try again.
                        </div>
                    `;
                });
        }

        function printReport() {
            const printWindow = window.open('', '_blank');
            const month = document.getElementById('month').options[document.getElementById('month').selectedIndex].text;
            const year = document.getElementById('year').value;
            
            // Get the forecast summary content
            const summaryContent = document.getElementById('forecastSummary').innerHTML;
            
            // Get the table content
            const tableContent = document.getElementById('forecastTableBody').innerHTML;
            
            // Get chart as image
            const chartCanvas = document.getElementById('forecastChart');
            const chartImage = chartCanvas.toDataURL('image/png');
            
            // Create print content with enhanced layout
            const printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Active Demand Forecast Report</title>
                    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
                    <style>
                        body {
                            font-family: 'Roboto', sans-serif;
                            padding: 40px;
                            color: #333;
                            line-height: 1.6;
                        }
                        .report-container {
                            max-width: 1000px;
                            margin: 0 auto;
                        }
                        .header {
                            text-align: center;
                            margin-bottom: 30px;
                            padding-bottom: 20px;
                            border-bottom: 2px solid #595959;
                        }
                        .header h2 {
                            color: #595959;
                            margin: 0;
                            font-size: 24px;
                            font-weight: 700;
                        }
                        .header p {
                            color: #666;
                            margin: 5px 0 0;
                            font-size: 16px;
                        }
                        .content {
                            margin-bottom: 30px;
                        }
                        .content h3 {
                            color: #595959;
                            font-size: 20px;
                            margin-bottom: 15px;
                            padding-bottom: 10px;
                            border-bottom: 1px solid #eee;
                        }
                        .chart-section {
                            text-align: center;
                            margin: 20px 0;
                            page-break-inside: avoid;
                        }
                        .chart-section img {
                            max-width: 100%;
                            height: auto;
                            margin: 10px 0;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin: 15px 0;
                            background-color: #fff;
                        }
                        th, td {
                            border: 1px solid #ddd;
                            padding: 12px;
                            text-align: left;
                        }
                        th {
                            background-color: #f8f9fa;
                            font-weight: 600;
                            color: #595959;
                        }
                        tr:nth-child(even) {
                            background-color: #f8f9fa;
                        }
                        .badge {
                            padding: 6px 12px;
                            border-radius: 4px;
                            font-size: 12px;
                            font-weight: 500;
                            display: inline-block;
                        }
                        .bg-success {
                            background: #28a745;
                            color: white;
                        }
                        .bg-warning {
                            background: #ffc107;
                            color: #000;
                        }
                        .bg-danger {
                            background: #dc3545;
                            color: white;
                        }
                        .forecast-summary {
                            background-color: #f8f9fa;
                            padding: 20px;
                            border-radius: 8px;
                            margin-bottom: 20px;
                        }
                        .forecast-summary h6 {
                            color: #595959;
                            margin: 0 0 5px 0;
                            font-size: 14px;
                            font-weight: 600;
                        }
                        .forecast-summary h3, .forecast-summary h4 {
                            margin: 0 0 15px 0;
                            color: #333;
                        }
                        .print-footer {
                            text-align: center;
                            margin-top: 30px;
                            padding-top: 20px;
                            border-top: 1px solid #eee;
                            font-size: 12px;
                            color: #666;
                        }
                        @media print {
                            body {
                                padding: 20px;
                            }
                            .no-print {
                                display: none;
                            }
                            .content {
                                page-break-inside: avoid;
                            }
                            .chart-section {
                                margin: 30px 0;
                            }
                        }
                        .demand-levels {
                            margin: 20px 0;
                            padding: 15px;
                            background-color: #f8f9fa;
                            border-radius: 8px;
                        }
                        .demand-level-item {
                            margin-bottom: 10px;
                            display: flex;
                            align-items: center;
                        }
                        .demand-level-item .badge {
                            margin-right: 10px;
                            min-width: 100px;
                            text-align: center;
                        }
                        .text-muted {
                            color: #666;
                            font-size: 14px;
                        }
                    </style>
                </head>
                <body>
                    <div class="report-container">
                        <div class="header">
                            <h2>Active Demand Forecast Report</h2>
                            <p>Generated for ${month} ${year}</p>
                        </div>

                        <div class="content">
                            <h3>Forecast Summary</h3>
                            ${summaryContent}
                        </div>

                        <div class="content chart-section">
                            <h3>Demand Visualization</h3>
                            <img src="${chartImage}" alt="Demand Forecast Chart">
                        </div>

                        <div class="content">
                            <h3>Detailed Demand Analysis</h3>
                            <div class="demand-levels">
                                <h4 style="margin-bottom: 15px; color: #595959;">Demand Level Indicators:</h4>
                                <div class="demand-level-item">
                                    <span class="badge bg-success">High Demand</span>
                                    <span class="text-muted">More than 100 units - Requires immediate attention for stock maintenance</span>
                                </div>
                                <div class="demand-level-item">
                                    <span class="badge bg-warning">Moderate Demand</span>
                                    <span class="text-muted">51-100 units - Regular monitoring needed</span>
                                </div>
                                <div class="demand-level-item">
                                    <span class="badge bg-danger">Low Demand</span>
                                    <span class="text-muted">50 or fewer units - Consider inventory optimization</span>
                                </div>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Active Demand</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${tableContent}
                                </tbody>
                            </table>
                        </div>

                        <div class="print-footer">
                            <p>Report generated on ${new Date().toLocaleString()}</p>
                        </div>

                        <div class="no-print" style="text-align: center; margin-top: 30px;">
                            <button onclick="window.print()" style="padding: 10px 20px; margin: 0 10px; cursor: pointer;">Print Report</button>
                            <button onclick="window.close()" style="padding: 10px 20px; margin: 0 10px; cursor: pointer;">Close</button>
                        </div>
                    </div>
                </body>
                </html>
            `;
            
            printWindow.document.write(printContent);
            printWindow.document.close();
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Generate initial forecast
            generateForecast();
        });
    </script>

    <style>
        .main-content {
            padding: 15px;
            margin-left: 250px;
            margin-top: 56px;
        }
        .description {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .forecast-description {
            max-width: 1200px;
            margin: 0 auto;
        }
        .forecast-summary {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .forecast-summary h6 {
            color: #495057;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .forecast-summary h3, .forecast-summary h4 {
            margin: 0;
            font-weight: bold;
            color: #212529;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1rem;
            background: #fff;
            border: 1px solid rgba(0,0,0,.125);
        }
        .card-header {
            padding: 1rem;
        }
        .card-header.bg-primary {
            background-color: #595959 !important;
        }
        .card-body {
            padding: 1.25rem;
        }
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
            background-color: #fff;
            padding: 15px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .form-inline {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin: 0.5rem;
            display: flex;
            align-items: center;
        }
        .form-control {
            min-width: 120px;
            border: 1px solid #ced4da;
        }
        .btn-primary {
            background-color: #595959;
            border-color: #595959;
            padding: 0.375rem 1rem;
        }
        .btn-primary:hover {
            background-color: #404040;
            border-color: #404040;
        }
        .btn-light {
            background-color: #fff;
            border-color: #dee2e6;
        }
        .btn-light:hover {
            background-color: #e9ecef;
        }
        .badge {
            padding: 0.5em 1em;
            font-size: 85%;
            font-weight: 600;
        }
        .alert-info {
            background-color: #e3f2fd;
            border-color: #bee5eb;
            color: #0c5460;
        }
        .alert-info h5 {
            color: #0c5460;
            margin-bottom: 0.5rem;
        }
        .alert-info ul {
            padding-left: 1.25rem;
            margin-top: 0.5rem;
        }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            .form-inline {
                flex-direction: column;
            }
            .form-group {
                margin: 0.5rem 0;
            }
            .chart-container {
                height: 300px;
            }
            .description {
                display: none;
            }
        }
        @media print {
            .main-content {
                margin: 0;
                padding: 0;
            }
            .card {
                border: none;
                box-shadow: none;
            }
            .btn-primary, .forecast-description, .form-inline {
                display: none;
            }
            .chart-container {
                page-break-inside: avoid;
            }
        }
        .demand-levels {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .demand-level-item {
            margin: 10px 0;
            display: flex;
            align-items: center;
        }
        .demand-level-item .badge {
            margin-right: 10px;
            min-width: 100px;
            text-align: center;
        }
        .text-muted {
            color: #6c757d;
            font-size: 0.875rem;
        }
    </style>
</body>
</html>
