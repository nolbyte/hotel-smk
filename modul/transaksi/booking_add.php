<?php
defined("RESMI") or die("Akses ditolak");
if(isset($_SESSION['SaveData'])){
	?>
	<script>toastr.success('Tamu Baru berhasil disimpan', 'Sukses!', {timeOut: 5000, progressBar: true})</script>
	<?php
	unset($_SESSION['SaveData']);
}
if(isset($_SESSION['errData'])){
	?>
	<script>toastr.error('<?php echo $_SESSION['errData']?>', 'Error!', {timeOut: 5000, progressBar: true})</script>
	<?php
	unset($_SESSION['errData']);
}
$kode_booking = 'RESV-'.date('Ymd').'-'.(rand(10,100));
//Ambil data kamar tersedia
$status = 'TERSEDIA';
$sqlk = $db->prepare("SELECT * FROM kamar k
	JOIN kamar_tipe kt ON k.id_kamar_tipe=kt.id_kamar_tipe
	WHERE status_kamar = ?");
$sqlk->bindParam(1, $status);
$sqlk->execute();
$kmr = $sqlk->fetch(PDO::FETCH_ASSOC);
//Ambil data Tamu
$id_tm = isset($_GET['id_tm']) ? $_GET['id_tm'] : '';
$sqt = $db->prepare("SELECT id_tamu, prefix, nama_depan, nama_belakang FROM tamu WHERE id_tamu = ?");
$sqt->execute(array($id_tm));
$tm = $sqt->fetch(PDO::FETCH_ASSOC);
$idt = $tm['id_tamu'];
$prf = $tm['prefix'];
$ndp = $tm['nama_depan'];
$nbl = $tm['nama_belakang'];
$tamu = $prf.'&nbsp;'.$ndp.'&nbsp;'.$nbl;

if(isset($_POST['book'])){
	$gump = new GUMP();
	$_POST = array(
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
		'id_tamu'          => 'required|integer',
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
		'id_tamu'          => 'trim|sanitize_numbers',		
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

		$tanggal           = date('Y-m-d');
		$tanggal_checkin   = date_create($_POST['tanggal_checkin']);
		$tanggal_checkout  = date_create($_POST['tanggal_checkout']);
		$durasi            = date_diff($tanggal_checkin,$tanggal_checkout)->format('%a');
		$total_biaya_kamar = $durasi * $tk['harga_malam'];
		
		$sql = $db->prepare("INSERT INTO reservasi SET tanggal = ?, kode_booking = ?, id_tamu = ?, flight_arrival = ?, flight_departure = ?, tanggal_checkin = ?, tanggal_checkout = ?, id_kamar = ?, jumlah_dewasa = ?, deposit = ?, remark = ?, trace = ?, notes = ?, id_user = ?, status = ?, total_biaya = ?");
		$sql->bindParam(1, $tanggal);
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
			<h3 class="box-title">Reservasi Baru</h3>&nbsp;&nbsp;<b>[Kode Reservasi: <?= $kode_booking?>]</b>
		</div>
		<form method="post" action="" autocomplete="off">
			<div class="box-body">
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group">
							<label class="control-label">#RESERVASI</label>
							<input type="text" name="kode_booking" class="form-control" value="<?= $kode_booking?>" readonly>
						</div>
						<div class="form-group">
							<label>Pilih Kamar</label>
							<select name="id_kamar" class="form-control" required>
								<option value="">--Pilih Kamar--</option>
								<?php foreach($sqlk->fetchAll() as $row){ ?>
									<option value="<?= $row['id_kamar']?>"><?= $row['nomor_kamar'].'-'.$row['nama_kamar_tipe'].'-'.format_rp($row['harga_malam'])?></option>
								<?php } ?>
							</select>
						</div>
						<div class="form-group">
							<label>Jumlah Deposit</label>
							<input type="number" name="deposit" class="form-control" required>
							<p class="help-text text-red">Tanpa tanda baca. Contoh: 2500000</p>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label class="control-label">Nama Tamu</label>							
							<div class="row">
                            <div class="col-md-8">                     
                              <input class="form-control" name="tamu" value="<?= $tamu ?>" type="text" readonly>
                              <input type="hidden" name="id_tamu" value="<?= $idt ?>">
                            </div>
                            <div class="col-md-2">
                              <a class="btn btn-sm btn-info" href="?mod=transaksi&hal=add_tamu"><i class="fa fa-plus"></i> Tamu</a>
                            </div>
                          </div>
						</div>
						<div class="well">
							<a href="" data-toggle="modal" data-target="#AddTamu"><b>Klik disini</b></a> untuk tamu baru.
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
							<div class="row">								
								<div class="col-sm-6">
									<label>Tanggal Check-In</label>
									<input id="checkin" class="form-control" name="tanggal_checkin" data-date-format="yyyy-mm-dd" required>
								</div>								
								<div class="col-sm-6">
									<label>Tanggal Checkout</label>
									<input id="checkout" class="form-control" name="tanggal_checkout" required>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-sm-6">
									<label>Flight Arrival</label>
									<input type="text" name="flight_arrival" class="form-control" required>
								</div>
								<div class="col-sm-6">
									<label>Flight Departure</label>
									<input type="text" name="flight_departure" class="form-control" required>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-sm-6">
									<label>Payment Instruction</label>
									<input type="text" name="remark" class="form-control" required>
								</div>
								<div class="col-sm-6">
									<label>Trace</label>
									<input type="text" name="trace" class="form-control" required>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-sm-6">
									<label>Notes</label>
									<input type="text" name="notes" class="form-control" required>
								</div>
								<div class="col-sm-6">
									<label>Status</label>
									<select name="status" class="form-control" required>
										<option value="">--Pilih Status--</option>
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
				<input type="hidden" name="kode_booking" value="<?= $kode_booking; ?>">				
				<button class="btn btn-success" type="submit" name="book">Booking</button>
				<a class="btn btn-warning" href="index.php?mod=transaksi&hal=booking">Batal</a>
			</div>
		</form>
	</div>
</div>
<!-- Tambah Data Tamu-->
<div class="modal fade" tabindex="-1" role="dialog" id="AddTamu">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Tambah Tamu</h4>
			</div>
			<form method="post" action="post.php?mod=transaksi&hal=add-guest">
				<div class="modal-body">            
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label>Nama Tamu</label>
								<div class="row">
									<div class="col-sm-3">
										<select class="form-control" name="prefix" required>
											<option value="Mr">Mr</option>
											<option value="Ms">Ms</option>
											<option value="Mrs">Mrs</option>
										</select>
									</div>
									<div class="col-sm-4">
										<input class="form-control" name="nama_depan" placeholder="Nama Depan" required>
									</div>
									<div class="col-sm-4">
										<input class="form-control" name="nama_belakang" placeholder="Nama Belakang" required>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label>Identitas</label>
								<div class="row">
									<div class="col-sm-3">
										<select class="form-control" name="tipe_identitas" required>
											<option value="KTP">KTP</option>
											<option value="SIM">SIM</option>
											<option value="PASSPORT">PASSPORT</option>
										</select>
									</div>
									<div class="col-sm-6">
										<input class="form-control" name="nomor_identitas" placeholder="Nomor Identitas" required>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label>Warga Negara</label>
								<div class="row">
									<div class="col-sm-4">
										<input class="form-control" name="warga_negara" required>
									</div>
								</div>
							</div>
							<div class="form-group">							
								<div class="row">
									<div class="col-sm-5">
										<label>Contact Person</label>
										<input type="text" name="contact_person" class="form-control" required>	
									</div>
									<div class="col-sm-5">
										<label>Email</label>
										<input class="form-control" name="email" required>
									</div>
								</div>							
							</div>	
							<div class="form-group">
								<div class="row">
									<div class="col-sm-5">
										<label>Nomor Telp / Handphone</label>
										<input class="form-control" name="nomor_telp" required>
									</div>
									<div class="col-sm-5">
										<label>Nomor Fax</label>
										<input class="form-control" name="nomor_fax" required>
									</div>
								</div>
							</div>					
						</div>
						<div class="col-sm-5">
							<div class="form-group">
								<label>Nama Perusahaan</label>
								<input type="text" name="nama_perusahaan" class="form-control" required>
							</div>
							<div class="form-group">
								<label>Alamat</label>
								<textarea class="form-control" name="alamat_jalan" required></textarea>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<input class="form-control" name="alamat_kabupaten" placeholder="Kabupaten / Kota" required>
									</div>
									<div class="col-sm-6">
										<input class="form-control" name="alamat_provinsi" placeholder="Provinsi" required>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label>Negara</label>
										<input class="form-control" name="negara" required>
									</div>
									<div class="col-sm-6">
										<label>Kode Pos</label>
										<input class="form-control" name="kode_pos" required>
									</div>
								</div>
							</div>						
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="submit" class="btn bg-orange btn-flat btn-simpan-brg" value="Simpan">
					<button type="button" class="btn btn-danger btn-flat" data-dismiss="modal">Tutup</button>
				</div>
			</form>
		</div>
	</div>
</div>