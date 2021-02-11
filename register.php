<?php

$message = isset($_GET["message"]) ? htmlentities($_GET["message"]) : '' ;
$first = isset($_POST["first"]) ? $_POST["first"] : '' ;
$last = isset($_POST["last"]) ? $_POST["last"] : '' ;
$email = isset($_POST["email"]) ? $_POST["email"] : '' ;
$password = isset($_POST["password"]) ? $_POST["password"] : '' ;


if (isset($_POST["RegistrationForm"])) {
// did submit form
	
// check for valid e-mail address
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
   $message =  'Email (' .$email . ') is not in a valid email format! ';
}

if(!empty($_POST['first']) && !empty($_POST['last']) && !empty($_POST['password']) && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ) {
			  
  $found = 0;

//*************************************************************
//*************************************************************
// FIND REQUEST
	// required values exist, search for existing user record
	// DO assume email address is unique identifier for user record
	
	// query either MySQL or FileMaker
	
	if($datasource == 'MySQL'){
// MySQL *****************************************************
	$sql = "SELECT id, first, last, email, password FROM ". $tUser . " WHERE email = :email";
	$query = $conn->prepare($sql);
	
	 // :email & :password are placeholders, replaced with variable values below:
	 // Alternate way do bind params & execute query is: $query->execute(array(':email' => $email));
	 $query->bindValue(':email', $email);
	 $query->execute();
	 
	 // check for error
	 $errorInfo = $query->errorInfo();
	 
	  if($errorInfo[0] != 0){
		   echo '<p>Error: ' . $errorInfo[2] . '</p>'; 
		   exit;
	  }	
	 	 
	 // put single found record into an array variable called $result
	 $result = $query->fetch(PDO::FETCH_ASSOC);
	 $found = $query->rowCount();
	 
	 
	 
	 } elseif ($datasource == 'FileMaker') {
// FILEMAKER *****************************************************
	  $cmd = $fm->newFindCommand($tUser);
	  $cmd->addFindCriterion('email', '=="'.$email .'"');
	  $query = $cmd->execute();
	  
	   if (!FileMaker::isError($query)) { 
		  // put single found record into an array variable called $result
		  $result = $query->getFirstRecord();
		  $found = $query->getFoundSetCount();
		  } 
	 }
	 
	if($found > 0){
		  $message = 'You have already registered. Please login.';  // because matching email was found
		  
/*
For additional security INSTEAD of displaying a message to the user that tells them they've already registered, thereby telling anyone with evil intent that the email address does have an existing registration on your site, you could EMAIL a message to the email address entered into the registration form, saying "You have already registered. Please login.", and DISPLAY in the BROWSER a message that says they should "Please check your email for the next step".
*/

/*
//*************************************************************
// ALTERNATIVE
// NOTE -- the code below checks for a user record with a matching email address AND password
// This would allow multiple users to have the SAME email address and different passwords
// Which is NOT what we want, this is just an example of how to check for both email + hashed password
//*************************************************************
		 if($datasource == 'FileMaker'){
			if (password_verify($password, $result->getField('password') )) {
			   $message = 'You have already registered. Please login.';	
			}
		 }
		 
		 if($datasource == 'MySQL'){
			if (password_verify($password, $result['password'])) {
			// user record found using email address only
			// using password_verify, compare hashed plaintext password submitted at login against hashed password stored in database
			   $message = 'You have already registered. Please login.';
			} 
		 }
	   
		// check if $message contains registered b/c there was a password match
		if (strpos($message, 'registered') !== false) {
		   // found a matching password
		   } else {
		   // did not find a matching password
			$message .= 'Status: password does not exist.';
		}
//*************************************************************
*/
   	} 
	 

// NOTE -- if we allowed multiple user records with the SAME email address but different passwords
// we would create a new user record using this condition:
// if (strpos($message, 'not exist') !== false) {
// BUT since email addresses must be unique, we only create a new User record if no matching email address was found

  if (strpos($message, 'registered') == false) {
  //*************************************************************
  //*************************************************************
  // CREATE USER RECORD

	  // Hash and secure the password
	  $hash = password_hash($password , PASSWORD_DEFAULT);

	  if($datasource == 'MySQL'){
  // MySQL *****************************************************

		  $sql = "INSERT INTO " . $tUser . " (first, last, email, password, password_plaintext) VALUES (:first, :last, :email, :hash, :password)";
		  $query = $conn->prepare($sql);
		
		  // :email & :token are placeholders, placeholders are replaced with variable values below:
		  $query->bindValue(':first', $first);
		  $query->bindValue(':last', $last);
		  $query->bindValue(':email', $email);
		  $query->bindValue(':hash', $hash);
		  $query->bindValue(':password', $password);
		  $query->execute();
		
		  $errorInfo = $query->errorInfo();
	 
		   if($errorInfo[0] != 0){
				echo '<p>Error: ' . $errorInfo[2] . '</p>'; 
				exit;
		   }	
		
		  $newUserID = $conn->lastInsertId();	// get ID of User record that was just created
		
		  // find last inserted row
		  $sql = "SELECT id, first, last, email, password, password_plaintext FROM " . $tUser . " WHERE id = :id ";
		  $query = $conn->prepare($sql);
		  $query->bindValue(':id', $newUserID);
		  $query->execute();
		
		  $errorInfo = $query->errorInfo();
	
		  if($errorInfo[0] != 0){
			   echo '<p>Database Query Error: ' . $errorInfo[2] . '</p>'; 
			   exit;
		  }	
		
		  // put single found record into an array variable called $result
		  $result = $query->fetch(PDO::FETCH_ASSOC);
		  $created = $query->rowCount();
		
		
	   } elseif ($datasource == 'FileMaker') {
  // FILEMAKER *****************************************************
	  
		  $newUserArray = array(
					   'first' => $first,
					   'last' => $last,
					   'email' => $email,
					   'password' => $hash,
					   'password_plaintext' => $password,
				   );
		
		  $cmd = $fm->newAddCommand($tUser,$newUserArray);
		  $query = $cmd->execute();

		  if (FileMaker::isError($query)) { 
			  echo '<p>Error: ' . $query->getMessage() . '</p>'; 
			  exit; 
		  } 
	   
		  $newUserID = $query->getLastRecord()->getField('ID');	
	   
		  // find last inserted row
		  $cmd = $fm->newFindCommand($tUser);
		  $cmd->addFindCriterion('id', $newUserID);
	   
		  // Alternate way to find last inserted row
		  // $recID =  current($query->getRecords())->getRecordID();
		  // OR, $recID = $query->getLastRecord()->getRecordID();
		  // $cmd = $fm->getRecordById($tUser, $recID);
	   
		  $query = $cmd->execute();

		  if (FileMaker::isError($query)) { 
			   echo '<p>Error: ' . $query->getMessage() . '</p>'; 
			   exit;
		  } 

		  // put single found record into an array variable called $result
		  $result = $query->getFirstRecord();
		  $created = $query->getFoundSetCount();

	  }

  //*************************************************************
  //*************************************************************

	  if($created == 1) {		
		  // set session variables
	
		  if(($datasource == 'MySQL')){
			  $userID = $result['id'];
			  $first = $result['first'];
			  $last = $result['last'];
			  $email = $result['email'];
	
		  } elseif ($datasource == 'FileMaker'){
			  $userID = $result->getField('ID');
			  $first = $result->getField('first');
			  $last = $result->getField('last');
			  $email = $result->getField('email');
		  }
	
		  $_SESSION['is_logged_in'] = 1;
		  $_SESSION['userID'] = $userID;
		  $_SESSION['first'] = $first;
		  $_SESSION['last'] = $last;
		  $_SESSION['email'] = $email;
	
	
		  // Create the login link url
		  $url = SITEURL . 'login';

		  // send registration email
		  $mailbody = "<h2>". $first . ",</h2><p>You have succesfully registered.</p><p>Please <a href=\"". $url . "\">click here to login</a>.</p><p>If you have any questions please contact us.</p><p>Thanks!</p><p>Customer Support</p>";

	  
// ***************************************************************		  
		  // to use a html template for the email body INSTEAD of the $mailbody variable:
		  // when sending the email via the FileMaker script use html template b/c html in $mailbody will not work
		  $filename = 'registration_confirmation.html';
		 	 
		  
		  if ($datasource == 'FileMaker') {
// FILEMAKER *****************************************************
// create FM account via a FileMaker script which takes a new / existing record and creates / resets their FM Security account password

		 	 $subject = gSITENAME . " - Registration Confirmation";
		 	 $sendemail = 1; 	// tells FM script to send email
		  
			 $layout = 'web_user';
			 $script = 'FMAccount';
			 
			 if(empty($filename)){
			 	$parameter = '$sendemail=' . $sendemail .'||$subject=' . $subject . '||$email=' . $email . '||$password=' . $password . '||$body=' . $mailbody . '||$filename=' . '||$name=' . $first . ' ' . $last;
			 } else {
			 	$parameter = '$sendemail=' . $sendemail .'||$subject=' . $subject . '||$email=' . $email . '||$password=' . $password . '||$filename=' . $filename . '||$body=' . '||$name=' . $first . ' ' . $last;
			 }

			 
			 $cmd = $fm->newPerformScriptCommand($layout,$script,$parameter);
			 $result = $cmd->execute();
			 
			 if (FileMaker::isError($result)) {
			 echo 'Error creating FMAccount: '  . $result->getMessage();
			 }
		  }
		  
		  
		  // send mail
		  $subject = gSITENAME . " - Registration Confirmation";
		  $name = $first . ' ' . $last ;
		  
		  try {
			  $sendMail = sendMail($to=$email, $subject, $message=$mailbody, $name, $filename='', $url='');
		  } catch (Exception $e) {
			  echo $e->getMessage();
		  } 

		  header('Location: ' . PAGE . 'post_login/dashboard');
		  exit;

	  } else {
		  echo "Error creating new User record.";
		  exit;
	  }
  }


} else {

  // required values missing, show message
  $message .= 'All fields are required.'; 
  
  }	// end of check for required values
  
} // end of did submit form

		
		

?>

      <div class="mt-1">
        <h1>Create an Account</h1>
      </div>
      <p class="lead">Register using this form.</p>
       		<?php 
      			include(FEEDBACK);
      		?>
 
        <form class="form" action="<?php echo PAGE  . 'register'; ?>" method="POST" autocomplete="off" id="floating-label">
        
		<div class="form-group">
        <label for="first" class="control-label">First Name</label>
        <input type="text" id="first" name="first" class="form-control" value="<?php echo htmlentities($first) ?>" placeholder="First Name" required autofocus>
        </div>
        
        <div class="form-group">
        <label for="last" class="control-label">Last Name</label>
        <input type="text" id="last" name="last" class="form-control" value="<?php echo htmlentities($last) ?>" placeholder="Last Name" required>
        </div>
        
        <div class="form-group">
        <label for="email" class="control-label">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlentities($email) ?>" placeholder="Email Address" required pattern="^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$" title="name@host.com" >
        </div>
        
        <div class="form-group">
		<label for="password" class="control-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" value="<?php echo htmlentities($password) ?>" placeholder="Password" required>
        </div>
        
        <div class="form-group">
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="RegistrationForm" value="Register">Register</button>
        </div>
        
        <div class="text-center">
            Already have an account? <a href="<?php echo PAGE  . 'login' ?>">Login.</a>
        </div>
      </form>
