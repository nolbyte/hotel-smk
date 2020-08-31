<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 0);
	session_start();
	define("RESMI", "OK");
	
	//konfigurasi
	require('config/database.php');
	require('config/fungsi.php');
  require('config/gump.class.php');
  if(isset($_GET['mod'])){
    $mod = antixss($_GET['mod']);
    $hal = antixss($_GET['hal']);
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
  </head>
<body class="hold-transition login-page">
  <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $gump = new GUMP();
      
      $username= $_POST["username"];
      $sandi   = $_POST["sandi"];
      
      $_POST = array(
        'username' => $username,
        'sandi' => $sandi
      );

      $_POST = $gump->sanitize($_POST);

      $gump->validation_rules(array(
        'username'=> 'required',
        'sandi'   => 'required|min_len,6'
      ));

      $gump->filter_rules(array(
        'username' => 'trim|sanitize_string'
      ));

      $validated_data = $gump->run($_POST);

      if($validated_data === false){
        $error[] = $gump->get_readable_errors(true);        
      }else{
        $sql = $db->prepare("SELECT id_user, passwd, nama, id_user_role, jabatan FROM user WHERE username = ?");
        //$sql->bindParam(1, $username);
        $sql->execute(array($username));      
        $r = $sql->fetch(PDO::FETCH_ASSOC);
        if($r){
          if(password_verify($sandi, $r['passwd'])){
            $_SESSION['userid']  = $r['id_user'];
            $_SESSION['username']= $username;
            $_SESSION['ngaran']  = $r['nama'];
            $_SESSION['role']    = $r['id_user_role'];
            $_SESSION['jabatan'] = $r['jabatan'];
            header("Location:index.php");
          } else {
            ?><script>toastr.error('Password Salah', 'Error!', {timeOut: 3000, progressBar: true})</script><?php
          }
        } else {
          ?><script>toastr.error('Username tidak ditemukan', 'Error!', {timeOut: 3000, progressBar: true})</script><?php
        }
      }
    }   
  ?>
  <div class="login-box">
    <div class="login-logo">
      <a href="index.php"><b>Hotel Management</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
      <?php
      if(isset($error)){
        foreach($error as $error){
          ?>
          <script>toastr.error('<?php echo $error ?>', 'Error!', {timeOut: 3000, progressBar: true})</script>
          <?php
        }
      }
      ?>
      <p class="login-box-msg">Sign in to start your session</p>

      <form action="" method="post">
        <div class="form-group has-feedback">
          <input type="text" name="username" class="form-control" placeholder="Username Login" readonly onfocus="this.removeAttribute('readonly');" required="">
          <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
          <input type="password" name="sandi" class="form-control" placeholder="Password" required="">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row">
          <div class="col-xs-8">

          </div>
          <!-- /.col -->
          <div class="col-xs-4">
            <button type="submit" class="btn btn-primary btn-block btn-flat" name="log">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
    </div>
  </div>
  <script src="assets/js/bootstrap.min.js"></script>  
  <script src="assets/js/app.min.js"></script>
  <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="assets/plugins/datatables/dataTables.bootstrap.min.js"></script>
  <script src="assets/plugins/fastclick/fastclick.js"></script>
  <script src="assets/plugins/bootbox/bootbox.min.js"></script>
  <script src="assets/plugins/jquery.mask.js"></script>
  <script src="assets/plugins/datepicker/bootstrap-datepicker.js"></script>  
  <script>
    $(document).ready(function() {     
     toastr.options = {
      "closeButton"      : true,
      "debug"            : false,
      "newestOnTop"      : false,
      "progressBar"      : true,
      "positionClass"    : "toast-top-right",
      "preventDuplicates": false,
      "onclick"          : null,
      "showDuration"     : "300",
      "hideDuration"     : "1000",
      "timeOut"          : "5000",
      "extendedTimeOut"  : "1000",
      "showEasing"       : "swing",
      "hideEasing"       : "linear",
      "showMethod"       : "fadeIn",
      "hideMethod"       : "fadeOut"
    }
    });
  </script> 
</body>
</html>