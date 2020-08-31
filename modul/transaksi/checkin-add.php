<?php
defined("RESMI") or die("Akses ditolak");
$nomor_invoice='INV-'.date('Ymd').'-'.(rand(10,100));
if(!empty($_GET['kamar'])){
	$id = intval($_GET['kamar']);
}
//ambil data kamar
$sql = $db->prepare("SELECT * FROM kamar k
	JOIN kamar_tipe kt ON k.id_kamar_tipe=kt.id_kamar_tipe
	WHERE id_kamar = ?");
$sql->execute(array($id));
$cek = $sql->fetch(PDO::FETCH_ASSOC);
if($cek === false){
	echo 'ID tidak ditemukan';
	die();
}
if(isset($_POST['checkin'])){	
	$gump = new GUMP();
	$_POST = array(		
		'nomor_invoice'   => $_POST['nomor_invoice'],		
		'id_tamu'         => $_POST['id_tamu'],
		'id_kamar'        => $_POST['id_kamar'],
		'jumlah_dewasa'   => $_POST['jumlah_dewasa'],
		'jumlah_anak'     => $_POST['jumlah_anak'],
		'tanggal_checkin' => $_POST['tanggal_checkin'],
		'waktu_checkin'   => $_POST['waktu_checkin'],
		'tanggal_checkout'=> $_POST['tanggal_checkout'],
		'waktu_checkout'  => $_POST['waktu_checkout'],
		'deposit'         => $_POST['deposit'],
	);
	$_POST = $gump->sanitize($_POST);
	$gump->validation_rules(array(		
		'nomor_invoice'   => 'required',
		'id_tamu'         => 'required|integer',
		'id_kamar'        => 'required|integer',
		'jumlah_dewasa'   => 'required|numeric',
		'jumlah_anak'     => 'required|numeric',
		'tanggal_checkin' => 'required|date',
		'waktu_checkin'   => 'required',
		'tanggal_checkout'=> 'required|date',
		'waktu_checkout'  => 'required',
		'deposit'         => 'required|numeric'
	));
	$gump->filter_rules(array(		
		'nomor_invoice'   => 'trim',
		'id_tamu'         => 'trim|sanitize_numbers',
		'id_kamar'        => 'trim|sanitize_numbers',
		'jumlah_dewasa'   => 'trim|sanitize_numbers',
		'jumlah_anak'     => 'trim|sanitize_numbers',
		'tanggal_checkin' => 'trim',
		'waktu_checkin'   => 'trim',
		'tanggal_checkout'=> 'trim',
		'waktu_checkout'  => 'trim',
		'deposit'         => 'trim|sanitize_numbers'
	));
	$ok = $gump->run($_POST);
	if($ok === false){
		$error[] = $gump->get_readable_errors(true);
	}else{
		$tanggal          = date('Y-m-d');
		$status           = 'CHECK IN';
		$statusK          = 'TERPAKAI';
		$tanggal_checkin  = date_create($_POST['tanggal_checkin']);
		$tanggal_checkout = date_create($_POST['tanggal_checkout']);
		$durasi           = date_diff($tanggal_checkin,$tanggal_checkout)->format('%a');
		$total_biaya_kamar= $durasi * $cek['harga_malam'];

		$sqlt = $db->prepare("INSERT INTO transaksi_kamar SET id_user = ?, nomor_invoice = ?, tanggal = ?, id_tamu = ?, id_kamar = ?, jumlah_dewasa = ?, jumlah_anak = ?, tanggal_checkin = ?, waktu_checkin = ?, tanggal_checkout = ?, waktu_checkout =?, deposit = ?, total_biaya_kamar = ?, status = ?");
		$sqlt->bindParam(1, $_SESSION['userid']);
		$sqlt->bindParam(2, $_POST['nomor_invoice']);
		$sqlt->bindParam(3, $tanggal);
		$sqlt->bindParam(4, $_POST['id_tamu']);
		$sqlt->bindParam(5, $_POST['id_kamar']);
		$sqlt->bindParam(6, $_POST['jumlah_dewasa']);
		$sqlt->bindParam(7, $_POST['jumlah_anak']);
		$sqlt->bindParam(8, $_POST['tanggal_checkin']);
		$sqlt->bindParam(9, $_POST['waktu_checkin']);
		$sqlt->bindParam(10, $_POST['tanggal_checkout']);
		$sqlt->bindParam(11, $_POST['waktu_checkout']);
		$sqlt->bindParam(12, $_POST['deposit']);
		$sqlt->bindParam(13, $total_biaya_kamar);
		$sqlt->bindParam(14, $status);

		$sqlk = $db->prepare("UPDATE kamar SET status_kamar = ? WHERE id_kamar = ?");
		$sqlk->bindParam(1, $statusK);
		$sqlk->bindParam(2, $_POST['id_kamar']);

		if(!$sqlt->execute()){
			print_r($sqlt->errorInfo());
		}elseif(!$sqlk->execute()){
			print_r($sqlk->errorInfo());
		}else{
			$_SESSION['SaveData'] = '';
			echo "<script> location.replace('index.php?mod=transaksi&hal=checkin'); </script>";
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
			<h3>Kamar Nomor: <?= $cek['nomor_kamar']?></h3>
		</div>
		<form method="post" action="">
			<div class="box-body">				
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group">
							<label class="control-label">#INVOICE</label>
							<input type="text" name="nomor_invoice" class="form-control" value="<?= $nomor_invoice?>" readonly>
						</div>
						<div class="alert alert-info">
							<h4><?= $cek['nama_kamar_tipe']; ?></h4>
							<ul class="list-unstyled">
								<li>Harga / Malam : <b><?= format_rp($cek['harga_malam']); ?></b></li>
								<li>Maximal Orang Dewasa : <b><?= $cek['max_dewasa']; ?> Orang</b></li>
								<li>Maximal Anak-anak : <b><?= $cek['max_anak']; ?> Orang</b></li>
							</ul>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label class="control-label">Nama Tamu</label>							
							<?php
							$sqlt = $db->prepare("SELECT id_tamu, prefix, nama_depan, nama_belakang FROM tamu ORDER BY nama_depan ASC");
							$sqlt->execute();
							?>
							<select name="id_tamu" class="form-control">
								<option value="">--Pilih--</option>
								<?php foreach($sqlt->fetchAll() as $row){ ?>
									<option value="<?= $row['id_tamu']?>"><?= $row['prefix'].' '.$row['nama_depan'].' '.$row['nama_belakang']?></option>
								<?php } ?>
							</select>	
						</div>
						<div class="well">
							<a href="index.php?mod=tamu&hal=tamu-add"><b>Klik disini</b></a> jika nama tamu yang dimaksud tidak ditemukan untuk ditambah pada daftar buku tamu.
						</div>					
					</div>
					<div class="col-sm-5">
						<div class="form-group">
							<label>Jumlah Tamu</label>
							<div class="row">
								<div class="col-sm-6">									
									<select class="form-control" name="jumlah_dewasa">
										<option value="0">- Dewasa -</option>
										<option value="1">1 Orang</option>
										<option value="2">2 Orang</option>
										<option value="3">3 Orang</option>
										<option value="4">4 Orang</option>
										<option value="5">5 Orang</option>
									</select>
								</div>
								<div class="col-sm-6">									
									<select class="form-control" name="jumlah_anak">
										<option value="0">- Anak-anak -</option>
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
									<input id="checkin" class="form-control" name="tanggal_checkin" value="<?= date('Y-m-d'); ?>" readonly>
								</div>
								<div class="col-sm-6">
									<input class="form-control" name="waktu_checkin" value="<?= date('H:i'); ?>" readonly>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>Tanggal / Waktu Check-Out</label>
							<div class="row">
								<div class="col-sm-6">
									<input id="checkout" class="form-control" name="tanggal_checkout" data-date-format="yyyy-mm-dd">
								</div>
								<div class="col-sm-6">
									<input class="form-control" name="waktu_checkout" value="12:00">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>Jumlah Deposit (Rp)</label>
							<input class="form-control" name="deposit">
							<p class="help-text text-red">Tanpa tanda baca. Contoh: 2500000</p>
						</div>
					</div>
				</div>
			</div>
			<div class="box-footer">
				<input type="hidden" name="id_kamar" value="<?= $cek['id_kamar']; ?>">				
				<button class="btn btn-success" type="submit" name="checkin">Check In</button>
				<a class="btn btn-warning" href="index.php?mod=transaksi&hal=checkin-list">Batal</a>
			</div>
		</form>
	</div>
</div>