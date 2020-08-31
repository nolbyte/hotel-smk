<?php
defined("RESMI") or die("Akses ditolak");

//Jumlah Kamar
$sql0 = $db->prepare("SELECT COUNT(*) FROM kamar");
$sql0->execute();
$jk = $sql0->fetchColumn();
//Kamar Tersedia
$sql = $db->prepare("SELECT COUNT(*) FROM kamar WHERE status_kamar = 'TERSEDIA'");
$sql->execute();
$kt = $sql->fetchColumn();
//Kamar Terpakai
$sql2 = $db->prepare("SELECT COUNT(*) FROM kamar WHERE status_kamar = 'TERPAKAI'");
$sql2->execute();
$ktp = $sql2->fetchColumn();
//Kamar Terpakai
$sql3 = $db->prepare("SELECT COUNT(*) FROM kamar WHERE status_kamar = 'KOTOR'");
$sql3->execute();
$ktr = $sql3->fetchColumn();
?>
<div class="row">
	<div class="col-sm-3">
		<div class="small-box bg-aqua">
			<div class="inner">
				<h3><?= $jk ?></h3>
				<p>Jumlah Kamar</p>
			</div>
			<div class="icon">
				<i class="fa fa-bed"></i>
			</div>
			<a class="small-box-footer" href="index.php?mod=kamar&hal=list">Lihat Selengkapnya</a>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="small-box bg-red">
			<div class="inner">
				<h3><?= $ktp; ?></h3>
				<p>Kamar Terpakai</p>
			</div>
			<div class="icon">
				<i class="fa fa-bed"></i>
			</div>
			<a class="small-box-footer" href="index.php?mod=kamar&hal=list">Lihat Selengkapnya</a>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="small-box bg-green">
			<div class="inner">
				<h3><?= $kt; ?></h3>
				<p>Kamar Tersedia</p>
			</div>
			<div class="icon">
				<i class="fa fa-bed"></i>
			</div>
			<a class="small-box-footer" href="index.php?mod=kamar&hal=list">Lihat Selengkapnya</a>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="small-box bg-yellow">
			<div class="inner">
				<h3><?= $ktr; ?></h3>
				<p>Kamar Kotor</p>
			</div>
			<div class="icon">
				<i class="fa fa-bed"></i>
			</div>
			<a class="small-box-footer" href="index.php?mod=kamar&hal=list">Lihat Selengkapnya</a>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="box">
			<div class="box-header with-border">
				<h3 class="box-title">Tamu yang sedang menginap</h3>
			</div>
			<div class="box-body">
				<?php
					$status = 'REGISTER';
					$sql = $db->prepare("SELECT * FROM transaksi_kamar tk JOIN tamu t ON tk.id_tamu=t.id_tamu JOIN kamar k ON tk.id_kamar=k.id_kamar WHERE status = ?");
					$sql->bindParam(1, $status);
					$sql->execute();
					$num = $sql->rowCount();
				?>
				<?php if($num === 0){
				?>
				<div class="alert alert-warning">
					<h4>Mohon maaf</h4>
					Untuk sementara tidak ada tamu yang sedang menginap di hotel.
				</div>				
				<?php }else{ ?>
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Nama Tamu</th>
								<th># Kamar</th>
								<th>Tanggal / Waktu Check-In</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								foreach($tamu = $sql->fetchAll() as $row){
							?>
							<tr>
								<td><?= $row['prefix'].'. '.$row['nama_depan'].' '.$row['nama_belakang']?></td>
								<td><?= $row['nomor_kamar']?></td>
								<td><?= $row['tanggal_checkin'].'-'.$row['waktu_checkin']?></td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="box">
			<div class="box-header with-border">
				<h3 class="box-title">Tamu yang akan checkout hari ini</h3>
			</div>
			<div class="box-body">
				<?php
					$date = date('Y-m-d');
					echo 'Tanggal Hari Ini: '.$date;
					$status = 'REGISTER';
					$sql = $db->prepare("SELECT * FROM transaksi_kamar tk JOIN tamu t ON tk.id_tamu=t.id_tamu JOIN kamar k ON tk.id_kamar=k.id_kamar WHERE status = ? AND tanggal_checkout = ?");
					$sql->bindParam(1, $status);
					$sql->bindParam(2, $date);
					$sql->execute();
					$num = $sql->rowCount();					
				?>
				<?php if($num === 0){ ?>
				<div class="alert alert-warning">
					<h4>Mohon maaf</h4>
					Tidak ada tamu yang akan checkout hari ini.
				</div>				
				<?php }else{ ?>
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Nama Tamu</th>
								<th># Kamar</th>
								<th>Check-In</th>
								<th>Check-Out</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								foreach($tamu = $sql->fetchAll() as $row){
							?>
							<tr>
								<td><?= $row['prefix'].'. '.$row['nama_depan'].' '.$row['nama_belakang']?></td>
								<td><?= $row['nomor_kamar']?></td>
								<td><?= $row['tanggal_checkin']?></td>
								<td><?= $row['tanggal_checkout']?></td>
								<td><a href="#" class="btn btn-xs btn-info">Proses</a></td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				<?php } ?>
			</div>
		</div>
	</div>
</div>