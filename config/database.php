<?php

	$host="localhost"; // Host name 
	$username="mmc-mhd"; // Mysql username 
	$password="harenong007$$"; // Mysql password 
	$db_name="mahadhika3_hotel"; // Database name 
	
	// Connect to server and select databse.
	try
	{
		$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
		$db = new PDO('mysql:host='.$host.';dbname='.$db_name.';charset=utf8', $username, $password);
	}
	catch(Exception $e)
	{
		die('Error : ' . $e->getMessage());
	}
	$db->exec('SET FOREIGN_KEY_CHECKS = 0');
	
	date_default_timezone_set('Asia/Jakarta');
	
?>