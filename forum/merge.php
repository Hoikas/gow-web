<?php

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'common.' . $phpEx);
require($phpbb_root_path . 'includes/functions_user.' . $phpEx);
require($phpbb_root_path . 'includes/functions_admin.' . $phpEx);

set_time_limit(0);

$sql = "UPDATE `phpbb_users` SET `username_clean` = REPLACE(`username_clean`, '&#39;', 'Ê¹');";
$db->sql_query($sql);

?>
