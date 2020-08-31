<?php
defined("RESMI") or die("Akses ditolak");
if(isset($_POST['tamu-add'])){
	$gump = new GUMP();
	$_POST = array(
		'prefix'           => $_POST['prefix'],
		'nama_depan'       => $_POST['nama_depan'],
		'nama_belakang'    => $_POST['nama_belakang'],
		'tipe_identitas'   => $_POST['tipe_identitas'],
		'nomor_identitas'  => $_POST['nomor_identitas'],
		'warga_negara'     => $_POST['warga_negara'],
		'nama_perusahaan'  => $_POST['nama_perusahaan'],
		'alamat_jalan'     => $_POST['alamat_jalan'],
		'alamat_kabupaten' => $_POST['alamat_kabupaten'],
		'alamat_provinsi'  => $_POST['alamat_provinsi'],
		'kode_pos'         => $_POST['kode_pos'],
		'negara'           => $_POST['negara'],
		'contact_person'   => $_POST['contact_person'],
		'nomor_telp'       => $_POST['nomor_telp'],
		'nomor_fax'        => $_POST['nomor_fax'],
		'email'            => $_POST['email']
	);
	$_POST = $gump->sanitize($_POST);
	$gump->validation_rules(array(
		'prefix'           => 'required',
		'nama_depan'       => 'required',
		'nama_belakang'    => 'required',
		'tipe_identitas'   => 'required',
		'nomor_identitas'  => 'required|numeric',
		'warga_negara'     => 'required',
		'nama_perusahaan'  => 'required',
		'alamat_jalan'     => 'required',
		'alamat_kabupaten' => 'required',
		'alamat_provinsi'  => 'required',
		'kode_pos'         => 'required|numeric',
		'negara'           => 'required',
		'contact_person'   => 'required',
		'nomor_telp'       => 'required',
		'nomor_fax'        => 'required',
		'email'            => 'required'
	));
	$gump->filter_rules(array(
		'prefix'           => 'trim|sanitize_string',
		'nama_depan'       => 'trim|sanitize_string',
		'nama_belakang'    => 'trim|sanitize_string',
		'tipe_identitas'   => 'trim|sanitize_string',
		'nomor_identitas'  => 'trim|sanitize_numbers',
		'warga_negara'     => 'trim|sanitize_string',
		'nama_perusahaan'  => 'trim|sanitize_string',
		'alamat_jalan'     => 'trim|sanitize_string',
		'alamat_kabupaten' => 'trim|sanitize_string',
		'alamat_provinsi'  => 'trim|sanitize_string',
		'kode_pos'         => 'trim|sanitize_numbers',
		'negara'           => 'trim|sanitize_string',
		'contact_person'   => 'trim|sanitize_string',
		'nomor_telp'       => 'trim|sanitize_numbers',
		'nomor_fax'        => 'trim|sanitize_string',
		'email'            => 'trim|sanitize_email'
	));
	$ok = $gump->run($_POST);
	if($ok === false){
		$error[] = $gump->get_readable_errors(true);
	}else{
		$sql = $db->prepare("INSERT INTO tamu SET prefix = ?, nama_depan = ?, nama_belakang = ?, tipe_identitas = ?, nomor_identitas = ?, warga_negara = ?, nama_perusahaan = ?, alamat_jalan = ?, alamat_kabupaten = ?, alamat_provinsi = ?, kode_pos = ?, negara = ?, contact_person = ?, nomor_telp = ?, nomor_fax = ?, email = ?");
		$sql->bindParam(1, $_POST['prefix']);
		$sql->bindParam(2, $_POST['nama_depan']);
		$sql->bindParam(3, $_POST['nama_belakang']);
		$sql->bindParam(4, $_POST['tipe_identitas']);
		$sql->bindParam(5, $_POST['nomor_identitas']);
		$sql->bindParam(6, $_POST['warga_negara']);
		$sql->bindParam(7, $_POST['nama_perusahaan']);
		$sql->bindParam(8, $_POST['alamat_jalan']);
		$sql->bindParam(9, $_POST['alamat_kabupaten']);
		$sql->bindParam(10, $_POST['alamat_provinsi']);
		$sql->bindParam(11, $_POST['kode_pos']);
		$sql->bindParam(12, $_POST['negara']);
		$sql->bindParam(13, $_POST['contact_person']);
		$sql->bindParam(14, $_POST['nomor_telp']);
		$sql->bindParam(15, $_POST['nomor_fax']);
		$sql->bindParam(16, $_POST['email']);
		if(!$sql->execute()){
			print_r($sql->errorInfo());
		}else{
			$_SESSION['SaveData'] = '';
			echo "<script> location.replace('index.php?mod=tamu&hal=tamu-list'); </script>";
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
	<div class="box box-warning">
		<div class="box-header with-border">
			<h3 class="box-title">Tambah Tamu Baru</h3>
		</div>
		<form method="post" action="">
			<div class="box-body">
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
			<div class="box-footer">
				<button class="btn btn-success" type="submit" name="tamu-add">Tambah Tamu</button>
				<a class="btn btn-warning" href="index.php?mod=tamu&hal=tamu-list">Batal</a>
			</div>
		</form>
	</div>
</div>