<?php
defined("RESMI") or die("Akses ditolak");
if(isset($_SESSION['SaveData'])){
	?>
	<script>toastr.success('Tamu berhasil checkout', 'Sukses!', {timeOut: 5000, progressBar: true})</script>
	<?php
	unset($_SESSION['SaveData']);
}
if(isset($_GET['id'])){
	$id = intval($_GET['id']);
}
$sqlt = $db->prepare("SELECT * FROM transaksi_kamar tk
	JOIN tamu t ON tk.id_tamu=t.id_tamu
	JOIN kamar k ON tk.id_kamar=k.id_kamar
	WHERE id_transaksi_kamar = ?");
$sqlt->bindParam(1, $id);
$sqlt->execute();
$tm = $sqlt->fetch(PDO::FETCH_ASSOC);
if($tm === false){
	echo 'Data tidak ditemukan';
	die();
}
if(isset($_POST['pesan-layanan'])){
	$gump = new GUMP();
	$_POST = array(
		'id_transaksi_kamar' => $_POST['id_transaksi_kamar'],
		'id_layanan'         => $_POST['id_layanan'],
		'harga_layanan'      => $_POST['harga_layanan'],
		'jumlah'             => $_POST['jumlah']
	);
	$_POST = $gump->sanitize($_POST);
	$gump->validation_rules(array(
		'id_transaksi_kamar' => 'required|integer',
		'id_layanan'         => 'required|integer',
		'harga_layanan'      => 'required|numeric',
		'jumlah'             => 'numeric'
	));
	$gump->filter_rules(array(
		'id_transaksi_kamar' => 'trim|sanitize_numbers',
		'id_layanan' => 'trim|sanitize_numbers',
		'harga_layanan' => 'trim|sanitize_numbers',
		'jumlah' => 'trim|sanitize_numbers'
	));
	$ok = $gump->run($_POST);
	if($ok === false){
		$error[] = $gump->get_readble_errors(true);
	}else{
		$total_pesanan = $_POST['harga_layanan'] * $_POST['jumlah'];
		$tanggal       = date('Y-m-d');
		$waktu         = date('H:i:s');
		$sql = $db->prepare("INSERT INTO transaksi_layanan SET id_user = ?, tanggal = ?, waktu = ?, id_transaksi_kamar = ?, id_layanan = ?, jumlah = ?, total = ?");
		$sql->bindParam(1, $_SESSION['userid']);
		$sql->bindParam(2, $tanggal);
		$sql->bindParam(3, $waktu);
		$sql->bindParam(4, $_POST['id_transaksi_kamar']);
		$sql->bindParam(5, $_POST['id_layanan']);
		$sql->bindParam(6, $_POST['jumlah']);
		$sql->bindParam(7, $total_pesanan);
		if(!$sql->execute()){
			print_r($sql->errorInfo());
		}else{
			?>
			<script>toastr.success('Layanan berhasil ditambahkan', 'Sukses!', {timeOut: 5000, progressBar: true})</script>
			<?php
		}
	}
}

?>
<div class="box box-info">
	<div class="box-header with-border">
		<h3 class="box-title">PESANAN KAMAR : <b><?= $tm['nomor_kamar'].' - '.$tm['prefix'].'. '.$tm['nama_depan'].' '.$tm['nama_belakang']?></b></h3><span class="pull-right"><a class="btn btn-danger btn-flat" href="index.php?mod=transaksi&hal=pesan-layanan">kembali</a>
	</div>
	<div class="box-body">
		<?php
		if(isset($error)){
			foreach($error as $error){
				?>
				<script>toastr.error('<?php echo $error ?>', 'Error!', {timeOut: 3000, progressBar: true})</script>
				<?php
			}
		}
		?>
		<?php
		if(!empty($_GET['layanan-filter'])){
			$filter = $_GET['layanan-filter'];			
			$sql = $db->prepare("SELECT * FROM layanan WHERE id_layanan_kategori = ?");
			$sql->execute(array($filter));			
		?>
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>Nama Produk / Layanan</th>
					<th>Harga</th>
					<th class="col-sm-2">Jumlah Pesanan</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($sql->fetchAll() as $filterize) { ?>
					<form action="" method="post">
						<input type="hidden" name="id_transaksi_kamar" value="<?= $tm['id_transaksi_kamar']; ?>" />
						<input type="hidden" name="harga_layanan" value="<?= $filterize['harga_layanan']; ?>" />
						<input type="hidden" name="id_layanan" value="<?= $filterize['id_layanan']; ?>" />
						<tr>
							<td><?= $filterize['nama_layanan']; ?></td>
							<td><?= format_rp($filterize['harga_layanan']).' / '.$filterize['satuan']; ?></td>
							<td>
								<div class="row">
									<div class="col-sm-6">
										<input type="number" class="form-control" name="jumlah">
									</div>
									<div class="col-sm-6">
										<?php echo $filterize['satuan']; ?>
									</div>
								</div>
							</td>
							<td>
								<button class="btn btn-xs btn-success" type="submit" name="pesan-layanan">Pesan</button>
							</td>
						</tr>
					</form>
				<?php } ?>
			</tbody>
		</table>
		<?php
		}else{
		?>
		<div class="row">
			<?php 
				$sql = $db->prepare("SELECT id_layanan_kategori, nama_layanan_kategori FROM layanan_kategori");
				$sql->execute();
				foreach($sql->fetchAll() as $layanan_kategori) { ?>
				<div class="col-sm-3">
					<a class="btn btn-lg btn-block btn-primary" href="<?= $url; ?>&layanan-filter=<?= $layanan_kategori['id_layanan_kategori']; ?>"><?= $layanan_kategori['nama_layanan_kategori']; ?></a>
				</div>				
			<?php } ?>
		</div>
		<?php } ?>
	</div>
</div>