<?php include 'db_connect.php' ?>

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
    }

    .card-header h4 {
        margin: 0;
        font-weight: 600;
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
    .btn-primary {
        background: linear-gradient(45deg, #3498db, #2980b9);
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .btn-warning, .btn-danger {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .btn-warning:hover, .btn-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
</style>

<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h4><b>Receiving</b></h4>
						<div class="d-flex">
							<button class="btn btn-primary btn-sm" id="new_receiving"><i class="fa fa-plus"></i> New Receiving</button>
						</div>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-bordered table-hover" id="receivingTable">
								<thead class="thead-dark">
									<tr>
										<th class="text-center" style="width: 5%">#</th>
										<th class="text-center" style="width: 20%">Date & Time</th>
										<th class="text-center" style="width: 15%">Reference #</th>
										<th class="text-center" style="width: 30%">Supplier</th>
										<th class="text-center" style="width: 30%">Action</th>
									</tr>
								</thead>
								<tbody>
							<?php 
								$supplier = $conn->query("SELECT * FROM supplier_list order by supplier_name asc");
								while($row=$supplier->fetch_assoc()):
									$sup_arr[$row['id']] = $row['supplier_name'];
								endwhile;
								$i = 1;
								$receiving = $conn->query("SELECT * FROM receiving_list r order by date(date_added) desc");
								while($row=$receiving->fetch_assoc()):
							?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class="text-center"><?php echo date("M d, Y h:i A",strtotime($row['date_added'])) ?></td>
									<td class="text-center"><?php echo $row['ref_no'] ?></td>
									<td class="text-center"><?php echo isset($sup_arr[$row['supplier_id']])? $sup_arr[$row['supplier_id']] :'N/A' ?></td>
									<td class="text-center">
										<a class="btn btn-sm btn-info" href="index.php?page=manage_receiving&id=<?php echo $row['id'] ?>">View</a>
										<button class="btn btn-sm btn-danger delete_receiving" data-id="<?php echo $row['id'] ?>">Delete</button>
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


<script>
	$(document).ready(function() {
		// Initialize DataTable
		$('#receivingTable').DataTable({
			"pageLength": 10,
			"order": [[1, "desc"]],
			"dom": '<"top"f>rt<"bottom"lp><"clear">',
			"language": {
				"search": "",
				"searchPlaceholder": "Search receiving..."
			}
		});

		$('#new_receiving').click(function(){
			location.href = "index.php?page=manage_receiving"
		});

		$('.delete_receiving').click(function(){
			_conf("Are you sure to delete this data?","delete_receiving",[$(this).attr('data-id')])
		});
	});

	function delete_receiving($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_receiving',
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