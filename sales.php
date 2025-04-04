<?php include 'db_connect.php' ?>

<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<div class="col-md-12">
				<!-- Notification Area -->
				<div id="notification_area"></div>
				<!-- End Notification Area -->
				<div class="card">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h4><b>Sales</b></h4>
						<div class="d-flex">
							<button class="btn btn-primary btn-sm" id="new_sales"><i class="fa fa-plus"></i> New Sales</button>
						</div>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-bordered table-hover" id="salesTable">
								<thead class="thead-dark">
									<tr>
										<th class="text-center" style="width: 5%">#</th>
										<th class="text-center" style="width: 15%">Date</th>
										<th class="text-center" style="width: 15%">Reference No</th>
										<th class="text-center" style="width: 20%">Customer</th>
										<th class="text-center" style="width: 15%">Total Amount</th>
										<th class="text-center" style="width: 30%">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$customer = $conn->query("SELECT * FROM customer_list order by name asc");
									while($row=$customer->fetch_assoc()):
										$cus_arr[$row['id']] = $row['name'];
									endwhile;
									$cus_arr[0] = "GUEST";

									$i = 1;
									$sales = $conn->query("SELECT * FROM sales_list ORDER BY date_updated DESC");
									while($row=$sales->fetch_assoc()):
									?>
									<tr>
										<td class="text-center"><?php echo $i++ ?></td>
										<td class="text-center"><?php echo date('M d, Y h:i A', strtotime($row['date_updated'])) ?></td>
										<td class="text-center"><?php echo $row['ref_no'] ?></td>
										<td class="text-center"><?php echo isset($cus_arr[$row['customer_id']])? $cus_arr[$row['customer_id']] :'N/A' ?></td>
										<td class="text-right">₱<?php echo number_format($row['total_amount'], 2) ?></td>
										<td class="text-center">
											<a class="btn btn-sm btn-info" href="print_sales.php?id=<?php echo $row['id'] ?>" target="_blank">View Receipt</a>
											<button onclick="void()" class="btn btn-sm btn-danger delete_sales" data-id="<?php echo $row['id'] ?>">Delete</button>
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

<script>
	$(document).ready(function() {
		// Initialize DataTable
		$('#salesTable').DataTable({
			"pageLength": 10,
			"order": [[1, "desc"]],
			"dom": '<"top"f>rt<"bottom"lp><"clear">',
			"language": {
				"search": "",
				"searchPlaceholder": "Search sales..."
			}
		});

		$('#new_sales').click(function(){
			location.href = "index.php?page=pos"
		});

		$('.delete_sales').click(function(){
			const saleId = $(this).attr('data-id');
			$('#deletePasswordModal').modal('show');
			$('#saleIdToDelete').val(saleId);
		});

		function delete_sales($id, password){
			start_load()
			$.ajax({
				url:'ajax.php?action=delete_sales',
				method:'POST',
				data:{
					id: $id,
					password: password
				},
				success:function(resp){
					console.log('Response:', resp);
					if(resp.trim() == '1'){
						alert_toast("Data successfully deleted",'success')
						setTimeout(function(){
							location.reload()
						},1500)
					} else if(resp == 'invalid_password') {
						alert_toast("Invalid password",'error')
						end_load()
					}
				},
				error: function() {
					alert_toast("An error occurred",'error')
					end_load()
				}
			})
		}

		// Function to check and display notifications
		function checkSalesNotifications() {
			console.log('Checking sales notifications...');
			$.ajax({
				url: 'notifications.php?action=get_notifications',
				method: 'GET',
				dataType: 'json',
				success: function(response) {
					if (response && Array.isArray(response) && response.length > 0) {
						$('#notification_area').empty();
						response.forEach((notification, index) => {
							let alertContent = `
								<div class="alert alert-${notification.type} alert-dismissible fade show" role="alert">
									${notification.message}
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
							`;
							const alert = $(alertContent);
							$('#notification_area').append(alert);
						});
						$('#notification_area .alert').each(function(index) {
							$(this).delay(index * 200).fadeIn(300);
						});
					}
				}
			});
		}

		// Initial check
		checkSalesNotifications();
		
		// Set up periodic checks every 5 minutes
		setInterval(checkSalesNotifications, 300000);
	});
</script>

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
		text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
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
		padding: 12px;
	}

	.table td {
		padding: 12px;
		vertical-align: middle;
		border-color: #e9ecef;
	}

	.table-hover tbody tr:hover {
		background-color: rgba(52, 152, 219, 0.05);
		transition: background-color 0.3s ease;
	}

	/* Button Styling */
	.btn {
		padding: 6px 12px;
		font-weight: 500;
		border-radius: 5px;
		transition: all 0.3s ease;
	}

	.btn-primary {
		background: linear-gradient(45deg, #3498db, #2980b9);
		border: none;
		box-shadow: 0 2px 4px rgba(0,0,0,0.1);
	}

	.btn-primary:hover {
		background: linear-gradient(45deg, #2980b9, #3498db);
		transform: translateY(-1px);
		box-shadow: 0 4px 8px rgba(0,0,0,0.2);
	}

	.btn-info {
		background: linear-gradient(45deg, #17a2b8, #138496);
		border: none;
		box-shadow: 0 2px 4px rgba(0,0,0,0.1);
	}

	.btn-info:hover {
		background: linear-gradient(45deg, #138496, #17a2b8);
		transform: translateY(-1px);
		box-shadow: 0 4px 8px rgba(0,0,0,0.2);
	}

	.btn-danger {
		background: linear-gradient(45deg, #e74c3c, #c0392b);
		border: none;
		box-shadow: 0 2px 4px rgba(0,0,0,0.1);
	}

	.btn-danger:hover {
		background: linear-gradient(45deg, #c0392b, #e74c3c);
		transform: translateY(-1px);
		box-shadow: 0 4px 8px rgba(0,0,0,0.2);
	}

	/* DataTables Customization */
	.dataTables_wrapper {
		padding: 0 10px;
	}

	.dataTables_wrapper .dataTables_filter input {
		border: 1px solid #ddd;
		border-radius: 5px;
		padding: 8px 12px;
		margin-left: 10px;
		box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
		transition: all 0.3s ease;
	}

	.dataTables_wrapper .dataTables_filter input:focus {
		border-color: #3498db;
		box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
	}

	.dataTables_wrapper .dataTables_length select {
		border: 1px solid #ddd;
		border-radius: 5px;
		padding: 8px 12px;
		box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
	}

	/* Notification Area */
	#notification_area {
		margin-bottom: 20px;
	}

	.alert {
		border-radius: 5px;
		box-shadow: 0 2px 4px rgba(0,0,0,0.1);
	}

	/* Responsive Adjustments */
	@media (max-width: 768px) {
		.card-header {
			flex-direction: column;
			gap: 10px;
		}
		
		.btn {
			width: 100%;
			margin-bottom: 5px;
		}
		
		.table-responsive {
			border-radius: 0 0 10px 10px;
		}
	}
</style>

<!-- Delete Password Modal -->
<div class="modal fade" id="deletePasswordModal" tabindex="-1" role="dialog" aria-labelledby="deletePasswordModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="deletePasswordModalLabel">Enter Password to Delete</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<input type="hidden" id="saleIdToDelete">
				<div class="form-group">
					<label for="deletePassword">Password</label>
					<input type="password" class="form-control" id="deletePassword" required>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
			</div>
		</div>
	</div>
</div>

<script>
function confirmDelete() {
	const saleId = $('#saleIdToDelete').val();
	const password = $('#deletePassword').val();
	
	if (!password) {
		alert_toast("Please enter the password",'error');
		return;
	}
	
	$.ajax({
		url:'ajax.php?action=delete_sales',
		method:'POST',
		data:{
			id: saleId,
			password: password
		},
		success:function(resp){
			console.log('Response:', resp);
			if(resp.trim() == '1'){
				$('#deletePasswordModal').modal('hide');
				$('#deletePassword').val('');
				alert_toast("Data successfully deleted",'success');
				setTimeout(function(){
					location.reload();
				}, 1500);
			} else if(resp.trim() == 'invalid_password') {
				alert_toast("Invalid password",'error');
			} else {
				alert_toast("Failed to delete",'error');
			}
		},
		error: function() {
			alert_toast("An error occurred",'error');
		}
	});

}
</script>