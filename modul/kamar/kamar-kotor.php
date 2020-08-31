<?php
defined("RESMI") or die("Akses ditolak");
if(isset($_SESSION['SaveData'])){
	?>
	<script>toastr.success('Tamu berhasil checkin', 'Sukses!', {timeOut: 5000, progressBar: true})</script>
	<?php
	unset($_SESSION['SaveData']);
}
//ambil data kamar tersedia
$status = 'KOTOR';
$sql = $db->prepare("SELECT * FROM kamar k
	JOIN kamar_tipe kt ON k.id_kamar_tipe=kt.id_kamar_tipe
	WHERE status_kamar = ? ORDER BY nomor_kamar");
$sql->execute(array($status));
$list = $sql->rowCount();
?>
<div class="row">
	<div class="box box-warning">
		<div class="box-header with-border">
			<h3 class="box-title">Pembersihan Kamar</h3>&nbsp;&nbsp;<small>Pilih kamar yang KOTOR</small>
		</div>
		<div class="box-body">
			<?php if(!empty($list)){ ?>
				<div class="row">
					<?php foreach($sql->fetchAll() as $kamar_tersedia){ ?>
						<div class="col-sm-3">
							<div class="small-box bg-yellow">
								<div class="inner">
									<h3><?= $kamar_tersedia['nomor_kamar']; ?></h3>
									<p><?= $kamar_tersedia['nama_kamar_tipe']; ?></p>
								</div>
								<div class="icon">
									<i class="fa fa-bed"></i>
								</div>
								<a class="small-box-footer" href="index.php?mod=kamar&hal=update&id=<?=$kamar_tersedia['id_kamar']; ?>">Pilih Kamar</a>
							</div>
						</div>
					<?php } ?>
				</div>
			<?php }else{ ?>
				<div class="alert alert-warning">
					<h4>Mohon Maaf</h4>
					Saat ini, tidak ada kamar yang KOTOR.
				</div>
			<?php } ?>
		</div>
	</div>
</div>