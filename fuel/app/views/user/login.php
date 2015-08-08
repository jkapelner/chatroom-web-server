<?php
    $disabled_flag = ($current_user && !\Access::can('impersonate_any_user', $current_user)) ? true : false;
?>
<div id="login">
	<?php echo Form::open(array('action' => $disabled_flag ? false : Uri::create('user/login', array(), array(), \Config::get('ssl_enable')), 'id' => 'login-form')); ?>
		<h1 class="center">Sign In</h1>
		<?php echo Form::hidden('destination', $destination); ?>
		<?php echo Form::hidden('omniauth', '', array('id' => 'omniauth')); ?>
		<div class="input"><?php echo Form::input('username_or_email', $validation->input('username_or_email'), array(
				'type'  => 'text',
				'id'    => 'login_username_or_email',
				'class' => 'text',
				'placeholder' => 'Username or Email',
				'disabled' => $disabled_flag
			)); ?>
		</div>
		<div class="input"><?php echo Form::password('password', $validation->input('password'), array(
				'id'    => 'login_password',
				'class' => 'text',
				'placeholder' => 'Password',
				'disabled' => $disabled_flag
			)); ?>
		</div>
		<div class="input links">
			<span id="remember-me"><?php echo Form::checkbox('remember_me', false, array('id' => 'remember-me-checkbox')); ?><label for="remember-me-checkbox"> Remember Me</label></span>
			<span id="forgot-password"><a href="<?php echo Uri::create('user/forgot_password', array(), array(), \Config::get('ssl_enable')); ?>">Forgot<br/>Password</a></span>
		</div>
		<div class="input submit">
			<?php echo Form::submit('submit', 'Log In', array('id' => 'login-submit-button', 'class' => 'big-button', 'disabled' => $disabled_flag)); ?>
		</div>
	<?php echo Form::close(); ?>
</div>

