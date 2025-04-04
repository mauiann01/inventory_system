<?php include('db_connect.php');?>

<style>
    /* Card Styling */
    .card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .card-header {
        background: linear-gradient(45deg, #2c3e50, #3498db);
        color: white;
        border-radius: 10px 10px 0 0 !important;
        padding: 15px 20px;
        font-weight: 600;
    }

    /* Form Styling */
    .form-control {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 8px 12px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    /* Table Styling */
    .table {
        margin-bottom: 0;
        width: 100% !important;
    }

    .table thead th {
        background: linear-gradient(45deg, #f8f9fa, #ffffff);
        color: #2c3e50;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        padding: 12px 8px;
        white-space: nowrap;
    }

    .table tbody td {
        padding: 12px 8px;
        vertical-align: middle;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(52, 152, 219, 0.05);
        transition: background-color 0.3s ease;
    }

    /* DataTables specific styling */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_processing,
    .dataTables_wrapper .dataTables_paginate {
        margin: 10px 0;
        color: #2c3e50;
    }

    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 6px 12px;
        margin-left: 8px;
    }

    .dataTables_wrapper .dt-buttons {
        margin: 10px 0;
    }

    .dataTables_wrapper .dt-buttons .dt-button {
        background: linear-gradient(45deg, #3498db, #2980b9);
        color: white;
        border: none;
        border-radius: 4px;
        padding: 6px 12px;
        margin-right: 5px;
        transition: all 0.3s ease;
    }

    .dataTables_wrapper .dt-buttons .dt-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    /* Button Styling */
    .btn {
        border-radius: 4px;
        font-weight: 500;
        padding: 8px 16px;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(45deg, #3498db, #2980b9);
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .btn-danger {
        background: linear-gradient(45deg, #e74c3c, #c0392b);
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .status-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }

    .status-active {
        background-color: #2ecc71;
    }

    .status-warning {
        background-color: #f1c40f;
    }

    .status-danger {
        background-color: #e74c3c;
    }

    /* Product List Styles */
    .stats-card {
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    .stats-card:hover {
        transform: translateY(-2px);
    }
    .stats-icon {
        font-size: 2em;
        margin-bottom: 10px;
    }
    .stats-value {
        font-size: 1.5em;
        font-weight: bold;
    }
    .stats-label {
        color: #666;
        font-size: 0.9em;
    }
    .alert-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        padding: 3px 6px;
        font-size: 0.7em;
    }
    .scanner-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }
    .scanner-button {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #007bff;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        transition: all 0.3s;
    }
    .scanner-button:hover {
        transform: scale(1.1);
    }
    .filter-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .stock-status {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.85em;
        font-weight: 500;
    }
    .stock-normal { background: #d4edda; color: #155724; }
    .stock-low { background: #fff3cd; color: #856404; }
    .stock-out { background: #f8d7da; color: #721c24; }
    .table-responsive { overflow-x: auto; }
    
    /* Status Indicators */
    .status-indicator {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 5px;
    }
    .status-success { background: #2ecc71; }
    .status-warning { background: #f1c40f; }
    .status-danger { background: #e74c3c; }

    /* Product Icons */
    .product-icon {
        transition: all 0.3s ease;
    }
    .product-icon:hover {
        transform: scale(1.1);
    }

    /* Progress Bar */
    .progress {
        background-color: #f8f9fa;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
    }
    .progress-bar {
        transition: width 0.6s ease;
    }

    /* Table Styles */
    #productTable {
        border-collapse: separate;
        border-spacing: 0 8px;
        margin-top: -8px;
    }
    #productTable tbody tr {
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    #productTable tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    #productTable td {
        vertical-align: middle;
        padding: 12px;
    }

    /* Button Group */
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        margin: 0 2px;
    }
    .btn-group .btn i {
        font-size: 0.875rem;
    }

    /* Category Badge */
    .badge-info {
        padding: 5px 10px;
        font-weight: 500;
    }

    /* DataTables Custom Styling */
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 4px 8px;
    }
    .dt-buttons {
        margin-bottom: 1rem;
    }
    .dt-buttons .btn {
        margin-right: 0.5rem;
    }
</style>

<div class="container-fluid">
    <div class="col-lg-12">
        <?php 
        // Get category filter if set
        $category_filter = isset($_GET['category']) ? " AND category_id = ".$_GET['category'] : "";
        
        // Get product statistics
        $stats_query = $conn->query("SELECT 
            COUNT(DISTINCT p.id) as total_products,
            COUNT(DISTINCT CASE 
                WHEN (
                    COALESCE((SELECT SUM(qty) FROM inventory WHERE type=1 AND product_id=p.id AND status='completed'), 0) -
                    COALESCE((SELECT SUM(qty) FROM inventory WHERE type=2 AND product_id=p.id AND status='completed'), 0)
                ) <= 100 AND (
                    COALESCE((SELECT SUM(qty) FROM inventory WHERE type=1 AND product_id=p.id AND status='completed'), 0) -
                    COALESCE((SELECT SUM(qty) FROM inventory WHERE type=2 AND product_id=p.id AND status='completed'), 0)
                ) > 0
                THEN p.id 
            END) as low_stock_count,
            COALESCE(SUM(
                p.price * (
                    COALESCE((SELECT SUM(qty) FROM inventory WHERE type=1 AND product_id=p.id AND status='completed'), 0) -
                    COALESCE((SELECT SUM(qty) FROM inventory WHERE type=2 AND product_id=p.id AND status='completed'), 0)
                )
            ), 0) as total_value
        FROM product_list p
        WHERE 1=1 ".$category_filter);

        $stats = $stats_query->fetch_assoc();
        ?>

        <!-- Statistics Row -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="mb-0"><?php echo $stats['total_products']; ?></h3>
                        <p class="text-muted">Total Products</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="mb-0"><?php echo $stats['low_stock_count']; ?></h3>
                        <p class="text-muted">Low Stock Items</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="mb-0">₱<?php echo number_format($stats['total_value'], 2); ?></h3>
                        <p class="text-muted">Total Inventory Value</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Table Panel -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="btn-group mr-3">
                                    <button class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-file-import"></i> Import/Export
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#importModal">
                                            <i class="fa fa-file-upload"></i> Import Products
                                        </a>
                                        <a class="dropdown-item export-excel" href="#">
                                            <i class="fa fa-file-excel"></i> Export to Excel
                                        </a>
                                        <a class="dropdown-item export-template" href="#">
                                            <i class="fa fa-file-download"></i> Download Template
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="bulk-actions" style="display:none">
                                <button class="btn btn-warning btn-sm bulk-edit-stock"><i class="fa fa-edit"></i> Update Stock</button>
                                <button class="btn btn-info btn-sm export-qr"><i class="fa fa-qrcode"></i> Export QR</button>
                                <button class="btn btn-danger btn-sm bulk-delete"><i class="fa fa-trash"></i> Delete</button>
                                <span class="low-stock-alert badge badge-warning" style="display:none">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    Low Stock Alert: <span class="low-stock-count">0</span>
                                </span>
                            </div>
                            <?php if(isset($_GET['category'])): ?>
                            <a href="index.php?page=categories" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left"></i> Back to Categories
                            </a>
                            <?php else: ?>
                            <h4 class="mb-0"><i class="fa fa-list"></i> Product List</h4>
                            <?php endif; ?>
                            <a href="index.php?page=products" class="btn btn-primary btn-sm <?php echo !isset($_GET['category']) ? 'd-none' : '' ?>">
                                <i class="fa fa-list"></i> Show All Products
                            </a>
                        </div>
                        <?php if(isset($_GET['category'])): 
                        $cat = $conn->query("SELECT name FROM category_list WHERE id = ".$_GET['category'])->fetch_assoc();
                        ?>
                        <div class="alert alert-info mb-0">
                            <i class="fa fa-filter"></i> Showing products in category: <strong><?php echo $cat['name']; ?></strong>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <!-- Charts Section -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Stock Movement History</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="stockChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Sales by Category</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="categoryChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Statistics Section -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="stats-card bg-primary text-white">
                                    <div class="stats-icon"><i class="fa fa-box"></i></div>
                                    <?php 
                                        $result = $conn->query("SELECT COUNT(*) as count FROM product_list");
                                        if ($result === false) {
                                            die("Query failed: " . $conn->error);
                                        }
                                        $total_products = $result->fetch_assoc()['count'];
                                    ?>
                                    <div class="stats-value"><?php echo number_format($total_products); ?></div>
                                    <div class="stats-label">Total Products</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card bg-success text-white">
                                    <div class="stats-icon"><i class="fa fa-check-circle"></i></div>
                                    <?php 
                                        $result = $conn->query("SELECT COUNT(*) as count FROM product_list p 
                                            WHERE (
                                                COALESCE((SELECT SUM(qty) FROM inventory WHERE type=1 AND product_id=p.id AND status='completed'), 0) -
                                                COALESCE((SELECT SUM(qty) FROM inventory WHERE type=2 AND product_id=p.id AND status='completed'), 0)
                                            ) > 0");
                                        if ($result === false) {
                                            die("Query failed: " . $conn->error);
                                        }
                                        $in_stock = $result->fetch_assoc()['count'];
                                    ?>
                                    <div class="stats-value"><?php echo number_format($in_stock); ?></div>
                                    <div class="stats-label">In Stock</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card bg-warning text-white">
                                    <div class="stats-icon"><i class="fa fa-exclamation-triangle"></i></div>
                                    <?php 
                                        $result = $conn->query("SELECT COUNT(*) as count FROM product_list p 
                                            WHERE (
                                                COALESCE((SELECT SUM(qty) FROM inventory WHERE type=1 AND product_id=p.id AND status='completed'), 0) -
                                                COALESCE((SELECT SUM(qty) FROM inventory WHERE type=2 AND product_id=p.id AND status='completed'), 0)
                                            ) <= 100 AND (
                                                COALESCE((SELECT SUM(qty) FROM inventory WHERE type=1 AND product_id=p.id AND status='completed'), 0) -
                                                COALESCE((SELECT SUM(qty) FROM inventory WHERE type=2 AND product_id=p.id AND status='completed'), 0)
                                            ) > 0");
                                        if ($result === false) {
                                            die("Query failed: " . $conn->error);
                                        }
                                        $low_stock = $result->fetch_assoc()['count'];
                                    ?>
                                    <div class="stats-value"><?php echo number_format($low_stock); ?></div>
                                    <div class="stats-label">Low Stock</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card bg-danger text-white">
                                    <div class="stats-icon"><i class="fa fa-times-circle"></i></div>
                                    <?php 
                                        $result = $conn->query("SELECT COUNT(*) as count FROM product_list p 
                                            WHERE (
                                                COALESCE((SELECT SUM(qty) FROM inventory WHERE type=1 AND product_id=p.id AND status='completed'), 0) -
                                                COALESCE((SELECT SUM(qty) FROM inventory WHERE type=2 AND product_id=p.id AND status='completed'), 0)
                                            ) = 0");
                                        if ($result === false) {
                                            die("Query failed: " . $conn->error);
                                        }
                                        $out_stock = $result->fetch_assoc()['count'];
                                    ?>
                                    <div class="stats-value"><?php echo number_format($out_stock); ?></div>
                                    <div class="stats-label">Out of Stock</div>
                                </div>
                            </div>
                        </div>
                        <!-- Advanced Filter Section -->
                        <div class="filter-section mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="text" id="searchName" class="form-control" placeholder="Search by name...">
                                </div>
                                <div class="col-md-2">
                                    <select id="categoryFilter" class="form-control">
                                        <option value="">All Categories</option>
                                        <?php 
                                        $cat_query = $conn->query("SELECT * FROM category_list ORDER BY name ASC");
                                        while($crow = $cat_query->fetch_assoc()):
                                        ?>
                                        <option value="<?php echo $crow['id'] ?>"><?php echo $crow['name'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="stockStatus" class="form-control">
                                        <option value="">All Stock Status</option>
                                        <option value="normal">In Stock</option>
                                        <option value="low">Low Stock</option>
                                        <option value="out">Out of Stock</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="number" id="minPrice" class="form-control" placeholder="Min Price">
                                        <input type="number" id="maxPrice" class="form-control" placeholder="Max Price">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button id="resetFilters" class="btn btn-secondary">Reset Filters</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="productTable">
                                <thead>
                                    <tr>
                                        <th style="width:3%">
                                            <input type="checkbox" id="selectAll" class="select-checkbox">
                                        </th>
                                        <th style="width:22%">Product Information</th>
                                        <th style="width:15%">Category</th>
                                        <th style="width:15%">Price</th>
                                        <th style="width:25%">Stock Status</th>
                                        <th style="width:20%">Quick Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $i = 1;
                                    $products = $conn->query("SELECT p.*, c.name as category,
                                        (SELECT SUM(qty) FROM inventory WHERE status='completed' AND type=1 AND product_id = p.id) as total_in,
                                        (SELECT SUM(qty) FROM inventory WHERE status='completed' AND type=2 AND product_id = p.id) as total_out,
                                        (SELECT date_updated FROM inventory WHERE product_id = p.id ORDER BY date_updated DESC LIMIT 1) as last_movement
                                        FROM product_list p 
                                        INNER JOIN category_list c ON c.id = p.category_id
                                        WHERE 1=1 ".$category_filter." 
                                        ORDER BY p.name ASC");
                                    while($row = $products->fetch_assoc()):
                                        $stock = ($row['total_in'] ?? 0) - ($row['total_out'] ?? 0);
                                        $stock_value = $stock * $row['price'];
                                        $stock_status = $stock <= 0 ? 'danger' : ($stock <= 100 ? 'warning' : 'success');
                                        $last_movement = $row['last_movement'] ? date('M d, Y', strtotime($row['last_movement'])) : 'No movement';
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="select-checkbox product-select" data-id="<?php echo $row['id']; ?>">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="product-icon mr-2 rounded-circle text-white d-flex align-items-center justify-content-center" 
                                                    style="width: 40px; height: 40px; background: <?php echo $stock_status == 'danger' ? '#e74c3c' : ($stock_status == 'warning' ? '#f1c40f' : '#2ecc71'); ?>">
                                                    <i class="fa fa-box"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?php echo $row['name']; ?></h6>
                                                    <small class="text-muted"><?php 
                                                        echo !empty($row['description']) ? substr($row['description'], 0, 50) . '...' : 'No description';
                                                    ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-info"><?php echo $row['category']; ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="font-weight-bold">₱<?php echo number_format($row['price'], 2); ?></span>
                                                <small class="text-muted">Value: ₱<?php echo number_format($stock_value, 2); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    <div class="d-flex align-items-center">
                                                        <span class="status-indicator status-<?php echo $stock_status; ?> mr-2" 
                                                            title="<?php echo $stock_status == 'danger' ? 'Out of Stock' : ($stock_status == 'warning' ? 'Low Stock' : 'Stock OK'); ?>"></span>
                                                        <span class="font-weight-bold"><?php echo $stock; ?> units</span>
                                                    </div>
                                                    <small class="text-muted">Last movement: <?php echo $last_movement; ?></small>
                                                </div>
                                                <div class="progress flex-grow-1" style="height: 5px;">
                                                    <?php 
                                                    $stock_percentage = $stock <= 0 ? 0 : ($stock <= 100 ? 50 : 100);
                                                    $progress_class = $stock_status == 'danger' ? 'bg-danger' : ($stock_status == 'warning' ? 'bg-warning' : 'bg-success');
                                                    ?>
                                                    <div class="progress-bar <?php echo $progress_class; ?>" 
                                                        role="progressbar" 
                                                        style="width: <?php echo $stock_percentage; ?>%" 
                                                        aria-valuenow="<?php echo $stock_percentage; ?>" 
                                                        aria-valuemin="0" 
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="index.php?page=view_product&id=<?php echo $row['id'] ?>" 
                                                    class="btn btn-sm btn-info" title="View Details">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="index.php?page=manage_receiving&product=<?php echo $row['id'] ?>" 
                                                    class="btn btn-sm btn-success" title="Add Stock">
                                                    <i class="fa fa-plus"></i>
                                                </a>
                                                <a href="index.php?page=pos&product=<?php echo $row['id'] ?>" 
                                                    class="btn btn-sm btn-primary" title="Add to Sale">
                                                    <i class="fa fa-shopping-cart"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Table Panel -->
        </div>
    </div>
</div>

<!-- Scanner Button -->
<div class="scanner-container">
    <div class="scanner-button" data-toggle="tooltip" title="Scan QR Code">
        <i class="fa fa-qrcode"></i>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Products</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="importForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Excel File</label>
                        <input type="file" class="form-control" name="import_file" accept=".xlsx,.xls,.csv" required>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="updateExisting" name="update_existing">
                            <label class="custom-control-label" for="updateExisting">Update existing products</label>
                        </div>
                    </div>
                </form>
                <div class="alert alert-info">
                    <small>Download the template first to see the required format.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="importSubmit">Import</button>
            </div>
        </div>
    </div>
</div>

<!-- Price History Modal -->
<div class="modal fade" id="priceHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Price History</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <canvas id="priceHistoryChart" height="300"></canvas>
                <div class="table-responsive mt-3">
                    <table class="table table-sm" id="priceHistoryTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Old Price</th>
                                <th>New Price</th>
                                <th>Changed By</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Variants Modal -->
<div class="modal fade" id="variantsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Product Variants</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="variantForm">
                    <input type="hidden" id="variantProductId">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="variantName" placeholder="Variant Name (e.g., Size)">
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary" id="addVariantOption">
                                <i class="fa fa-plus"></i> Add Option
                            </button>
                        </div>
                    </div>
                    <div id="variantOptions"></div>
                </form>
                <div class="table-responsive mt-3">
                    <table class="table table-sm" id="variantsTable">
                        <thead>
                            <tr>
                                <th>Variant</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveVariants">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Scanner Modal -->
<div class="modal fade" id="scannerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan QR Code</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="qr-reader"></div>
            </div>
        </div>
    </div>
</div>

<!-- Add Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    // Initialize Charts
    function initCharts() {
        // Stock Movement Chart
        $.ajax({
            url: 'ajax.php?action=get_stock_movement',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('Server error:', response.message);
                    $('#stockChart').closest('.card').html('<div class="alert alert-danger m-3">' + response.message + '</div>');
                    return;
                }

                if (response.labels && response.stock_in && response.stock_out) {
                    let ctx = document.getElementById('stockChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: response.labels,
                            datasets: [{
                                label: 'Stock In',
                                data: response.stock_in,
                                borderColor: '#28a745',
                                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                                fill: true,
                                tension: 0.4
                            }, {
                                label: 'Stock Out',
                                data: response.stock_out,
                                borderColor: '#dc3545',
                                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                                fill: true,
                                tension: 0.4
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
                                        text: 'Date'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top'
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false
                                }
                            }
                        }
                    });
                } else {
                    console.error('Invalid data format received');
                    $('#stockChart').closest('.card').html('<div class="alert alert-info m-3">No stock movement data available</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching stock movement data:', error);
                $('#stockChart').closest('.card').html('<div class="alert alert-danger m-3">Error loading stock movement data</div>');
            }
        });

        // Category Sales Chart
        $.ajax({
            url: 'ajax.php?action=get_category_sales',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('Server error:', response.message);
                    $('#categoryChart').closest('.card').html('<div class="alert alert-danger m-3">' + response.message + '</div>');
                    return;
                }

                if (response.labels && response.values) {
                    let ctx = document.getElementById('categoryChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: response.labels,
                            datasets: [{
                                data: response.values,
                                backgroundColor: [
                                    '#007bff',
                                    '#28a745',
                                    '#ffc107',
                                    '#dc3545',
                                    '#17a2b8',
                                    '#6c757d',
                                    '#6610f2',
                                    '#fd7e14',
                                    '#20c997',
                                    '#e83e8c'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right'
                                },
                                title: {
                                    display: true,
                                    text: 'Sales Distribution by Category'
                                }
                            }
                        }
                    });
                } else {
                    console.error('Invalid data format received');
                    $('#categoryChart').closest('.card').html('<div class="alert alert-info m-3">No sales data available</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching category sales data:', error);
                $('#categoryChart').closest('.card').html('<div class="alert alert-danger m-3">Error loading sales data</div>');
            }
        });
    }

    // Initialize charts when document is ready
    initCharts();

    // Function to check and display notifications
    function checkProductNotifications() {
        $.ajax({
            url: 'notifications.php?action=get_notifications',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response && Array.isArray(response) && response.length > 0) {
                    $('#notification_area').empty();
                    
                    // Sort notifications by type (danger first, then warning) and timestamp
                    response.sort((a, b) => {
                        if (a.type === 'danger' && b.type !== 'danger') return -1;
                        if (a.type !== 'danger' && b.type === 'danger') return 1;
                        return new Date(b.timestamp) - new Date(a.timestamp);
                    });
                    
                    // Group notifications by type
                    const groupedNotifications = {
                        danger: [],
                        warning: []
                    };
                    
                    response.forEach(notification => {
                        groupedNotifications[notification.type].push(notification);
                    });
                    
                    // Display notifications in groups
                    Object.entries(groupedNotifications).forEach(([type, notifications]) => {
                        if (notifications.length > 0) {
                            const groupHeader = `
                                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                                    <strong>${type === 'danger' ? 'Out of Stock Items' : 'Low Stock Items'}</strong>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `;
                            $('#notification_area').append(groupHeader);
                            
                            notifications.forEach(notification => {
                                const alertContent = `
                                    <div class="alert alert-${notification.type} alert-dismissible fade show" role="alert">
                                        ${notification.message}
                                        ${notification.has_order_button ? 
                                            `<button type="button" class="btn btn-sm btn-${notification.type === 'danger' ? 'danger' : 'warning'} ml-2" 
                                                onclick="location.href='index.php?page=manage_stock&id=${notification.product_id}'">
                                                Manage Stock
                                            </button>` : ''}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                `;
                                $('#notification_area').append(alertContent);
                            });
                        }
                    });
                    
                    // Animate notifications
                    $('#notification_area .alert').each(function(index) {
                        $(this).delay(index * 200).fadeIn(300);
                    });
                    
                    // Show toast notification for new alerts
                    const outOfStock = groupedNotifications.danger.length;
                    const lowStock = groupedNotifications.warning.length;
                    
                    if (outOfStock > 0 || lowStock > 0) {
                        let toastMessage = '';
                        if (outOfStock > 0 && lowStock > 0) {
                            toastMessage = `You have ${outOfStock} out of stock items and ${lowStock} low stock items!`;
                        } else if (outOfStock > 0) {
                            toastMessage = `You have ${outOfStock} out of stock items!`;
                        } else if (lowStock > 0) {
                            toastMessage = `You have ${lowStock} low stock items!`;
                        }
                        
                        if (toastMessage) {
                            alert_toast(toastMessage, 'warning');
                        }
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching notifications:', error);
                $('#notification_area').html(`
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Error loading notifications. Please try again later.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `);
            }
        });
    }

    // Initial check
    checkProductNotifications();
    
    // Set up periodic checks every 5 minutes
    setInterval(checkProductNotifications, 300000);
});
</script>
