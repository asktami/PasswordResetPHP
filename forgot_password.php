<?php

/*
- NOTE this code assumes that EMAIL is an unique identifier of a User record
- search for email address
- if email address NOT found, send an email to user with error message
OR
- if email address IS found, assign it a random token and store that token and the user's id and email address in the reset password table
- send email to user with password reset link with token
*/

$message = isset($_GET["message"]) ? htmlentities($_GET["message"]) : '' ;
$email = isset($_POST["email"]) ? $_POST["email"] : '' ;


if(!isset($_POST['ForgotPasswordForm'])){
	include('forgot_password_form.php');
exit;
}

// Was the forgot password form submitted?
if (isset($_POST["ForgotPasswordForm"])) {

	// check for valid e-mail address
	if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$message .=  'Email (' .$email . ') is not in a valid email format! ';
		header('Location: ' . PAGE . 'forgot_password&message=' . $message);
		exit;
	}
	
	$found = 0;
	$created = 0;

	// Check to see if a record exists with this e-mail

	if($datasource == 'MySQL'){
// MySQL *****************************************************

	$sql = "SELECT id, first, last, email FROM ". $tUser . " WHERE email = :email";
	$query = $conn->prepare($sql);
	
	// :email is a placeholder, replaced with variable value $email below:
	$query->bindValue(':email', $email);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_ASSOC);
	
	$found = $query->rowCount();

	 } elseif ($datasource == 'FileMaker') {
// FILEMAKER *****************************************************

 	  $cmd = $fm->newFindCommand($tUser);
	  $cmd->addFindCriterion('email', '=="'.$email .'"');
	  $query = $cmd->execute();
	  
	   if (!FileMaker::isError($query)) { 
		  $found = $query->getFoundSetCount();
		  $result = $query->getFirstRecord();
		  } 
	 }
}	
	
	if ($found > 0)
	{
		// Create random token
		$length = 16;	// integer larger than 15
		$token = bin2hex(openssl_random_pseudo_bytes($length));
			
		// grab userID, first and last name
		
		if(($datasource == 'MySQL')){
			$userID = $result["id"] ;
			$first = $result["first"] ;
			$last = $result["last"] ;
		
		} elseif ($datasource == 'FileMaker'){
			$userID = $result->getField('ID');
			$first = $result->getField('first');
			$last = $result->getField('last');
		}
			

/* 
------------- NOTE
For PHP 5 >= 5.3.0, use openssl_random_pseudo_bytes($length)
For PHP 7 and up, use random_bytes($length)) 
*/
		
		// token expires within 60 minutes 
		$expires = new DateTime();
		$expires->modify('+1 hour');	// 1 hour
		
		
		if($datasource == 'MySQL'){
		// using MySQL database
		$expires = $expires->format('Y/m/d H:i:s');	// YYYY/MM/DD HH:MM:SS, 24hour
		}else{
		// using FileMaker database
		$expires = $expires->format('m/d/Y H:i:s');	// MM/DD/YYYY HH:MM:SS, 24hour
		}
		

// Create resetPassword record with random token and email address
	if($datasource == 'MySQL'){
// MySQL *****************************************************

		$sql = "INSERT INTO " . $tReset . " (email, id_user, token, expires) VALUES (:email, :userID, :token, :expires)";
		$query = $conn->prepare($sql);
	
		// :email & :token are placeholders, placeholders are replaced with variable values below:
		$query->bindValue(':email', $email);
		$query->bindValue(':userID', $userID);
		$query->bindValue(':token', $token);
		$query->bindValue(':expires', $expires);
		$query->execute();		
		
		$created = $query->rowCount();

	 } elseif ($datasource == 'FileMaker') {
// FILEMAKER *****************************************************
		$newRecordArray = array(
					 'email' => $email,
					 'id_user' => $userID,
					 'token' => $token,
					 'expires' => $expires,
				 );
		
		$cmd = $fm->newAddCommand($tReset,$newRecordArray);
		$query = $cmd->execute();
		
		$created = $query->getFoundSetCount();

	  if (FileMaker::isError($query)) { 
		  echo '<p>Error creating ResetPassword record: ' . $query->getMessage() . '</p>'; 
		  exit; 
	  } 
}

	
if($created = 0){
    echo 'There was an error creating the reset password link!';
    exit;
}

		// Create the reset link url
		$url = SITEURL . 'reset_password&q=' . $token;
		
		// Mail them their reset password token
		$mailbody = "<p>Hi ". $first .",<br><br>We got a request to reset your www.asktami.com password.\n\nClick on the link below to reset it. If you cannot click it, please paste it into your web browser's address bar.<br><br>" . $url . "</p><p>If you did not request a password reset, please ignore this email or reply to let us know. If you ignore this message, your password will not be changed. The password reset is only valid for the next <font color='red'><strong>60 minutes</strong></font>.</p><p>Thanks!</p><p>Cusomer Support</p>";
		
		
		// echo 'Your password reset token has been sent to your e-mail address (' . $email . ').';
		
	} else {
	
// no matching email address found
		$mailbody = "<p>You (or someone else) entered this email address when trying to change the password at " . gSITEADDRESS . ".</p><p>However, this email address is not in our database of registered users and therefore the attempted password change has failed.</p><p>If you are a registered user and were expecting this email, please try again using the email address you used to register your account.</p><p>If you are not a registered user, please ignore this email.</p><p>Thanks!</p><p>Customer Support</p>";
		
		// echo "No user with that e-mail address exists.";
	}
	
	
// ALWAYS say that an email has been sent so that bad users don't keep trying different email addresses
		echo '<div class="mt-1">
        <h1>Password Reset Email Sent</h1>
      </div>
      <p class="lead">We\'ve just sent an email to reset your password to <mark>' . $email . '</mark>.<br><br>If you do not use this email to reset your password within the next 60 minutes, it will expire and you will need to submit another password reset request.<br><br>If you do not see a message from ' . gSITENAME . ' soon, please check your junk mail folder.<br><br>If you need extra help, email <a href="mailto:' . gEMAIL . '">' . gEMAIL . '</a> or call ' . gPHONE . '.</p>';
      
      
      // ALWAYS send an email	
      	$url = !empty($url) ? $url : '' ;
		$subject = gSITENAME. " - Reset Your Password";
		try {
			$sendMail = sendMail($to=$email, $subject, $message=$mailbody, $name='', $filename='', $url);
		} catch (Exception $e) {
			echo $e->getMessage();
		}

?>