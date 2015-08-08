<?php
    $tooltips = array(
        'password' => 'Must contain at least 8 characters (no spaces)',
        'password_confirm' => 'Must match the password field',
    );
?>
<h1>Enter Your New Password</h1>
<?php echo Form::open(array('action' => Uri::create('user/reset_password', array(), array(), \Config::get('ssl_enable')), 'id' => 'reset-password-form'), array('token' => $token)); ?>
	<div class="input"><?php echo Form::password('password', '', array(
		'id' => 'reset_password',
		'class' => 'text required',
		'title' => $tooltips['password'],
		'placeholder' => 'Password',
	)); ?>
    </div>
    <div class="input"><?php echo Form::password('password_confirm', '', array(
		'id' => 'reset_password_confirm',
		'class' => 'text required',
		'title' => $tooltips['password_confirm'],
		'placeholder' => 'Password again',
	)); ?>
    </div>
    <div class="input submit"><?php echo Form::submit('submit', 'Submit', array('class' => 'big-button')); ?></div>
<?php echo Form::close(); ?>
