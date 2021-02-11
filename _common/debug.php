<?php

if( !empty($_SESSION['is_logged_in']) ){
echo '<hr><hr><h4>DEBUG</h4><hr>' ;

echo '<pre>';
print_r($_SESSION);
echo '</pre>';
}

?>