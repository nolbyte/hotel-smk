<?php
defined("RESMI") or die("Akses ditolak");
$nomor_invoice='INV-'.date('Ymd').'-'.(rand(10,100));
$status = 'TERSEDIA';
$sqlk = $db->prepare("SELECT * FROM kamar k
	JOIN kamar_tipe kt ON k.id_kamar_tipe=kt.id_kamar_tipe
	WHERE status_kamar = ?");
$sqlk->bindParam(1, $status);
$sqlk->execute();
$kmr = $sqlk->fetch(PDO::FETCH_ASSOC);
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
if(isset($_POST['book'])){
	$gump = new GUMP();
	$_POST = array(
		'id_reservasi'     => $_POST['id_reservasi'],
		'tanggal'          => $_POST['tanggal'],
		'kode_booking'     => $_POST['kode_booking'],
		'id_tamu'          => $_POST['id_tamu'],
		'flight_arrival'   => $_POST['flight_arrival'],
		'flight_departure' => $_POST['flight_departure'],
		'tanggal_checkin'  => $_POST['tanggal_checkin'],
		'tanggal_checkout' => $_POST['tanggal_checkout'],
		'id_kamar'         => $_POST['id_kamar'],
		'jumlah_dewasa'    => $_POST['jumlah_dewasa'],
		'deposit'          => $_POST['deposit'],
		'remark'           => $_POST['remark'],
		'trace'            => $_POST['trace'],
		'notes'            => $_POST['notes'],
		'id_user'          => $_POST['id_user'],
		'status'           => $_POST['status']
	);
	$_POST = $gump->sanitize($_POST);
	$gump->validation_rules(array(
		'id_reservasi'     => 'required|integer',
		'tanggal'          => 'required',
		'kode_booking'     => 'required',
		'id_tamu'          =>'required|integer',
		'flight_arrival'   => 'required',
		'flight_departure' => 'required',
		'tanggal_checkin'  => 'required',
		'tanggal_checkout' => 'required',
		'id_kamar'         => 'required|integer',
		'jumlah_dewasa'    => 'required|numeric',
		'deposit'          => 'required|numeric',
		'remark'           => 'required',
		'trace'            => 'required',
		'notes'            => 'required',
		'id_user'          => 'required',
		'status'           => 'required'
	));
	$gump->filter_rules(array(
		'id_reservasi'     => 'trim|sanitize_numbers',
		'tanggal'          => 'trim|sanitize_string',
		'kode_booking'     => 'trim|sanitize_string',
		'id_tamu'          =>'trim|sanitize_numbers',
		'flight_arrival'   => 'trim|sanitize_string',
		'flight_departure' => 'trim|sanitize_string',
		'tanggal_checkin'  => 'trim|sanitize_string',
		'tanggal_checkout' => 'trim|sanitize_string',
		'id_kamar'         => 'trim|sanitize_numbers',
		'jumlah_dewasa'    => 'trim|sanitize_numbers',
		'deposit'          => 'trim|sanitize_numbers',
		'remark'           => 'trim|sanitize_string',
		'trace'            => 'trim|sanitize_string',
		'notes'            => 'trim|sanitize_string',
		'id_user'          => 'trim|sanitize_string',
		'status'           => 'trim|sanitize_string'
	));
	$ok = $gump->run($_POST);
	if($ok === false){
		$error[] = $gump->get_readable_errors(true);
	}else{
		$k = $db->prepare("SELECT * FROM kamar k
			JOIN kamar_tipe kt ON k.id_kamar_tipe=kt.id_kamar_tipe
			WHERE id_kamar = ?");
		$k->execute(array($_POST['id_kamar']));
		$tk = $k->fetch(PDO::FETCH_ASSOC);
		
		$tanggal_checkin   = date_create($_POST['tanggal_checkin']);
		$tanggal_checkout  = date_create($_POST['tanggal_checkout']);
		$durasi            = date_diff($tanggal_checkin,$tanggal_checkout)->format('%a');
		$total_biaya_kamar = $durasi * $tk['harga_malam'];

		if($_POST['status'] === 'Register'){
			$tanggal       = date('Y-m-d');
			$status        = 'REGISTER';
			$statusK       = 'TERPAKAI';
			$waktu_checkin = date('H:i:s');
			$waktu_checkout= '12:00';

			$sqlt = $db->prepare("INSERT INTO transaksi_kamar SET id_user = ?, nomor_invoice = ?, tanggal = ?, id_tamu = ?, id_kamar = ?, jumlah_dewasa = ?, tanggal_checkin = ?, waktu_checkin = ?, tanggal_checkout = ?, waktu_checkout =?, deposit = ?, total_biaya_kamar = ?, status = ?");
			$sqlt->bindParam(1, $_SESSION['userid']);
			$sqlt->bindParam(2, $nomor_invoice);
			$sqlt->bindParam(3, $tanggal);
			$sqlt->bindParam(4, $_POST['id_tamu']);
			$sqlt->bindParam(5, $_POST['id_kamar']);
			$sqlt->bindParam(6, $_POST['jumlah_dewasa']);			
			$sqlt->bindParam(7, $_POST['tanggal_checkin']);
			$sqlt->bindParam(8, $waktu_checkin);
			$sqlt->bindParam(9, $_POST['tanggal_checkout']);
			$sqlt->bindParam(10, $waktu_checkout);
			$sqlt->bindParam(11, $_POST['deposit']);
			$sqlt->bindParam(12, $total_biaya_kamar);
			$sqlt->bindParam(13, $status);

			$sqlk = $db->prepare("UPDATE kamar SET status_kamar = ? WHERE id_kamar = ?");
			$sqlk->bindParam(1, $statusK);
			$sqlk->bindParam(2, $_POST['id_kamar']);

			if(!$sqlt->execute()){
				print_r($sqlt->errorInfo());
			}elseif(!$sqlk->execute()){
				print_r($sqlk->errorInfo());
			}else{
				$_SESSION['SaveData'] = '';
				echo "<script> location.replace('index.php?mod=transaksi&hal=booking'); </script>";
			}

		}

			$sql = $db->prepare("UPDATE reservasi SET tanggal = ?, kode_booking = ?, id_tamu = ?, flight_arrival = ?, flight_departure = ?, tanggal_checkin = ?, tanggal_checkout = ?, id_kamar = ?, jumlah_dewasa = ?, deposit = ?, remark = ?, trace = ?, notes = ?, id_user = ?, status = ?, total_biaya = ? WHERE id_reservasi = ?");
			$sql->bindParam(1, $_POST['tanggal']);
			$sql->bindParam(2, $_POST['kode_booking']);
			$sql->bindParam(3, $_POST['id_tamu']);
			$sql->bindParam(4, $_POST['flight_arrival']);
			$sql->bindParam(5, $_POST['flight_departure']);
			$sql->bindParam(6, $_POST['tanggal_checkin']);
			$sql->bindParam(7, $_POST['tanggal_checkout']);
			$sql->bindParam(8, $_POST['id_kamar']);
			$sql->bindParam(9, $_POST['jumlah_dewasa']);
			$sql->bindParam(10, $_POST['deposit']);
			$sql->bindParam(11, $_POST['remark']);
			$sql->bindParam(12, $_POST['trace']);
			$sql->bindParam(13, $_POST['notes']);
			$sql->bindParam(14, $_POST['id_user']);
			$sql->bindParam(15, $_POST['status']);
			$sql->bindParam(16, $total_biaya_kamar);
			$sql->bindParam(17, $_POST['id_reservasi']);
			if(!$sql->execute()){
				print_r($sql->errorInfo());
			}else{
				$_SESSION['SaveData'] = '';
				echo "<script> location.replace('index.php?mod=transaksi&hal=booking'); </script>";
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
		<div class="box-header with-border">
			<h3 class="box-title">Update Reservasi </h3>&nbsp;&nbsp;<b>[Kode Reservasi: <?= $rsv['kode_booking']?>]</b>
		</div>
		<form method="post" action="" autocomplete="off">
			<div class="box-body">
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group">
							<label class="control-label">#RESERVASI</label>
							<input type="text" name="kode_booking" class="form-control" value="<?= $rsv['kode_booking']?>" readonly>
						</div>
						<div class="form-group">
							<label>Pilih Kamar</label>
							<input class="form-control" value="<?= $rsv['nomor_kamar'].'-'.$rsv['nama_kamar_tipe'].'-'.format_rp($rsv['harga_malam'])?>" readonly>
							<select name="id_kamar" class="form-control" required>
								<option value="<?=$rsv['id_kamar']?>">--Pilih Kamar--</option>
								<?php foreach($sqlk->fetchAll() as $row){ ?>
									<option value="<?= $row['id_kamar']?>"><?= $row['nomor_kamar'].'-'.$row['nama_kamar_tipe'].'-'.format_rp($row['harga_malam'])?></option>
								<?php } ?>
							</select>
						</div>
						<div class="form-group">
							<label>Jumlah Deposit</label>
							<input type="number" name="deposit" class="form-control" value="<?= $rsv['deposit']?>" required>
							<p class="help-text text-red">Tanpa tanda baca. Contoh: 2500000</p>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label>Tanggal Reservasi</label>
							<input class="form-control" type="text" name="tanggal" value="<?=$rsv['tanggal']?>" readonly>
						</div>
						<div class="form-group">
							<label class="control-label">Nama Tamu</label>
							<input class="form-control" value="<?=$rsv['prefix'].'. '.$rsv['nama_depan'].' '.$rsv['nama_belakang']?>" readonly>							
							<?php
							$sqlt = $db->prepare("SELECT id_tamu, prefix, nama_depan, nama_belakang FROM tamu ORDER BY nama_depan ASC");
							$sqlt->execute();
							?>
							<select name="id_tamu" class="form-control">
								<option value="<?=$rsv['id_tamu']?>">--Pilih--</option>
								<?php foreach($sqlt->fetchAll() as $row){ ?>
									<option value="<?= $row['id_tamu']?>"><?= $row['prefix'].' '.$row['nama_depan'].' '.$row['nama_belakang']?></option>
								<?php } ?>
							</select>	
						</div>						
						<div class="form-group">
							<label>Petugas Reservasi</label>
							<input class="form-control" type="text" value="<?= $_SESSION['ngaran']?>" readonly>
							<input type="hidden" name="id_user" value="<?= $_SESSION['userid']?>">
						</div>
					</div>
					<div class="col-sm-5">
						<div class="form-group">
							<label>Jumlah Tamu</label>							
							<div class="row">
								<div class="col-sm-6">
									<input class="form-control" value="<?=$rsv['jumlah_dewasa'].' Dewasa'?>" readonly>									
									<select class="form-control" name="jumlah_dewasa">
										<option value="<?=$rsv['jumlah_dewasa']?>">- Dewasa -</option>
										<option value="1">1 Orang</option>
										<option value="2">2 Orang</option>
										<option value="3">3 Orang</option>
										<option value="4">4 Orang</option>
										<option value="5">5 Orang</option>
									</select>
								</div>
								<div class="col-sm-6">
									<input class="form-control" value="<?=$rsv['jumlah_anak'].' Anak'?>" readonly>									
									<select class="form-control" name="jumlah_anak">
										<option value="<?=$rsv['jumlah_anak']?>">- Anak-anak -</option>
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
							<div class="row">								
								<div class="col-sm-6">
									<label>Tanggal Check-In</label>
									<input id="checkin" class="form-control" name="tanggal_checkin" data-date-format="yyyy-mm-dd" value="<?=$rsv['tanggal_checkin']?>" required>
								</div>								
								<div class="col-sm-6">
									<label>Tanggal Checkout</label>
									<input id="checkout" class="form-control" name="tanggal_checkout" value="<?=$rsv['tanggal_checkout']?>" required>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-sm-6">
									<label>Flight Arrival</label>
									<input type="text" name="flight_arrival" class="form-control" value="<?=$rsv['flight_arrival']?>" required>
								</div>
								<div class="col-sm-6">
									<label>Flight Departure</label>
									<input type="text" name="flight_departure" class="form-control" value="<?=$rsv['flight_departure']?>" required>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-sm-6">
									<label>Payment Instruction</label>
									<input type="text" name="remark" class="form-control" value="<?=$rsv['remark']?>" required>
								</div>
								<div class="col-sm-6">
									<label>Trace</label>
									<input type="text" name="trace" class="form-control" value="<?=$rsv['trace']?>" required>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-sm-6">
									<label>Notes</label>
									<input type="text" name="notes" class="form-control" value="<?=$rsv['notes']?>" required>
								</div>
								<div class="col-sm-6">
									<label>Status</label>
									<input class="form-control" value="<?=$rsv['status']?>" readonly>
									<select name="status" class="form-control" required>
										<option value="<?=$rsv['status']?>">--Pilih Status--</option>
										<option value="Definite">Definite</option>
										<option value="Register">Register</option>
										<option value="Check In">Check In</option>
										<option value="Check Out">Check Out</option>
										<option value="No Show">No Show</option>
										<option value="Tentive">Tentative</option>
										<option value="Cancel">Cancel</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="box-footer">				
				<input type="hidden" name="id_reservasi" value="<?= $rsv['id_reservasi']; ?>">		
				<button class="btn btn-success" type="submit" name="book">Update Booking</button>
				<a class="btn btn-warning" href="index.php?mod=transaksi&hal=booking">Batal</a>
			</div>
		</form>
	</div>
</div>