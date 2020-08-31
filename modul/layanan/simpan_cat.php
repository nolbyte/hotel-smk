<?php
defined("RESMI") or die("Akses ditolak");
$gump = new GUMP();
$_POST = array(
	'id_layanan_kategori'  => $_POST['id_layanan_kategori'],
	'nama_layanan_kategori'=> $_POST['nama_layanan_kategori'],
	'keterangan'           => $_POST['keterangan']
);
$_POST = $gump->sanitize($_POST);
$gump->validation_rules(array(
	'id_layanan_kategori'  => 'required|integer',
	'nama_layanan_kategori'=> 'required',
	'keterangan'           => 'required'
));
$gump->filter_rules(array(
	'id_layanan_kategori'  => 'trim|sanitize_numbers',
	'nama_layanan_kategori'=> 'trim|sanitize_string',
	'keterangan'           => 'trim|sanitize_string'
));
$ok = $gump->run($_POST);
if($ok === false){
	$_SESSION['errData'] = $gump->get_readable_errors(true);
	echo "<script> location.replace('index.php?mod=layanan&hal=kategori'); </script>";
}else{
	$sql = $db->prepare("UPDATE layanan_kategori SET nama_layanan_kategori = ?, keterangan = ? WHERE id_layanan_kategori = ?");
	$sql->bindParam(1, $_POST['nama_layanan_kategori']);
	$sql->bindParam(2, $_POST['keterangan']);
	$sql->bindParam(3, $_POST['id_layanan_kategori']);
	if(!$sql->execute()){
		print_r($sql->errorInfo());
	}else{
		$_SESSION['SaveData']= '';
		echo "<script> location.replace('index.php?mod=layanan&hal=kategori'); </script>";
	}
}