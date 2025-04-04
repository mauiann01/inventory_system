<?php include 'db_connect.php' ?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<!-- <button class="col-md-2 float-right btn btn-primary btn-sm" id="new_receiving"><i class="fa fa-plus"></i> New Receiving</button> -->
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
						<table class="table table-bordered">
							<thead>
								<th class="text-center">#</th>
								<th class="text-center">Date</th>
								<th class="text-center">Time</th>
								<th class="text-center">Reference #</th>
								<th class="text-center">Supplier</th>
								<th class="text-center">Action</th>
							</thead>
							<tbody>
							<?php
                                $sp_name = $_SESSION['login_name'];
								$supplier = $conn->query("SELECT * FROM supplier_list WHERE username = '$sp_name'");
								while($row=$supplier->fetch_assoc()):
                                    $sp_id = $row['id'];
									$sup_arr[$row['id']] = $row['supplier_name'];
								endwhile;
								$i = 1;
								$receiving = $conn->query("SELECT * FROM receiving_list r WHERE supplier_id = '$sp_id' order by date(date_added) desc");
								while($row=$receiving->fetch_assoc()):
							?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class=""><?php echo date("M d, Y",strtotime($row['date_added'])) ?></td>
									<td class=""><?php echo date("h:i a",strtotime($row['date_added'])) ?></td>
									<td class=""><?php echo $row['ref_no'] ?></td>
									<td class=""><?php echo isset($sup_arr[$row['supplier_id']])? $sup_arr[$row['supplier_id']] :'N/A' ?></td>
									<td class="text-center">
										<a class="btn btn-sm btn-warning" href="index.php?page=manage_receiving_supplier&id=<?php echo $row['id'] ?>">View</a>
										<!-- <a class="btn btn-sm btn-danger delete_receiving" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a> -->
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
	$('table').dataTable()
	$('#new_receiving').click(function(){
		location.href = "index.php?page=manage_receiving_supplier"
	})
	$('.delete_receiving').click(function(){
		_conf("Are you sure to delete this data?","delete_receiving",[$(this).attr('data-id')])
	})
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