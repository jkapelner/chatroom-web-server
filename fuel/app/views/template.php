<!DOCTYPE HTML>
<html>
<head>
    <title><?php echo $title; ?></title>
    <meta http-equiv="content-type" content="text/html; charset=windows-1252" />
    <!-- mobile setting -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2" />
    <?php 
    if (!empty($metatags)) 
    {
        foreach ($metatags as $metatag)
        {
            echo $metatag;
        }
    }
    if (!empty($css)) 
    {
        foreach ($css as $csspath)
        {
            echo $csspath;
        }
    }
    if (!empty($scripts)) 
    {
        foreach ($scripts as $script)
        {
            echo $script;
        }
    }
    ?>
    <script type="text/javascript">        
        function flashErrorMsg(msg, clearPreviousMsg) {
            var flash = $('#flash-error');

            if (!flash.length) {
                $( "#main" ).prepend('<ul id="flash-error" class="alert-message error"></ul>');
                flash = $('#flash-error');
            }

            if (clearPreviousMsg) {
                flash.empty();
            }

            flash.append('<li>' + msg + '</li>');
        }
    </script> 

</head>
<body>
    
<div id="body-wrapper">
    <nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">ChatRoom</a>
			</div>
			<div id="navbar" class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li><a href="<?php echo Uri::create('/blog'); ?>">Blog</a></li>
					<?php if ($current_user) { ?>
					<li><a href="<?php echo Uri::create('member/chat'); ?>">Chat Room</a></li>
					<li><a href="<?php echo Uri::create('member/view'); ?>">Account</a></li>
					<li><a href="<?php echo Uri::create('user/logout'); ?>">Logout</a></li>
					<?php } else { ?>
					<li><a href="<?php echo Uri::create('user/login'); ?>">Login</a></li>
					<li><a href="<?php echo Uri::create('user/register'); ?>">Register</a></li>
					<?php } ?>
				</ul>
			</div><!--/.nav-collapse -->
		</div>
    </nav>
    <div class="container">
		<div id="main">
			<?php if (Session::get_flash('success')): ?>
			<ul class="alert-message success">
				<li><?php echo implode('</li><li>', e((array) Session::get_flash('success'))); ?></li>
			</ul>
			<?php endif; ?>
			<?php if (Session::get_flash('error')): ?>
			<ul id="flash-error" class="alert-message error">
				<li><?php echo implode('</li><li>', e((array) Session::get_flash('error'))); ?></li>
			</ul>
			<?php endif; ?>
			<?php if (!empty($breadcrumbs)) { ?>
				<ul id="breadcrumbs">
					<?php foreach ($breadcrumbs as $crumb) { ?>
					<li><a href="<?php echo Uri::create($crumb['url']); ?>"><?php echo $crumb['name']; ?></a></li>
					<?php } ?>
				</ul>
			<?php } ?>
			<?php if (!empty($content) && !empty($content->title)) { ?>
				<h1 id="content-title"><?php echo $content->title; ?></h1>
			<?php } ?>
			<div id="content">
				<?php 
					echo $content; 
				?>
			</div> 
		</div>
	</div>
	<div id="footer">
		<div id="footer-container">
		</div>
	</div>
</div>
</body>
</html>

