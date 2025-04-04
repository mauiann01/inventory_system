<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Admin | Sales and Inventory System</title>
 	

<?php include('./header.php'); ?>
<?php include('./db_connect.php'); ?>
<?php 
session_start();
if(isset($_SESSION['login_id']))
header("location:index.php?page=home");

$query = $conn->query("SELECT * FROM system_settings limit 1")->fetch_array();
		foreach ($query as $key => $value) {
			if(!is_numeric($key))
				$_SESSION['setting_'.$key] = $value;
		}
?>

</head>
<style>
	body{
		width: 100%;
	    height: calc(100%);
	    /*background: #007bff;*/
	}
	main#main{
		width:100%;
		height: calc(100%);
		background:white;
	}
	#login-right{
		position: absolute;
		right:0;
		width:40%;
		height: calc(100%);
		background:white;
		display: flex;
		align-items: center;
	}
	#login-left{
		position: absolute;
		left:0;
		width:60%;
		height: calc(100%);
		background:#00000061;
		display: flex;
		align-items: center;
	}
	#login-right .card{
		margin: auto
	}
	.logo {
    margin: auto;
    font-size: 8rem;
    background: white;
    padding: .5em 0.8em;
    border-radius: 50% 50%;
    color: #000000b3;
}
</style>

<body>


  <main id="main" class=" bg-dark" >
  		<div id="login-left" style="background-image: url('assets/img/login_back3.jpg');
							  background-repeat: no-repeat;
							  background-size: 100% 100%; ">
  				<span class=""></span>

			  <h1 style="text-align: center; color: white; float: left; margin-right: -39px; margin-left: ; font-weight: bold; font-family: Calibri; font-size: 50px; background-color: #313131; padding: 10px; border-radius: ;">&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp   INVENTORY SYSTEM &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  <span style="font-size: 30px;">MP GENERAL MERCHANDISE<span></h1>
			<div class="logo" style="background-image: url('logo4.png'); background-size: 100% 100%; height: 230px; width: 250px; ">
			</div>
</div>

  		<div id="login-right" style="background-color: #313131;">
  			<div class="card col-md-8" style="background-color: #4c4c4c; ">
  				<div class="card-body">
  					<form id="login-form" >
  						<div class="form-group">
						  <h2 style="color: white; font-weight: bold; text-align: center;">LOGIN</h2>
  							<br>
						  <label for="username" class="control-label" style="color: white;">Username</label>
  							<input type="text" id="username" name="username" class="form-control">
  						</div>
  						<div class="form-group">
  							<label for="password" class="control-label" style="color: white;">Password</label>
  							<input type="password" id="password" name="password" class="form-control">
  						</div>
  						<center><button class="btn-sm btn-block btn-wave col-md-4 btn-primary" style="padding: 10px; font-size: 17px;">Login</button></center>
  					</form>
  				</div>
  			</div>
  		</div>
   

  </main>

  <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>


</body>
<script>
	$('#login-form').submit(function(e){
		e.preventDefault()
		$('#login-form button[type="button"]').attr('disabled',true).html('Logging in...');
		if($(this).find('.alert-danger').length > 0 )
			$(this).find('.alert-danger').remove();
		$.ajax({
			url:'ajax.php?action=login',
			method:'POST',
			data:$(this).serialize(),
			error:err=>{
				console.log(err)
		$('#login-form button[type="button"]').removeAttr('disabled').html('Login');

			},
			success:function(resp){
				if(resp == 1){
					location.href ='index.php?page=home';
				}else if(resp == 2){
					location.href ='index.php?page=supplier_receiving';

				}else{
					$('#login-form').prepend('<div class="alert alert-danger">Username or password is incorrect.</div>')
					$('#login-form button[type="button"]').removeAttr('disabled').html('Login');
				}
			}
		})
	})
</script>	
</html>