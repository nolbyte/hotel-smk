<?php 
defined("RESMI") or die('Akses ditolak'); 

//require_once('../config/fungsi.php');
//require_once('../config/konek_carangka.php');


if(!empty($_GET['id'])){
		$kode = intval($_GET['id']);
	}

$sql = $db->prepare("DELETE FROM kamar_tipe WHERE id_kamar_tipe = ?");

$sql->execute(array($kode));