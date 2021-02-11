<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="img/favicon.ico">

    <title><?php echo $PAGE_TITLE ?></title>

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="assets/css/demo.css" rel="stylesheet">
    
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
      <a class="navbar-brand" href="<?php echo PAGE  . 'home' ?>">Demo</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="<?php echo PAGE  . 'home' ?>">Home</a>
          </li>
          
           <?php 
                if(!empty($_SESSION['is_logged_in'])) {
        	?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo PAGE  . 'post_login/dashboard' ?>">Dashboard</a>
          </li>
          
          <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Upload to Server
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="<?php echo PAGE  . 'post_login/upload' ?>">PHP to Create Record and FMScript to Insert into Container</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="<?php echo PAGE  . 'post_login/upload_many' ?>">FMScript to Create Multiple Records and Insert into Container</a>
        </div>
      </li>
 
	 <li class="nav-item">
	   <a class="nav-link" href="<?php echo PAGE  . 'post_login/upload_cloudinary' ?>">Upload to Cloudinary</a>
	 </li>
	 
	<!--
	// STOPPED WORKING 2/10/2021 WITH PHP 7.3 
	 <li class="nav-item">
	   <a class="nav-link" href="post_login/upload_cloudinary_widget.php">Cloudinary Upload Widget </a>
	 </li>
	-->
          
          <?php 
          		}
          ?>
          </ul>
        
        <ul class="navbar-nav">
                <li class="nav-item">
                <?php 
                if(empty($_SESSION['is_logged_in'])) {
                	echo '<a class="nav-link" href="'. PAGE  . 'login">Login</a>';
                } else {
                	echo '<a class="nav-link" href="'.  PAGE  . 'home&logout">Logout</a>';
                } 
                ?>
                </li>
                <?php if(empty($_SESSION['is_logged_in']) ){?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo PAGE  . 'register&register'  ?>">Register</a>
                </li>
                <?php } ?>
            </ul>
      </div>
    </nav>

<!-- Begin page content -->
<main class="container">

<?php

//	INCLUDE PAGE
	include($display);


// INCLUDE DEBUG 
include(DEBUG);

?>
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


 <!-- Bootstrap Core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="assets/js/jquery-3.3.1.slim.min.js"><\/script>')</script>   
  <!-- bootstrap.bundle.min.js includes Popper -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
 <!-- holder.js to render image placeholders (renamed holder.min.js)  -->
    <script src="assets/js/holder.js"></script>
    
    
<script>
$(function() {

// for material design style floating labels
	$('.form-control').on('focus blur', function (e) {
			$(this).parents('.form-group').toggleClass('focused', (e.type === 'focus' || this.value.length > 0));
		}).trigger('blur');
	
//for active nav link
	var url = window.location.href;
	// Will work if string in href matches with location AND for relative and absolute hrefs

	$('nav ul li a').filter(function() {
		return this.href == url;
		$('nav ul li.active').removeClass("active");		// remove default
	}).removeClass("active").addClass('active');



// for multiple file upload
 var counter = 2;

    $("#addrow").on("click", function () {
        var newRow = $("<tr>");
        var cols = "";

		cols += '<td scope="row"><input type="hidden" name="id[]" value="' + counter + '">'+ counter + '</td>';
        cols += '<td><input type="text" name="description[]" required="required" class="form-control"></td>';
        cols += '<td><input type="file" name="fileToUpload[]" required="required" class="form-control" onChange="ValidateFile(this);"></td>';
       
        cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
        newRow.append(cols);
        $("table.order-list").append(newRow);
        counter++;
    });


    $("table.order-list").on("click", ".ibtnDel", function (event) {
        $(this).closest("tr").remove();       
        counter -= 1
    });

});

// outside document ready

// file uploading message
if(document.getElementById("uploadForm").length > 0){
	document.getElementById("uploadForm").onsubmit = function() {
		document.getElementById("loadingMessage").style.display = 'block';
		return true;
	};
}


// handle each selected file to upload:
// check file extension
function ValidateFile(thisFile){
	// alert(thisFile.value);
    var fileInput = thisFile.value;
    var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.pdf)$/i;
    if(!allowedExtensions.exec(fileInput)){
        alert('Please upload file having extensions .pdf/.jpeg/.jpg/.png/.gif only.');
        thisFile.value = '';
        return false;
    }
}


	</script>
  </body>
</html>
