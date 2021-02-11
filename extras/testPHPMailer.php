<?php 

define( 'ROOT', __DIR__ . '/' );	// path to the folder for the current script
define( 'PAGE', $_SERVER['PHP_SELF'] . '?page=' );
define( 'SITEURL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . PAGE );

define( 'gEMAIL', 'tami@asktami.com' );
define( 'gPHONE', '770-605-7656' );
define( 'gSITENAME', 'Creative Computing: PasswordResetPHP' );
define( 'gSITEADDRESS', 'https://www.asktami.com/' );

define( 'IPADDRESS' , $_SERVER['REMOTE_ADDR'] ?:($_SERVER['HTTP_X_FORWARDED_FOR']?:$_SERVER['HTTP_CLIENT_IP']) );
define( 'BROWSER' , !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '' );


include('../_common/functions.php');


// to use the function 
			$to = 'tami@asktami.com' ;
			$name = 'Test Name' ;
			$subject = "Test PHPMailer";
			$message = 'test email message' ;
			
 sendMail($to,$subject,$message,$name,$filename='reset_password.html',$url='http://www.google.com/');
			
// to use the function and pass a mail_template for the email message body
// sendMail($to,$subject,$message,$name,$filename='',$url='');
			

?>