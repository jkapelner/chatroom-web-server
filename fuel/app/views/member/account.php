<?php
$tooltips = array(
    'username' => 'Must contain at least 6 characters using only letters and numbers (case insensitive)',
    'email' => 'Must be a valid email address',
    'password' => 'Must contain at least 8 characters (no spaces)',
    'password_confirm' => 'Must match the password field',
);

$create_username_flag = (strpos($user->username, '_oauth') === 0) ? true : false; //if username was generated by omniauth, the user should create their own
$edit_account_title = $create_username_flag ? 'Set Your Username and Password' : 'Change Your Password';
$edit_account_description = $create_username_flag ? 'Create a username and password to login directly into this site' : 'Change your login password';

?>
<?php if ($editable) { ?>
<script type="text/javascript">
    $(function() {
        $("#account-form").dialog({
            autoOpen: false,
            width: 600,
            height: 400,
            modal: true,
            resizable: false,
            show: {
                effect: "fade",
                duration: 1000
            }
        });
        $("#edit-account-button").click(function() {
            $( "#account-form" ).dialog( "open" );
        });
    });
</script>

<?php echo Form::open(array('action' => Uri::create('member/account/' . $user->id, array(), array(), \Config::get('ssl_enable')), 'id' => 'account-form', 'title' => $edit_account_title)); ?>
    <?php if ($create_username_flag) { ?>
        <div class="required field input-field">
            <div class="input-label"><?php echo Form::label('Username', 'username'); ?>:</div>
            <div class="input"><?php echo Form::input('username', isset($validation) ? $validation->input('username') : '', array(
                'type'  => 'text',
                'id' => 'account_username',
                'class' => 'text required',
                'title' => $tooltips['username'],
            )); ?></div>
        </div>
    <?php } else {
        echo Form::hidden('username', $user->username);
    } ?>
    <div class="required field input-field">
        <div class="input-label"><?php echo Form::label('Password', 'password'); ?>:</div>
        <div class="input"><?php echo Form::password('password', '', array(
            'id' => 'account_password',
            'class' => 'text required',
            'title' => $tooltips['password'],
        )); ?></div>
    </div>
    <div class="field input-field">
        <div class="input-label"><?php echo Form::label('Confirm Password', 'password_confirm'); ?>:</div>
        <div class="input"><?php echo Form::password('password_confirm', '', array(
            'id' => 'account_password_confirm',
            'class' => 'text required',
            'title' => $tooltips['password_confirm'],
        )); ?></div>
    </div>
    <div class="field"><div class="input-label"></div><div class="input submit"><?php echo Form::submit('submit', 'Save', array('class' => 'big-button')); ?></div></div>
<?php echo Form::close(); ?>

<?php } ?>
<?php if ($editable) { echo Form::button('button', $edit_account_title, array('class' => 'button', 'id' => 'edit-account-button', 'title' => $edit_account_description)); } ?>    
<div id="profile-view">
    <div class="profile-column" id="profile-data">
        <div>
            <div class="profile-data-line">
                <span class="profile-question">Username:</span>
                <span class="profile-answer"><?php echo $user->username; ?></span>
            </div>
            <div class="profile-data-line">
                <span class="profile-question">Member since</span>
                <span class="profile-answer"><?php 
                    $dt = new DateTime($user->created_at);
                    echo $dt->format('F j, Y');
                ?></span>
            </div>
			<div class="profile-data-line"><a href="<?php echo Uri::create('member/blogs/' . $user->id); ?>">My Blogs</a></div>
        </div>
        <?php if (!empty($can_unlock)) {
            echo "<br/><br/>\n";
            echo Form::open(array('action' => Uri::create('member/unlock')));
            echo Form::hidden('user_id', $user->id);
            echo Form::submit('submit', 'Unlock User', array('class' => 'button'));
            echo Form::close();
        } ?>
        <?php if (!empty($roles) && is_array($roles)) {
            echo "<br/><br/>\n<h4>Select roles:</h4>\n";
            echo Form::open(array('action' => Uri::create('member/roles/' . $user->id)));
            echo FormBuilder::scrollbox('roles', $roles, array_keys($user->roles));
            echo "<br/>\n";
            echo Form::submit('submit', 'Save', array('class' => 'button'));
            echo Form::close();
        } ?>
    </div>
</div>
