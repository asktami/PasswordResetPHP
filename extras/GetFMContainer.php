<?php
    require_once("connections.php");
    
    if ($datasource == 'FileMaker') {
// FILEMAKER *****************************************************
	  $email = 'tami@asktami.com' ;
	 
 	  $cmd = $fm->newFindCommand($tUser);
	  $cmd->addFindCriterion('email', '=="'.$email .'"');
	  $query = $cmd->execute();
	  
	   if (!FileMaker::isError($query)) { 
		  // put ALL found records into an array variable called $result
		  $result = $query->getFirstRecord();
		  $found = $query->getFoundSetCount();
		  } 

  		if($found > 0) {
		  $first = $result->getField('first'); 
		  $last = $result->getField('last');
		  $email = $result->getField('email');
		  $image = $result->getField('fileContainer');
		 } // end of if found > 0
		  
		  
	
	 

    
// display image
// echo '<img src="ContainerBridge.php?path=' . urlencode($result->getField('fileContainer')) . '">';

echo 'first = ' . $first;
echo '<br>last = ' . $last;
echo '<br>email = ' . $email;

// use FMServer host IP address
echo '<br>image = http://'. $host . $image;

// get server IP address
echo '<br>';
echo getHostByName(getHostName());
echo '<br>';
echo $_SERVER['SERVER_ADDR'];


echo '<hr>';
// display image
// $host = FM Server IP from db settings file connections.php
echo '<image src="http://'.$host . $image .'">';

} // end of FileMaker qry

else {

echo 'Switch the database to FileMaker in connections.php' ;
}
?>

