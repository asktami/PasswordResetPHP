<?php

// log out of sesssion when go to registration page via link or click logout link
if(isset($_GET["logout"]) || isset($_GET["register"]) ){

// Hande logout
	session_start();
    session_unset();     // unset $_SESSION variable = remove all session variables
    session_destroy();   // destroy session data in storage


} else {


// Start the session
session_start();

// Handle session timeout
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // last request was more than 30 minutes ago (30 min = 1800)
    session_unset();     // unset $_SESSION variable = remove all session variables
    session_destroy();   // destroy session data in storage
    
    // redirect user b/c session timed out
	header('Location: ' .  PAGE  . 'logout&logout');
	exit();
}

$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

}

?>
