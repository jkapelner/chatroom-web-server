<?php 
$tooltips = array(
    'username' => 'Must contain at least 6 characters using only letters and numbers (case insensitive)',
    'email' => 'Must be a valid email address',
    'password' => 'Must contain at least 8 characters (no spaces)',
    'password_confirm' => 'Must match the password field',
);
?>
<script type="text/javascript">
    $(function() {
        var rules = {};
        var messages = {};
		
        rules.username = {
            required: true,
            pattern: /[A-Za-z0-9]{6,}/
        };
        rules.email = {
            required: true,
            email: true
        };
           
        rules.password = {
            required: true,
            pattern: /\S{8,}/
        };
        rules.password_confirm = {
            required: true,
            equalTo: "#register_password"
        };
        <?php foreach ($tooltips as $field=>$msg) { ?>
        messages.<?php echo $field; ?> = '<?php echo $msg; ?>';
        <?php } ?>
        $("#register-form").validate({rules: rules, messages: messages});
    });
</script>

<?php echo Form::open(array('action' => Uri::create('user/register', array(), array(), \Config::get('ssl_enable')), 'id' => 'register-form')); ?>
	<div class="required field input-field">
		<div class="input-label"><?php echo Form::label('Username', 'username'); ?>:</div>
		<div class="input"><?php echo Form::input('username', isset($validation) ? $validation->input('username') : '', array(
			'type'  => 'text',
			'id' => 'register_username',
			'class' => 'text required',
			'title' => $tooltips['username'],
		)); ?></div>
	</div>
    <div class="required field input-field">
        <div class="input-label"><?php echo Form::label('Email', 'email'); ?>:</div>
        <div class="input"><?php echo Form::input('email', isset($validation) ? $validation->input('email') : '', array(
            'type'  => 'email',
            'id' => 'register_email',
            'class' => 'text required',
            'title' => $tooltips['email'],
        )); ?></div>
    </div>
    <div class="required field input-field">
        <div class="input-label"><?php echo Form::label('Password', 'password'); ?>:</div>
        <div class="input"><?php echo Form::password('password', '', array(
            'id' => 'register_password',
            'class' => 'text required',
            'title' => $tooltips['password'],
        )); ?></div>
    </div>
    <div class="field input-field">
        <div class="input-label"><?php echo Form::label('Confirm Password', 'password_confirm'); ?>:</div>
        <div class="input"><?php echo Form::password('password_confirm', '', array(
            'id' => 'register_password_confirm',
            'class' => 'text required',
            'title' => $tooltips['password_confirm'],
        )); ?></div>
    </div>
    <div class="field"><div class="input-label"></div><div class="note"><img alt="Orange Asterisk" src="<?php echo Uri::create('assets/img/asterisk_orange.png'); ?>" /> denotes a required field</div></div>
    <div class="field"><div class="input-label"></div><div class="input submit"><?php echo Form::submit('submit', 'Sign Up', array('class' => 'big-button')); ?></div></div>
<?php echo Form::close(); ?>
