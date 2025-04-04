<?php include('db_connect.php');?>

<div class="container-fluid">
	
	<div class="col-lg-12">
		<div class="row">
			<!-- FORM Panel -->
			<div class="col-md-4">
			<form action="" id="manage-supplier">
				<div class="card">
					<div class="card-header bg-primary text-white">
						<h5 class="card-title mb-0"><i class="fa fa-user-plus mr-2"></i>Supplier Form</h5>
				  	</div>
					<div class="card-body">
						<input type="hidden" name="id">
						
						<!-- Company Information -->
						<div class="form-group">
							<label class="control-label"><i class="fa fa-building mr-2"></i>Company Name</label>
							<input type="text" class="form-control" name="name" required placeholder="Enter company name">
						</div>

						<!-- Contact Information -->
						<div class="form-group">
							<label class="control-label"><i class="fa fa-phone mr-2"></i>Contact Number</label>
							<input type="text" class="form-control" name="contact" required placeholder="Enter contact number">
						</div>

						<div class="form-group">
							<label class="control-label"><i class="fa fa-map-marker mr-2"></i>Address</label>
							<textarea class="form-control" name="address" rows="3" required placeholder="Enter complete address"></textarea>
						</div>

						<!-- Login Credentials -->
						<div class="form-group">
							<label class="control-label"><i class="fa fa-user mr-2"></i>Username</label>
							<input type="text" class="form-control" name="username" required placeholder="Enter username">
						</div>

						<div class="form-group mb-0">
							<label class="control-label"><i class="fa fa-lock mr-2"></i>Password</label>
							<div class="input-group">
								<input type="password" class="form-control" name="password" required placeholder="Enter password">
								<div class="input-group-append">
									<button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
										<i class="fa fa-eye"></i>
									</button>
								</div>
							</div>
						</div>
					</div>

					<div class="card-footer bg-light">
						<div class="row">
							<div class="col-md-12 text-right">
								<button class="btn btn-secondary" type="button" onclick="$('#manage-supplier').get(0).reset()">
									<i class="fa fa-times mr-1"></i> Cancel
								</button>
								<button class="btn btn-primary" type="submit">
									<i class="fa fa-save mr-1"></i> Save
								</button>
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
					<div class="card-header">
						<div class="row">
							<div class="col">
								<h4 class="card-title">Supplier List</h4>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-striped table-hover">
								<thead class="thead-dark">
									<tr>
										<th class="text-center" width="5%">#</th>
										<th class="text-left" width="65%">Supplier Information</th>
										<th class="text-center" width="30%">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php 
									$i = 1;
									$cats = $conn->query("SELECT * FROM supplier_list order by supplier_name asc");
									while($row=$cats->fetch_assoc()):
									?>
									<tr>
										<td class="text-center align-middle"><?php echo $i++ ?></td>
										<td class="align-middle">
											<h6 class="mb-1"><?php echo $row['supplier_name'] ?></h6>
											<p class="mb-1 text-muted">
												<i class="fa fa-phone mr-2"></i><?php echo $row['contact'] ?>
											</p>
											<p class="mb-0 text-muted">
												<i class="fa fa-map-marker mr-2"></i><?php echo $row['address'] ?>
											</p>
										</td>
										<td class="text-center align-middle">
											<div class="btn-group" role="group">
												<button class="btn btn-primary btn-sm edit_supplier" type="button" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['supplier_name'] ?>" data-contact="<?php echo $row['contact'] ?>" data-address="<?php echo $row['address'] ?>" data-username="<?php echo $row['username'] ?>" data-password="<?php echo $row['password'] ?>">
													<i class="fa fa-edit"></i> Edit
												</button>
												<button class="btn btn-danger btn-sm delete_supplier" type="button" data-id="<?php echo $row['id'] ?>">
													<i class="fa fa-trash"></i> Delete
												</button>
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
<style>
	.table {
		background: #fff;
		border-radius: 3px;
		box-shadow: 0 1px 1px rgba(0,0,0,.05);
	}
	.table thead.thead-dark th {
		color: #fff;
		background-color: #343a40;
		border-color: #454d55;
	}
	.table td {
		vertical-align: middle !important;
	}
	.btn-group {
		box-shadow: 0 1px 1px rgba(0,0,0,.05);
	}
	.card-title {
		margin-bottom: 0;
		color: #fff;
		font-weight: 500;
	}
	.text-muted {
		color: #6c757d !important;
	}
	.fa {
		width: 16px;
		text-align: center;
	}
	.table-responsive {
		margin: -1px;
	}
	.form-group label {
		font-weight: 500;
		color: #495057;
	}
	.form-control {
		border-radius: 4px;
		border: 1px solid #ced4da;
		transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
	}
	.form-control:focus {
		border-color: #80bdff;
		box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
	}
	.card {
		box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
	}
	.card-header {
		border-bottom: 0;
	}
	.card-footer {
		border-top: 1px solid rgba(0,0,0,.125);
		background-color: #f8f9fa;
	}
</style>
<script>
	function togglePassword() {
		var passwordField = $('input[name="password"]');
		var eyeIcon = $('.fa-eye');
		
		if (passwordField.attr('type') === 'password') {
			passwordField.attr('type', 'text');
			eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
		} else {
			passwordField.attr('type', 'password');
			eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
		}
	}

	$('#manage-supplier').submit(function(e){
		e.preventDefault()
		start_load()
		$.ajax({
			url:'ajax.php?action=save_supplier',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully added",'success')
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
	$('.edit_supplier').click(function(){
		start_load()
		var cat = $('#manage-supplier')
		cat.get(0).reset()
		cat.find("[name='id']").val($(this).attr('data-id'))
		cat.find("[name='name']").val($(this).attr('data-name'))
		cat.find("[name='contact']").val($(this).attr('data-contact'))
		cat.find("[name='address']").val($(this).attr('data-address'))
		cat.find("[name='username']").val($(this).attr('data-username'))
		cat.find("[name='password']").val($(this).attr('data-password'))
		end_load()
	})
	$('.delete_supplier').click(function(){
		_conf("Are you sure to delete this supplier?","delete_supplier",[$(this).attr('data-id')])
	})
	function delete_supplier($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_supplier',
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