<?php
// code to protect post-login pages

    // check to see whether the user is logged in or not
    if(empty($_SESSION['is_logged_in']))
    {
        // if they are not, redirect them to the login page
		header('Location: ' . PAGE . 'login');
		exit;
    }
?> 


<div class="mt-1">
  <h1>Dashboard</h1>
</div>
<p class="lead">Welcome to the protected post-login dashboard.
<br>Your Name is: <?php echo $_SESSION['first'] . ' ' . $_SESSION['last'] ?>
<br>Your userID is: <?php echo $_SESSION['userID'] ?>
<br>Your email is: <?php echo $_SESSION['email'] ?>
</p>

