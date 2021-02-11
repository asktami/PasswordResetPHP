<?php

// Get Server name
$server_name = $_SERVER['SERVER_NAME'];


// DATASOURCE is either FileMaker or MySQL, comment out whichever is not used
//	$datasource = 'FileMaker' ;
	$datasource = 'MySQL' ;


// IF USING SINGLE FILEMAKER HOST
// FileMaker Host is always
$filemaker_host = "YOUR_FILEMAKERHOST";


// MySQL connection
if($datasource === 'MySQL'){
// Connect to MySQL

if($server_name == 'YOUR_SERVERNAME){
// For example, on www.asktami.com
	$host = "localhost";
	$usn = "DB_USERNAME";
	$pwd = "DB_PASSWORD";
	$db = "DB_NAME";
	
} else {
// on 127.0.0.1
	$host = "localhost:3306";
	$usn = "root";
	$pwd = "admin";
	$db = "DB_NAME";
}

// MySQL table names
$tUser = 'demo_user';
$tReset = 'demo_reset';
$tLog = 'demo_log';
$tFile = 'demo_file';


	// for PDO connection to database
	$pdo = 'mysql:host='.$host.';dbname='.$db .';charset=utf8mb4';


	// Check connection
	try {
	// establish connection
		$conn = new PDO($pdo, $usn, $pwd,
		array(
		// set the PDO error mode to ERRMODE_WARNING to show MySQL errors:
			PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
			)
		);

	// report success
	//	echo 'Successfully connected to database.<br>';

	} catch (PDOException $e) {
	// report error
		echo '<pre>';
		echo 'Failed to connect to the MySQL database: ' . $e->getMessage();
		echo '</pre>';
		file_put_contents('/pdo_errors/pdo_errors.txt', $e->getMessage()."\n", FILE_APPEND);
		exit;
	}

}



// FileMaker connection
if($datasource === 'FileMaker'){
// Connect to FileMaker
require_once('FileMaker.php'); 

   $database = 'Demo';
   $host = '10.211.55.9';
   $username = 'Admin';
   $password = 'admin';

   // Alternative way to connect:
   //  $fm = new FileMaker('DatabaseName', 'HostSpec', 'UserName', 'Password');
   //  $fm = new FileMaker($database, $host, $username, $password);

   $fm = new FileMaker(); 
   $fm->setProperty('database', $database); 
   $fm->setProperty('hostspec', $host); 
   $fm->setProperty('username', $username); 
   $fm->setProperty('password', $password); 

   // FileMaker layout names
	   $tUser = 'web_user';
	   $tReset = 'web_reset';
	   $tLog = 'web_log';
	   $tFile = 'web_file';
	   $tRelated = 'web_related';
   
   
	   $result = $fm->listLayouts();

	   if (FileMaker::isError($result)) {
	   // report error
		   echo '<p>Failed to connect to the FileMaker <b>' . $database . '</b> database. ' .  $result->getMessage() . '</p>'; 
		   exit;
	   } 
	
}

?>