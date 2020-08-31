<?php
defined("RESMI") or die("Akses ditolak");

$id = 1;
$sql1 = $db->prepare("SELECT * FROM perusahaan WHERE id_perusahaan = ?");
$sql1->execute(array($id));
$htl = $sql1->fetch(PDO::FETCH_ASSOC);

if(isset($_POST['simpan'])){
	$gump = new GUMP();
	$_POST = array(
		'id_perusahaan'    => $_POST['id_perusahaan'],
		'nama_hotel'       => $_POST['nama_hotel'],
		'nama_perusahaan'  => $_POST['nama_perusahaan'],
		'alamat_jalan'     => $_POST['alamat_jalan'],
		'alamat_kabupaten' => $_POST['alamat_kabupaten'],
		'alamat_provinsi'  => $_POST['alamat_provinsi'],
		'nomor_telp'       => $_POST['nomor_telp'],
		'nomor_fax'        => $_POST['nomor_fax'],
		'website'          => $_POST['website'],
		'email'            => $_POST['email']
	);
	$_POST = $gump->sanitize($_POST);
	$gump->validation_rules(array(
		'id_perusahaan'    => 'required|integer',
		'nama_hotel'       => 'required',
		'nama_perusahaan'  => 'required',
		'alamat_jalan'     => 'required',
		'alamat_kabupaten' => 'required',
		'alamat_provinsi'  => 'required',
		'nomor_telp'       => 'required',
		'nomor_fax'        => 'required',
		'website'          => 'required|valid_url',
		'email'            => 'required|valid_email'
	));
	$gump->filter_rules(array(
		'id_perusahaan'    => 'trim|sanitize_numbers',
		'nama_hotel'       => 'trim|sanitize_string',
		'nama_perusahaan'  => 'trim|sanitize_string',
		'alamat_jalan'     => 'trim|sanitize_string',
		'alamat_kabupaten' => 'trim|sanitize_string',
		'alamat_provinsi'  => 'trim|sanitize_string',
		'nomor_telp'       => 'trim|sanitize_string',
		'nomor_fax'        => 'trim|sanitize_string',
		'website'          => 'trim|sanitize_string',
		'email'            => 'trim|sanitize_email'
	));
	$ok = $gump->run($_POST);
	if($ok === false){
		$error[] = $gump->get_readable_errors(true);
	}else{
		$sql2 = $db->prepare("UPDATE perusahaan SET nama_hotel = ?, nama_perusahaan = ?, alamat_jalan = ?, alamat_kabupaten = ?, alamat_provinsi = ?, nomor_telp = ?, nomor_fax = ?, website = ?, email = ? WHERE id_perusahaan = ?");
		$sql2->bindParam(1, $_POST['nama_hotel']);
		$sql2->bindParam(2, $_POST['nama_perusahaan']);
		$sql2->bindParam(3, $_POST['alamat_jalan']);
		$sql2->bindParam(4, $_POST['alamat_kabupaten']);
		$sql2->bindParam(5, $_POST['alamat_provinsi']);
		$sql2->bindParam(6, $_POST['nomor_telp']);
		$sql2->bindParam(7, $_POST['nomor_fax']);
		$sql2->bindParam(8, $_POST['website']);
		$sql2->bindParam(9, $_POST['email']);
		$sql2->bindParam(10, $_POST['id_perusahaan']);
		if(!$sql2->execute()){
			print_r($sql2->errorInfo());
		}else{
			?>
			<script>toastr.success('Data hotel berhasil diperbarui', 'Sukses!', {timeOut: 5000, progressBar: true,onHidden: function () {
    			window.location.replace('<?= $url?>');
    		}})</script>
			<?php
		}
	}
}
if(isset($_POST['savelogo'])) {
	$uploaddir = 'logo/';
	$id_perusahaan = $_POST['id_perusahaan'];
    $target_file = $uploaddir . basename($_FILES["logo"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["logo"]["tmp_name"]);
    if($check === false){
    	$error[] = 'Berkas bukan gambar. Mohon ulangi';
    	//die();
    }
    if ($_FILES["logo"]["size"] > 100000){
    	$error[] = 'Ukuran gambar terlalu lebih dari 1MB. Mohon ulangi';
    	//die();
    }
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"){
    	$error[] = ' Hanya gambar jpg, jpeg, dan png yang diijinkan';
    	//die();
    }
        
    if(empty($error)){
    	move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file);
    	$sql3 = $db->prepare("UPDATE perusahaan SET logo = ? WHERE id_perusahaan = ?");
    	$sql3->bindParam(1, $_FILES['logo']['name']);
    	$sql3->bindParam(2, $id_perusahaan);
    	if(!$sql3->execute()){
    		print_r($sql3->errorInfo());
    	}else{
    		?>
    		<script>toastr.success('Logo hotel berhasil diperbarui', 'Sukses!', {timeOut: 5000, progressBar: true,onHidden: function () {
    			window.location.replace('<?= $url?>');
    		}})</script>
			<?php			
    	}
    }else{
 		$error[] = 'Gambar gagal diupload';	
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
	<div class="col-md-7">
		<div class="box box-warning">
			<div class="box-header with-border">
				<h3 class="box-title">Konfigurasi Perusahaan</h3>
			</div>
			<form method="post" action="">
				<div class="box-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Nama Hotel</label>
								<input type="hidden" name="id_perusahaan" value="<?=$htl['id_perusahaan']?>">
								<input type="text" name="nama_hotel" class="form-control" value="<?= $htl['nama_hotel']?>">
							</div>
							<div class="form-group">
								<label>Nomor Telpon</label>
								<input type="text" name="nomor_telp" class="form-control" value="<?= $htl['nomor_telp']?>">
							</div>
							<div class="form-group">
								<label>Nomor Fax</label>
								<input type="text" name="nomor_fax" value="<?= $htl['nomor_fax']?>" class="form-control">
							</div>
							<div class="form-group">
								<label>Website</label>
								<input type="text" name="website" class="form-control" value="<?= $htl['website']?>">
							</div>
							<div class="form-group">
								<label>Alamat Email</label>
								<input type="text" name="email" class="form-control" value="<?= $htl['email']?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Nama Perusahaan</label>
								<input type="text" name="nama_perusahaan" class="form-control" value="<?= $htl['nama_perusahaan']?>">
							</div>
							<div class="form-group">
								<label>Alamat</label>
								<textarea name="alamat_jalan" class="form-control"><?= $htl['alamat_jalan']?></textarea>
							</div>
							<div class="form-group">
								<label>Kota/Kabupaten</label>
								<input type="text" name="alamat_kabupaten" class="form-control" value="<?= $htl['alamat_kabupaten']?>">
							</div>
							<div class="form-group">
								<label>Provinsi</label>
								<input type="text" name="alamat_provinsi" class="form-control" value="<?= $htl['alamat_provinsi']?>">
							</div>
						</div>
					</div>
				</div>
				<div class="box-footer">
					<button type="submit" name="simpan" class="btn btn-info">Simpan</button>
				</div>
			</form>		
		</div>
	</div>
	<div class="col-md-5">
		<div class="box box-danger">
			<div class="box-header with-border">
				<h3 class="box-title">Logo Hotel</h3>
			</div>	
			<form method="post" action="" class="form-horizontal" enctype="multipart/form-data">
				<div class="box-body">
					<div class="form-group">
						<label class="col-sm-4 control-label">Logo Hotel</label>
						<div class="col-sm-7">
							<img class="attachment-img" src="logo/<?=$htl['logo']?>" style="width: 100px">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Upload Logo</label>
						<div class="col-sm-7">
							<input type="hidden" name="id_perusahaan" value="<?=$htl['id_perusahaan']?>">
							<input type="file" name="logo" class="form-control" required>
						</div>
					</div>
				</div>
				<div class="box-footer">
					<button type="submit" name="savelogo" class="btn btn-info">Simpan</button>
				</div>
			</form>
		</div>
	</div>
</div>