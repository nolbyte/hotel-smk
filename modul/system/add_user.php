<?php
defined("RESMI") or die("Akses ditolak");
$options = [
	'cost' => 12,
    ];
$gump = new GUMP();
$_POST = array(	
	'username'     => $_POST['username'],
	'nama'         => $_POST['nama'],
	'jabatan'      => $_POST['jabatan'],
	'id_user_role' => $_POST['id_user_role'],
	'password'     => $_POST['password']
);
$_POST = $gump->sanitize($_POST);
$gump->validation_rules(array(	
	'username'     => 'required',
	'nama'         => 'required',
	'jabatan'      => 'required',
	'id_user_role' => 'required|integer',
	'password'     => 'required|min_len,8|max_len,10'
));
$gump->filter_rules(array(	
	'username'     => 'trim|sanitize_string',
	'nama'         => 'trim|sanitize_string',
	'jabatan'      => 'trim|sanitize_string',
	'id_user_role' => 'trim|sanitize_numbers',
	'password'     => 'trim'
));
$ok = $gump->run($_POST);
if($ok === false){
	$_SESSION['errData'] = $gump->get_readable_errors(true);
	echo "<script> location.replace('index.php?mod=system&hal=user'); </script>";
}else{
	$passwd = password_hash($_POST['password'], PASSWORD_BCRYPT, $options);
	$sql = $db->prepare("INSERT INTO user SET username = ?, nama = ?, jabatan = ?, id_user_role = ?, passwd = ?");
	$sql->bindParam(1, $_POST['username']);
	$sql->bindParam(2, $_POST['nama']);
	$sql->bindParam(3, $_POST['jabatan']);
	$sql->bindParam(4, $_POST['id_user_role']);	
	$sql->bindParam(5, $passwd);
	if(!$sql->execute()){
		print_r($sql->errorInfo());
	}else{
		$_SESSION['SaveData']= '';
		echo "<script> location.replace('index.php?mod=system&hal=user'); </script>";
	}
}