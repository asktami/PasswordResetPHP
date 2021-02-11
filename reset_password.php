<?php

$message = isset($_GET["message"]) ? htmlentities($_GET["message"]) : '' ;

// if token NOT in session, get token from URL
if(isset($_GET['q'])) {
	$token = $_GET["q"] ;
	$_SESSION['token'] = $token ;
} else {
	$token = $_SESSION['token'];
}

	$found = 0;
	$now = new DateTime();

// Always check to see if an active reset record exists for the token, within timeframe

	if($datasource == 'MySQL'){
// MySQL *****************************************************


//	$sql = 'SELECT token, id_user, email, expires, ind_used FROM '. $tReset . ' WHERE token = :token and ind_used = 0 and expires >= NOW()';

	$sql = 'SELECT id_user, email, token, expires, ind_used FROM '. $tReset . ' WHERE token = :token ';
	$query = $conn->prepare($sql);
	$query->bindValue(':token', $token);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_ASSOC);
	
	$found = $query->rowCount();

	$expires = new DateTime($result["expires"]) ;
	$ind_used = (int)$result["ind_used"] ;
	$email = $result["email"] ;
	$userID = $result["id_user"] ;

	 } elseif ($datasource == 'FileMaker') {
// FILEMAKER *****************************************************

      $cmd = $fm->newFindCommand($tReset);
	  $cmd->addFindCriterion('token', '=="'.$token .'"');
	  $query = $cmd->execute();
	  
	   if (!FileMaker::isError($query)) { 
		  $found = $query->getFoundSetCount();
		  $result = $query->getFirstRecord();
		  
		  $expires = new DateTime($result->getField('expires')) ;
		  $ind_used = (int)$result->getField('ind_used') ;
		  $email = $result->getField('email') ;
		  $userID = $result->getField('id_user') ;
		  
		  }
}

	if ($found > 0 && $expires >= $now  && $ind_used === 0 ) {
		$_SESSION['email'] = $email;
		$_SESSION['userID'] = $userID;
	}
	
	
   if ($found > 0 && $ind_used === 1 ) {
   $message .="<br>This password reset link has already been used.";

   } elseif ($found > 0 && $expires >= $now ) {
   // show reset_password_form
   $message .="<br>This password reset link is active.";

   } elseif ($found > 0  && $expires <= $now ) {

   // show invalid token message
   $message .="<br>This password reset link has expired.";

   } else {
   // show invalid token message
   $message .="<br>This password reset link does not exist.";
   }



// was the form submitted?
if (!isset($_POST["ResetPasswordForm"])) {
// did NOT submit form

   // if link is active, show reset form
    if (strpos($message, 'active') !== false) {
    	include('reset_password_form.php');
	   exit;
   } 


} else {
//**************************************************
// DID submit form, only reset password if link is still active and unused

	if( isset($_POST['password']) && isset($_SESSION['email']) && strpos($message, 'active') !== false) {

	$email = $_SESSION['email'];
	$userID = $_SESSION['userID'];

// Gather the post data
	$password = $_POST["password"];
	$confirm_password = $_POST["confirm_password"];
	
// Process form
	if ($password == $confirm_password) {
	
			// Hash and secure the password
			$hash = password_hash($password , PASSWORD_DEFAULT);

			// Update the user password

if($datasource == 'MySQL'){
// MySQL *****************************************************

				$sql = 'UPDATE '. $tUser . ' SET password = :password, password_plaintext = :confirm_password WHERE email = :email and ID = :userID';
				$query = $conn->prepare($sql);
				$query->bindValue(':password', $hash);
				$query->bindValue(':confirm_password', $confirm_password);
				$query->bindValue(':email', $email);
				$query->bindValue(':userID', $userID);
				$query->execute();
				
				$found = $query->rowCount();
				

} elseif ($datasource == 'FileMaker') {
// FILEMAKER *****************************************************

 	  $cmd = $fm->newFindCommand($tUser);
	  $cmd->addFindCriterion('email', '=="'.$email .'"');
	  $cmd->addFindCriterion('ID', '=="'.$userID .'"');
	  $query = $cmd->execute();
	  
	  $recID = $query->getFirstRecord()->getRecordID();
	  
	   if (!FileMaker::isError($query)) { 
	   	// once record found, update
	   	$cmd = $fm->getRecordById($tUser, $recID); 
		$cmd->setField('password', $hash); 
		$cmd->setField('password_plaintext', $confirm_password); 
		$query = $cmd->commit();
		
		$found = $query;	// equals 1 with successful commit
		
			if (FileMaker::isError($query)) { 
			// capture update error
				echo ' Error resetting password.';
				exit;
			} 
		} 
}				  
				
// if no error, update reset record & send confirmation email
// $found = 1 when successful
if($found > 0){

				  // update reset record 
				  
if($datasource == 'MySQL'){
// MySQL *****************************************************

				  $sql = 'UPDATE '. $tReset . ' SET ind_used = 1 WHERE token = :token';
				  $query = $conn->prepare($sql);
				  $query->bindValue(':token', $token);
				  $query->execute();
				  
} elseif ($datasource == 'FileMaker') {
// FILEMAKER *****************************************************

 	  $cmd = $fm->newFindCommand($tReset);
	  $cmd->addFindCriterion('token', '=="'.$token .'"');
	  $query = $cmd->execute();
	  
	  $recID = $query->getFirstRecord()->getRecordID();
	  
	   if (!FileMaker::isError($query)) { 
	   	// once record found, update
	   	$cmd = $fm->getRecordById($tReset, $recID); 
		$cmd->setField('ind_used', 1); 
		$query = $cmd->commit();
		
		$found = $query;	// equals 1 with successful commit
		
			if (FileMaker::isError($query)) { 
			// capture update error
				echo ' Error updating reset ind_used.';
				exit;
			} 
		} 
}				  
				
				// $result = 1 when successful, 1 = TRUE
				  if($result){
				   // Create the login link url
				   $url = SITEURL . 'login';
		
				   // send confirmation email
				   $mailbody = "<p>Your password has been reset.</p><p>Please <a href=\"". $url . "\">click here to login</a>.</p><p>If you have any questions please contact us.</p><p>Thanks!</p><p>Customer Support</p>";


// ***************************************************************		  
				   // to use a html template for the email body INSTEAD of the $mailbody variable:
				   // when sending the email via the FileMaker script use html template b/c html in $mailbody will not work
				   $filename = 'reset_password.html';
			 
		  
				   if ($datasource == 'FileMaker') {
		 // FILEMAKER *****************************************************
		 // create FM account via FileMaker script
		 // take the new record or the existing record and create their FM Security account / reset their FM Security account password
		 // script also sends the email message from FileMaker using PHPMailer via insert from url

					  $subject = gSITENAME . " - Password Reset";
					  $sendemail = 1; 	// tells FM script to send email
		  
					  $layout = 'web_user';
					  $script = 'FMAccount';
			 
					  if(empty($filename)){
						 $parameter = '$sendemail=' . $sendemail .'||$subject=' . $subject . '||$email=' . $email . '||$password=' . $password . '||$body=' . $mailbody . '||$filename=';
					  } else {
						 $parameter = '$sendemail=' . $sendemail .'||$subject=' . $subject . '||$email=' . $email . '||$password=' . $password . '||$filename=' . $filename . '||$body=';
					  }

			 
					  $cmd = $fm->newPerformScriptCommand($layout,$script,$parameter);
					  $result = $cmd->execute();
			 
					  if (FileMaker::isError($result)) {
					  echo 'Error creating FMAccount: '  . $result->getMessage();
					  }
				   }
		  
				   // send mail via PHP when using MySQL:
					  if($datasource == 'MySQL'){
						  $subject = gSITENAME . " - Password Reset";
						  try {
						  $sendMail = sendMail($to=$email, $subject, $message=$mailbody, $name='', $filename='', $url='');
						   } catch (Exception $e) {
							 echo $e->getMessage();
						   }
					   }
				  
				  }
				}

			$message ="Your password has been successfully reset.<br><br>You may now use your new password to login. We have also sent an email to you at <strong>" . $email . "</strong> confirming that your password has been reset.";
		} else {

			$message = 'Your passwords do not match.';
			header('Location: ' . PAGE . 'reset_password&message=' . $message);
			exit;
		}
	}
}

// show message
echo '<div class="mt-1">
        <h1>Password Reset</h1>
      </div>';

include(FEEDBACK);
  
?>