<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
define("RESMI", "OK");

require('config/database.php');
require('config/fungsi.php');
require('config/gump.class.php');

if(!isset($_SESSION['userid'])){
  header("Location: login.php");
}
if(!empty($_GET['id'])){
	$id = intval($_GET['id']);
}

//Ambil Transaksi kamar
$sqlt = $db->prepare("SELECT * FROM transaksi_kamar tk
		JOIN tamu t ON tk.id_tamu=t.id_tamu
		JOIN kamar k ON tk.id_kamar=k.id_kamar
		JOIN kamar_tipe kt ON k.id_kamar_tipe=kt.id_kamar_tipe
		WHERE tk.id_transaksi_kamar = ?");
$sqlt->bindParam(1, $id);
$sqlt->execute();
$trans = $sqlt->fetch(PDO::FETCH_ASSOC);

//Ambil transaksi layanan
$sqll = $db->prepare("SELECT * FROM transaksi_layanan ly
	JOIN layanan l ON ly.id_layanan=l.id_layanan 
	WHERE id_transaksi_kamar = ?");
//$sql->bindParam(1, $id);
$sqll->execute(array($id));


//hitung total layanan
$sub = $db->prepare("SELECT sum(total) as totalSub FROM transaksi_layanan WHERE id_transaksi_kamar = ?");
$sub->execute(array($id));
$sb = $sub->fetch(PDO::FETCH_ASSOC);
/*hitung transaksi
$checkin=date_create($trans['tanggal_checkin']);
$checkout=date_create($trans['tanggal_checkout']);
$durasi=date_diff($checkin,$checkout)->format('%a');
$subtotal_kamar=$durasi * $trans['harga_malam'];
$subtotal=$subtotal_kamar + $sb['totalSub'];
$ppn=$subtotal * 0.10;
$total=$subtotal + $ppn;
$grand_total=$subtotal + $ppn - $trans['deposit'];
*/
$tglM = date('M d', strtotime($trans['tanggal_checkin']));
$tglC = date('M d', strtotime($trans['tanggal_checkout']));
$hari = date('Y-m-d');
$tangall = '';
if($hari <= $trans['tanggal_checkout']){
	$tanggall = date("Y-m-d", strtotime($trans['tanggal_checkout'].'-1 day'));
}elseif ($hari > $trans['tanggal_checkout']) {
	$tanggall = date("Y-m-d", strtotime($hari.'-1 day'));
}else{
	echo 'nah error';
}

//setting
$user = $_SESSION['userid'];
$sqlu = $db->prepare("SELECT * FROM user u
	JOIN user_role ur ON u.id_user_role=ur.id_user_role
	WHERE id_user = ?");
$sqlu->execute(array($user));
$usr = $sqlu->fetch(PDO::FETCH_ASSOC);

//ambil settingan
$sql = $db->prepare("SELECT * FROM perusahaan");
$sql->execute();
$owner = $sql->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html style="height: auto;">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
		.kotak{
		width: 205px;
		height: 85px;
		outline: 1px solid black;
		display: block;	
		}
		.boks{
		width: 500px;
		height: 200px;
		outline: 0px solid black;
		display: block;	
		font-size: 8pt;
		padding: 5px;	
	}
	</style>
</head>
<body onload="window.print();" class="hold-transition">
	<div class="wrapper">		          
		<section class="content">
			<table>
				<tr>
					<td style="width: 30%">
						<div class="kotak">
							<div style="padding: 10px">
								<?= $trans['prefix'].'. '.$trans['nama_depan'].'&nbsp;'.$trans['nama_belakang']; ?><br>MAHADHIKA PACKAGE
							</div>
						</div>
					</td>
					<td style="width: 30%" class="text-center">
						<span class="text-center"><b>FOLIO</b></span>
					</td>
					<td style="width: 30%">
						<div class="text-center" style="font-size: 8pt">
							<img src="logo/<?=$owner['logo']?>" width="65px"><br><?= $owner['nama_hotel']; ?><br>
							<?= $owner['alamat_jalan'].', '.$owner['alamat_kabupaten'].' - '.$owner['alamat_provinsi']; ?>
							<br/><b>Telp :</b> <?= $owner['nomor_telp']; ?> -  <b>Fax :</b> <?= $owner['nomor_fax']; ?> -  <b>Email :</b> <?php echo $owner['email']; ?>
							<br/><b><?= $owner['website']; ?></b> 
						</div>
					</td>
				</tr>
			</table>
			<div class="row">
				<span  style="padding-top:25px" class="pull-right">DATE: <?= date('d-M-Y')?></span>
			</div>
			<div class="row">
				<table class="table table-bordered">
					<tr>
						<td class="text-center">FOLIO NO</td>
						<td class="text-center">ROOM NO.</td>
						<td class="text-center">No. of GUESTS</td>
						<td class="text-center">ARRIVAL</td>
						<td class="text-center">DEPARTURE</td>
					</tr>
					<tr>
						<td class="text-center"><?= $trans['nomor_invoice']?></td>
						<td class="text-center"><?= $trans['nomor_kamar']?></td>
						<td class="text-center">Adl:<?= $trans['jumlah_dewasa']?>/Chl:<?= $trans['jumlah_anak']?></td>
						<td class="text-center"><?= $trans['tanggal_checkin']?></td>
						<td class="text-center"><?= $trans['tanggal_checkout']?></td>
					</tr>
				</table>
				<table class="table table-bordered">
					<tr>
						<td></td>
						<td class="text-center">DATE</td>
						<td class="text-center">REFERENCE</td>
						<td class="text-center">DESCRIPTION</td>
						<td class="text-center">AMOUNT (Rp.)</td>
					</tr>
					<tr>
						<td></td>
						<td><?= $tglM?></td>
						<td></td>
						<td>Posting Adv Depos</td>
						<td class="text-right"><?= number_format($trans['deposit'])?><span class="pull-right">&nbsp;&nbsp;-</span></td>
					</tr>
					<?php
						$data = createRange($trans['tanggal_checkin'], $tanggall, 'M d');
						implode(',', $data);
						$sum1 = 0;
						foreach($data as $data){
					?>
					<tr>
						<td></td>
						<td><?= $data;?></td>
						<td>IDR: <?= number_format($trans['harga_malam'])?></td>
						<td>Room Package/<?=$trans['nomor_kamar'].'/'.$trans['nama_depan']?></td>
						<td class="text-right"><?= number_format($trans['harga_malam'])?><span class="pull-right">&nbsp;&nbsp;+</span></td>
					</tr>
					<?php
						$sum1 += $trans['harga_malam'];
						} 
						$no = 2;
						$no2 = $no+1;
						$sum2 = 0;
						foreach($sqll->fetchAll() as $row){ 
						$tgll = date('M d', strtotime($row['tanggal']));
					?>
						<tr>
							<td></td>
							<td><?= $tgll ?></td>
							<td>IDR: <?= number_format($row['harga_layanan'])?></td>
							<td><?= $row['jumlah'].'&nbsp;'.$row['satuan'].'&nbsp;'.$row['nama_layanan']?></td>
							<td class="text-right"><?= number_format($row['total'])?><span class="pull-right">&nbsp;&nbsp;+</span></td>
						</tr>
					<?php 
						$sum2 += $row['total'];
						} 
					?>
					<tr>
						<td colspan="5"></td>
					</tr>
					<tr>
						<td colspan="4" class="text-right">Sub Total</td>
						<td class="text-right">
							<?php
								$subT = $sum1+$sum2;
							 	echo number_format($subT);
							 ?>
						</td>
					</tr>
					<tr>
						<td colspan="4" class="text-right">Tax(21%)</td>
						<td class="text-right">
							<?php
								$tax = $subT*0.21;
							 	echo number_format($tax);
							?>
						</td>
					</tr>
					<tr>
						<td colspan="4" class="text-right">Total</td>
						<td class="text-right">
							<?= number_format($subT+$tax)?>
						</td>
					</tr>
					<tr>
						<td colspan="4" class="text-right">Payment Due (Deposit - Total)</td>
						<td class="text-right">
							<?= number_format($subT+$tax-$trans['deposit'])?>
						</td>
					</tr>
				</table>
			</div>
			<div class="boks">
				"I agree that I am personallyliable for the payment of the following statement and if the person, company or association indicated by me as being responsible for payment of some does not do so, that my liability for such payment shall be joint and several with such person, company or association"
			</div>
			<div class="row">
				<table class="table">
					<tr>
						<td>
							<p style="text-align: center">Guest Signature</p><br><br>
							<p style="text-align: center;font-weight: bold"><u><?= $trans['prefix'].'. '.$trans['nama_depan'].'&nbsp;'.$trans['nama_belakang']; ?></u></p>
						</td>
						<td>
							<p style="text-align: center">Guest Service Representative</p><br><br>
							<p style="text-align: center;font-weight: bold"><u><?= $usr['nama']?></u></p>
						</td>
					</tr>
				</table>
			</div>
		</section>     
	</div>
	<script src="assets/js/bootstrap.min.js"></script>  
	<script src="assets/js/app.min.js"></script>
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
				"iDisplayLength": 10,
			});
			$('#checkin').datepicker();
			$('#checkout').datepicker();
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
			}
		});
	</script>
</body>
</html>