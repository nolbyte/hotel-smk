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
if(isset($_GET['id'])){
	$id = intval($_GET['id']);
}
$sql = $db->prepare("SELECT * FROM reservasi r
	JOIN kamar k ON r.id_kamar=k.id_kamar
	JOIN kamar_tipe kt ON k.id_kamar_tipe=kt.id_kamar_tipe
	JOIN user u ON r.id_user=u.id_user
	JOIN tamu t ON r.id_tamu=t.id_tamu
	WHERE id_reservasi = ?");
$sql->execute(array($id));
$rsv = $sql->fetch(PDO::FETCH_ASSOC);
if($rsv === false){
	echo 'data tidak ditemukan';
	die();
}
$payment = $rsv['deposit'] - $rsv['total_biaya'];
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
	.eusi {
		height: auto;
		overflow: hidden;
		margin-bottom: 0px;
	}

	.katuhu {
		width: 250px;
		float: right;
		border-bottom: 1px solid black;
		display: inline-block;
		vertical-align: bottom;		
	}

	.kenca {
		float: none; /* not needed, just for clarification */		
		width: 230;		
		overflow: hidden;
	}
	.konfirm {
		height: auto;
		overflow: hidden;
		padding-bottom: 0px;
		margin-bottom: 0px;
	}	
	.logo {
		width: 15%;
		float: right;
		display: inline-block;
	}
	.tabel{
		float: none;
		width: 80%;
	}
	.kotak{
		width: 65px;
		height: 20px;
		outline: 1px solid black;
		display: block;
		margin-left: auto;
		margin-right: auto;
	}
	</style>
</head>
<body onload="window.print();" class="hold-transition">
	<div class="wrapper">
		<section class="content">
			<table class="table table-bordered" style="font-size: 9pt">
				<tr>
					<td style="width: 60%">
						<div class="eusi">
							<div class="katuhu align-text-bottom"><br>
								: <?= $rsv['prefix'].'. '.$rsv['nama_depan'].' '.$rsv['nama_belakang']?>
							</div>
							<div class="kenca">
								<i>Name</i><br>Nama
							</div>
						</div>
						<div class="eusi">
							<div class="katuhu align-text-bottom"><br>
								: <?= $rsv['nama_perusahaan']?>
							</div>
							<div class="kenca">
								<i>Company Affiliation</i><br>Nama Perusahaan
							</div>
						</div>
						<div class="eusi">
							<div class="katuhu align-text-bottom"><br>
								: <?= $rsv['tanggal_checkin']?>
							</div>
							<div class="kenca">
								<i>Arrival Date</i><br>Tgl. Kedatangan
							</div>
						</div>
						<div class="eusi">
							<div class="katuhu align-text-bottom"><br>
								: <?= $rsv['tanggal_checkout']?>
							</div>
							<div class="kenca">
								<i>Departure Date</i><br>Tgl. Keberangkatan
							</div>
						</div>
						<div class="eusi">
							<div class="katuhu align-text-bottom"><br>
								: 
							</div>
							<div class="kenca">
								<i>Guest Signature</i><br>Tanda Tangan Tamu
							</div>
						</div>
					</td>					
					<td style="width: 40%">
						<div class="text-center">
							<img src="logo/<?=$owner['logo']?>" width="65px" style="text-align: center;"><br><span class="text-center">MAHADHIKA<br>HOTEL</span>
						</div>						
						<i>Confirmation No</i><br>No. Konfirmasi<br>
						<div class="text-center" style="border-bottom: 1px solid black"><b><?= $rsv['kode_booking']?></b>
						</div>
						<div class="text-center">
						<i>Show the card for free parking</i><br>Tunjukkan kartu ini supaya tidak dipungut biaya parkir
						</div>
					</td>
				</tr>
			</table>			
			<hr style="border-top: 1px dashed black">
			<span class="text-center"><i>REGISTRATION</i><br>REGISTRASI</span>
			<div class="konfirm">
				<div class="logo">
					<div class="text-center">
						<img src="logo/<?=$owner['logo']?>" width="65px" style="text-align: center;"><br><span class="text-center">MAHADHIKA<br>HOTEL</span>
					</div>
				</div>
				<div class="tabel table-bordered" style="font-size: 9pt">
					<table class="table" style="padding: 0px auto; margin: 0px auto">						
							<tr>
								<td><i>Confirmation No.</i><br>No. Konfirmasi</td>
								<td><i>Arrival Date</i><br>Tgl Kedatangan</td>
								<td><i>Arrival Time</i><br>Waktu Kedatangan</td>
								<td><i>Daily Room/Rate</i><br>Harga Kamar/Hari</td>
							</tr>												
							<tr>
								<td><?= $rsv['kode_booking']?></td>
								<td><?= $rsv['tanggal_checkin']?></td>
								<td><?= $rsv['waktu_checkin']?></td>
								<td><?= format_rp($rsv['harga_malam'])?></td>
							</tr>
						
					</table>
				</div>
			</div>
			<span style="font-size: 8pt">
				<i>Room rate are subject local taxes adn service chare and will converted using prevalling rate of exchange at time of check out</i><br>
				Harga kamar belum termasuk pajak dan pelayanan dan akan dikonversikan dengan kurs yang berlaku pada hari lapor keluar
			</span>
			<div class="konfirm">
				<div class="logo">
					<div class="text-center">
						
					</div>
				</div>
				<div class="tabel table-bordered" style="font-size: 9pt">
					<table class="table" style="padding: 0px auto; margin: 0px auto">						
						<tr style="padding: 0px auto; margin: 0px auto">
							<td><i>No. of Guest</i><br>Jumlah Tamu</td>
							<td><i>Departure Date</i><br>Tgl Keberangkatan</td>
							<td><i>Room No.</i><br>Nomor Kamar</td>
							<td><i>Advanced Deposit</i><br>Uang Muka</td>
						</tr>					
						<tr style="padding: 0px auto; margin: 0px auto">
							<td><?= $rsv['jumlah_dewasa']?></td>
							<td><?= $rsv['tanggal_checkout']?></td>
							<td><?= $rsv['nomor_kamar']?></td>
							<td><?= format_rp($rsv['deposit'])?></td>
						</tr>
					</table>
				</div>
			</div>			
			<table class="table" style="font-size: 9pt;padding: 0px auto;margin: 0px auto">
				<tr>
					<td colspan="2">
						<div style="height: auto;overflow: hidden;">
							<div style="float: right; width: 75%; border-bottom: 1px solid black;"><br>
								: <?= $rsv['prefix'].'. '.$rsv['nama_depan'].' '.$rsv['nama_belakang']?>
							</div>
							<div style="float: none;width: 22%;">
								<i>Name: Mr/Mrs/Ms</i><br>Nama: Tn/Ny/Nn
							</div>				
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div style="height: auto;overflow: hidden;">
							<div style="float: right; width: 75%; border-bottom: 1px solid black;"><br>
								: <?= $rsv['alamat_jalan']?>
							</div>
							<div style="float: none;width: 22%;">
								<i>Address: Office/Residence</i><br>Alamat: Kantor/Rumah
							</div>				
						</div>
					</td>
				</tr>
				<tr>
					<td>						
						<div style="height: auto;overflow: hidden;">
							<div style="float: right; width: 58%; border-bottom: 1px solid black;"><br>
								: <?= $rsv['alamat_kabupaten']?>
							</div>
							<div style="float: none;width: 38%;">
								<i>City</i><br>Kota
							</div>									
						</div>
					</td>
					<td>						
						<div style="height: auto;overflow: hidden;">
							<div style="float: right; width: 75%; border-bottom: 1px solid black;"><br>
								: <?= $rsv['alamat_provinsi']?>
							</div>
							<div style="float: none;width: 22%;">
								<i>State</i><br>Provinsi
							</div>
						</div>									
					</td>
				</tr>
				<tr>
					<td>
						<div style="height: auto;overflow: hidden;">
							<div style="float: right; width: 58%; border-bottom: 1px solid black;"><br>
								: <?= $rsv['negara']?>
							</div>
							<div style="float: none;width: 38%;">
								<i>Country</i><br>Negara
							</div>									
						</div>
					</td>
					<td>
						<div style="height: auto;overflow: hidden;">
							<div style="float: right; width: 75%; border-bottom: 1px solid black;"><br>
								: <?= $rsv['kode_pos']?>
							</div>
							<div style="float: none;width: 22%;">
								<i>Postal Code</i><br>Kode Pos
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div style="height: auto;overflow: hidden;">
							<div style="float: right; width: 58%; border-bottom: 1px solid black;"><br>
								: <?= $rsv['nomor_identitas']?>
							</div>
							<div style="float: none;width: 38%;">
								<i>Id Card/Passport No.</i><br>No. KTP/No. Paspor
							</div>									
						</div>
					</td>
					<td>
						<div style="height: auto;overflow: hidden;">
							<div style="float: right; width: 75%; border-bottom: 1px solid black;"><br>
								: <?= $rsv['warga_negara']?>
							</div>
							<div style="float: none;width: 22%;">
								<i>Nationality</i><br>Kebangsaan
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div style="height: auto;overflow: hidden;">
							<div style="float: right; width: 75%; border-bottom: 1px solid black;"><br>
								: <?= $rsv['nama_perusahaan']?>
							</div>
							<div style="float: none;width: 22%;">
								<i>Company Affiliation</i><br>Nama Perusahaan
							</div>				
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div style="height: auto;overflow: hidden;">
							<div style="float: right; width: 75%; border-bottom: 1px solid black;">
								: <?= $rsv['tanggal_checkout']?>
								<span class="pull-right" style="font-size: 8pt">
									<i>Please Note: Check Out Time is: 12.00 Noon<br>Waktu Lapor Keluae Pk. 12.00 Siang
								</span>
							</div>
							<div style="float: none;width: 22%;">
								<i>My Departure Date Will be</i><br>Tgl. Keberangkatan Saya
							</div>				
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div style="height: auto;overflow: hidden;">
							<div style="float: right; width: 75%;"><br>
								<table>
									<tr>
										<td style="width:15%; text-align: center">
											<div class="kotak"></div><i>Cash</i><br>Tunai
										</td>
										<td style="width:15%; text-align: center;">
											<div class="kotak"></div><i>Credit Card</i><br>Kartu
										</td>
										<td style="width:15%; text-align: center;">
											<div class="kotak"></div><i>Voucher</i><br>Voucher
										</td>
										<td style="width:15%; text-align: center;">
											<div class="kotak"></div><i>Change to Company</i><br>Beban Perusahaan
										</td>
									</tr>
								</table>
							</div>
							<div style="float: none;width: 22%;">
								<i>Method of Payment</i><br>Cara Pembayaran
							</div>				
						</div>
					</td>
				</tr>
			</table>
			<span style="font-size: 8pt">
				<i>I agree to pay all charge incurred my stay in hotel unless prior arrangements have been made. The management have no reponsibility for guest's valuable as Safety Deposit Box is provide in all guest room.</i><br>
				Saya setuju untuk membayar semua biaya selama saya tinggal di hotel ini kecuali ada perjanjian sebelumnya manajemen tidak bertanggung jawab atas barang yang ditinggalkan di dalam kamar. Simpanlah barang tersebut di lemari besi yang ada.
			</span>
			<div class="row">
				<table class="table">
					<tr>
						<td>
							<p style="text-align: center">Guest Signature</p><br><br>
							<p style="text-align: center;font-weight: bold"><u><?= $rsv['prefix'].'. '.$rsv['nama_depan'].'&nbsp;'.$rsv['nama_belakang']; ?></u></p>
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