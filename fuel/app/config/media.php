<?php

return array(
    'storage' => 's3', //local or s3
    'max_size' => 10485760 /* 10MB */, //max size in bytes of all files
    'permissions' => 0755,
    'images' => array( //image dimensions in pixels (width,height)
        'max' => array(200,200),
        'profile' => array(200,200),
        'thumbnail' => array(100,100),
        'small-thumbnail' => array(60,60),
        'tiny-thumbnail' => array(40,40),
        'facebook' => array(200,200), //dimensions required for facebook post
    ),
    'image_cache' => array( //image dimensions in pixels (width,height)
        'tiny-thumbnail' => array(60,60),
    ),
    'upload' => array(
        'image' => array(
            'max_size' => 5242880 /* 5MB */, //max size in bytes of a single file
            'randomize' => false,
            'normalize' => false,
            'mime_whitelist' => array('image/jpeg', 'image/gif', 'image/png'),
            'type_whitelist' => array('image'), 
            'auto_process' => false,
            'overwrite' => false,
            'auto_rename' => false,            
        ),
        'file' => array(
            'max_size' => 10485760 /* 10MB */, //max size in bytes of a single file
            'randomize' => false,
            'normalize' => false,
            'mime_whitelist' => array('application/pdf'),
            'type_whitelist' => array('application'),
            'auto_process' => false,
            'overwrite' => false,
            'auto_rename' => false,            
        )
    ),
    'private_download_route' => 'ajax/private/download',
    'default_user_image' => 'assets/img/comm-logo.jpg',
    'tempfile_prefix' => 'xleads_',
    'uri_base' => '',
);

?>
