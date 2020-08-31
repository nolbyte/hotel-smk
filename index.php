<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
define("RESMI", "OK");

if(!isset($_SESSION['userid'])){
  header("Location: login.php");
}

require('config/database.php');
require('config/fungsi.php');
require('config/gump.class.php');

if(isset($_GET['mod'])){
  $mod = sanitasi($_GET['mod']);
  $hal = sanitasi($_GET['hal']);
}
$sql = $db->prepare("SELECT nama_hotel, nama_perusahaan FROM perusahaan");
$sql->execute();
$owner = $sql->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html style="height: auto;">
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= $owner['nama_hotel']?> Management</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/font-awesome.min.css">
  <link rel="stylesheet" href="assets/css/ionicons.min.css">
  <link rel="stylesheet" href="assets/css/AdminLTE.min.css">
  <link rel="stylesheet" href="assets/css/skin-blue.min.css">
  <link rel="stylesheet" href="assets/plugins/datatables/dataTables.bootstrap.css">
  <link rel="stylesheet" href="assets/plugins/datepicker/datepicker3.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
  <!-- jQuery 2.2.3 -->
  <script src="assets/js/jquery-2.2.3.min.js"></script>  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
  <!-- CKEDITOR-->
  <script src="https://cdn.ckeditor.com/4.4.3/full/ckeditor.js"></script>
  <style type="text/css">
    .dataTables_filter {
      /*float: left !important;*/
    }    
  </style>
</head>
<body class="hold-transition skin-blue layout-top-nav">
  <div class="wrapper">
    <header class="main-header">
      <nav class="navbar navbar-static-top">
        <div class="container">
          <div class="navbar-header">
            <a href="index.php" class="navbar-brand"><b>MHD3</b>HOTEL</a>
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
              <i class="fa fa-bars"></i>
            </button>
          </div>
          <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
            <?php include('component/navigation.php'); ?>
          </div>
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">              
              <li class="dropdown user user-menu">                
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">                  
                  <img src="assets/img/anonim.jpg" class="user-image" alt="User Image">                  
                  <span class="hidden-xs"><?= $_SESSION['ngaran']?></span>
                </a>
                <ul class="dropdown-menu">                  
                  <li class="user-header">
                    <img src="assets/img/anonim.jpg" class="img-circle" alt="User Image">

                    <p>
                      <?= $_SESSION['username']." - ".$_SESSION["ngaran"]?>
                      <small><?= $_SESSION['jabatan']?></small>
                    </p>
                  </li>                 
                  <li class="user-footer">
                    <div class="pull-left">
                      <a href="#" class="btn btn-default btn-flat">Profile</a>
                    </div>
                    <div class="pull-right">
                      <a href="logout.php" class="btn btn-default btn-flat">Sign out</a>
                    </div>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>
    <div class="content-wrapper">
      <div class="container">           
        <section class="content">
          <?php 
          if(isset($_GET['mod'])){
            include('modul/' . $mod . '/' . $hal . '.php');
          }else{      
            include('dashboard.php');
          }
          ?>          
        </section>       
      </div>      
    </div>
    <footer class="main-footer">
      <div class="container">
        <div class="pull-right hidden-xs">
          <b>Version</b> 2.4.0
        </div>
        <strong>Copyright &copy; 2017-<?= date('Y').'&nbsp;&nbsp;'.$owner['nama_perusahaan'];?>.</strong> All rights
        reserved.
      </div>    
    </footer>
  </div>  
  <script src="assets/js/bootstrap.min.js"></script>  
  <script src="assets/js/app.min.js"></script>
  <script src="assets/js/typeahead.bundle.js"></script>
  <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="assets/plugins/datatables/dataTables.bootstrap.min.js"></script>
  <script src="assets/plugins/fastclick/fastclick.js"></script>
  <script src="assets/plugins/bootbox/bootbox.min.js"></script>
  <script src="assets/plugins/jquery.mask.js"></script>
  <script src="assets/plugins/datepicker/bootstrap-datepicker.js"></script> 
  <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.js"></script>
  <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.js"></script>   
  <script>
    $(document).ready(function() {
     $('#tabelku').DataTable({          
      "iDisplayLength": 15,
    });
     $('#checkin').datepicker({
      format:'yyyy-mm-dd',
      autoclose: true
    });
     $('#checkout').datepicker({
      format:'yyyy-mm-dd',
      autoclose: true
    });
     toastr.options = {
      "closeButton": true,
      "debug": false,
      "newestOnTop": false,
      "progressBar": true,
      "positionClass": "toast-top-right",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "5000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    };    
    $('#Tamu').typeahead({
      source: function (query, result) {
        $.ajax({
          url: "DataTamu.php",
          data: 'query=' + query,            
          dataType: "json",
          type: "POST",
          success: function (data) {
            result($.map(data, function (item) {
              return item;
            }));
          }
        });
      }
    });  
  });
</script>
</body>
</html>