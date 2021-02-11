<?php

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
   
   
	   $result = $fm->listLayouts();

	   if (FileMaker::isError($result)) {
	   // report error
		   echo '<p>Error connecting to the FileMaker database: ' . $result->getMessage() . '</p>'; 
		   exit;
	   } 
	   
	   echo 'Success!';
	   
	   
// Run FM Script   
			 $layout = 'web_user';
			 $script = 'FMTestScript';
			 $parameter = '$email=tami@asktami.com||$password=xxx';
			 $cmd = $fm->newPerformScriptCommand($layout,$script,$parameter);
			 $result = $cmd->execute();
			 
			 if (FileMaker::isError($result)) {
			 echo 'Error creating FMAccount: '  . $result->getMessage();
			 }


?>