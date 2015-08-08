<?php

return array(
    //role based permissions
    'permissions' => array(
        'edit_content' => array('admin', 'developer', 'editor'),
        'impersonate_any_user' => array('admin'),
        'edit_own_account' => 'all',
        'edit_any_account' => array('admin'),
        'edit_campaigns' => array('admin'),
        'upload_suppression_list' => array('admin'),
        'assign_roles' => array('admin'),
        'unlock_any_user' => array('admin'),
        'edit_own_account' => 'all',
        'edit_any_account' => array('admin'),
        'edit_own_blog' => array('standard'),
        'delete_own_blog' => array('standard'),
        'edit_any_blog' => array('admin', 'developer', 'editor'),
        'delete_any_blog' => array('admin', 'developer', 'editor'),
        'publicize_own_blog' => array('standard'),
        'publicize_any_blog' => array('standard'),
    ),
);

?>
