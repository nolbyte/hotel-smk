<?php
defined("RESMI") or die("Akses ditolak");
$gump = new GUMP();
$_POST = array(
	'id_user'      => $_POST['id_user'],
	'username'     => $_POST['username'],
	'nama'         => $_POST['nama'],
	'jabatan'      => $_POST['jabatan'],
	'id_user_role' => $_POST['id_user_role']
);
$_POST = $gump->sanitize($_POST);
$gump->validation_rules(array(
	'id_user'      => 'required|integer',
	'username'     => 'required',
	'nama'         => 'required',
	'jabatan'      => 'required',
	'id_user_role' => 'required|integer'
));
$gump->filter_rules(array(
	'id_user'      => 'trim|sanitize_numbers',
	'username'     => 'trim|sanitize_string',
	'nama'         => 'trim|sanitize_string',
	'jabatan'      => 'trim|sanitize_string',
	'id_user_role' => 'trim|sanitize_numbers'
));
$ok = $gump->run($_POST);
if($ok === false){
	$_SESSION['errData'] = $gump->get_readable_errors(true);
	echo "<script> location.replace('index.php?mod=system&hal=user'); </script>";
}else{
	$sql = $db->prepare("UPDATE user SET username = ?, nama = ?, jabatan = ?, id_user_role = ? WHERE id_user = ?");
	$sql->bindParam(1, $_POST['username']);
	$sql->bindParam(2, $_POST['nama']);
	$sql->bindParam(3, $_POST['jabatan']);
	$sql->bindParam(4, $_POST['id_user_role']);
	$sql->bindParam(5, $_POST['id_user']);
	if(!$sql->execute()){
		print_r($sql->errorInfo());
	}else{
		$_SESSION['SaveData']= '';
		echo "<script> location.replace('index.php?mod=system&hal=user'); </script>";
	}
}