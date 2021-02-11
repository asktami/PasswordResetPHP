<!DOCTYPE html>
<html>
<head>
	<title>File Upload</title>
</head>
<body>

<?php

// This solution inspired by
// https://www.w3schools.com/php/php_file_upload.asp

if($_GET){
//get the url (the current file location)
$url = $_GET['url'];

// get the file name
$name = basename($url);

// get the file contents
$file = file_get_contents($url);


//this is where we want to put the file
$target_dir =  __DIR__. "/uploads/" ;

if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// rename file to remove unsafe characters
$rename = preg_replace( '`[^a-z0-9-_.]`i','_',$name); 
$target_file = $target_dir . $rename ;  // from the URL, file to be uploaded


$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

// Check if image file is a actual image or fake image
$check = getimagesize($url);
if($check !== false) {
	$message = "File is an image - " . $check["mime"] . ".<br>";
	$uploadOk = 1;
} else {
	$message = "File is not an image.<br>";
	$uploadOk = 0;
}


// Check file size
if (file_exists($url) > 500000) {
    $message .= "Sorry, your file is too large.<br>";
    $uploadOk = 0;
} 


// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" && $imageFileType != "pdf") {
    $message .= "Sorry, only PDF, JPG, JPEG, PNG & GIF files are allowed.<br>";
    $uploadOk = 0;
} 


// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    $message .= "Sorry, your file was not uploaded.<br>";
// if everything is ok, try to upload file
} else {


// move the file
// if the destination file already exists, it will be overwritten
    if (copy($url, $target_file)) {
        $message .= "The file ". $rename. " has been uploaded.<br>";
    } else {
        $message .= "Sorry, there was an error uploading your file.<br>";
    }

if(!@copy($url,$target_file))
{
    $errors= error_get_last();
    $message .=  "COPY ERROR: ".$errors['type'];
    $message .=  "<br />\n".$errors['message'];
} else {
    $message .=  "File copied from remote!";
}


}
}

echo $message;
?>

</body>
</html>
