<?php
# Setup phpBB
$phpbb_root_path = '/home/writers/www/forum/';
$phpEx = 'php';
define('PHPBB_INSTALLED', true);
define('IN_PHPBB', true);
define('IN_WORDPRESS', true);
include_once $phpbb_root_path .'common.php';

function phpbb_to_wp($user, $pass) {
    global $auth;
    global $wpdb;
    
    $login = $auth->login($user, $pass);
    if ($login['status'] == LOGIN_SUCCESS) {
        
        # Hack to set the permission work
        $role = 'author';
        if ($login['user_row']['user_type'] == 3) {
            $role = 'administrator';
        }
        
        $data = array(
            'user_login'        =>      $user,
            'user_pass'         =>      $pass,
            'user_email'        =>      $login['user_row']['user_email'],
        );
        
        # If the user already exists, grab the ID. Also,
        # don't change the role...
        $wpdb->select(DB_NAME);
        if (username_exists($user)) {
            $data['ID'] = get_profile('ID', $user);
            wp_update_user($data);
        } else {
            $data['role'] = $role;
            wp_insert_user($data);
        }
    }
}
?>