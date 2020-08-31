<?php
defined("RESMI") or die("Akses ditolak");
if(isset($_SESSION['SaveData'])){
	?>
	<script>toastr.success('Data tamu berhasil disimpan', 'Sukses!', {timeOut: 5000, progressBar: true})</script>
	<?php
	unset($_SESSION['SaveData']);
}
$status='REGISTER';
$sqlt = $db->prepare("SELECT * FROM transaksi_kamar tk
	JOIN tamu t ON tk.id_tamu=t.id_tamu
	JOIN kamar k ON tk.id_kamar=k.id_kamar
	WHERE status = ? ORDER BY id_transaksi_kamar DESC");
$sqlt->bindParam(1, $status);
$sqlt->execute();
?>
<div class="row">
	<div class="box box-warning">
		<div class="box-header with-border">
			<h3 class="box-title">Daftar Tamu In House</h3>
		</div>
		<div class="box-body">
			<table class="table table-striped">
				<thead>
					<tr>
						<td>#Kamar</td>
						<td>Nama Tamu</td>
						<td>Tanggal Checkin</td>
						<td>Tanggal Checkout</td>
						<td>Jumlah Deposit</td>
						<td></td>
					</tr>
				</thead>
				<tbody>
					<?php foreach($sqlt->fetchAll() as $row){ ?>
					<tr>
						<td><?= $row['nomor_kamar']?></td>
						<td><?= $row['prefix'].'. '.$row['nama_depan'].' '.$row['nama_belakang']?></td>
						<td><?= $row['tanggal_checkin'].' - '.$row['waktu_checkin']?></td>
						<td><?= $row['tanggal_checkout'].' - '.$row['waktu_checkout']?></td>
						<td><?= format_rp($row['deposit'])?></td>
						<td>
							<a class="btn btn-xs btn-info btn-flat" href="index.php?mod=transaksi&hal=checkin-update&transaksi=<?=$row['id_transaksi_kamar']?>">update</a>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>