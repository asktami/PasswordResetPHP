<?php
// code to protect post-login pages

    // check to see whether the user is logged in or not
    if(empty($_SESSION['is_logged_in']))
    {
        // if they are not, redirect them to the login page
		header('Location: ' . PAGE . 'login');
		exit;
    }


// DELETE FILE *****************************************************
// set variables
$userID = $_SESSION["userID"] ;
$description = isset($_POST["description"]) ? htmlentities($_POST["description"]) : '' ;
$fileRecID = isset($_POST["fileRecID"]) ? htmlentities($_POST["fileRecID"]) : '' ;
$fileURL = isset($_POST["fileURL"]) ? htmlentities($_POST["fileURL"]) : '' ;
$filename = isset($_POST["filename"]) ? htmlentities($_POST["filename"]) : '' ;

// DELETE FILE *****************************************************
// find &  delete file from the database and the user's folder

if( isset($_POST["DeleteFileForm"]) && 
!empty($_POST["fileRecID"]) && !empty($_POST["filename"])  ) {
	
	$message = "File Deleted";
	
	// delete file from directory
	 $filepath ='post_login/uploads/'.$userID.'/'.$filename;
	 unlink($filepath);
	
	
	if($datasource == 'MySQL'){
	// MySQL *****************************************************
	
	 // DELETE  fileRecID
	 $sql = 'DELETE FROM ' . $tFile . ' WHERE id = :fileRecID ';
	 $query = $conn->prepare($sql);
	 $query->bindValue(':fileRecID', $fileRecID);
	 $query->execute();
	 
	 $errorInfo = $query->errorInfo();
	 
			  if($errorInfo[0] != 0){
				   echo '<p>Error: ' . $errorInfo[2] . '</p>'; 
				   exit;
			  }	
		
	
			
	} elseif ($datasource == 'FileMaker') {
	// FILEMAKER *****************************************************
	
	 $cmd = $fm->newDeleteCommand($tFile,$fileRecID);
	 $query= $cmd->execute();
	 
	 if (FileMaker::isError($query)) { 
			  echo '<p>Error: ' . $query->getMessage() . '</p>'; 
			  exit; 
		  }
	}
}
// END DELETE FILE *****************************************************


// UPLOAD FILE *****************************************************
$continue = null;
$message = array();
$uploadOK = 0;
$deleteAfterUpload = 0 ; // to delete file from dirctory after uploading, change to 1

$target_dir =  __DIR__. "/uploads/" .  $_SESSION['userID'] . "/" ;


if (isset($_POST["UploadForm"]) && $_FILES["fileToUpload"]['tmp_name'] == '') {
	$message[] = "Select a file to upload before clicking upload!";
} else {
	$continue = 1;
}

// CREATE THE UPLOAD FOLDER, CHECK THE FILE
if ( isset($_POST['UploadForm']) && $continue && !empty($_FILES["fileToUpload"]) && ($_FILES['fileToUpload']['error'] == 0) ) {
// did submit form, with a file and without an error

// rename file to remove unsafe characters
$rename = preg_replace( '`[^a-z0-9-_.]`i','_',basename($_FILES["fileToUpload"]["name"]) ); 

$target_file = $target_dir . $rename ;  // from the form input, file to be uploaded

$fileURL =  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/uploads/" .  $_SESSION['userID'] . "/" .  $rename;

$fileURL = str_replace("index.php?page=","",$fileURL);

$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

// Check if image file is a actual image or fake image
$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
if($check !== false) {
	$message[] = "File is an image - " . $check["mime"] . ".<br>";
} else {
	$message[] = "File is not an image.<br>";
}

// Check if file already exists
if (file_exists($target_file)) {
    $message[] = "File already existed and was overwritten.<br>";
}


// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
    $message[] = "Sorry, your file is too large.<br>";
    $continue = 0;
} 


// Only allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" && $imageFileType != "pdf") {
    $message[] = "Sorry, only PDF, JPG, JPEG, PNG & GIF files are allowed.<br>";
    $continue = 0;
} 
}

// UPLOAD THE FILE
// Check if $continue is set to 0 by an error
if ( isset($_POST['UploadForm']) && $continue == 0 ){
    $message[] = "Sorry, your file was not uploaded.<br>";
}

// if everything is ok, try to upload file
if ( isset($_POST['UploadForm']) && $continue == 1 ){

// create upload directory if it doesn't already exist
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// upload a file (both for MySQL and FileMaker)
// if the destination file already exists, it will be overwritten
	if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $message[] = "The file ". $rename. " has been uploaded.<br>";  
        $uploadOK = 1;      	
    } else {
        $message[] = "Sorry, there was an error uploading your file.<br>";
    }
    	
	if( $uploadOK && $deleteAfterUpload && file_exists($target_file) ){
	   // delete the original (before moved) uploaded file
		   if (!unlink($target_file)){ 
			   $message[] = "Error deleting $target_file from directory after uploading.";
		   } else{
			   $message[] = "Deleted $target_file from directory after uploading.";
		   }
	}
}

// CREATE THE MYSQL DATABASE RECORD
if( isset($_POST['UploadForm']) && $uploadOK && $datasource == 'MySQL'){
 // MySQL *****************************************************
 // Find and update the User's MySQL record with the file's URL
 // AND create a new File record
 
 // Find and update the User's MySQL record with the file's URL
		   $sql = 'UPDATE '. $tUser . ' SET fileURL = :fileURL WHERE id = :userID';
				$query = $conn->prepare($sql);
				$query->bindValue(':fileURL', $fileURL);
				$query->bindValue(':userID', $_SESSION['userID']);
				$query->execute();
		
				$errorInfo = $query->errorInfo();
	
				  if($errorInfo[0] != 0){
					   echo '<p>MySQL Update Error: ' . $errorInfo[2] . '</p>'; 
					   exit;
				  }	
				  
// ADD a new File record				  
		  $sql = "INSERT INTO " . $tFile . " (id_user, description, fileURL) VALUES (:userID, :description, :fileURL)";
				$query = $conn->prepare($sql);
				$query->bindValue(':userID', $_SESSION['userID']);
				$query->bindValue(':fileURL', $fileURL);
				$query->bindValue(':description', $rename);
				$query->execute();
		
				$errorInfo = $query->errorInfo();
				
				 if($errorInfo[0] != 0){
					   echo '<p>MySQL Insert Error: ' . $errorInfo[2] . '</p>'; 
					   exit;
				  }	
 } 

// CREATE THE FILEMAKER DATABASE RECORD
if ( isset($_POST['UploadForm']) && $uploadOK && $datasource == 'FileMaker') {
 // FILEMAKER *****************************************************
 // Find and update the User's FileMaker record with the file's URL, then insert from URL to get that file into the container field
 // AND create a new File record
 
 
// UPDATE USER RECORD
 
	   $cmd = $fm->newFindCommand($tUser);
	   $cmd->addFindCriterion('ID', $_SESSION['userID']);
	   $query = $cmd->execute();
	  
	   $recID = $query->getFirstRecord()->getRecordID();
	  
		if (!FileMaker::isError($query)) { 
			// once record found, update
			$cmd = $fm->getRecordById($tUser, $recID); 
			$cmd->setField('fileURL', $fileURL); 
			$query = $cmd->commit();
		
				if (FileMaker::isError($query)) { 
				// display update error
					echo '<p>FileMaker Update Error: ' . $query->getMessage() . '</p>'; 
					exit;
				} 

// Use a FileMaker script that uses the "Insert from URL" step to insert the image into the container from your fileURL field
			$parameter = '$ID=' . $_SESSION['userID'] ;

			$cmd = $fm->newPerformScriptCommand($tUser,'InsertUserFile', $parameter);
			$result = $cmd->execute(); 

			if (FileMaker::isError($result)) { 
			// display script error
				echo '<p>FileMaker Script Error: ' . $result->getMessage() . '</p>'; 
				exit;
			} 
			
			// get FileMaker Container contents
			$cmd = $fm->newFindCommand($tUser);
			$cmd->addFindCriterion('ID', $_SESSION['userID']);
			$query = $cmd->execute();
			
			if (!FileMaker::isError($query)) { 
			   $image = $query->getFirstRecord()->getField('fileContainer');
			  }
	   } 

// CREATE FILE RECORD
	// use a FileMaker script to create the File record instead of PHP
	// the script uses the "Insert from URL" step to insert the image into the container from the fileURL field

		  $parameter = '$ID=' . $_SESSION['userID'] . '||$fileURL=' . $fileURL;

		  $cmd = $fm->newPerformScriptCommand($tFile,'CreateFileRecord', $parameter);
		  $result = $cmd->execute(); 

		  if (FileMaker::isError($result)) { 
		  // display script error
			  echo '<p>FileMaker Script Error: ' . $result->getMessage() . '</p>'; 
			  exit;
		  } 
		
 }
?> 

      <div class="mt-1">
        <h1>Upload a File</h1>
      </div>
      <p class="lead">Please use this form to upload a file.<br>
      <span class="text-danger">NOTE: adding the ability for users to upload files to your web server is a major security risk!  Please do your research before adding this ability.</span>
      <br>PHP to upload a single file to your server.<br>
        For MySQL and FileMaker: PHP to create a File record, and find and update the User record with the uploaded file's URL.<br>
        For FileMaker: FileMaker script to insert uploaded file into container field <em>(will only work from pages located on FileMaker Server)</em>.</p>
    
      <?php 
      		include(FEEDBACK);
      ?>
      
 <div><form id="uploadForm" class="form" action="<?php echo PAGE  . 'post_login/upload'; ?>" method="POST" enctype="multipart/form-data">

        <fieldset>
        <div class="form-group">
        <label for="file" class="control-label">File</label>
        <input type="file" name="fileToUpload"></input>
        </div>
            
        
        <div class="form-group">
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="UploadForm" value="Upload">Upload</button>
        </div>
        </fieldset>
      </form></div>
      
      
	   <div id="loadingMessage" style="display:none" class="container alert alert-info" role="alert text-center">
		 Please wait while uploading...
		 <br><img src="assets/img/uploading.gif" width="128" alt="">
	   </div>

<?php 
// DISPLAY UPLOADED FILE *****************************************************
      		
      		if(!empty($fileURL) && $uploadOK == 1){
      		
      		$ext = pathinfo($fileURL, PATHINFO_EXTENSION);
      		
			// file uploaded
			echo "<p class=\"text-info\">File Uploaded Successfully!</p>";
			
			if($ext == "pdf"){
			echo "<image src='assets/img/PDF.jpg' alt='Uploaded PDF'>";
			
			} else {
			echo "<image src=\"" . $fileURL . "\" alt=\"Uploaded File\">";
			}  
			
			   if($datasource == 'FileMaker') {
					 echo "<br>";
					 echo "view FileMaker container:<br>";
					 if($ext == "pdf"){
					 	echo "<image src='assets/img/PDF.jpg' alt='Uploaded PDF'>";
					 } else {
					 // $filemaker_host = FM Server IP from connections.php
					 	echo "<image src=\"http://".$filemaker_host . $image ."\" alt=\"Uploaded File\">";
					 }
			   }
			}
			
      		
if (isset($_POST["UploadForm"]) && $uploadOK == 1 ) {    		
// DEBUG *****************************************************
      		echo "<p>&nbsp;</p>";
      		echo "<h3>FilePaths</h3>";
      		echo "<b>uploadOK =  </b>" . $uploadOK;
      		echo "<br>";
      		echo "<b>fileURL =  </b>" . $fileURL;
      		echo "<br>";
      		echo "<b>dirname =  </b>" . dirname($target_file) ;
      		echo "<br>";
      		echo "<b>realpath = </b>" . realpath($target_file) ;
      		echo "<br>";
      		echo "<b>orignial filename (basename) = </b>" . basename($_FILES["fileToUpload"]["name"]) ;
      		echo "<br>";
      		echo "<b>renamed filename = </b>" . $rename ;
      		echo "<br>";
      		echo "<b>SERVER[DOCUMENT_ROOT] = </b>" . $_SERVER["DOCUMENT_ROOT"];
      		echo "<br>";
      		echo "<b>SERVER[SCRIPT_FILENAME] = </b>" . $_SERVER['SCRIPT_FILENAME'];
      		echo "<br>";
      		echo "<b>SERVER[PHP_SELF]  = </b>" . $_SERVER['PHP_SELF'];
      		echo "<br>";
      		echo "<b>directory for SERVER[PHP_SELF]  = </b>" . dirname($_SERVER['PHP_SELF']);
      		echo "<br>";
      		echo "<b>ROOT  = </b>" . ROOT;
      		echo "<p>&nbsp;</p>";
// DEBUG *****************************************************
}
?>

<!-- 
*****************************************************
FILE LIST 
*****************************************************
-->



<?php  
// FIND ALL FILES UPLOADED for logged in user *****************************************************

if($datasource == 'MySQL'){
// MySQL *****************************************************
// find records
  
	// required values found, search for user using email address
	// do NOT assume email addresses are unique identifier for user record
	$sql = "SELECT id, id_user, description, fileURL FROM ". $tFile . " WHERE id_user = :userID and fileURL NOT LIKE :fileURL";
	$query = $conn->prepare($sql);
	$query->bindValue(':userID', $userID);
	$query->bindValue(':fileURL', '%cloudinary%'); // not like Cloudinary
	$query->execute();
	
	$result = $query->fetchAll(PDO::FETCH_ASSOC);
	$found = $query->rowCount();

	 if($found > 0) {
	 
echo '<p>&nbsp;</p>
<div class="mt-1">
  <h1>MySQL Files <span class="lead">for ' . $_SESSION['first'] . ' ' . $_SESSION['last'] . '</span></h1>
</div>';

echo '	<div class="container">
      	<div class="row">';
foreach($result as $row){
	 		echo '<div class="col-md-6 text-center card">';

		// Get the image from MySQL
		$url = $row["fileURL"];
		$fileRecID = $row["id"];
		
		// check if PDF
		$ext = pathinfo($url, PATHINFO_EXTENSION);
		 if(!empty($url)){
		 
		 $thisPath = parse_url($url, PHP_URL_PATH);	
		 // $thisPath = str_ireplace( '/www-asktami-com/image/upload/', "", $thisPath );
		 $explodeurl = explode('/' , trim($thisPath));
		// remove extension for filename
		$filename = end($explodeurl);
		
		
		echo '<a href="' . $url  .'" class="img-fluid img-thumbnail rounded mx-auto d-block" target="_blank">';
		   if($ext == "pdf"){
		   echo "<img width='200' height='200' alt='Uploaded PDF' src='assets/img/PDF.jpg'>";			   
		   } else {
		   echo "<img width='200' alt='Uploaded Image' src='".$thisPath."'>";
		   }
		echo '</a>';
		   
		 } else {
		 $msg = 'No Image' ;
		 echo '<br>' . $msg ;
		 }

		echo '<p class="text-center">' . $row["description"] . ' (' . $ext. ')</p>';
		?>
		
		<!-- DELETE BUTTON -->
		<form class="form-inline" action="<?php echo PAGE  . 'post_login/upload'; ?>" method="POST" style="display: inline;">
		<fieldset>
		<div class="form-group">
		<input type="hidden" name="fileRecID" value="<?php echo $fileRecID ?>"></input>
		</div>

		<div class="form-group">
		<input type="hidden" name="filename" value="<?php echo $filename ?>"></input>
		</div>

		<div class="form-group">
		<button class="btn btn-sm btn-danger btn-block" type="submit" name="DeleteFileForm" value="Delete">Delete</button>
		</div>
		</fieldset>
		</form>

		<?php 
		echo '</div>';
	 }
	 	
	echo '	</div></div>';
  }

} elseif ($datasource == 'FileMaker') {
 // FILEMAKER *****************************************************
     
$found = 0;

// find records
$findCommand = $fm->newFindCommand($tFile);
$findCommand->addFindCriterion('id_user', $userID);
$findCommand->addFindCriterion('flag_non_cloudinary', '1');
$query = $findCommand->execute();

if (!FileMaker::isError($query)) { 
	$found = $query->getFoundSetCount();
}

if ($found > 0 ) { 

// put ALL found records into an array variable called $records
$records = $query->getRecords(); 
	
echo '<p>&nbsp;</p>
<div class="mt-1">
  <h1>FileMaker Files <span class="lead">for ' . $_SESSION['first'] . ' ' . $_SESSION['last'] . '</span></h1>
</div>';

  echo  '<div class="container">
      	<div class="row">';
       
foreach ($records as $record) { 
  echo '<div class="col-md-6 text-center card">';


  // Get the image from FileMaker
  $url = $record->getField('fileURL');
  $fileRecID = $record->getRecordId();
  $description = $record->getField('description');
  $filename = $record->getField('filename');

  // check if PDF
  $ext = pathinfo($url, PATHINFO_EXTENSION);

if(!empty($url)){
   $thisPath = parse_url($url, PHP_URL_PATH);	
   $explodeurl = explode('/' , trim($thisPath));
   
   $pdfThumb = str_ireplace( '.pdf', ".jpg", $thisPath );

echo '<a href="' . $url  .'" class="img-fluid img-thumbnail rounded mx-auto d-block" target="_blank">';
	 if($ext == "pdf"){
	 echo "<img width='200' height='200' alt='Uploaded PDF' src='assets/img/PDF.jpg'>";			   	
	 } else {
	 echo "<img width='200' alt='Uploaded Image' src='".$thisPath."'>";
	 }
echo '</a>';



} else {
   $msg = 'No Image' ;
   echo '<br>' . $msg ;
}


  echo '<p class="text-center">' . $filename . ' (' . $ext. ')</p>';
  echo '<p class="text-center">' . $description .'</p>';
?>

  <!-- DELETE BUTTON -->
  <form class="form-inline" action="<?php echo PAGE  . 'post_login/upload'; ?>" method="POST" style="display: inline;">
  <fieldset>
  <div class="form-group">
  <input type="hidden" name="fileRecID" value="<?php echo $fileRecID ?>"></input>
  </div>

  <div class="form-group">
  <input type="hidden" name="filename" value="<?php echo $filename ?>"></input>
  </div>

  <div class="form-group">
  <button class="btn btn-sm btn-danger btn-block" type="submit" name="DeleteFileForm" value="Delete">Delete</button>
  </div>
  </fieldset>
  </form>

<?php 
  echo '</div>';
  }
  
echo '	</div></div>';
} 
}
// END FIND ALL UPLOADED FILES for logged in user *****************************************************

?>

<!-- 
*****************************************************
END FILE LIST 
*****************************************************
-->


