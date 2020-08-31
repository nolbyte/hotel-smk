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
if($trans === FALSE){
	echo 'Transaksi tidak ditemukan';
	die();
}
//Ambil transaksi layanan
$sql = $db->prepare("SELECT * FROM transaksi_layanan tl
	JOIN layanan l ON tl.id_layanan=l.id_layanan
	WHERE id_transaksi_kamar = ?");
$sql->bindParam(1, $id);
$sql->execute();

//hitung total layanan
$sub = $db->prepare("SELECT sum(total) as totalSub FROM transaksi_layanan WHERE id_transaksi_kamar = ?");
$sub->execute(array($id));
$sb = $sub->fetch(PDO::FETCH_ASSOC);
//hitung transaksi
$checkin=date_create($trans['tanggal_checkin']);
$checkout=date_create($trans['tanggal_checkout']);
$durasi=date_diff($checkin,$checkout)->format('%a');
$subtotal_kamar=$durasi * $trans['harga_malam'];
$subtotal=$subtotal_kamar + $sb['totalSub'];
$ppn=$subtotal * 0.10;
$total=$subtotal + $ppn;
$grand_total=$subtotal + $ppn - $trans['deposit'];
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
</head>
<body onload="window.print();" class="hold-transition">
	<div class="wrapper">		          
		<section class="invoice">
			<h2 class="page-header">
			  <img src="assets/img/logo.png" width="65px">&nbsp;&nbsp;<?= $owner['nama_hotel']; ?> 
			  <span class="small"><?= $owner['nama_perusahaan']; ?></span>
			  <span class="lead text-red pull-right"><b>CHECKOUT BILLING</b></span>
			</h2>
			<h6>
			  <?= $owner['alamat_jalan'].', '.$owner['alamat_kabupaten'].' - '.$owner['alamat_provinsi']; ?>
			  <br/><b>Telp :</b> <?= $owner['nomor_telp']; ?> -  <b>Fax :</b> <?= $owner['nomor_fax']; ?> -  <b>Email :</b> <?php echo $owner['email']; ?>
			  <br/><b><?= $owner['website']; ?></b>
			</h6>
			<br/>
			<br/>
			<div class="row">
				<table class="table">
					<tr>
						<td style="padding-left: 15px">
							Ditujukan Kepada :
							<address>
								<strong><?= $trans['prefix'].'. '.$trans['nama_depan'].'&nbsp;'.$trans['nama_belakang']; ?></strong><br/>
								<?= $trans['alamat_jalan']; ?><br/>
								<?= $trans['alamat_kabupaten'].' - '.$trans['alamat_provinsi']; ?><br/>
								<br/>
								Nomor Telp : <?= $trans['nomor_telp']; ?><br/>
							</address>
						</td>
						<td>
							<b>NOMOR INVOICE : </b><br/>
							<span class="lead"><?= $trans['nomor_invoice']; ?></span><br/><br/>
							<b>Tanggal Terbit :</b><br/>
							<span class="lead"><?= date('d M Y'); ?></span>
						</td>
					</tr>
				</table>
			</div>

			<h3>RINCIAN TAGIHAN</h3>
			<table class="table table-bordered table-striped table-responsive">
				<thead>
					<tr>
						<th>Produk / Layanan</th>
						<th>Harga</th>
						<th>Qty</th>
						<th>Total</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Room Type : <?= $trans['nama_kamar_tipe']; ?></td>
						<td><?= format_rp($trans['harga_malam']); ?></td>
						<td><?= $durasi; ?> Malam</td>
						<td><?= format_rp($trans['total_biaya_kamar']); ?></td>
					</tr>
					<?php foreach($sql->fetchAll() as $transaksi_layanan) { ?>
						<tr>
							<td><?= $transaksi_layanan['nama_layanan']; ?></td>
							<td><?= format_rp($transaksi_layanan['harga_layanan']); ?></td>
							<td><?= $transaksi_layanan['jumlah'].'&nbsp;'.$transaksi_layanan['satuan']; ?></td>
							<td><?= format_rp($transaksi_layanan['total']); ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

			<div class="row">
				<table class="table">
					<tr>
						<td style="width: 45%;padding-left: 15px">
							<p class="text-muted well well-sm no-shadow">
								<b>Catatan :</b> Mohon simpan bukti pembayaran ini sebaik mungkin. Pihak hotel tidak akan melayani keluhan-keluhan tamu yang tidak memiliki bukti pembayaran resmi oleh Pihak Hotel
							</p>
						</td>
						<td style="width: 55%">
							<table class="table table-bordered table-responsive">
								<tr>
									<td><b>Sub-Total</b></td>
									<td><?= format_rp($subtotal); ?></td>
								</tr>
								<tr>
									<td>PPn 10%</td>
									<td><?= format_rp($ppn); ?></td>
								</tr>					
								<tr>							
									<td >Gran Total</td>
									<td><?= format_rp($total); ?></td>
								</tr>						
								<tr>							
									<td>Jumlah Deposit</td>
									<td class="text-red"><?= format_rp($trans['deposit']); ?></td>
								</tr>
								<tr>							
									<td><b>Total Bayar</b></td>
									<td><b><?= format_rp($grand_total); ?></b></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
			<div class="row">
				<table class="table">
					<tr>
						<td>
							<p style="text-align: center"><?= $usr['jabatan']?></p><br><br><br>
							<p style="text-align: center;font-weight: bold"><?= $usr['nama']?></p>
						</td>
						<td>
							<p style="text-align: center">Customer</p><br><br><br>
							<p style="text-align: center;font-weight: bold"><?= $trans['prefix'].'. '.$trans['nama_depan'].'&nbsp;'.$trans['nama_belakang']; ?></p>
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