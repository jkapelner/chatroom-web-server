<div id="container">
<h1>Reset Password</h1>
<strong>Enter your username or email address for your account to receive an email for resetting your password</strong>
<?php echo Form::open(array('action' => Uri::create('user/forgot_password', array(), array(), \Config::get('ssl_enable')), 'id' => 'forgot-password-form')); ?>
	<div class="input"><?php echo Form::input('username_or_email', $validation->input('username_or_email'), array(
		'type'  => 'text',
		'id'    => 'login_username_or_email',
		'class' => 'text required',
		'placeholder' => 'Username or Email',
	)); ?></div>
    <div class="input"><?php echo $captcha->html(); ?></div>
    <div class="input submit"><?php echo Form::submit('submit', 'Send Password Reset Email', array('class' => 'button')); ?></div>
<?php echo Form::close(); ?>
</div>
