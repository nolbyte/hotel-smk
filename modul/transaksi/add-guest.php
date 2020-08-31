<?php
defined("RESMI") or die("Akses ditolak");
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
		$_SESSION['errData'] = $gump->get_readable_errors(true);
		echo "<script> location.replace('index.php?mod=transaksi&hal=booking_add'); </script>";
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
			echo "<script> location.replace('index.php?mod=transaksi&hal=booking_add'); </script>";
		}
	}