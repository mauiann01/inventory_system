<?php include('db_connect.php');?>
<?php
// Get category statistics
$total_categories = $conn->query("SELECT COUNT(*) as count FROM category_list")->fetch_assoc()['count'];

// Get product statistics per category
$category_stats = array();
$stats_query = $conn->query("SELECT 
    c.id, 
    c.name,
    COUNT(DISTINCT p.id) as product_count,
    COALESCE(SUM(
        p.price * (
            COALESCE((SELECT SUM(qty) FROM inventory WHERE type=1 AND product_id=p.id AND status='completed'), 0) -
            COALESCE((SELECT SUM(qty) FROM inventory WHERE type=2 AND product_id=p.id AND status='completed'), 0)
        )
    ), 0) as total_value,
    COUNT(DISTINCT CASE 
        WHEN (
            COALESCE((SELECT SUM(qty) FROM inventory WHERE type=1 AND product_id=p.id AND status='completed'), 0) -
            COALESCE((SELECT SUM(qty) FROM inventory WHERE type=2 AND product_id=p.id AND status='completed'), 0)
        ) <= 100 
        THEN p.id 
    END) as low_stock_count
FROM category_list c
LEFT JOIN product_list p ON p.category_id = c.id
GROUP BY c.id, c.name");

if(!$stats_query) {
    die('Error in stats query: ' . $conn->error);
}

while($row = $stats_query->fetch_assoc()) {
    $category_stats[$row['id']] = $row;
}
?>

<style>
    /* Main Card Styling */
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
    }

    .table thead th {
        background: #f8f9fa;
        color: #2c3e50;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(52, 152, 219, 0.05);
        transition: background-color 0.3s ease;
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

    .btn-default {
        background: #f8f9fa;
        color: #2c3e50;
        border: 1px solid #ddd;
    }

    .btn-default:hover {
        background: #e9ecef;
    }

    /* Stats Card Styling */
    .stats-card {
        background: #fff;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-5px);
    }

    .stats-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }

    .stats-primary {
        background: linear-gradient(45deg, #3498db, #2980b9);
    }

    .stats-success {
        background: linear-gradient(45deg, #2ecc71, #27ae60);
    }

    .stats-warning {
        background: linear-gradient(45deg, #f1c40f, #f39c12);
    }

    .stats-danger {
        background: linear-gradient(45deg, #e74c3c, #c0392b);
    }

    .stats-value {
        font-size: 24px;
        font-weight: bold;
        margin: 10px 0;
    }

    .stats-label {
        color: #7f8c8d;
        font-size: 14px;
    }

    /* Category Status Indicators */
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
</style>

<div class="container-fluid">
    <div class="col-lg-12">
        <!-- Statistics Row -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon stats-primary">
                        <i class="fa fa-list text-white"></i>
                    </div>
                    <div class="stats-value"><?php echo $total_categories; ?></div>
                    <div class="stats-label">Total Categories</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon stats-success">
                        <i class="fa fa-box text-white"></i>
                    </div>
                    <div class="stats-value"><?php 
                        $total_products = 0;
                        foreach($category_stats as $stat) {
                            $total_products += $stat['product_count'];
                        }
                        echo $total_products;
                    ?></div>
                    <div class="stats-label">Total Products</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon stats-warning">
                        <i class="fa fa-exclamation-triangle text-white"></i>
                    </div>
                    <div class="stats-value"><?php 
                        $total_low_stock = 0;
                        foreach($category_stats as $stat) {
                            $total_low_stock += $stat['low_stock_count'];
                        }
                        echo $total_low_stock;
                    ?></div>
                    <div class="stats-label">Low Stock Items</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon stats-danger">
                        <i class="fa fa-dollar-sign text-white"></i>
                    </div>
                    <div class="stats-value">₱<?php 
                        $total_value = 0;
                        foreach($category_stats as $stat) {
                            $total_value += $stat['total_value'];
                        }
                        echo number_format($total_value, 2);
                    ?></div>
                    <div class="stats-label">Total Inventory Value</div>
                </div>
            </div>
        </div>

        <div class="row">
			<!-- FORM Panel -->
			<div class="col-md-4">
			<form action="" id="manage-category">
				<div class="card">
					<div class="card-header">
						<h4 class="mb-0"><i class="fa fa-list"></i> Category Form</h4>
				  	</div>
					<div class="card-body">
						<input type="hidden" name="id">
						<div class="form-group">
							<label class="control-label">Category Name</label>
							<input type="text" class="form-control" name="name" placeholder="Enter category name" required>
						</div>
					</div>
					<div class="card-footer bg-light">
						<div class="row">
							<div class="col-md-12 text-right">
								<button class="btn btn-default" type="button" onclick="$('#manage-category').get(0).reset()">Cancel</button>
								<button class="btn btn-primary">Save</button>
							</div>
						</div>
					</div>
				</div>
			</form>
			</div>
			<!-- FORM Panel -->

			<!-- Table Panel -->
			<div class="col-md-8">
				<div class="card">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h4 class="mb-0"><i class="fa fa-table"></i> Category List</h4>
						<a href="index.php?page=products" class="btn btn-primary btn-sm">
							<i class="fa fa-boxes"></i> View All Products
						</a>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-bordered table-hover" id="categoryTable">
							<thead class="thead-dark">
								<tr>
									<th class="text-center" style="width: 10%">#</th>
									<th class="text-center" style="width: 60%">Category Name</th>
									<th class="text-center" style="width: 30%">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$cats = $conn->query("SELECT * FROM category_list order by name asc");
								while($row=$cats->fetch_assoc()):
								    $stats = $category_stats[$row['id']] ?? array(
								        'product_count' => 0,
								        'total_value' => 0,
								        'low_stock_count' => 0
								    );
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td>
										<div class="d-flex align-items-center justify-content-between">
											<div>
												<?php 
												    if($stats['low_stock_count'] > 0) {
												        echo '<span class="status-indicator status-warning" title="Has low stock items"></span>';
												    } else {
												        echo '<span class="status-indicator status-active" title="All stock levels normal"></span>';
												    }
												?>
												<strong><?php echo $row['name'] ?></strong>
											</div>
											<div class="text-muted small">
												<span title="Number of products">
													<i class="fa fa-box"></i> <?php echo $stats['product_count'] ?>
												</span>
												<span class="mx-2">|</span>
												<span title="Total value">
													<i class="fa fa-dollar-sign"></i> ₱<?php echo number_format($stats['total_value'], 2) ?>
												</span>
												<?php if($stats['low_stock_count'] > 0): ?>
												<span class="mx-2">|</span>
												<span class="text-warning" title="Low stock items">
													<i class="fa fa-exclamation-triangle"></i> <?php echo $stats['low_stock_count'] ?>
												</span>
												<?php endif; ?>
											</div>
										</div>
									</td>
									<td class="text-center">
										<a href="javascript:void(0)" onclick="viewProducts(<?php echo $row['id'] ?>)" class="btn btn-sm btn-info" title="View Products">
											<i class="fa fa-eye"></i> Products (<?php echo $stats['product_count'] ?>)
										</a>
										<button class="btn btn-sm btn-primary edit_cat" type="button" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['name'] ?>" title="Edit Category">
											<i class="fa fa-edit"></i>
										</button>
										<button class="btn btn-sm btn-danger delete_cat" type="button" data-id="<?php echo $row['id'] ?>" title="Delete Category">
											<i class="fa fa-trash"></i>
										</button>
									</td>
								</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- Table Panel -->
		</div>
	</div>	

</div>

<script>
	$(document).ready(function() {
		// Initialize DataTable
		$('#categoryTable').DataTable({
			"pageLength": 10,
			"order": [[1, "asc"]],
			"dom": '<"top"f>rt<"bottom"lp><"clear">',
			"language": {
				"search": "",
				"searchPlaceholder": "Search categories..."
			},
			"drawCallback": function(settings) {
				// Initialize tooltips after each draw
				$('[title]').tooltip();
			}
		});

		// Add a refresh button to update the table
		$('#categoryTable_wrapper .dataTables_filter').append('\
			<button class="btn btn-primary btn-sm ml-2" onclick="location.reload()">\
				<i class="fa fa-sync"></i> Refresh\
			</button>\
		');


	});

	$('#manage-category').submit(function(e){
		e.preventDefault()
		start_load()
		$.ajax({
			url:'ajax.php?action=save_category',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp==1){
					alert_toast("Category successfully saved",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}
				else if(resp==2){
					alert_toast("Data successfully updated",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	})
	$('.edit_cat').click(function(){
		start_load()
		var cat = $('#manage-category')
		cat.get(0).reset()
		cat.find("[name='id']").val($(this).attr('data-id'))
		cat.find("[name='name']").val($(this).attr('data-name'))
		end_load()
	})
	$('.delete_cat').click(function(){
		_conf("Are you sure to delete this category?","delete_cat",[$(this).attr('data-id')])
	})
	function delete_cat($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_category',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>

<script>
// Add this function to your existing script section
function viewProducts(categoryId) {
    if(categoryId) {
        window.location.href = 'index.php?page=products&category=' + categoryId;
    } else {
        alert_toast("Invalid category selection", "warning");
    }
}
</script>