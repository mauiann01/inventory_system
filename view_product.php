<?php include('db_connect.php');

if(!isset($_GET['id'])) {
    echo "<script>window.location.href='index.php?page=products';</script>";
    exit;
}

$product = $conn->query("SELECT p.*, c.name as category 
    FROM product_list p 
    INNER JOIN category_list c ON c.id = p.category_id 
    WHERE p.id = ".$_GET['id'])->fetch_assoc();

if(!$product) {
    echo "<script>window.location.href='index.php?page=products';</script>";
    exit;
}

// Get stock information
$inn = $conn->query("SELECT SUM(qty) as inn FROM inventory WHERE status='completed' AND type=1 AND product_id = ".$_GET['id'])->fetch_assoc()['inn'];
$out = $conn->query("SELECT SUM(qty) as `out` FROM inventory WHERE status='completed' AND type=2 AND product_id = ".$_GET['id'])->fetch_assoc()['out'];
$stock = $inn - $out;

// Get recent transactions
$transactions_query = "SELECT i.*, 
    CASE 
        WHEN i.type = 1 THEN 'Receiving'
        WHEN i.type = 2 THEN 'Sales'
        ELSE 'Unknown'
    END as transaction_type,
    DATE_FORMAT(i.date_updated, '%M %d, %Y %h:%i %p') as formatted_date
    FROM inventory i 
    WHERE i.product_id = ".$_GET['id']." 
    AND i.status = 'completed'
    ORDER BY i.date_updated DESC 
    LIMIT 10";

$transactions = $conn->query($transactions_query);

if(!$transactions) {
    die('Error in transactions query: ' . $conn->error);
}
?>

<style>
    .product-details {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    .product-header {
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }

    .product-title {
        margin: 0;
        color: #2c3e50;
        font-size: 24px;
    }

    .product-category {
        color: #7f8c8d;
        font-size: 16px;
    }

    .product-price {
        font-size: 24px;
        color: #27ae60;
        font-weight: bold;
    }

    .stock-status {
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 14px;
        font-weight: 500;
    }

    .stock-normal {
        background-color: #2ecc71;
        color: white;
    }

    .stock-warning {
        background-color: #f1c40f;
        color: #34495e;
    }

    .stock-danger {
        background-color: #e74c3c;
        color: white;
    }

    .transaction-item {
        padding: 10px;
        border-bottom: 1px solid #eee;
        transition: background-color 0.3s;
    }

    .transaction-item:hover {
        background-color: #f8f9fa;
    }

    .transaction-receiving {
        border-left: 4px solid #2ecc71;
    }

    .transaction-sales {
        border-left: 4px solid #e74c3c;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="product-details">
                <!-- Back Button and Title -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="<?php echo isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php?page=products' ?>" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                    <div class="d-flex">
                        <button class="btn btn-primary mr-2" onclick="window.location.href='index.php?page=manage_product&id=<?php echo $product['id'] ?>'">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-danger" onclick="delete_product(<?php echo $product['id'] ?>)">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </div>
                </div>

                <!-- Product Header -->
                <div class="product-header">
                    <h1 class="product-title"><?php echo $product['name'] ?></h1>
                    <div class="product-category">
                        <i class="fa fa-tag"></i> <?php echo $product['category'] ?>
                    </div>
                </div>

                <!-- Product Details -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Product Information</h5>
                                <p class="card-text"><?php echo $product['description'] ? $product['description'] : 'No description available.' ?></p>
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <h6>Price</h6>
                                        <div class="product-price">₱<?php echo number_format($product['price'], 2) ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Current Stock</h6>
                                        <span class="stock-status <?php 
                                            if($stock > 100) echo 'stock-normal';
                                            else if($stock > 0) echo 'stock-warning';
                                            else echo 'stock-danger';
                                        ?>">
                                            <?php echo $stock ?> units
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Transactions -->
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Recent Transactions</h5>
                                <?php 
                                if($transactions && $transactions->num_rows > 0): 
                                    while($row = $transactions->fetch_assoc()): 
                                ?>
                                        <div class="transaction-item transaction-<?php echo strtolower($row['transaction_type']) ?>">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><?php echo $row['transaction_type'] ?></strong>
                                                    <div class="text-muted small"><?php echo $row['formatted_date'] ?></div>
                                                </div>
                                                <div class="text-right">
                                                    <strong><?php echo $row['qty'] ?> units</strong>
                                                    <?php if($row['type'] == 1): ?>
                                                        <div class="text-success">+ Received</div>
                                                    <?php else: ?>
                                                        <div class="text-danger">- Sold</div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                <?php 
                                    endwhile; 
                                else: 
                                ?>
                                    <div class="text-center text-muted py-3">No transactions found</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Stock Summary -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Stock Summary</h5>
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        Total Received
                                        <span class="badge badge-success badge-pill"><?php echo $inn ? $inn : 0 ?></span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        Total Sold
                                        <span class="badge badge-danger badge-pill"><?php echo $out ? $out : 0 ?></span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        Current Stock
                                        <span class="badge badge-primary badge-pill"><?php echo $stock ?></span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        Stock Value
                                        <span class="badge badge-info badge-pill">₱<?php echo number_format($stock * $product['price'], 2) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Quick Actions</h5>
                                <div class="list-group">
                                    <a href="index.php?page=manage_receiving&product=<?php echo $product['id'] ?>" class="list-group-item list-group-item-action">
                                        <i class="fa fa-plus-circle text-success"></i> Add Stock
                                    </a>
                                    <a href="index.php?page=pos&product=<?php echo $product['id'] ?>" class="list-group-item list-group-item-action">
                                        <i class="fa fa-shopping-cart text-primary"></i> Add to Sale
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function delete_product($id){
    _conf("Are you sure you want to delete this product?", function() {
        start_load();
        $.ajax({
            url:'ajax.php?action=delete_product',
            method:'POST',
            data:{id:$id},
            success:function(resp){
                if(resp==1){
                    alert_toast("Product successfully deleted", 'success');
                    setTimeout(function(){
                        location.href = 'index.php?page=products';
                    },1500);
                }
            }
        });
    });
}
</script>
