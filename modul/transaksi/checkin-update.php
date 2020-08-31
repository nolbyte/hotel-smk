<?php
defined("RESMI") or die("Akses ditolak");
if(!empty($_GET['transaksi'])){
	$trns = intval($_GET['transaksi']);
	//$kmr = intval($_GET['kamar']);
}
$sqlt = $db->prepare("SELECT * FROM transaksi_kamar tk
		JOIN tamu t ON tk.id_tamu=t.id_tamu
		JOIN kamar k ON tk.id_kamar=k.id_kamar
		JOIN kamar_tipe kt ON k.id_kamar_tipe=kt.id_kamar_tipe
		WHERE tk.id_transaksi_kamar = ?");
$sqlt->bindParam(1, $trns);
//$sqlt->bindParam(2, $kmr);
$sqlt->execute();
$trans = $sqlt->fetch(PDO::FETCH_ASSOC);
if($_POST){
	$gump = new GUMP();
	$_POST = array(
		'id_transaksi_kamar' => $_POST['id_transaksi_kamar'],
		'id_kamar'           => $_POST['id_kamar'],
		'nomor_invoice'      => $_POST['nomor_invoice'],
		'id_tamu'            => $_POST['id_tamu'],
		'jumlah_dewasa'      => $_POST['jumlah_dewasa'],
		'jumlah_anak'        => $_POST['jumlah_anak'],
		'tanggal_checkin'    => $_POST['tanggal_checkin'],
		'waktu_checkin'      => $_POST['waktu_checkin'],
		'tanggal_checkout'   => $_POST['tanggal_checkout'],
		'waktu_checkout'     => $_POST['waktu_checkout'],
		'deposit'            => $_POST['deposit']
	);
	$_POST = $gump->sanitize($_POST);
	$gump->validation_rules(array(
		'id_transaksi_kamar'=> 'required|integer',
		'id_kamar'          => 'required|integer',
		'nomor_invoice'     => 'required',
		'id_tamu'           => 'required|integer',
		'jumlah_dewasa'     => 'required|numeric',
		'jumlah_anak'       => 'required|numeric',
		'tanggal_checkin'   => 'required|date',
		'waktu_checkin'     => 'required',
		'tanggal_checkout'  => 'required|date',
		'waktu_checkout'    => 'required',
		'deposit'           => 'required|numeric'
	));
	$gump->filter_rules(array(
		'id_transaksi_kamar'=> 'trim|sanitize_numbers',
		'id_kamar'          => 'trim|sanitize_numbers',
		'nomor_invoice'     => 'trim|sanitize_string',
		'id_tamu'           => 'trim|sanitize_numbers',
		'jumlah_dewasa'     => 'trim|sanitize_numbers',
		'jumlah_anak'       => 'trim|sanitize_numbers',
		'tanggal_checkin'   => 'trim|sanitize_string',
		'waktu_checkin'     => 'trim|sanitize_string',
		'tanggal_checkout'  => 'trim|sanitize_string',
		'waktu_checkout'    => 'trim|sanitize_string',
		'deposit'           => 'trim|sanitize_numbers'
	));
	$ok = $gump->run($_POST);
	if($ok === false){
		$error[] = $gump->get_readable_errors(true); 
	}else{
		$tanggal_checkin=date_create($_POST['tanggal_checkin']);
		$tanggal_checkout=date_create($_POST['tanggal_checkout']);
		$durasi=date_diff($tanggal_checkin,$tanggal_checkout)->format('%a');
		$total_biaya_kamar=$durasi * $trans['harga_malam'];
		$sql = $db->prepare("UPDATE transaksi_kamar SET nomor_invoice = ?, id_kamar = ?, id_tamu = ?, tanggal_checkin = ?, waktu_checkin = ?, tanggal_checkout = ?, waktu_checkout = ?, jumlah_dewasa = ?, jumlah_anak = ?, deposit = ?, total_biaya_kamar = ? WHERE id_transaksi_kamar = ?");
		$sql->bindParam(1, $_POST['nomor_invoice']);
		$sql->bindParam(2, $_POST['id_kamar']);
		$sql->bindParam(3, $_POST['id_tamu']);
		$sql->bindParam(4, $_POST['tanggal_checkin']);
		$sql->bindParam(5, $_POST['waktu_checkin']);
		$sql->bindParam(6, $_POST['tanggal_checkout']);
		$sql->bindParam(7, $_POST['waktu_checkout']);
		$sql->bindParam(8, $_POST['jumlah_dewasa']);
		$sql->bindParam(9, $_POST['jumlah_anak']);
		$sql->bindParam(10, $_POST['deposit']);
		$sql->bindParam(11, $total_biaya_kamar);
		$sql->bindParam(12, $_POST['id_transaksi_kamar']);
		if(!$sql->execute()){
			print_r($sql->errorInfo());
		}else{
			$_SESSION['SaveData']= '';
			echo "<script> location.replace('index.php?mod=transaksi&hal=checkin-list'); </script>";
		}
	}
}
?>
<div class="row">
	<?php
	if(isset($error)){
		foreach($error as $error){
			?>
			<script>toastr.error('<?php echo $error ?>', 'Error!', {timeOut: 3000, progressBar: true})</script>
			<?php
		}
	}
	?>
	<div class="box box-info">
		<div class="box-header">
			<h3>Kamar Nomor: <?= $trans['nomor_kamar']?></h3>
		</div>
		<form method="post" action="">
			<div class="box-body">
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group">
							<label class="control-label">#INVOICE</label>
							<input type="text" name="nomor_invoice" class="form-control" value="<?= $trans['nomor_invoice']?>" readonly>
						</div>
						<div class="alert alert-info">
							<h4><?= $trans['nama_kamar_tipe']; ?></h4>
							<ul class="list-unstyled">
								<li>Harga / Malam : <b><?= format_rp($trans['harga_malam']); ?></b></li>
								<li>Maximal Orang Dewasa : <b><?= $trans['max_dewasa']; ?> Orang</b></li>
								<li>Maximal Anak-anak : <b><?= $trans['max_anak']; ?> Orang</b></li>
							</ul>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label class="control-label">Nama Tamu</label>
							<input class="form-control" value="<?= $trans['prefix'].' '.$trans['nama_depan'].' '.$trans['nama_belakang']?>" readonly>
							<?php
							$sql = $db->prepare("SELECT id_tamu, prefix, nama_depan, nama_belakang FROM tamu ORDER BY nama_depan ASC");
							$sql->execute();
							?>
							<select name="id_tamu" class="form-control">
								<option value="<?= $trans['id_tamu']?>">--Pilih--</option>
								<?php foreach($sql->fetchAll() as $row){ ?>
									<option value="<?= $row['id_tamu']?>"><?= $trans['prefix'].' '.$row['nama_depan'].' '.$row['nama_belakang']?></option>
								<?php } ?>
							</select>	
						</div>
						<div class="well">
							<a href="?module=tamu/tamu-add"><b>Klik disini</b></a> jika nama tamu yang dimaksud tidak ditemukan untuk ditambah pada daftar buku tamu.
						</div>					
					</div>
					<div class="col-sm-5">
						<div class="form-group">
							<label>Jumlah Tamu</label>
							<div class="row">
								<div class="col-sm-6">
									<input class="form-control" value="<?= $trans['jumlah_dewasa'].' Orang Dewasa'; ?>" readonly>
									<select class="form-control" name="jumlah_dewasa">
										<option value="<?= $trans['jumlah_dewasa']; ?>">- Dewasa -</option>
										<option value="1">1 Orang</option>
										<option value="2">2 Orang</option>
										<option value="3">3 Orang</option>
										<option value="4">4 Orang</option>
										<option value="5">5 Orang</option>
									</select>
								</div>
								<div class="col-sm-6">
									<input class="form-control" value="<?= $trans['jumlah_anak'].' Anak-anak'; ?>" readonly>
									<select class="form-control" name="jumlah_anak">
										<option value="<?= $trans['jumlah_anak']; ?>">- Anak-anak -</option>
										<option value="1">1 Orang</option>
										<option value="2">2 Orang</option>
										<option value="3">3 Orang</option>
										<option value="4">4 Orang</option>
										<option value="5">5 Orang</option>
									</select>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>Tanggal / Waktu Check-In</label>
							<div class="row">
								<div class="col-sm-6">
									<input id="checkin" class="form-control" name="tanggal_checkin" data-date-format="yyyy-mm-dd" value="<?= $trans['tanggal_checkin']; ?>">
								</div>
								<div class="col-sm-6">
									<input class="form-control" name="waktu_checkin" value="<?= $trans['waktu_checkin']; ?>">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>Tanggal / Waktu Check-Out</label>
							<div class="row">
								<div class="col-sm-6">
									<input id="checkout" class="form-control" name="tanggal_checkout" data-date-format="yyyy-mm-dd" value="<?= $trans['tanggal_checkout']; ?>">
								</div>
								<div class="col-sm-6">
									<input class="form-control" name="waktu_checkout" value="<?= $trans['waktu_checkout']; ?>">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>Jumlah Deposit (Rp)</label>
							<input class="form-control" name="deposit" value="<?= $trans['deposit']; ?>">
							<p class="help-text text-red">Tanpa tanda baca. Contoh: 2500000</p>
						</div>
					</div>
				</div>
			</div>
			<div class="box-footer">
				<input type="hidden" name="id_kamar" value="<?= $trans['id_kamar']; ?>">
				<input type="hidden" name="id_transaksi_kamar" value="<?= $trans['id_transaksi_kamar']; ?>">
				<button class="btn btn-success" type="submit">Ubah Data</button>
				<a class="btn btn-warning" href="index.php?mod=transaksi&hal=checkin-list">Batal</a>
			</div>
		</form>
	</div>
</div>