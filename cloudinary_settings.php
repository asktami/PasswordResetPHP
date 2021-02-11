<?php 

// include cloudinary files
require('cloudinary/Cloudinary.php');
require('cloudinary/Uploader.php');
require('cloudinary/Api.php');

\Cloudinary::config(array(
    "cloud_name" => "YOUR_CLOUD_NAME",
    "api_key" => "YOUR_API_KEY",
    "api_secret" => "YOUR_API_SECRET",
    'secure' => true
));

?>
