<?php
defined("RESMI") or die("Akses ditolak");
$gump = new GUMP();
$_POST = array(
	'nama_kamar_tipe'=> $_POST['nama_kamar_tipe'],
	'harga_malam'    => $_POST['harga_malam'],
	'harga_orang'    => $_POST['harga_orang']
);
$_POST = $gump->sanitize($_POST);
$gump->validation_rules(array(
	'nama_kamar_tipe'=> 'required',
	'harga_malam'    => 'required|numeric',
	'harga_orang'    => 'required|numeric'
));
$gump->filter_rules(array(
	'nama_kamar_tipe'=> 'trim|sanitize_string',
	'harga_malam'    => 'trim|sanitize_numbers',
	'harga_orang'    => 'trim|sanitize_numbers'
));
$ok = $gump->run($_POST);
if($ok === false){
	$_SESSION['errData'] = $gump->get_readable_errors(true);
	echo "<script> location.replace('index.php?mod=kamar&hal=tipe-list'); </script>";
}else{
	$keterangan = 'Tipe kamar';
	$sql = $db->prepare("INSERT INTO kamar_tipe SET nama_kamar_tipe = ?, harga_malam = ?, harga_orang = ?, keterangan = ?");
	$sql->bindParam(1, $_POST['nama_kamar_tipe']);
	$sql->bindParam(2, $_POST['harga_malam']);
	$sql->bindParam(3, $_POST['harga_orang']);
	$sql->bindParam(4, $keterangan);
	if(!$sql->execute()){
		print_r($sql->errorInfo());
	}else{
		$_SESSION['SaveData']= '';
		echo "<script> location.replace('index.php?mod=kamar&hal=tipe-list'); </script>";
	}
}