<?php 
defined("RESMI") or die('Akses ditolak'); 

//require_once('../config/fungsi.php');
//require_once('../config/konek_carangka.php');


if(!empty($_GET['id'])){
		$kode = intval($_GET['id']);
	}

$sql = $db->prepare("DELETE FROM reservasi WHERE id_reservasi = ?");

$sql->execute(array($kode));