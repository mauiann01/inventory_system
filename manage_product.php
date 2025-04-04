<?php 
include('db_connect.php');

$product = array();
if(isset($_GET['id'])) {
    $product = $conn->query("SELECT * FROM product_list WHERE id = ".$_GET['id'])->fetch_assoc();
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fa fa-box"></i> <?php echo isset($product['id']) ? "Edit Product" : "Add New Product" ?>
                    </h4>
                    <a href="<?php echo isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php?page=products' ?>" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <form action="" id="manage-product">
                        <input type="hidden" name="id" value="<?php echo isset($product['id']) ? $product['id'] : '' ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Category</label>
                                    <select name="category_id" class="form-control" required>
                                        <option value="">Select Category</option>
                                        <?php 
                                        $cats = $conn->query("SELECT * FROM category_list ORDER BY name ASC");
                                        while($row = $cats->fetch_assoc()):
                                        ?>
                                        <option value="<?php echo $row['id'] ?>" <?php echo isset($product['category_id']) && $product['category_id'] == $row['id'] ? 'selected' : '' ?>>
                                            <?php echo $row['name'] ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Product Name</label>
                                    <input type="text" class="form-control" name="name" value="<?php echo isset($product['name']) ? $product['name'] : '' ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Price</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">₱</span>
                                        </div>
                                        <input type="number" step="0.01" class="form-control" name="price" value="<?php echo isset($product['price']) ? $product['price'] : '' ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Description</label>
                                    <textarea class="form-control" name="description" rows="7"><?php echo isset($product['description']) ? $product['description'] : '' ?></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button class="btn btn-default mr-2" type="button" onclick="$('#manage-product').get(0).reset()">Cancel</button>
                            <button class="btn btn-primary" form="manage-product">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#manage-product').submit(function(e){
            e.preventDefault();
            start_load();
            $.ajax({
                url:'ajax.php?action=save_product',
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                success:function(resp){
                    if(resp == 1){
                        alert_toast("Product successfully saved", 'success');
                        setTimeout(function(){
                            // If editing, go back to view page
                            if($('[name="id"]').val()) {
                                location.href = 'index.php?page=view_product&id=' + $('[name="id"]').val();
                            } else {
                                location.href = 'index.php?page=products';
                            }
                        },1500);
                    }
                }
            });
        });
    });
</script>
