      <div class="mt-1">
        <h1>Reset Your Password</h1>
      </div>
      <p class="lead">Please enter your new password. We will send you email confirmation of the change.</p>
        <?php 
      			include(FEEDBACK);
      	?>
    
    <form class="form" action="<?php echo PAGE  . 'reset_password'; ?>" method="POST" autocomplete="off">
        
		<div class="form-group">
        <label for="password" class="control-label">New Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        
		<div class="form-group">
        <label for="confirm_password" class="control-label">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
        </div>
        
		<div class="form-group">
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="ResetPasswordForm" value="Reset Password">Reset Password</button>
        </div>
        
      </form>