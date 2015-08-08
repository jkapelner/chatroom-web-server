<?php echo Form::open(array('action' => Uri::create('user/login', array(), array(), \Config::get('ssl_enable')), 'method' => 'get', 'id' => 'confirm-form')); ?>
    <div class="content-text"><b>Welcome <?php echo $user->username; ?>, your email account needs to be confirmed to activate your account.
        <br/>An email was sent to <?php echo $user->email; ?> containing your activation link.
        <br/>If you can't find the email or the token is invalid or expired, click the following button to login again and generate a new confirmation token:</b><br/><br/></div>
    <div><?php echo Form::submit('submit', 'Login and get new token', array('class' => 'button')); ?></div>
<?php echo Form::close(); ?>
