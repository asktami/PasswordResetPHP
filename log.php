<?php

// date_default_timezone_set is SET IN INDEX.PHP

$userID = isset($_SESSION["userID"]) ? $_SESSION["userID"] : '' ;
$email = isset($_SESSION["email"]) ? $_SESSION["email"] : '' ;

$subject = isset($subject) ? $subject : '' ;
$body = isset($body) ? $body : '' ;

$querystring = $_SERVER['QUERY_STRING'];
$method = $_SERVER['REQUEST_METHOD'];

if ( $method == "GET" ){
	$parameters = json_encode($_GET) ;
} elseif ( $method == "POST" ){
	$parameters = json_encode($_POST) ;
} else {
	$parameters = '' ;
}


// log all page loads to a file 
error_log(date("m-d-Y h:i:s")."\t".$userID."\t".$email."\t".$PAGE."\t".$_SERVER['QUERY_STRING']."\t".$_SERVER['REQUEST_METHOD']."\t". json_encode($_REQUEST)."\t". IPADDRESS ."\t". BROWSER ."\t". $subject."\t". $body);


// *****************************************************
//also create a record in the Log table for all page loads

if($datasource == 'MySQL'){
// MySQL ***************************

$sql = "INSERT INTO " . $tLog . " (id_user, email, page, querystring, method, parameters, ipaddress, browser) VALUES (:userID, :email, :page, :querystring, :method, :parameters, :ipaddress, :browser)";

$query = $conn->prepare($sql);

$query->bindValue(':userID', $userID);
$query->bindValue(':email', $email);
$query->bindValue('page', $PAGE);
$query->bindValue(':querystring', $querystring);
$query->bindValue(':method', $method);
$query->bindValue(':parameters', $parameters);
$query->bindValue(':ipaddress', IPADDRESS);
$query->bindValue(':browser', BROWSER);

$query->execute();	

$errorInfo = $query->errorInfo();
		 if($errorInfo[0] != 0){
			  echo '<p>Error: ' . $errorInfo[2] . '</p>'; 
			  exit;
		 }	
		 

	
} elseif ($datasource == 'FileMaker') {
// FILEMAKER *****************************************************

$newRecordArray = array(
					 'id_user' => $userID,
					 'email' => $email,
					 'page' => $PAGE,
					 'querystring' => $querystring,
					 'method' => $method,
					 'parameters' => $parameters,
					 'ipaddress' => IPADDRESS,
					 'browser' => BROWSER,
				 );
		
		$cmd = $fm->newAddCommand($tLog,$newRecordArray);
		$query = $cmd->execute();

	  if (FileMaker::isError($query)) { 
		  echo '<p>Error: ' . $query->getMessage() . '</p>'; 
		  exit; 
	  } 

}

?>