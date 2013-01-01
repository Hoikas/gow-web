<?php
define('IN_PHPBB', true);
$phpbb_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.'.$phpEx);
include($phpbb_root_path . 'includes/acp/auth.' . $phpEx);
// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();
$auth_admin = new auth_admin();

// Add permissions
$auth_admin->acl_add_option(array(
    'global'   => array('u_view_event', 'u_new_event', 'u_edit_event', 'u_delete_event', 
						'm_edit_event', 'm_delete_event', 'a_edit_event', 'a_delete_event')
));
?>