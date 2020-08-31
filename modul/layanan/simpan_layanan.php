<?php
defined("RESMI") or die("Akses ditolak");
$gump = new GUMP();
$_POST = array(
	'id_layanan'         => $_POST['id_layanan'],
	'nama_layanan'       => $_POST['nama_layanan'],
	'id_layanan_kategori'=> $_POST['id_layanan_kategori'],	
	'harga_layanan'      => $_POST['harga_layanan'],
	'satuan'             => $_POST['satuan']
);
$_POST = $gump->sanitize($_POST);
$gump->validation_rules(array(
	'id_layanan'         => 'required|integer',
	'nama_layanan'       => 'required',
	'id_layanan_kategori'=> 'required|integer',	
	'harga_layanan'      => 'required|numeric',
	'satuan'             => 'required'
));
$gump->filter_rules(array(
	'id_layanan'         => 'trim|sanitize_numbers',
	'nama_layanan'       => 'trim|sanitize_string',
	'id_layanan_kategori'=> 'trim|sanitize_numbers',	
	'harga_layanan'      => 'trim|sanitize_numbers',
	'satuan'             => 'trim|sanitize_string'
));
$ok = $gump->run($_POST);
if($ok === false){
	$_SESSION['errData'] = $gump->get_readable_errors(true);
	echo "<script> location.replace('index.php?mod=layanan&hal=list'); </script>";
}else{
	$sql= $db->prepare("UPDATE layanan SET nama_layanan = ?, id_layanan_kategori = ?, satuan = ?, harga_layanan = ? WHERE id_layanan = ?");
	$sql->bindParam(1, $_POST['nama_layanan']);
	$sql->bindParam(2, $_POST['id_layanan_kategori']);
	$sql->bindParam(3, $_POST['satuan']);
	$sql->bindParam(4, $_POST['harga_layanan']);
	$sql->bindParam(5, $_POST['id_layanan']	);
	if(!$sql->execute()){
		print_r($sql->errorInfo());
	}else{
		$_SESSION['SaveData']= '';
		echo "<script> location.replace('index.php?mod=layanan&hal=list'); </script>";
	}
}