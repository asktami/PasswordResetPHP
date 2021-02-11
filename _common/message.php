<?php 
$message = isset($message) ? $message : '' ;
      		
      		if (is_array($message)) {
      			foreach ($message as $m){
				echo '<mark>' . $m . '</mark>';
				} 
			}
				elseif (!empty($message)) {
      			echo '<mark>' . $message . '</mark>';
      			}
?>