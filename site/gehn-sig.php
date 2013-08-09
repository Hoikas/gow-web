<?php
/**
 * PNG ALPHA CHANNEL SUPPORT for imagecopymerge();
 * by Sina Salek
 *
 * Bugfix by Ralph Voigt (bug which causes it
 * to work only for $src_x = $src_y = 0.
 * Also, inverting opacity is not necessary.)
 * 08-JAN-2011
 *
 * ADDED BY HOIKAS: fix memory leak
 *
 **/
function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
    // creating a cut resource
    $cut = imagecreatetruecolor($src_w, $src_h);

    // copying relevant section from background to the cut resource
    imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
   
    // copying relevant section from watermark to the cut resource
    imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
   
    // insert cut resource to destination image
    imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
    
    // release resources
    imagedestroy($cut);
}

function draw_shadowed_text($im, $size, $x, $y, $color, $shadow, $font, $text) {
    imagettftext($im, $size, 0, $x+1, $y+1, $shadow, $font, $text);
    imagettftext($im, $size, 0, $x, $y, $color, $font, $text);
}

// Konstants
define('kPenHeight', 69);
define('kPenWidth', 71);
define('kPenDestX', 10);
define('kPenDestY', 20);
define('kShardOffsetX', 100);
define('kShardOffsetY', 63);

define('kFont', '/home/writers/www/site/gehnsigs/courbd.ttf');
define('kFontSize', 11);
define('kTextOffsetX', 345);
define('kNameTextOffsetY', 30);
define('kPlayerIdTextOffsetY', 45);
define('kOnlineCountTextOffsetY', 85);

$choices = array('fehnir', 'hood', 'trebivdil', 'vothol');
$base = -1; // haaaax

if (isset($_GET['style'])) {
    $base = $_GET['style'];
}
if (isset($_GET['ki'])) {
    if (is_numeric($_GET['ki'])) {
        $ki = $_GET['ki'];
    } else if (is_string($_GET['ki'])) {
        // silly mod_rewrite
        $ki = false;
        $base = $_GET['ki'];
    }
}

if (!in_array($base, $choices)) {
    $base = $choices[array_rand($choices)];
}

// Pull in the useful things...
require '/home/writers/www/site/ds-config.ini.php';
require '/home/writers/www/site/ds-functions.php';

// Init DB connection and find players online and avatar name
$pq = pg_connect("host={$dbhost} port={$dbport} dbname={$dbname} user={$dbuser} password={$dbpass}");
$online_count = get_online_player_count($pq);
if ($ki !== false) {
    $name = get_avatar_name($pq, $ki);
    $name = ($name !== false) ? $name : "{INVALID KI#}";
} else {
    $numAvatars = get_avatar_count($pq);
    $lakeScore = get_lake_score($pq);
}
pg_close($pq);

// Now do the GD image stuff
$im = imagecreatefrompng("/home/writers/www/site/gehnsigs/{$base}.png");
$pen = imagecreatefrompng('/home/writers/www/site/gehnsigs/pen.png');
imagecopymerge_alpha($im, $pen, kPenDestX, kPenDestY, 0, 0, kPenWidth, kPenHeight, 100);
imagedestroy($pen);

// Draw some basic text
$white = imagecolorallocate($im, 255, 255, 255);
$shadow = imagecolorallocatealpha($im, 0, 0, 0, 40);
draw_shadowed_text($im, 24, kShardOffsetX, kShardOffsetY, $white, $shadow, kFont, 'Gehn Shard');

// Now do the avatar OR shard stats
if ($ki !== false) {
    $line1 = $name;
    $line2 = "KI #{$ki}";
} else {
    $line1 = "Players: {$numAvatars}";
    $line2 = "Lake: {$lakeScore}";
}
draw_shadowed_text($im, kFontSize, kTextOffsetX, kNameTextOffsetY, $white, $shadow, kFont, $line1);
draw_shadowed_text($im, kFontSize, kTextOffsetX, kPlayerIdTextOffsetY, $white, $shadow, kFont, $line2);

// Now do online player count OR offline
if (ds_running()) {
    $plural = ($online_count != 1) ? "s" : "";
    draw_shadowed_text($im, kFontSize, kTextOffsetX, kOnlineCountTextOffsetY, $white, $shadow, kFont, "{$online_count} Player{$plural} Online");
} else {
    $red = imagecolorallocate($im, 255, 0, 0);
    draw_shadowed_text($im, kFontSize + 1, kTextOffsetX, kOnlineCountTextOffsetY, $red, $shadow, kFont, "OFFLINE");
}

// Spit it out!
header('Content-Type: image/png');
imagepng($im);
imagedestroy($im);
?>