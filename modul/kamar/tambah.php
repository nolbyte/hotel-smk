<?php
defined("RESMI") or die("Akses ditolak");
if($_POST){
	$gump = new GUMP();
	$_POST = array(		
		'nomor_kamar' => $_POST['nomor_kamar'],
		'id_kamar_tipe' => $_POST['id_kamar_tipe'],
		'max_dewasa' => $_POST['max_dewasa'],
		'max_anak' => $_POST['max_anak'],
		'status_kamar' => $_POST['status_kamar']
	);
	$_POST = $gump->sanitize($_POST);
	$gump->validation_rules(array(		
		'nomor_kamar' => 'required|numeric',
		'id_kamar_tipe' => 'required|integer',
		'max_dewasa' => 'required|numeric',
		'max_anak' => 'required|numeric',
		'status_kamar' => 'required'
	));
	$gump->filter_rules(array(		
		'nomor_kamar' => 'trim|sanitize_numbers',
		'id_kamar_tipe' => 'trim|sanitize_numbers',
		'max_dewasa' => 'trim|sanitize_numbers',
		'max_anak' => 'trim|sanitize_numbers',
		'status_kamar' => 'trim|sanitize_string'
	));
	$ok = $gump->run($_POST);
	if($ok === false){
		$error[] = $gump->get_readable_errors(true); 
	}else{
		$sql = $db->prepare("INSERT INTO kamar SET nomor_kamar = ?, id_kamar_tipe = ?, max_dewasa = ?, max_anak = ?, status_kamar = ?");
		$sql->bindParam(1, $_POST['nomor_kamar']);
		$sql->bindParam(2, $_POST['id_kamar_tipe']);
		$sql->bindParam(3, $_POST['max_dewasa']);
		$sql->bindParam(4, $_POST['max_anak']);
		$sql->bindParam(5, $_POST['status_kamar']);		
		if(!$sql->execute()){
			print_r($sql->errorInfo());
		}else{ 
			$_SESSION['SaveData']= '';
			echo "<script> location.replace('index.php?mod=kamar&hal=list'); </script>";
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
	<form method="post" action="">
		<div class="box">
			<div class="box-body">				
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Nomor Kamar</label>
							<input type="text" name="nomor_kamar" class="form-control">
						</div>
						<div class="form-group">
							<label class="control-label">Tipe Kamar</label>							
							<select name="id_kamar_tipe" class="form-control">
								<?php
								$sql = $db->prepare("SELECT id_kamar_tipe, nama_kamar_tipe FROM kamar_tipe ORDER BY id_kamar_tipe ASC");
								$sql->execute();
								?>
								<option value="">--Pilih--</option>
								<?php foreach ($sql->fetchAll() as $row) { ?>
									<option value="<?= $row['id_kamar_tipe']?>"><?= $row['nama_kamar_tipe']?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Maksimal Orang Dewasa</label>							
							<select name="max_dewasa" class="form-control">
								<option value="">--Pilih--</option>
								<option value="1">1 Orang </option>
								<option value="2">2 Orang </option>
								<option value="3">3 Orang </option>
								<option value="4">4 Orang </option>
								<option value="5">5 Orang </option>
							</select>
						</div>
						<div class="form-group">
							<label class="control-label">Maksimal Anak-anak</label>							
							<select name="max_anak" class="form-control">
								<option value="">--Pilih--</option>
								<option value="1">1 Orang </option>
								<option value="2">2 Orang </option>
								<option value="3">3 Orang </option>
								<option value="4">4 Orang </option>
								<option value="5">5 Orang </option>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Status Kamar</label>							
							<select class="form-control" name="status_kamar">
								<option value="">-- Pilih --</option>
								<option value="TERSEDIA">TERSEDIA</option>
								<option value="TERPAKAI">TERPAKAI</option>
								<option value="KOTOR">KOTOR</option>
							</select>
						</div>
					</div>
				</div>			
			</div>
			<div class="box-footer">
				<input type="hidden" name="id_kamar">
				<button class="btn btn-success" type="submit">Update Kamar</button>				
				<a class="btn btn-warning" href="index.php?mod=kamar&hal=list">Batal</a>
			</div>
		</div>	
	</form>
</div>