# PasswordResetPHP (and Multiple File Upload)

PHP MySQL FileMaker User Registration System with Secure Password Reset and Multiple File Upload

User registration and secure password reset code.
With registration, login, and password reset pages.

Also includes code for uploading files to your web server and to a [Cloudinary](https://cloudinary.com/) account.

Built using PHP, MySQL, FileMaker API for PHP, FileMaker, jQuery and Bootstrap 4.
You can use either MySQL or FileMaker as your database. Sample MySQL and FileMaker databases included.

The FileMaker database includes scripts to dynamically create FileMaker accounts and put uploaded files into a container field.

**NOTE: The FileMaker username is Admin, password is admin.**

**NOTE:** This code stores passwords in the database in plain text. <strong>Never store passwords in plain text!</strong> It is only done here for demonstration purposes. If you use this code in a real site remove all references to the **password_plaintext** field from **register.php** and **reset_password.php** and remove the **password_plaintext** field from the **user** table.

**NOTE:** If you want to use [Cloudinary](https://cloudinary.com/), create your own account and make changes in **cloudinary_settings.php**, **post_login/cloudinary_signature.php**, and **post_login/upload_cloudinary_widget.php**.

Please change all global variables in **index.php**:

define( 'gEMAIL', 'XXX' );
define( 'gPHONE', 'XXX' );
define( 'gSITENAME', 'XXX' );
define( 'gSITEADDRESS', 'XXX' );

Also change all settings in **\_common/functions.php** and **cloudinary_settings.php**.

**NOTE:** The FileMaker PHP API is not compatible with PHP 7. And the FMS installer since 17.0.3 installs PHP 7! Fortunately, there’s a mod of it that’s made it compatible: [https://github.com/matatirosolutions/filemakerapi](https://github.com/matatirosolutions/filemakerapi). Swap out the original FileMaker.php file and FileMaker folder with these, and you should be good as long as your PHP is PHP 7 compatible.

**Last updated 2/10/2021.<br>Tested with the Cloudinary PHP SDK (version 1), FileMaker 19, PHP 7.3.27 and PHPMailer 6.2.0**

## Demo

[Register, Login, Try Everything Out](http://www.asktami.com/demo/PasswordResetPHP/index.php?page=register&register)

## Inspiration

- [Everything you ever wanted to know about building a secure password reset feature](https://www.troyhunt.com/everything-you-ever-wanted-to-know/)
- [PHP: simple multiple file upload](https://gist.github.com/N-Porsh/7766039)
- [Handling File Upload With Cloudinary](https://cloudinary.com/blog/file_upload_with_php#handling_file_upload_with_cloudinary)

## Screenshots

![multiple field upload form](https://github.com/asktami/PasswordResetPHP/blob/master/img/Screenshots/multiple_file_upload.png)

![registration pageflow](https://github.com/asktami/PasswordResetPHP/blob/master/__PAGEFLOWS/Registration_Pageflow.png)
![reset password pageflow](https://github.com/asktami/PasswordResetPHP/blob/master/__PAGEFLOWS/Reset_Password_Pageflow.png)

![registration form](https://github.com/asktami/PasswordResetPHP/blob/master/img/Screenshots/1_registration_form.png)
![login form](https://github.com/asktami/PasswordResetPHP/blob/master/img/Screenshots/2_login_form.png)
![forgot password form](https://github.com/asktami/PasswordResetPHP/blob/master/img/Screenshots/3_forgot_password_form.png)
![post login dashboard](https://github.com/asktami/PasswordResetPHP/blob/master/img/Screenshots/4_post_login_dashboard.png)
![password reset email](https://github.com/asktami/PasswordResetPHP/blob/master/img/Screenshots/5_password_reset_email.png)
![password reset email sent](https://github.com/asktami/PasswordResetPHP/blob/master/img/Screenshots/6_password_reset_email_sent.png)
![password reset email error](https://github.com/asktami/PasswordResetPHP/blob/master/img/Screenshots/7_password_reset_error_email.png)
![reset password form](https://github.com/asktami/PasswordResetPHP/blob/master/img/Screenshots/8_reset_password_form.png)

# PasswordResetPHP-
