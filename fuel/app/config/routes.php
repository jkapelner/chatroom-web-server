<?php
return array(
	'_root_'  => 'welcome/index',  // The default route
	'_404_'   => 'welcome/404',    // The main 404 route
    'home'    => 'welcome/home',    // home page (same as index above, but always displays the home page for site editors so they can edit content)
	
    'user/confirm/:token' => 'user/confirm',
    'user/unlock/:token' => 'user/unlock',
    
    //disabled routes
    'user/get_login_form(/:any)?' => false,
    'user/get_registration_form(/:any)?' => false,
    'content/get(/:any)?' => false,
    'content/find(/:any)?' => false,
    'unsubscribe/get_form(/:any)?' => false,
);
