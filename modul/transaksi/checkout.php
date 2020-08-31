<?php
defined("RESMI") or die("Akses ditolak");
if(isset($_SESSION['SaveData'])){
	?>
	<script>toastr.success('Tamu berhasil checkout', 'Sukses!', {timeOut: 5000, progressBar: true})</script>
	<?php
	unset($_SESSION['SaveData']);
}
$status='REGISTER';
$sqlt = $db->prepare("SELECT * FROM transaksi_kamar tk
	JOIN tamu t ON tk.id_tamu=t.id_tamu
	JOIN kamar k ON tk.id_kamar=k.id_kamar
	WHERE status = ? ORDER BY id_transaksi_kamar");
$sqlt->bindParam(1, $status);
$sqlt->execute();
$listC = $sqlt->rowCount();
?>
<div class="row">
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">Check Out</h3>&nbsp;&nbsp;<small>Pilih kamar yang terpakai</small>
		</div>
		<div class="box-body">
			<?php if(!empty($listC)){ ?>			
				<div class="row">
					<?php foreach($sqlt->fetchAll() as $row){ ?>
						<div class="col-sm-3">
							<div class="small-box bg-red">
								<div class="inner">
									<h3><?= $row['nomor_kamar']; ?></h3>
									<p><?= $row['prefix'].'. '.$row['nama_depan'].'&nbsp;'.$row['nama_belakang']; ?></p>
								</div>
								<div class="icon">
									<i class="fa fa-bed"></i>
								</div>
								<a class="small-box-footer" href="index.php?mod=transaksi&hal=checkout-proses&id=<?= $row['id_transaksi_kamar']?>">Pilih Kamar</a>
							</div>
						</div>
					<?php } ?>
				</div>	
			<?php }else{ ?>
				<div class="alert alert-warning">
					<h4>Mohon Maaf</h4>
					Saat ini, tidak ada kamar yang terpakai.
				</div>
			<?php } ?>		
		</div>
	</div>
</div>