<?php 
defined("RESMI") or die('Akses ditolak');
	if(!empty($_GET['id'])){
		$id = intval($_GET['id']);
	}
	$sql = $db->prepare("DELETE FROM user WHERE id_user = ?");
	$sql->execute(array($id));
	
?>