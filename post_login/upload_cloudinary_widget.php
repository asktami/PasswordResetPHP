<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../img/favicon.ico">

    <title>Cloudinary Upload Widget</title>

    <!-- Bootstrap core CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../assets/css/demo.css" rel="stylesheet">
    
    <!-- to fix FOUC (Flash of Unstyled Content) -->
    <script type="text/javascript">
    var elm=document.documentElement;
    elm.style.display="none";
    document.addEventListener("DOMContentLoaded",function(event) { elm.style.display="block"; });
    </script>
  </head>

  <body>
    <!-- Fixed navbar -->
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
      <a class="navbar-brand" href="../index.php?page=home">Demo</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="../index.php?page=home">Home</a>
          </li>
      </div>
    </nav>

<!-- Begin page content -->
<main class="container">

<!-- 
*****************************************************
UPLOAD FORM 
*****************************************************
-->
      <div class="mt-1">
        <h1>Upload a File<span class="lead"></span></h1>
      </div>
      <p class="lead">Please use this form to upload a file to <a href="http://www.cloudinary.com" TARGET="_blank">Cloudinary</a> using the <em>Cloudinary Upload Widget</em>.<br>
      <span class="text-info">NOTE: Cloudinary provides a secure and comprehensive API for easily uploading images from server-side code, directly from the browser or from a mobile application.</span>
      <br>Does <strong class="text-danger">not</strong> upload file to your server or create/delete a database record.</p>
      
<!-- UPLOAD WIDGET BUTTON - multiple without cropping -->
<button id="multiple" type="button" class="btn btn-primary"></button>


<hr>

<!-- UPLOAD WIDGET BUTTON - single with cropping -->
<button id="single" type="button" class="btn btn-primary"></button>

</main><!-- /container -->
    

<footer class="footer">
<div class="container">
<p class="text-center">
Copyright 
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
<!--
// This script and many more are available free online at
// The JavaScript Source!! http://javascript.internet.com

// Begin
var months=new Array(13);
months[1]="January";
months[2]="February";
months[3]="March";
months[4]="April";
months[5]="May";
months[6]="June";
months[7]="July";
months[8]="August";
months[9]="September";
months[10]="October";
months[11]="November";
months[12]="December";
var time=new Date();
var lmonth=months[time.getMonth() + 1];
var date=time.getDate();
var year=time.getYear();
if (year < 2000)    // Y2K Fix, Isaac Powell
year = year + 1900; // http://onyx.idbsu.edu/~ipowell
document.write("&copy; 1999-" + year );
// End
-->
</script>
 &nbsp;<a href="https://www.asktami.com" TARGET="_new">AskTami Inc. dba Creative Computing</a>.&nbsp;&nbsp;&nbsp;All rights reserved.
</p>
</div>
</footer>


<!-- include jQuery -->
<script src='//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'></script>
<!-- include Cloudinary widget -->
<script src="//widget.cloudinary.com/global/all.js" type="text/javascript"></script>
<script>
// set cloud_name globally
  cloudinary.setCloudName('YOUR_CLOUD_NAME');
</script>

<script>
// function to generate the signature. 
var generateSignature = function(callback, params_to_sign){
    $.ajax({
     url     : 'https://www.asktami.com/demo/PasswordResetPHP/post_login/cloudinary_signature.php',
     type    : "GET",
     dataType: "text",
     data    : { data: params_to_sign},
     complete: function() {console.log("complete")},
     success : function(signature, textStatus, xhr) { callback(signature); },
     error   : function(xhr, status, error) { console.log(xhr, status, error); }
    });
  }
</script>

<script type="text/javascript"> 
/*
NOTES:
- signed
- 'server' will allow cropping only if multiple is false
- additional steps to CROP image, see: https://support.cloudinary.com/hc/en-us/articles/203062071-How-to-crop-images-via-the-Upload-Widget-
- additional steps to show delete link next to each thumbnail after upload, see: https://support.cloudinary.com/hc/en-us/community/posts/200788712-Upload-widget-how-to-get-delete-link-to-appear-next-to-thumbnails-
*/

// multiple without cropping
cloudinary.applyUploadWidget(document.getElementById('multiple'), 
  {api_key: 'YOUR_API_KEY',
  		upload_preset: 'YOUR_PRESET', 
        sources: [ 'local', 'url','instagram'],
    //	cropping: 'server', // remove to allow multiple file uploads
    	theme: 'white',
    	folder: 'demo_files',
    	max_files: 100,
    	button_class: 'btn btn-primary',
    	button_caption: 'Upload Files', // if removed will see default "Upload image"
    	upload_signature: generateSignature,
    	text: {
			 "powered_by_cloudinary": "Powered by Cloudinary - Image management in the cloud",
			 "sources.local.title": "My files",
			 "sources.local.drop_file": "Drop file here",
			 "sources.local.drop_files": "Drop files here",
			 "sources.local.drop_or": "Or",
			 "sources.local.select_file": "Select File",
			 "sources.local.select_files": "Select Files",
			 "sources.url.title": "Web Address",
			 "sources.url.note": "Public URL of an image file:",
			 "sources.url.upload": "Upload",
			 "sources.url.error": "Please type a valid HTTP URL.",
			 "sources.camera.title": "Camera",
			 "sources.camera.note": "Make sure your browser allows camera capture, position yourself and click Capture:",
			 "sources.camera.capture": "Capture",
			 "progress.uploading": "Uploading...",
			 "progress.upload_cropped": "Upload",
			 "progress.processing": "Processing...",
			 "progress.retry_upload": "Try again",
			 "progress.use_succeeded": "OK",
			 "progress.failed_note": "Some of your images failed uploading."
		   }
    	 }, 
  function(error, result) {console.log(error, result)});
  
  
// single with cropping
cloudinary.applyUploadWidget(document.getElementById('single'), 
  {api_key: 'YOUR_API_KEY',
  		upload_preset: 'YOUR_PRESET', 
        sources: [ 'local', 'url','instagram'],
    	cropping: 'server', // remove to allow multiple file uploads
    	theme: 'white',
    	folder: 'demo_files',
    	max_files: 100,
    	button_class: 'btn btn-primary',
    	button_caption: 'Upload File (and Crop)', // if removed will see default "Upload image"
    	upload_signature: generateSignature,
    	text: {
			 "powered_by_cloudinary": "Powered by Cloudinary - Image management in the cloud",
			 "sources.local.title": "My files",
			 "sources.local.drop_file": "Drop file here",
			 "sources.local.drop_files": "Drop files here",
			 "sources.local.drop_or": "Or",
			 "sources.local.select_file": "Select File",
			 "sources.local.select_files": "Select Files",
			 "sources.url.title": "Web Address",
			 "sources.url.note": "Public URL of an image file:",
			 "sources.url.upload": "Upload",
			 "sources.url.error": "Please type a valid HTTP URL.",
			 "sources.camera.title": "Camera",
			 "sources.camera.note": "Make sure your browser allows camera capture, position yourself and click Capture:",
			 "sources.camera.capture": "Capture",
			 "progress.uploading": "Uploading...",
			 "progress.upload_cropped": "Upload",
			 "progress.processing": "Processing...",
			 "progress.retry_upload": "Try again",
			 "progress.use_succeeded": "OK",
			 "progress.failed_note": "Some of your images failed uploading."
		   }
    	 }, 
  function(error, result) {console.log(error, result)});

</script>

</body>
</html>
