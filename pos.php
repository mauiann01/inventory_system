<?php include 'db_connect.php';

if(isset($_GET['id'])){
	$my_id = $_GET['id'];
	$qry = $conn->query("SELECT * FROM sales_list where id=".$_GET['id'])->fetch_array();
	foreach($qry as $k => $val){
		$$k = $val;
	}
	$inv = $conn->query("SELECT * FROM inventory where type=2 and form_id=".$_GET['id']);

}

?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<h4>Sales</h4>
			</div>
			<div class="card-body">
				<form action="" id="manage-sales">
					<input type="hidden" name="id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
					<input type="hidden" name="ref_no" value="<?php echo isset($ref_no) ? $ref_no : '' ?>">
					<div class="col-md-12">
						<div class="row">
							<div class="form-group col-md-5">
								<label class="control-label">Customer</label>
								<select name="customer_id" id="" class="custom-select browser-default select2">
									<option value="0" selected="">Guest</option>
								<?php 

								$customer = $conn->query("SELECT * FROM customer_list order by name asc");
								while($row=$customer->fetch_assoc()):
								?>
									<option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
								<?php endwhile; ?>
								</select>
							</div>
						</div>
						<hr>
						<div class="row mb-3">
								<div class="col-md-12 mb-3">
									<div class="btn-group" role="group">
										<button type="button" class="btn btn-primary" id="manual_entry">Manual Entry</button>
										<button type="button" class="btn btn-secondary" id="barcode_entry">Barcode Scanner</button>
									</div>
								</div>
								<div class="col-md-4 manual-input">
									<label class="control-label">Product</label>
									<select name="" id="product" class="custom-select browser-default select2">
										<option value=""></option>
									<?php 
									$cat = $conn->query("SELECT * FROM category_list order by name asc");
										while($row=$cat->fetch_assoc()):
											$cat_arr[$row['id']] = $row['name'];
										endwhile;
									$product = $conn->query("SELECT * FROM product_list  order by name asc");
									while($row=$product->fetch_assoc()):
										$prod[$row['id']] = $row;
									?>
										<option value="<?php echo $row['id'] ?>" data-name="<?php echo $row['name'] ?>" data-description="<?php echo $row['description'] ?>" data-sku="<?php echo htmlspecialchars($row['sku']) ?>"><?php echo $row['name'] . ' | ' . $row['sku'] ?></option>
									<?php endwhile; ?>
									</select>
								</div>
								<div class="col-md-4 barcode-input" style="display:none;">
									<label class="control-label">Scan Barcode</label>
									<input type="text" class="form-control" id="barcode" placeholder="Scan barcode here...">
								</div>
								<div class="col-md-2">
									<label class="control-label">Qty</label>
									<input type="number" class="form-control text-right" step="any" id="qty" >
								</div>
								<div class="col-md-3">
									<label class="control-label">&nbsp</label>
									<button class="btn btn-block btn-sm btn-primary" type="button" id="add_list"><i class="fa fa-plus"></i> Add to List</button>
								</div>


						</div>
						<div class="row">
							<table class="table table-bordered" id="list">
								<colgroup>
									<col width="25%">
									<col width="10%">
									<col width="20%">
									<col width="15%">
									<col width="15%">
									<col width="15%">
								</colgroup>
								<thead>
									<tr>
										<th class="text-center">Product</th>
										<th class="text-center">Qty</th>
										<th class="text-center">Price</th>
										<th class="text-center">Subtotal</th>
										<th class="text-center">VAT (6%)</th>
										<th class="text-center">Total</th>
									</tr>
								</thead>
								<tbody>
									<?php 
									if(isset($id)):
									while($row = $inv->fetch_assoc()): 
										foreach(json_decode($row['other_details']) as $k=>$v){
											$row[$k] = $v;
										}
									?>
										<tr class="item-row">
											<td>
												<input type="hidden" name="inv_id[]" value="<?php echo $row['id'] ?>">
												<input type="hidden" name="product_id[]" value="<?php echo $row['product_id'] ?>">
												<p class="pname">Name: <b><?php echo $prod[$row['product_id']]['name'] ?></b></p>
												<p class="pdesc"><small><i>Description: <b><?php echo $prod[$row['product_id']]['description'] ?></b></i></small></p>
											</td>
											<td>
												<input type="number" min="1" step="any" name="qty[]" value="<?php echo $row['qty'] ?>" class="text-right">
											</td>
											<td>
												<input type="hidden" min="1" step="any" name="price[]" value="<?php echo $row['price'] ?>" class="text-right">
												<p class="text-right"><?php echo $row['price'] ?></p>
											</td>
											<td>
												<p class="subtotal text-right"></p>
											</td>
											<td>
												<p class="vat text-right"></p>
											</td>
											<td>
												<p class="amount text-right"></p>
											</td>
											<!-- <td class="text-center">
												<buttob class="btn btn-sm btn-danger" onclick = "rem_list($(this))"><i class="fa fa-trash"></i></buttob>
											</td> -->
										</tr>
									<?php endwhile; ?>
									<?php endif; ?>
								</tbody>
								<tfoot>
									<tr>
										<th class="text-right" colspan="3">Total</th>
										<th class="text-right tamount"></th>
										<th></th>
									</tr>
								</tfoot>
							</table>
						</div>
						<div class="row">
							<button class="btn btn-primary btn-sm btn-block float-right " type="button" id="pay">Update</button>
							<?php if(isset($my_id)): ?>
								<a class="btn btn-secondary btn-sm btn-block float-right " href="index.php?page=view_receipt&id=<?php echo $my_id;?>">View Receipt</a>
							<?php endif; ?>
						</div>
					</div>
					<div class="modal fade" id="pay_modal" role='dialog'>
					    <div class="modal-dialog modal-md" role="document">
					      <div class="modal-content">
					        <div class="modal-header">
					        <h5 class="modal-title"></h5>
					      </div>
					      <div class="modal-body">
					      	<div class="container-fluid">
					      		<div class="form-group">
					      			<label for="" class="control-label">Total Amount</label>
					      			<div class="input-group">
					      				<div class="input-group-prepend">
					      					<span class="input-group-text">₱</span>
					      				</div>
					      				<input type="text" name="tamount" value="" class="form-control text-right" readonly="">
					      			</div>
					      		</div>
					      		<div class="form-group">
					      			<label for="amount_tendered" class="control-label">Amount Tendered</label>
					      			<div class="input-group">
					      				<div class="input-group-prepend">
					      					<span class="input-group-text">₱</span>
					      				</div>
					      				<input type="number" class="form-control text-right" id="amount_tendered" name="amount_tendered" min="0" step="0.01" required>
					      			</div>
					      		</div>
					      		<div class="form-group">
					      			<label for="" class="control-label">Change</label>
					      			<div class="input-group">
					      				<div class="input-group-prepend">
					      					<span class="input-group-text">₱</span>
					      				</div>
					      				<input type="number" name="change" value="0" min="0" class="form-control text-right" readonly="">
					      			</div>
					      		</div>
					      	</div>
					      </div>
					      <div class="modal-footer">
					        <button type="button" class="btn btn-primary" id='submit' onclick="$('#manage-sales').submit()">Pay</button>
					      </div>
					      </div>
					    </div>
					  </div>
				</form>
			</div>
			
		</div>
	</div>
</div>
<div id="tr_clone">
	<table>
	<tr class="item-row">
		<td>
			<input type="hidden" name="inv_id[]" value="">
			<input type="hidden" name="product_id[]" value="">
			<p class="pname">Name: <b>product</b></p>
			<p class="pdesc"><small><i>Description: <b>Description</b></i></small></p>
		</td>
		<td>
			<input type="number" min="1" step="any" name="qty[]" value="" class="text-right">
		</td>
		<td>
			<input type="hidden" min="1" step="any" name="price[]" value="" class="text-right" readonly="">
			<p class="price text-right">0</p>
		</td>
		<td>
			<p class="subtotal text-right">0</p>
		</td>
		<td>
			<p class="vat text-right">0</p>
		</td>
		<td>
			<p class="amount text-right">0</p>
		</td>
		<td class="text-center">
			<buttob class="btn btn-sm btn-danger" onclick = "rem_list($(this))"><i class="fa fa-trash"></i></buttob>
		</td>
	</tr>
	</table>
</div>
<style type="text/css">
	#tr_clone{
		display: none;
	}
	td{
		vertical-align: middle;
	}
	td p {
		margin: unset;
	}
	td input[type='number']{
		height: calc(100%);
		width: calc(100%);

	}
	input[type=number]::-webkit-inner-spin-button, 
	input[type=number]::-webkit-outer-spin-button { 
	  -webkit-appearance: none; 
	  margin: 0; 
	}
</style>
<script>
	$('.select2').select2({
	 	placeholder:"Please select here",
	 	width:"100%"
	})

	// Toggle between manual and barcode entry
	$('#manual_entry').click(function() {
		$(this).removeClass('btn-secondary').addClass('btn-primary');
		$('#barcode_entry').removeClass('btn-primary').addClass('btn-secondary');
		$('.manual-input').show();
		$('.barcode-input').hide();
	});

	$('#barcode_entry').click(function() {
		$(this).removeClass('btn-secondary').addClass('btn-primary');
		$('#manual_entry').removeClass('btn-primary').addClass('btn-secondary');
		$('.manual-input').hide();
		$('.barcode-input').show();
		$('#barcode').focus();
	});

	// Handle barcode input
	$('#barcode').on('keypress', function(e) {
		if(e.which == 13) {
			e.preventDefault();
			var barcode = $(this).val().trim();
			console.log('Scanned barcode:', barcode); // Debug log
			
			// Debug: Log all available SKUs
			console.log('Available SKUs:');
			$('#product option').each(function() {
				console.log('Option SKU:', $(this).data('sku'), 'Value:', $(this).val(), 'Text:', $(this).text());
			});
			
			// Find product by SKU
			var found = false;
			$('#product option').each(function() {
				var sku = $(this).data('sku');
				console.log('Comparing SKU:', sku, 'with barcode:', barcode, 'Type:', typeof sku, 'Barcode type:', typeof barcode); // Debug log
				if (sku && sku.toString().trim() === barcode.toString().trim()) {
					found = true;
					console.log('Product found:', $(this).val(), 'with SKU:', sku); // Debug log
					$('#product').val($(this).val()).trigger('change');
					$('#qty').val(1);
					// Automatically click the Add to List button
					$('#add_list').click();
					return false; // Break the loop
				}
			});
			
			if(!found) {
				console.log('No product found for barcode:', barcode); // Debug log
				alert_toast("Product not found! Please check the barcode.", 'danger');
			}
			$(this).val('');
		}
	});

	$('#pay').click(function(){
		if($("#list .item-row").length <= 0){
			alert_toast("Please insert atleast 1 item first.",'danger');
			end_load();
			return false;
		}
		$('#pay_modal').modal('show')
	})
	$(document).ready(function(){
		if('<?php echo isset($id) ?>' == 1){
			$('[name="supplier_id"]').val('<?php echo isset($supplier_id) ? $supplier_id :'' ?>').select2({
				placeholder:"Please select here",
	 			width:"100%"
			})
			calculate_total()
		}
	})
	function rem_list(_this){
		_this.closest('tr').remove()
		calculate_total()
	}
	$('#add_list').click(function(){
		var tr = $('#tr_clone tr.item-row').clone();
		var product = $('#product').val(),
			qty = $('#qty').val(),
			price = $('#price').val();
			if($('#list').find('tr[data-id="'+product+'"]').length > 0){
				alert_toast("Product already on the list",'danger')
				return false;
			}

			if(product == '' || qty == ''){
				alert_toast("Please complete the fields first",'danger')
				return false;
			}
			$.ajax({
				url:'ajax.php?action=chk_prod_availability',
				method:'POST',
				data:{id:product},
				success:function(resp){
					resp = JSON.parse(resp);
					if(resp.available >= qty){
						tr.attr('data-id',product)
						tr.find('.pname b').html($("#product option[value='"+product+"']").attr('data-name'))
						tr.find('.pdesc b').html($("#product option[value='"+product+"']").attr('data-description'))
						tr.find('.price').html(resp.price)
						tr.find('[name="product_id[]"]').val(product)
						tr.find('[name="qty[]"]').val(qty)
						tr.find('[name="price[]"]').val(resp.price)
						
						var subtotal = parseFloat(price) * parseFloat(qty);
						var vat = subtotal * 0.06; // 6% VAT
						var amount = subtotal + vat;
						
						tr.find('.subtotal').html('₱' + parseFloat(subtotal).toLocaleString('en-US',{style:'decimal',maximumFractionDigits:2,minimumFractionDigits:2}))
						tr.find('.vat').html('₱' + parseFloat(vat).toLocaleString('en-US',{style:'decimal',maximumFractionDigits:2,minimumFractionDigits:2}))
						tr.find('.amount').html('₱' + parseFloat(amount).toLocaleString('en-US',{style:'decimal',maximumFractionDigits:2,minimumFractionDigits:2}))
						
						$('#list tbody').append(tr)
						calculate_total()
						$('[name="qty[]"],[name="price[]"]').keyup(function(){
							calculate_total()
						})
						 $('#product').val('').select2({
						 	placeholder:"Please select here",
					 		width:"100%"
						 })
							$('#qty').val('')
							$('#price').val('')
					}else{
						alert_toast("Product quantity is greater than available stock.",'danger')
					}
				}
			})
	})
	function calculate_total(){
		var total = 0;
		var total_vat = 0;
		$('#list tbody').find('.item-row').each(function(){
			var _this = $(this).closest('tr')
			var qty = parseFloat(_this.find('[name="qty[]"]').val());
			var price = parseFloat(_this.find('[name="price[]"]').val());
			var subtotal = qty * price;
			var vat = subtotal * 0.06; // 6% VAT
			var amount = subtotal + vat;
			
			subtotal = subtotal > 0 ? subtotal : 0;
			vat = vat > 0 ? vat : 0;
			amount = amount > 0 ? amount : 0;
			
			_this.find('p.subtotal').html('₱' + parseFloat(subtotal).toLocaleString('en-US',{style:'decimal',maximumFractionDigits:2,minimumFractionDigits:2}))
			_this.find('p.vat').html('₱' + parseFloat(vat).toLocaleString('en-US',{style:'decimal',maximumFractionDigits:2,minimumFractionDigits:2}))
			_this.find('p.amount').html('₱' + parseFloat(amount).toLocaleString('en-US',{style:'decimal',maximumFractionDigits:2,minimumFractionDigits:2}))
			
			total += amount;
			total_vat += vat;
		})
		$('[name="tamount"]').val(total)
		$('#list .tamount').html('₱' + parseFloat(total).toLocaleString('en-US',{style:'decimal',maximumFractionDigits:2,minimumFractionDigits:2}))
	}
	$('#amount_tendered').on('input', function(){
		var amount = $(this).val();
		if(amount < 0) {
			$(this).val(0);
			amount = 0;
		}
		var total = $('[name="tamount"]').val();
		var change = amount - total;
		if(change < 0) {
			change = 0;
		}
		$('[name="change"]').val(change.toFixed(2));
	});
	$('#manage-sales').submit(function(e){
		e.preventDefault()
		start_load()
		if($("#list .item-row").length <= 0){
			alert_toast("Please insert atleast 1 item first.",'danger');
			end_load();
			return false;
		}
		$.ajax({
			url:'ajax.php?action=save_sales',
		    method: 'POST',
		    data: $(this).serialize(),
			success:function(resp){
				if(resp > 0){
					end_load()
					alert_toast("Data successfully submitted",'success')
					uni_modal('Receipt',"print_sales.php?id="+resp)
					$('#uni_modal').modal({backdrop:'static',keyboard:false})
					$('#uni_modal .modal-footer').remove() // Remove the footer with print and cancel buttons
				}
			}
		})
	})
</script>

<script>
	window.onload = function() {
	var tr = $('#tr_clone tr.item-row').clone();
		var product = $('#product').val(),
			qty = $('#qty').val(),
			price = $('#price').val();
			$('[name="qty[]"],[name="price[]"]').keyup(function(){
							calculate_total()
						})
					}
</script>