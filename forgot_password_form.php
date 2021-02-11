      <div class="mt-1">
        <h1>Reset Your Password</h1>
      </div>
      <p class="lead">Please enter the email address you used to register. We will send you an email to reset your password.</p>
      <?php 
      		include(FEEDBACK);
       ?>
        
        <form class="form" action="<?php echo PAGE  . 'forgot_password'; ?>" method="POST">

        <fieldset>
        <div class="form-group">
        <label for="email" class="control-label">Email address</label>
        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlentities($email) ?>" placeholder="Email address" required pattern="^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$" title="name@host.com" autofocus>
        </div>
        
        
        <div class="form-group">
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="ForgotPasswordForm" value="Request Reset">Request Reset</button>
        </div>
        
        </fieldset>
      </form>