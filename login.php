<?php

$message = isset($_GET["message"]) ? htmlentities($_GET["message"]) : "" ;
$email = isset($_POST["email"]) ? $_POST["email"] : '' ;
$password = isset($_POST["password"]) ? $_POST["password"] : "" ;

if (isset($_POST["LoginForm"])) {
// did submit form

	 if (empty($email) || empty($password) ) {
	 	$message = 'Email address and password are required.'; 
	 }
  
	// check for valid e-mail address
	if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$message =  'Email (' .$email . ') is not in a valid email format! ';
	} 

  if(!empty($password) && filter_var($email, FILTER_VALIDATE_EMAIL) ) {

  $found = 0;
  $message = 'Error: email address and/or password not found.';

	if($datasource == 'MySQL'){
// MySQL *****************************************************
	// required values found, search for user using email address
	// do NOT assume email addresses are unique identifier for user record
	$sql = "SELECT id, first, last, email, password FROM ". $tUser . " WHERE email = :email";
	$query = $conn->prepare($sql);
	
	 // :email & :password are placeholders, replaced with variable values below:
	 $query->bindValue(':email', $email);
	 $query->execute();
	 
	 // to handle MySQL errors
	 handle_sql_errors($query);

	 $result = $query->fetchAll(PDO::FETCH_ASSOC);
	 $found = $query->rowCount(); // gives # of rows
	 // NOTE that $result will equal boolean 1 if any records are found and = 0 if no records are found
	 
	 } // end of MySQL qry
	 
	if ($datasource == 'FileMaker') {
// FILEMAKER *****************************************************

 	  $cmd = $fm->newFindCommand($tUser);
	  $cmd->addFindCriterion('email', '=="'.$email .'"');
	  $query = $cmd->execute();
	  
	   if (!FileMaker::isError($query)) { 
		  // put ALL found records into an array variable called $result
		  $result = $query->getRecords();
		  $found = $query->getFoundSetCount();
		  } 
	 } // end of FileMaker qry
	 
	 if($found > 0) {
	 // loop thru all records found, handles more than one user record with the same email address
	 // NOTE --- registration prevents more than one user record with the same email address, so this loop is not really needed, this is just an example of how to do it
	   	
	  foreach($result as $row){
	  //loop over each $result (row), setting $key to the column name and $value to the value in the column.
    	//  echo '<hr>DEBUG<hr>';
        //  print_r( $row ) ;
        
        	if($datasource == 'FileMaker'){
				if (password_verify($password, $row->getField('password') )) {
				   $_SESSION['is_logged_in'] = 1;
				   $_SESSION['userID'] = $row->getField('ID');
				   $_SESSION['first'] =$row->getField('first');
				   $_SESSION['last'] = $row->getField('last');
				   $_SESSION['email'] = $row->getField('email');

				   header('Location: ' . PAGE . 'post_login/dashboard');
				   exit;
				  }
			}	// end of FileMaker qry
			
          
          	if($datasource == 'MySQL'){
			   foreach($row as $key=>$value){
			   // show the key and value
    		   // echo '<hr>DEBUG<hr>';
			   // echo $key . ' = ' . $value . '<br>';
			  
				  if (password_verify($password, $row['password'])) {
				  // user record found using email address only
				  // using password_verify, compare hashed plaintext password submitted at login against hashed password stored in database
					 $_SESSION['is_logged_in'] = 1;
					 $_SESSION['userID'] = $row["id"];
					 $_SESSION['first'] = $row["first"];
					 $_SESSION['last'] = $row["last"];
					 $_SESSION['email'] = $row["email"];
	
					 header('Location: ' . PAGE . 'post_login/dashboard');
					 exit;
					} 
			  } // end of MySQL foreach
			}	// end of MySQL qry
			
		} // end of foreach
	  
 	} // end of if found > 0
	
  } // end of if !empty password and valid email
	 
} // end of did submit form

?>

      <div class="mt-1">
        <h1>Login</h1>
      </div>
      <p class="lead">Login using this form.</p>
      		<?php 
      			include(FEEDBACK);
      		?>
    
        <form class="form" action="<?php echo PAGE  . 'login'; ?>" method="POST" id="floating-label">
        <fieldset>
        
		<div class="form-group">
        <label for="email" class="control-label">Email address</label>
        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlentities($email) ?>" placeholder="Email address" required pattern="^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$" title="name@host.com" autofocus>
        </div>
        
        
		<div class="form-group">
        <label for="password" class="control-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" value="<?php echo htmlentities($password) ?>" placeholder="Password" required >
        </div>
        
       	<div class="text-right">
            <a href="<?php echo PAGE  . 'forgot_password' ?>">Forgot password? Reset password?</a>
        </div><br>
        
        
         <div class="form-group">
        	<button class="btn btn-lg btn-primary btn-block" type="submit" name="LoginForm" value="Login">Login</button>
        </div>
        
        	<div class="text-center">
            Not registered yet? <a href="<?php echo PAGE  . 'register' ?>">Register here.</a>
        </div>
        
        </fieldset>
      </form>