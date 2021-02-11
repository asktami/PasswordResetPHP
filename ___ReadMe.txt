Please change all g___ variables in index.php

define( 'gEMAIL', 'tami@asktami.com' );
define( 'gPHONE', '770-457-3221' );
define( 'gSITENAME', 'Creative Computing: PasswordResetPHP' );
define( 'gSITEADDRESS', 'https://www.asktami.com/' );


Also change all settings in _common/functions.php and cloudinary_settings.php


the __databases folder has the SQL to create the MySQL database and the Demo.fmp12 FileMaker database (username = Admin, password = admin)
-----------------------------------------------------------------

User registration and secure password reset code.
With registration, login, and password reset pages.

Also includes code for uploading files to your web server and to a <a href="https://cloudinary.com/">Cloudinary</a> account.

Built using PHP, MySQL, FileMaker API for PHP, FileMaker, jQuery and Bootstrap 4.
You can use either MySQL or FileMaker as you database. Sample MySQL and FileMaker databases included.

The FileMaker database includes scripts to dynamically create FileMaker accounts and put uploaded files into a container field.

 
<h2>NOTE:</h2> 
This code stores passwords in the database in plain text. <strong>Never store passwords in plain text!</strong> It is only done here for demonstration purposes. If you use this code in a real site remove all references to the <em>password_plaintext</em> field from <em>register.php</em> and <em>reset_password.php</em> and remove the <em>password_plaintext</em> field from the <em>user</em> table.

Inspiration:
<ul>
<li><a href="https://www.troyhunt.com/everything-you-ever-wanted-to-know/" target="_blank">Everything you ever wanted to know about building a secure password reset feature</a></li>

<li><a href="https://gist.github.com/N-Porsh/7766039">PHP: simple multiple file upload</a></li>

<li><a href="https://cloudinary.com/blog/file_upload_with_php#handling_file_upload_with_cloudinary
">Handling File Upload With Cloudinary</a></li>
</ul>