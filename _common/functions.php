<?php

date_default_timezone_set("America/New_York");

// see instructions at https://blog.mailtrap.io/phpmailer/
// see example at: https://css-tricks.com/sending-nice-html-email-with-php/

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';



// Show PDO MySQL Errors
// works with PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
function handle_sql_errors($query)
{
    $errorInfo = $query->errorInfo();
    if($errorInfo[0] != 0){
      echo '<pre>';
	  echo '<p>Database Query Error: ' . $errorInfo[2] . '</p>'; 
	  file_put_contents('/pdo_errors/pdo_errors.txt', $e->getMessage()."\n", FILE_APPEND);
	  echo '</pre>';
	  exit;
	}
}

// TO SEND MAIL ********************************************************
function sendMail($to, $subject, $message, $name=null, $filename=null, $url=null){
			 
			 $mail             = new PHPMailer();
			 $mail->IsHTML(true);	//Set the encryption mechanism to use - STARTTLS or SMTPS
			 $mail->isSMTP();
			 $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged

// SMTP::DEBUG_OFF = off (for production use)
// SMTP::DEBUG_CLIENT = client messages
// SMTP::DEBUG_SERVER = client and server messages
			 $mail->SMTPDebug = SMTP::DEBUG_OFF;   
			 
			 $mail->SMTPAuth   = true;
			 $mail->Host       = 'MAIL_HOST';
			 $mail->Port       = MAIL_PORT;
			 $mail->Username   = 'MAIL_USERNAME';
			 $mail->Password   = 'MAIL_PASSWORD';
			 $mail->setFrom('FROM_EMAIL', 'FROM_NAME');
			 $mail->addReplyTo('REPLYTO_EMAIL', 'REPLYTO_NAME');
			 $mail->Subject    = $subject;
			 
			 if(!empty($name)){
			 	$mail->addAddress($to, $name);
			 } else {
			 	$mail->addAddress($to);
			 }
			 
			 if(!empty($filename)){
			// use template for email body		 
			   $getbody = file_get_contents('../mail_templates/' . $filename );
			   $getbody = str_replace('%emailaddress%', $to, $getbody );
			   $getbody = str_replace('%name%', $name, $getbody );
			   $getbody = str_replace('%url%', $url, $getbody );
			   $mail->msgHTML( $getbody );
			} else {
			// use variable for email body
			   	$getbody = '<html><body>';
				$getbody .= $message;
				$getbody .= "</body></html>";
				$mail->Body = $getbody;
			}
			
			 if(!$mail->Send()) {
			    $email_error = $mail->ErrorInfo;
			    // echo 'PHPMailer Error: ' . $email_result;
			 } else {
			    return 1;
			}
			
		   if($email_error){
		   	echo '<div class="container alert alert-danger" role="alert">';
		   	echo "<b>Error:</b>".$email_error."</b><br>";
		   	echo 'Site owner notified';
		   	echo '</div>';
		   }
}


// DISPLAY PHP ERROR MESSAGES ********************************************************
error_reporting(1);
ini_set('display_errors', 0);	// 1 during testing, 0 when live
ini_set("log_errors", 1);


// set location of error log folder and name of error log file & create a new log file each day
ini_set("error_log", __DIR__. "/log/" .date("Y-m-d"). "_log.txt");


// CUSTOM PHP ERROR HANDLER ********************************************************
//error handler function
function errorHandler($error_level, $error_message, $error_file, $error_line) {

	$errorBody = "Error: [$error_level] $error_message in $error_file on line $error_line\t". date("m-d-Y h:i:s")."\t".$_SERVER['QUERY_STRING']."\t".$_SERVER['REQUEST_METHOD']."\t". json_encode($_REQUEST)."\t". IPADDRESS ."\t". BROWSER; 
  
  // append error text
  	error_log($errorBody);
  	
  	$email_body = "
        <p>Error: [$error_level] <strong>$error_message</strong> occurred on line 
        <strong>$error_line</strong> in the file: <strong>$error_file.</strong></p>";

// ***********************************************
//	send mail
		  $subject = gSITENAME . " - Application Error";
		  $mail = sendMail($to=gEMAIL, $subject, $message=$email_body, $name='', $filename='', $url='');
		    
// **********************************************
	
} 

// use my "errorHandler" function for all PHP errors
set_error_handler('errorHandler');


// CUSTOM PHP FATAL ERROR HANDLER ********************************************************
// fatal error handler function
// catch fatal E_ERROR errors and direct them to the "errorHandler" function
// catches E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING, and most of E_STRICT 
function fatal_handler() {
	$last_error = error_get_last();
  	if ($last_error != null && $last_error['type'] === E_ERROR) {
    	// fatal error
    	errorHandler(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
    }
}

// use my "fatal_handler" function for all PHP FATAL errors
register_shutdown_function( "fatal_handler" );



?>