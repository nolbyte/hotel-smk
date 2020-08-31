<?php
defined("RESMI") or die("Akses ditolak");
if(isset($_SESSION['SaveData'])){
	?>
	<script>toastr.success('Data tamu berhasil disimpan', 'Sukses!', {timeOut: 5000, progressBar: true})</script>
	<?php
	unset($_SESSION['SaveData']);
}
$status='CHECK OUT';
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
			<h3 class="box-title">Daftar Tamu Checkout</h3>
		</div>
		<div class="box-body">
			<div class="callout callout-warning">
				<h4><i class="icon fa fa-info"></i> Informasi</h4>				
				<p>Halaman ini berisi daftar tamu yang sudah checkout atau pernah menginap di hotel.</p>
			</div>
			<table id="tabelku" class="table table-striped">
				<thead>
					<tr>
						<td>No</td>
						<td>Nama Tamu</td>
						<td>Tanggal Checkin</td>
						<td>Tanggal Checkout</td>
						<td>Nomor Telepon</td>						
						<td>Alamat Email</td>
					</tr>
				</thead>
				<tbody>
					<?php 
						$no = 1;
						foreach($sqlt->fetchAll() as $row){ 
					?>
					<tr>
						<td><?= $no++ ?></td>
						<td><?= $row['prefix'].'. '.$row['nama_depan'].' '.$row['nama_belakang']?></td>
						<td><?= $row['tanggal_checkin'].' - '.$row['waktu_checkin']?></td>
						<td><?= $row['tanggal_checkout'].' - '.$row['waktu_checkout']?></td>
						<td><?= $row['nomor_telp']?></td>						
						<td><?= $row['email']?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>