<?php
defined("RESMI") or die("Akses ditolak");
$options = [
	'cost' => 12,
    ];
$gump = new GUMP();
$_POST = array(
	'id_user'  => $_POST['id_user'],
	'password' => $_POST['password']
);
$_POST = $gump->sanitize($_POST);
$gump->validation_rules(array(
	'id_user'  => 'required|integer',
	'password' => 'required|min_len,8|max_len,10'
));
$gump->filter_rules(array(
	'id_user' => 'trim|sanitize_numbers',
	'password' => 'trim'
));
$ok = $gump->run($_POST);
if($ok === false){
	$_SESSION['errData'] = $gump->get_readable_errors(true);
	echo "<script> location.replace('index.php?mod=system&hal=user'); </script>";
}else{
	$passwd = password_hash($_POST['password'], PASSWORD_BCRYPT, $options);
	$sql = $db->prepare("UPDATE user SET passwd = ? WHERE id_user = ?");
	$sql->bindParam(1, $passwd);
	$sql->bindParam(2, $_POST['id_user']);
	if(!$sql->execute()){
		print_r($sql->errorInfo());
	}else{
		$_SESSION['SaveData']= '';
		echo "<script> location.replace('index.php?mod=system&hal=user'); </script>";
	}
}
?>