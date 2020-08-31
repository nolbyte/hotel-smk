<?php
defined("RESMI") or die("Akses ditolak");
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
<div class="row">
	<div class="box box-danger">
		<div class="box-header with-border">
			<h3 class="box-title">Reservation Detail</h3>
		</div>
		<div class="box-body">
			<div class="row">
				<div class="col-md-6">
					<dl class="dl-horizontal">
						<dt>Reservation Code</dt>
							<dd><?= $rsv['kode_booking']?></dd>
						<dt>Guest Name</dt>
							<dd><?= $rsv['prefix'].'. '.$rsv['nama_depan'].' '.$rsv['nama_belakang']?></dd>
						<dt>Identity</dt>
						<dd><?= $rsv['tipe_identitas'].' - '.$rsv['nomor_identitas']?></dd>
						<dt>Nationality</dt>
						<dd><?= $rsv['warga_negara']?></dd>
						<dt>Company Name</dt>
						<dd><?= $rsv['nama_perusahaan']?></dd>
						<dt>Address</dt>
						<dd><?= nl2br($rsv['alamat_jalan'])?></dd>
						<dt>City</dt>
						<dd><?= $rsv['alamat_kabupaten']?></dd>
						<dt>Province</dt>
						<dd><?= $rsv['alamat_provinsi']?></dd>
						<dt>Zip Code</dt>
						<dd><?= $rsv['kode_pos']?></dd>
						<dt>Country</dt>
						<dd><?= $rsv['negara']?></dd>
						<dt>Contact Person</dt>
						<dd><?= $rsv['contact_person']?></dd>
						<dt>Phone Number</dt>
						<dd><?= $rsv['nomor_telp']?></dd>
						<dt>Fax Number</dt>
						<dd><?= $rsv['nomor_fax']?></dd>
						<dt>Email</dt>
						<dd><?= $rsv['email']?></dd>
						<dt>Flight Arrival</dt>
						<dd><?= $rsv['flight_arrival']?></dd>
						<dt>Flight Departure</dt>
						<dd><?= $rsv['flight_departure']?></dd>
					</dl>
				</div>
				<div class="col-md-6">
					<dl class="dl-horizontal">
						<dt>Room Type</dt>
						<dd><?= $rsv['nama_kamar_tipe']?></dd>
						<dt>Room Number</dt>
						<dd><?= $rsv['nomor_kamar']?></dd>
						<dt>Number of Guest</dt>
						<dd><?= $rsv['jumlah_dewasa']?></dd>
						<dt>Room Rate</dt>
						<dd><?= format_rp($rsv['harga_malam'])?></dd>
						<dt>Arrival Date</dt>
						<dd><?= $rsv['tanggal_checkin']?></dd>
						<dt>Departure Date</dt>
						<dd><?= $rsv['tanggal_checkout']?></dd>
						<dt>Total Payment</dt>
						<dd><?= format_rp($rsv['total_biaya'])?></dd>
						<dt>Advance Deposit</dt>
						<dd><?= format_rp($rsv['deposit'])?></dd>
						<dt>Payment Instruction</dt>
						<dd><?= format_rp($payment)?></dd>
						<dt>Payment Instruction</dt>
						<dd><?= $rsv['remark']?></dd>
						<dt>Trace</dt>
						<dd><?= $rsv['trace']?></dd>
						<dt>Notes</dt>
						<dd><?= $rsv['notes']?></dd>
						<dt>Status</dt>
						<dd><?= $rsv['status']?></dd>
						<dt>Reservation Agent</dt>
						<dd><?= $rsv['nama']?></dd>
					</dl>
				</div>
			</div>
		</div>
		<div class="box-footer">
			<a target="_blank" href="reservation.php?id=<?= $rsv['id_reservasi']?>" class="btn btn-success btn-flat"><i class="fa fa-print"></i> Print</a>
			<a class="btn btn-default btn-flat" href="index.php?mod=transaksi&hal=booking"><i class="fa fa-reply"></i> Back</a>
		</div>
	</div>
</div>