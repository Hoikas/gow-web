<?php
/* SIMPLE RSS FEED for phpBB3
 * Written by Adam Johnson
 * For the Guild of Writers
 * http://www.guildofwriters.com
 */

/* VERSION 0.1.0 */

//Configurable stuffs...
$sLink = "http://www.guildofwriters.com"; //The feed's link
$fLink = "http://forum.guildofwriters.com"; //Forum's Link
$limit = 10; //Max items in feed
$phpbb = "../forum"; //phpBB3 root
$forum = 2; //Which forum do we need to feed?
$throw = true; //Do we throw errors? DISABLE THIS IN PRODUCTION ENVIRONMENT!!!!!

//Hacky
$str = <<<XML
<?xml version='1.0'?>
<rss version="2.0">
</rss>
XML;

//Begin real code...
$start = microtime();
$numQueries = 0;
header("Content-type: text/xml");

//Define some stuff
function dbError() {
    global $throw;
    
    if ($throw) return mysql_error();
    else return "Internal Error";
}

function dbQuery($sql) {
    global $numQueries;
    global $table_prefix;
    
    $numQueries += 1;
    $res = mysql_query(str_replace("phpbb_", $table_prefix, $sql)) or die(dbError());
    return $res;
}

function dbResult($res, $id) {
    $eax = mysql_result($res, $id) or die(dbError());
    return $eax;
}

//Okay, now let's get started
require $phpbb."/config.php";
$link = mysql_connect($dbhost, $dbuser, $dbpasswd) or die(dbError());
mysql_select_db($dbname) or die(dbError());

//Setup the RSS Feed
$xml = new SimpleXMLElement($str);
$rss = $xml->addChild("channel"); //Hack?
$rss->addChild("link", $sLink);

//Grab info dynamically from the db to fill into the feed...
$res = dbQuery("SELECT config_value FROM phpbb_config WHERE config_name = 'sitename'");
$rss->addChild("title", dbResult($res, 0));
mysql_free_result($res);

$res = dbQuery("SELECT config_value FROM phpbb_config WHERE config_name = 'site_desc'");
$rss->addChild("description", dbResult($res, 0));
mysql_free_result($res);

//Hokay! Let's talk to the topics table :D
$res = dbQuery("SELECT forum_id,topic_id,topic_title,topic_first_post_id,topic_time FROM phpbb_topics WHERE forum_id = 0 OR forum_id = {$forum} ORDER BY topic_id DESC LIMIT {$limit}");
while($assoc = mysql_fetch_assoc($res)) {
    //Grab post text...
    $r2 = dbQuery("SELECT post_text FROM phpbb_posts WHERE post_id = '{$assoc["topic_first_post_id"]}'");
    $text = dbResult($r2, 0);
    
    //The haxxor
    $text = preg_replace('/\\[[^\\]]*\\]/', '', $text);
    if (strlen($text) > 255) {
        $text = substr($text, 0, 255)."...";
    }
    
    mysql_free_result($r2);
    
    $item = $rss->addChild("item");
    $item->addChild("title", $assoc["topic_title"]);
    $item->addChild("link", "{$fLink}/viewtopic.php?f={$assoc["forum_id"]}&amp;t={$assoc["topic_id"]}");
    $item->addChild("pubDate", date("D, j F Y", $assoc["topic_time"]));
    $item->addChild("description", $text);
}

mysql_free_result($res);
mysql_close($link);

//Flush
echo $xml->asXML();
$time = round((microtime() - $start), 6);
echo("<!--Completed in {$time} seconds with {$numQueries} DBQueries-->");
?>