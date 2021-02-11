<?php

// echo '<form action="' . PAGE . 'my_job_tasks" method="POST">';
// header('Location: ' . PAGE . 'pageName&message=' . $message);


// ******************  NOTE 
// defined contstants where including file + path and is something I might use as a standard on multiple sites
// defined variables where is something that may change on each page load
// prefixed with g = variables which might be used as a standard on multiple sites BUT the value changes per site


define( 'LOG', 'log.php' );
define( 'DEBUG', '_common/debug.php' );
define( 'SESSION', '_common/session.php');
define( 'MISSING', '_common/missing.php');
define( 'TEMPLATE', '_lay/lay_site.php');
define( 'FEEDBACK', '_common/message.php');
define( 'FUNCTIONS', '_common/functions.php');


// ALTERNATIVE way to define path and page
// $path = dirname( __FILE__ ) ;
// define( "PAGE", $path. "/index.php?page=" );

define( 'ROOT', __DIR__ . '/' );	// path to the folder for the current script
define( 'PAGE', $_SERVER['PHP_SELF'] . '?page=' );
define( 'SITEURL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . PAGE );

define( 'gEMAIL', 'tami@asktami.com' );
define( 'gPHONE', '770-605-7656' );
define( 'gSITENAME', 'Creative Computing: PasswordResetPHP' );
define( 'gSITEADDRESS', 'https://www.asktami.com/' );

define( 'IPADDRESS' , $_SERVER['REMOTE_ADDR'] ?:($_SERVER['HTTP_X_FORWARDED_FOR']?:$_SERVER['HTTP_CLIENT_IP']) );
define( 'BROWSER' , !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '' );

include_once(FUNCTIONS);

include_once(SESSION);

include_once('connections.php');

				if(isset($_GET['message'])){
					$message = $_GET['message'];
				}

				   
			   if(empty($_GET['page'])) {
			   	// default page
			   		$PAGE = 'home';
			   	} else {
			   		$PAGE = $_GET['page'] ;
			   }
			    
			    
			   if (strpos($PAGE, '.php') == false) {
			   	   $PAGE_TITLE = ucwords(str_ireplace('_',' ',$PAGE));
				   $PAGE .= '.php' ;
			   }
			   

// must include log before include page to capture $_POST params before any page redirects
include(LOG);
			   
			   
			   if( file_exists(ROOT . $PAGE) && is_readable(ROOT . $PAGE)) {
			   
			   		$display = $PAGE ;
			   		include_once(ROOT . TEMPLATE);
			  	 
			   } else {
			   
			   		$display = MISSING;
			   		include_once(ROOT . TEMPLATE);
			   }
			   
?>