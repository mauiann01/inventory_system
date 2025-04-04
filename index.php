<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Sales and Inventory System</title>
 	

<?php
	session_start();
  if(!isset($_SESSION['login_id']))
    header('location:login.php');
 include('./header.php'); 
 include('./notifications.php'); 
 // include('./auth.php'); 
 ?>

</head>
<style>
	body{
        background: #80808045;
  }
</style>

<body>
	<?php include 'topbar.php' ?>
	<?php include 'navbar.php' ?>
  <div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body text-white">
    </div>
  </div>
  <main id="view-panel" >
      <?php $page = isset($_GET['page']) ? $_GET['page'] :'home'; ?>
  	<?php include $page.'.php' ?>
  </main>

  <div id="preloader"></div>
  <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

<div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Confirmation</h5>
      </div>
      <div class="modal-body">
        <div id="delete_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='submit' onclick="window.print()">Print</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
      </div>
    </div>
  </div>
</body>
<script>
	 window.start_load = function(){
    $('body').prepend('<di id="preloader2"></di>')
  }
  window.end_load = function(){
    $('#preloader2').fadeOut('fast', function() {
        $(this).remove();
      })
  }

  window.uni_modal = function($title = '' , $url=''){
    start_load()
    $.ajax({
        url:$url,
        error:err=>{
            console.log()
            alert("An error occured")
        },
        success:function(resp){
            if(resp){
                $('#uni_modal .modal-title').html($title)
                $('#uni_modal .modal-body').html(resp)
                $('#uni_modal').modal('show')
                end_load()
            }
        }
    })
}
window._conf = function($msg='',$func='',$params = []){
     $('#confirm_modal #confirm').attr('onclick',$func+"("+$params.join(',')+")")
     $('#confirm_modal .modal-body').html($msg)
     $('#confirm_modal').modal('show')
  }
   window.alert_toast= function($msg = 'TEST',$bg = 'success'){
      $('#alert_toast').removeClass('bg-success')
      $('#alert_toast').removeClass('bg-danger')
      $('#alert_toast').removeClass('bg-info')
      $('#alert_toast').removeClass('bg-warning')

    if($bg == 'success')
      $('#alert_toast').addClass('bg-success')
    if($bg == 'danger')
      $('#alert_toast').addClass('bg-danger')
    if($bg == 'info')
      $('#alert_toast').addClass('bg-info')
    if($bg == 'warning')
      $('#alert_toast').addClass('bg-warning')
    $('#alert_toast .toast-body').html($msg)
    $('#alert_toast').toast({delay:3000}).toast('show');
  }

  // Stock Notification System
  function checkStockNotifications() {
    console.log('Checking stock notifications...');
    $.ajax({
      url: 'notifications.php?action=get_notifications',
      method: 'GET',
      dataType: 'json',
      success: function(response) {
        console.log('Raw response:', response);
        if (response && Array.isArray(response) && response.length > 0) {
          console.log('Found notifications:', response.length);
          // Remove existing notifications
          $('.notifications-container').remove();
          
          // Create new notifications container
          const container = $('<div class="notifications-container"></div>');
          
          response.forEach((notification, index) => {
            console.log(`Creating notification ${index + 1}:`, notification);
            let alertContent = `
              <div class="alert alert-${notification.type} alert-dismissible fade show" role="alert">
                ${notification.message}
                ${notification.has_order_button ? `
                    <div class="mt-2">
                        <a href="index.php?page=manage_receiving" class="btn btn-sm btn-light">Order Stock</a>
                    </div>
                ` : ''}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            `;
            const alert = $(alertContent);
            container.append(alert);
          });
          
          $('body').append(container);
          
          // Show notifications with animation
          container.find('.alert').each(function(index) {
            $(this).delay(index * 200).fadeIn(300);
          });
        } else {
          console.log('No notifications to display or invalid response format');
        }
      },
      error: function(xhr, status, error) {
        console.error('Error checking notifications:', error);
        console.error('Status:', status);
        console.error('Response:', xhr.responseText);
        console.error('XHR:', xhr);
      }
    });
  }

  // Check notifications every 5 minutes
  $(document).ready(function(){
    $('#preloader').fadeOut('fast', function() {
        $(this).remove();
    });
    
    // Initial check
    console.log('Document ready, performing initial notification check');
    checkStockNotifications();
    
    // Set up periodic checks
    setInterval(checkStockNotifications, 300000); // 5 minutes
  });
</script>	
</html>