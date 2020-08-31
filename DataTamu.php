<?php	
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include 'config/database.php';
	
	$keyword = strval(isset($_POST['query']) ? $_POST['query'] : '');
	$search_param = "{$keyword}%";
	//$conn =new mysqli('localhost', 'mmc-mhd', 'harenong007$$' , 'mahadhika4_likeos');
	
	$sql = $db->prepare("SELECT * FROM tamu WHERE nama_depan LIKE '%".$search_param."%'");
	//$sql->bindParam(1, $search_param);
	$sql->execute();
	$num = $sql->rowCount();
	if($num > 0){
		foreach($row = $sql->fetchAll(PDO::FETCH_ASSOC) as $row){
			$guru[] = $row['nama_depan'];			
		}
		echo json_encode($guru);
	}	
?>