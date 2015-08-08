<div class="input-label"></div><div class="input"><img class="framed-image" src="<?php echo $captcha_route; ?>" alt="Simple Captcha" height="<?php echo $captcha_height; ?>" width="<?php echo $captcha_width; ?>" /><br/>
    <?php echo Form::input($captcha_post_name, '', array(
    'id' => $captcha_post_name,
    'type' => 'text',
    'class' => 'text required captcha-input',
    'title' => 'Enter the exact same text that you see in the above image'
)); ?></div>
