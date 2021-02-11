<?php
// code to protect post-login pages

    // check to see whether the user is logged in or not
    if(empty($_SESSION['is_logged_in']))
    {
        // if they are not, redirect them to the login page
		header('Location: ' . PAGE . 'login');
		exit;
    }


// include Cloudinary files
include_once('cloudinary_settings.php');


// set variables
$userID = $_SESSION["userID"] ;
$description = isset($_POST["description"]) ? htmlentities($_POST["description"]) : '' ;
$fileRecID = isset($_POST["fileRecID"]) ? htmlentities($_POST["fileRecID"]) : '' ;
$filename = isset($_POST["filename"]) ? htmlentities($_POST["filename"]) : '' ;
$fileURL = isset($_POST["fileURL"]) ? htmlentities($_POST["fileURL"]) : '' ;


// DELETE FILE *****************************************************
// delete file from Cloudinary AND the database
if ( isset($_POST["DeleteFileForm"]) && !empty($_POST["filename"]) && 
!empty($_POST["fileRecID"]) ) {
	\Cloudinary\Uploader::destroy($_POST["filename"], array("invalidate" => TRUE));
	
	$message = "File Deleted";

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
$phpFileUploadErrors = array(
    0 => 'There is no error, the file uploaded with success',
    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
    3 => 'The uploaded file was only partially uploaded',
    4 => 'No file was uploaded',
    6 => 'Missing a temporary folder',
    7 => 'Failed to write file to disk.',
    8 => 'A PHP extension stopped the file upload.',
);

if (isset($_POST["UploadForm"]) && $_FILES["fileToUpload"]["error"] == 4) {
	$message = "Select a file to upload before clicking upload!";
	
} elseif(isset($_POST["UploadForm"]) && $_FILES["fileToUpload"]["error"] != 0) {
	$error_message = $phpFileUploadErrors[$_FILES['fileToUpload']['error']]; 
	$message = "There was an error uploading the file! " . $error_message;	
}

if (isset($_POST["UploadForm"]) && ($_FILES['fileToUpload']['error'] == 0) ) {
// did submit form, with a file and without an error
$message = "File Uploaded Successfully!";

// rename file to remove unsafe characters
$rename = preg_replace( '`[^a-z0-9-_.]`i','_',basename($_FILES["fileToUpload"]["name"]) ); 

// remove extension
$without_extension = substr($rename, 0, strrpos($rename, "."));


// upload file to Cloudinary myfolder/myfilename (userID/filename) folder with new name
$finalname = $userID . "/" . $without_extension ;

$cloudUpload = \Cloudinary\Uploader::upload($_FILES["fileToUpload"]['tmp_name'], array("public_id" => $finalname, "timeout" => 60)
);

// Cloudinary file upload location url
$fileURL = $cloudUpload["url"];

$found = 0;
	
 if($datasource == 'MySQL'){
 // MySQL *****************************************************
 // CREATE the MySQL record with the file's URL
 // ONLY IF a record for that URL does NOT already exist
 // file will exist ONLY at Cloudinary
 
		   $sql = 'SELECT fileURL FROM '. $tFile . ' WHERE fileURL = :fileURL ';
		   $query = $conn->prepare($sql);
		   $query->bindValue(':fileURL', $fileURL);
		   $query->execute();
		   $result = $query->fetch(PDO::FETCH_ASSOC);
	
		   $found = $query->rowCount();
		   
		   if($found == 0){
			  $sql = "INSERT INTO " . $tFile . " (id_user, description, fileURL) VALUES (:userID, :desc, :fileURL)";
			  $query = $conn->prepare($sql);
		
			 // :userID & :fileURL are placeholders, placeholders are replaced with variable values below:
			 $query->bindValue(':userID', $userID);
			 $query->bindValue(':desc', $description);
			 $query->bindValue(':fileURL', $fileURL);
			 $query->execute();
			 
			 $errorInfo = $query->errorInfo();
	 
			  if($errorInfo[0] != 0){
				   echo '<p>Error: ' . $errorInfo[2] . '</p>'; 
				   exit;
			  }	
		  }
		  
  
 } elseif ($datasource == 'FileMaker') {
 // FILEMAKER *****************************************************
 // CREATE the FileMaker record with the file's URL, then insert from URL to get that image into the container field
 // ONLY IF a record for that URL does NOT already exist
 // file will exist BOTH at Cloudinary AND inside FileMaker

	   
	   $cmd = $fm->newFindCommand($tFile);
	   $cmd->addFindCriterion('fileURL', '=="'.$fileURL .'"');
	   $query = $cmd->execute();

	   if (!FileMaker::isError($query)) { 
	   	$found = $query->getFoundSetCount();
	   	$result = $query->getFirstRecord();
	   }

	   if($found == 0){
	   $newFileArray = array(
					   'id_user' => $userID,
					   'description' => $description,
					   'fileURL' => $fileURL,
				   );
		
		  $cmd = $fm->newAddCommand($tFile,$newFileArray);
		  $query = $cmd->execute();

		  if (FileMaker::isError($query)) { 
			  echo '<p>Error: ' . $query->getMessage() . '</p>'; 
			  exit; 
		  } 
	   
		  $newFileID = $query->getLastRecord()->getField('ID');	
	   
		  // find last inserted row
		  $cmd = $fm->newFindCommand($tFile);
		  $cmd->addFindCriterion('ID', $newFileID);
	   
		  // Alternate way to find last inserted row
		  // $recID =  current($query->getRecords())->getRecordID();
		  // OR, $recID = $query->getLastRecord()->getRecordID();
		  // $cmd = $fm->getRecordById($tFile, $recID);
	   
		  $query = $cmd->execute();

		  if (FileMaker::isError($query)) { 
			   echo '<p>Error: ' . $query->getMessage() . '</p>'; 
			   exit;
		  } 

 // Use a FileMaker script that uses the "Insert from URL" step to insert the image into the container from your fileURL field

			$parameter = '$ID=' . $newFileID ;

			$cmd = $fm->newPerformScriptCommand($tFile,'InsertFile', $parameter);
			$result = $cmd->execute(); 

			if (FileMaker::isError($result)) { 
			// display script error
				echo '<p>FileMaker Script Error: ' . $result->getMessage() . '</p>'; 
				exit;
			} 
	   } 
	}
 }
// END UPLOAD FILE *****************************************************
?> 

<!-- 
*****************************************************
UPLOAD FORM 
*****************************************************
-->
      <div class="mt-1">
        <h1>Upload a File<span class="lead"> for <?php echo $_SESSION['first'] . ' ' . $_SESSION['last']; ?></span></h1>
      </div>
      <p class="lead">Please use this form to upload a file to <a href="http://www.cloudinary.com" TARGET="_blank">Cloudinary</a>.<br>
      <span class="text-info">NOTE: Cloudinary provides a secure and comprehensive API for easily uploading images from server-side code, directly from the browser or from a mobile application.</span>
       <br>PHP to upload file to Cloudinary and create/delete a File record.
      </p>
      <?php 
      		include(FEEDBACK);
      		
      		if(!empty($fileURL)){
			// file uploaded, DISPLAY UPLOADED FILE *************
			 echo cl_image_tag($finalname, array( "alt" => "Uploaded Image" )); 
			}
			

       ?>

			 <div>
			 <form id="uploadForm" class="form" action="<?php echo PAGE  . 'post_login/upload_cloudinary'; ?>" method="POST" enctype="multipart/form-data">

			 <fieldset>
			 <div class="form-group">
			 <label for="file" class="control-label">Choose file:</label>
			 <input type="file" name="fileToUpload"></input>
			 </div>
	
			 <div class="form-group">
			 <label for="description" class="control-label">Description</label>
			 <input type="description" id="description" name="description" class="form-control" placeholder="File Description" autofocus>
			 </div>
        
		
			 <div class="form-group">
			 <button class="btn btn-lg btn-primary btn-block" type="submit" name="UploadForm" value="Upload">Upload</button>
			 </div>
		
			 </fieldset>
			 </form>
			 </div>
			 
			 <div id="loadingMessage" style="display:none" class="container alert alert-info" role="alert text-center">
		 Please wait while uploading...
		 <br><img src="assets/img/uploading.gif" width="128" alt="">
	   </div>
	   
<!-- 
*****************************************************
END UPLOAD FORM 
*****************************************************
-->


<!-- 
*****************************************************
FILE LIST 
*****************************************************
-->



<?php  
// FIND ALL FILES UPLOADED AT Cloudinary for logged in user *****************************************************

if($datasource == 'MySQL'){
// MySQL *****************************************************
// find records
  
	// required values found, search for user using email address
	// do NOT assume email addresses are unique identifier for user record
	$sql = "SELECT id, id_user, description, fileURL FROM ". $tFile . " WHERE id_user = :userID and fileURL LIKE :fileURL";
	$query = $conn->prepare($sql);
	$query->bindValue(':userID', $userID);
	$query->bindValue(':fileURL', '%cloudinary%');
	$query->execute();
	
	$result = $query->fetchAll(PDO::FETCH_ASSOC);
	$found = $query->rowCount();
	 
	 if($found > 0) {
	 
echo '<p>&nbsp;</p>
<div class="mt-1">
  <h1>Cloudinary Files <span class="lead">for ' . $_SESSION['first'] . ' ' . $_SESSION['last'] . '</span></h1>
</div>';

echo '	<div class="container">
      	<div class="row">';
          foreach($result as $row){
	 		echo '<div class="col-md-6 text-center card">';

		// Get the image from Cloudinary
		$url = $row["fileURL"];
		$fileRecID = $row["id"];

		// check if PDF
		$ext = pathinfo($url, PATHINFO_EXTENSION);

		 if(!empty($url)){
		 $thisurl = parse_url($url, PHP_URL_PATH);	
		 $thisurl = str_ireplace( '/www-asktami-com/image/upload/', "", $thisurl );
		 $explodeurl = explode('/' , trim($thisurl));
		 $imageURL = $explodeurl[1] . "/" . $explodeurl[2];
 
		// remove extension for filename
		$filename = substr($imageURL, 0, strrpos($imageURL, "."));

		 $pdfThumb = str_ireplace( '.pdf', ".jpg", $imageURL );
  
		echo '<a href="' . $url  .'" class="img-fluid img-thumbnail rounded mx-auto d-block" target="_blank">';
		   if($ext == "pdf"){
		   echo  cl_image_tag($pdfThumb, array("width"=>200, "height"=>300, "crop"=>"fill"));		
		   } else {
		   echo cl_image_tag($imageURL, array("alt" => "Uploaded Image", "width"=>200, "height"=>200, "crop"=>"scale"));
		   }
		echo '</a>';

		 } else {
		 $msg = 'No Image' ;
		 echo '<br>' . $msg ;
		 }

		echo '<p class="text-center">' . $row["description"] . ' (' . $ext. ')</p>';
		?>
		
		<!-- DELETE BUTTON -->
		<form class="form-inline" action="<?php echo PAGE  . 'post_login/upload_cloudinary'; ?>" method="POST" style="display: inline;">
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
 // Getting RELATED RECORDS from Portal via  getRelatedSet(portal table occurrence name)
     
$found = 0;

// find records
$findCommand = $fm->newFindCommand($tRelated);
$findCommand->addFindCriterion('ID', $userID);
$findCommand->addFindCriterion('cloudinary_files::fileURL', '*cloudinary*');
$query = $findCommand->execute();

if (!FileMaker::isError($query)) { 
	$found = $query->getFoundSetCount();
}

if ($found > 0) { 
// put ALL found records into an array variable called $records
	$records = $query->getRecords(); 

	// Loop through the found records 
	foreach ($records as $record){ 
		// Show the CHILD (file) records related to this PARENT (User)
		// file = table occurence name
		$portalRecords = $record->getRelatedSet('cloudinary_files');
	}
}
    
if ($found > 0 && $portalRecords > 0) { 
// only continue IF there are related portal records
// Display each Child (file) record in a separate card

echo '<p>&nbsp;</p>
<div class="mt-1">
  <h1>Cloudinary Files <span class="lead">for ' . $_SESSION['first'] . ' ' . $_SESSION['last'] . '</span></h1>
</div>';

  echo  '<div class="container">
      	<div class="row">';
       
  foreach ($portalRecords as $portalRecord) { 
  echo '<div class="col-md-6 text-center card">';

  // Get the image from Cloudinary
  $url = $portalRecord->getField('cloudinary_files::fileURL');
  $fileRecID = $portalRecord->getRecordId();

  // check if PDF
  $ext = pathinfo($url, PATHINFO_EXTENSION);

   if(!empty($url)){
   $thisurl = parse_url($url, PHP_URL_PATH);	
   $thisurl = str_ireplace( '/www-asktami-com/image/upload/', "", $thisurl );
   $explodeurl = explode('/' , trim($thisurl));
   $imageURL = $explodeurl[1] . "/" . $explodeurl[2];
   
   // remove extension for filename
   $filename = substr($imageURL, 0, strrpos($imageURL, "."));

   $pdfThumb = str_ireplace( '.pdf', ".jpg", $imageURL );
	
echo '<a href="' . $url  .'" class="img-fluid img-thumbnail rounded mx-auto d-block" target="_blank">';
	 if($ext == "pdf"){
	 echo  cl_image_tag($pdfThumb, array("width"=>200, "height"=>300, "crop"=>"fill"));		
	 } else {
	 echo cl_image_tag($imageURL, array("alt" => "Uploaded Image", "width"=>200, "crop"=>"scale"));
	 }
echo '</a>';

   } else {
   $msg = 'No Image' ;
   echo '<br>' . $msg ;
   }

  echo '<p class="text-center">' . $portalRecord->getField('file::description') . ' (' . $ext. ')</p>';
?>

  <!-- DELETE BUTTON -->
  <form class="form-inline" action="<?php echo PAGE  . 'post_login/upload_cloudinary'; ?>" method="POST" style="display: inline;">
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