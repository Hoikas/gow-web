<?php
/**
*
* @package GUILDOFWRITERS.ORG
* @version $Id$
* @copyright (c) 2011-2012 Adam Johnson
* @license (Thou shalt not use)
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB')) {
    exit;
}

function login_alcugs($userid, $password) {
    global $db;

    $sql = 'SELECT user_id FROM cgas_passwords WHERE user_id='. $userid;
    $result = $db->sql_query($sql);
    $cgas = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    $hash = md5($password);
    if (!$cgas) {
        $sql = 'INSERT INTO cgas_passwords (`user_id`, `user_password`) VALUES ('.$userid.', \''.$hash.'\');';
    }
    else {
        $sql = 'UPDATE cgas_passwords SET `user_password`=\''.$hash.'\' WHERE `user_id`='. $userid;
    }
    $db->sql_query($sql);
}

function login_dirtsand($username, $password) {
    require_once '/home/writers/www/site/ds-config.ini.php'; // internal code. i can do this. shut up.
    $link = pg_connect("host=localhost user={$dbuser} password={$dbpass} dbname={$dbname}");

    $qUser = pg_escape_string($username);
    $sql = "SELECT idx FROM auth.\"Accounts\" WHERE LOWER(\"Login\")=LOWER('{$qUser}') LIMIT 1;";
    $result = pg_query($link, $sql);

    $hash = sha1(substr($password, 0, 16));
    if (pg_num_rows($result) > 0) {
        $idx = pg_fetch_result($result, 0, 0);
        $trash = pg_query($link, "UPDATE auth.\"Accounts\" SET \"PassHash\"='{$hash}' WHERE idx={$idx};");
        pg_free_result($trash);
    } else {
        $trash = pg_query($link, "INSERT INTO auth.\"Accounts\" (\"AcctUuid\", \"PassHash\", \"Login\", \"AcctFlags\", \"BillingType\")
                                  VALUES (uuid_generate_v4(), '{$hash}', LOWER('{$qUser}'), '0', '1');");
        pg_free_result($trash);
    }
    pg_free_result($result);
    pg_close($link);
}

$aboutspam = 'http://www.stopforumspam.com/about';
function query_sfspam_db($ip) {
    $data = @file_get_contents('http://www.stopforumspam.com/api?ip='.$ip.'&f=xmlcdata');
    if ($data === false) {
        // Assume that the connection took too long.
        // Allow the user to register anyway (don't be a big dick)
        return false;
    }

    $xml = simplexml_load_string($data);
    if ($xml === false) { return -2; }
    if ($xml->success == 0) { return -1; }
    return ($xml->ip->appears == 1);
}

?>