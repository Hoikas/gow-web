<?php
/*
Plugin Name: Latest twitter sidebar widget
Plugin URI: http://www.tacticaltechnique.com/wordpress/latest-twitter-sidebar-widget/
Description: Creates a sidebar widget that displays the latest twitter updates for any user with public tweets.
Author: Corey Salzano
Version: 0.110330
Author URI: http://www.tacticaltechnique.com/
*/
$verion = "0.110330";

// no edits required, but if you do, share your mods with me!

// this plugin requires that PHP curl and PHP json be installed on your server


//load the saved or default options
if( !function_exists("get_admin_options_ltw")){
	function get_admin_options_ltw() {
		$optionName = "widget_latest_twitter";
		$default_options = array(	'user' => 'salzano',
									'count' => '3',
									'prefix' => '',
									'suffix' => '<br>&nbsp;',
									'beforeUpdate' => '<i>&quot;',
									'afterUpdate' => '&quot;</i>',
									'showProfilePicTF'=> false,
									'showTwitterIconTF' => true,
									'showTweetTimeTF' => false
									);
		$savedOptions = get_option($optionName);
		if(!empty($savedOptions)){
			foreach ($savedOptions as $key => $option) $default_options[$key] = $option;
		}
		update_option($optionName, $default_options);
		return $default_options;
	}
}

function latest_twitter_sidebar_widget() {
	$saved_options = get_admin_options_ltw( );
	$username = $saved_options['user'];
	$updateCount = $saved_options['count'];
	$prefix = stripslashes($saved_options['prefix']);
	$suffix = stripslashes($saved_options['suffix']);
	$beforeUpdate = stripslashes($saved_options['beforeUpdate']);
	$afterUpdate = stripslashes($saved_options['afterUpdate']);
	$showProfilePicTF = $saved_options['showProfilePicTF'];
	$showTwitterIconTF = $saved_options['showTwitterIconTF'];
	$showTweetTimeTF = $saved_options['showTweetTimeTF'];

	if ( !function_exists('fix_twitter_update') ){
		function fix_twitter_update($origTweet,$entities) {
			if( $entities == null ){ return $origTweet; }
			foreach( $entities->urls as $url ){
				$index[$url->indices[0]] = "<a href=\"".$url->url."\">".$url->url."</a>";
				$endEntity[(int)$url->indices[0]] = (int)$url->indices[1];
			}
			foreach( $entities->hashtags as $hashtag ){
				$index[$hashtag->indices[0]] = "<a href=\"http://twitter.com/#!/search?q=%23".$hashtag->text."\">#".$hashtag->text."</a>";
				$endEntity[$hashtag->indices[0]] = $hashtag->indices[1];
			}
			foreach( $entities->user_mentions as $user_mention ){
				$index[$user_mention->indices[0]] = "<a href=\"http://twitter.com/".$user_mention->screen_name."\">@".$user_mention->screen_name."</a>";
				$endEntity[$user_mention->indices[0]] = $user_mention->indices[1];
			}
			$fixedTweet="";
			for($i=0;$i<strlen($origTweet);$i++){
				if(strlen($index[(int)$i])>0){
					$fixedTweet .= $index[(int)$i];
					$i = $endEntity[(int)$i]-1;
				} else{
					$fixedTweet .= substr($origTweet,$i,1);
				}
			}
			return $fixedTweet;
		}
	}

	if( !function_exists('twitter_time_ltw')){
		function twitter_time_ltw($a) {
			//get current timestamp
			$b = strtotime("now");
			//get timestamp when tweet created
			$c = strtotime($a);
			//get difference
			$d = $b - $c;
			//calculate different time values
			$minute = 60;
			$hour = $minute * 60;
			$day = $hour * 24;
			$week = $day * 7;

			if(is_numeric($d) && $d > 0) {
				//if less then 3 seconds
				if($d < 3) return "right now";
				//if less then minute
				if($d < $minute) return floor($d) . " seconds ago";
				//if less then 2 minutes
				if($d < $minute * 2) return "about 1 minute ago";
				//if less then hour
				if($d < $hour) return floor($d / $minute) . " minutes ago";
				//if less then 2 hours
				if($d < $hour * 2) return "about 1 hour ago";
				//if less then day
				if($d < $day) return floor($d / $hour) . " hours ago";
				//if more then day, but less then 2 days
				if($d > $day && $d < $day * 2) return "yesterday";
				//if less then year
				if($d < $day * 365) return floor($d / $day) . " days ago";
				//else return more than a year
				return "over a year ago";
			}
		}
	}

	if ( !function_exists('curl_to_file') ){
		function curl_to_file( $url, $fileName ){
			if ( function_exists('curl_init')) {
				$userAgent = "Latest twitter widget WP plugin " . $version;
				$curl = curl_init( $url );
				$filePath = dirname(__FILE__) ."/". $fileName;
				$fp = fopen( $filePath, "w");
				curl_setopt ($curl, CURLOPT_URL, $url );
				curl_setopt($curl, CURLOPT_FILE, $fp);
				curl_setopt($curl, CURLOPT_REFERER, get_bloginfo('home'));
				curl_setopt($curl, CURLOPT_USERAGENT, $userAgent );
				if (!$result = curl_exec($curl)) {
					curl_close ($curl);
					return false;
				} else{
					curl_close ($curl);
					return true;
				}
			} else{
				return false;
			}
		}
	}

	$jsonFileName = $username . ".json";
	$jsonURL = "http://api.twitter.com/1/statuses/user_timeline.json?screen_name=" . $username . "&include_entities=true";
	$jsonData = file_get_contents($jsonURL);

	$haveTwitterData = true;
	// $jsonData now has the feed content
	if( !$jsonData ){
		// no tweets
		$haveTwitterData = false;
	}

	// check for errors--rate limit or curl not installed
	// data returned will be: {"error":"Rate limit exceeded. Clients may not make more than 150 requests per hour.","request":"\/1\/statuses\/user_timeline.json?screen_name=salzano&include_entities=true"}
	$tweets = json_decode( $jsonData );
	if( strlen( $tweets->error )){
		//don't have tweets because of an error
		$haveTwitterData = false;
		//TODO: delete the json file so it will surely be downloaded on next page view
	}

	// $jsonData now has the feed content, $tweets has been json_decoded

	if( $showProfilePicTF ){
		//make sure we have the profile picture saved locally
		$twitterUserData = $tweets[0]->user;
		$profilePicURL = $twitterUserData->profile_image_url;
		$profilePicPieces = explode( ".", $profilePicURL );
		$profilePicExt = end( $profilePicPieces );
		$profilePicFileName = $username . "." . $profilePicExt;
		if( file_missing_or_old( $profilePicFileName, .5 )){
			curl_to_file( $profilePicURL, $profilePicFileName );
		}
	}

	// output the widget
?>
<li id="twitter_div">
<h2>@guildofwriters</h2>
<?php
	$linkHTML = "<a href=\"http://twitter.com/".$username."\">";
	$pluginURL = get_bloginfo('home')."/wp-content/plugins/latest-twitter-sidebar-widget/";
	$icon = $pluginURL . "/twitter.png";
	$pic = $pluginURL . "/" . $profilePicFileName;
	if( $showTwitterIconTF ){
		echo $linkHTML . "<img id=\"latest-twitter-widget-icon\" src=\"".$icon."\" alt=\"t\"></a>";
	} else{
		if( $showProfilePicTF ){
			echo $linkHTML . "<img id=\"latest-twitter-widget-pic\" src=\"".$pic."\" alt=\"\"></a>";
		}
	}

	echo stripslashes( $prefix );
	if( $haveTwitterData ){
		$i=1;
		
		foreach( $tweets as $tweet ){
			if( $i > $updateCount ){ break; }

			echo $beforeUpdate;
			echo fix_twitter_update( $tweet->text, $tweet->entities );
			echo $afterUpdate;
			if( $showTweetTimeTF ){
				echo "<div class=\"latest-twitter-tweet-time\" id=\"latest-twitter-tweet-time-" . $i . "\">" . twitter_time_ltw( $tweet->created_at ) . "</div>";
			}
			$i++;
		}
	}

	echo "<div id=\"latest-twitter-follow-link\"><a href=\"http://twitter.com/$username\">follow @$username on twitter</a></div></li>";
	echo stripslashes( $suffix ) . "</li>";
}

function init_latest_twitter(){
	register_sidebar_widget("Latest twitter", "latest_twitter_sidebar_widget");
	register_widget_control("Latest twitter", "latest_twitter_control");
}

function latest_twitter_control() {

	if ( !function_exists('quot') ){
		function quot($txt){
			return str_replace( "\"", "&quot;", $txt );
		}
	}

	$options = get_admin_options_ltw( );

	if ( $_POST['latest-twitter-submit'] ) {
		// get posted values from form submission
		$new_options['user'] = strip_tags(stripslashes($_POST['latest-twitter-user']));
		$new_options['count'] = strip_tags(stripslashes($_POST['latest-twitter-count']));
		$new_options['prefix'] = $_POST['latest-twitter-prefix'];
		$new_options['suffix'] = $_POST['latest-twitter-suffix'];
		$new_options['beforeUpdate'] = $_POST['latest-twitter-beforeUpdate'];
		$new_options['afterUpdate'] = $_POST['latest-twitter-afterUpdate'];
		$new_options['showTwitterIconTF'] = false;
		$new_options['showProfilePicTF'] = false;
		switch( $_POST['showIconOrPic'] ){
			case "icon":
				$new_options['showTwitterIconTF'] = true;
				break;
			case "pic":
				$new_options['showProfilePicTF'] = true;
				break;
			case "none":
				break;
		}
		if( $_POST['showTweetTimeTF']=="1"){
			$new_options['showTweetTimeTF'] = true;
		} else{
			$new_options['showTweetTimeTF'] = false;
		}
		// if the posted options are different, save them
		if ( $options != $new_options ) {
			$options = $new_options;
			update_option('widget_latest_twitter', $options);
		}
	}

	// format some of the options as valid html
	$username = htmlspecialchars($options['user'], ENT_QUOTES);
	$updateCount = htmlspecialchars($options['count'], ENT_QUOTES);
	$prefix = stripslashes(quot($options['prefix']));
	$suffix = stripslashes(quot($options['suffix']));
	$beforeUpdate = stripslashes(quot($options['beforeUpdate']));
	$afterUpdate = stripslashes(quot($options['afterUpdate']));
	$showTwitterIconTF = $options['showTwitterIconTF'];
	$showProfilePicTF = $options['showProfilePicTF'];
	$showTweetTimeTF = $options['showTweetTimeTF'];
?>
	<div>
	<label for="latest-twitter-user" style="line-height:35px;display:block;">Twitter user: @<input type="text" size="12" id="latest-twitter-user" name="latest-twitter-user" value="<?php echo $username; ?>" /></label>
	<label for="latest-twitter-count" style="line-height:35px;display:block;">Show this many twitter updates: <input type="text" id="latest-twitter-count" size="2" name="latest-twitter-count" value="<?php echo $updateCount; ?>" /></label>
	<label for="latest-twitter-prefix" style="line-height:35px;display:block;">Before everything: <input type="text" id="latest-twitter-prefix" size="8" name="latest-twitter-prefix" value="<?php echo $prefix; ?>" /></label>
	<label for="latest-twitter-suffix" style="line-height:35px;display:block;">After everything: <input type="text" id="latest-twitter-suffix" size="8" name="latest-twitter-suffix" value="<?php echo $suffix; ?>" /></label>
	<label for="latest-twitter-beforeUpdate" style="line-height:35px;display:block;">Before each tweet: <input type="text" id="latest-twitter-beforeUpdate" size="8" name="latest-twitter-beforeUpdate" value="<?php echo $beforeUpdate; ?>" /></label>
	<label for="latest-twitter-afterUpdate" style="line-height:35px;display:block;">After each tweet: <input type="text" id="latest-twitter-afterUpdate" size="8" name="latest-twitter-afterUpdate" value="<?php echo $afterUpdate; ?>" /></label>
	<p>&nbsp;</p>
	<p><input type="radio" id="latest-twitter-showTwitterIconTF" value="icon" name="showIconOrPic"<?php if($showTwitterIconTF){ ?> checked="checked"<?php } ?>><label for="latest-twitter-showTwitterIconTF"> Show twitter icon</label></p>
	<p><input type="radio" id="latest-twitter-showProfilePicTF" value="pic" name="showIconOrPic"<?php if($showProfilePicTF){ ?> checked="checked"<?php } ?>><label for="latest-twitter-showProfilePicTF"> Show profile picture</label></p>
	<p><input type="radio" id="latest-twitter-showNeitherImageTF" value="none" name="showIconOrPic"<?php if((!$showProfilePicTF) && (!$showTwitterIconTF)){ ?> checked="checked"<?php } ?>><label for="latest-twitter-showNeitherImageTF"> Show no image</label></p>
	<p>&nbsp;</p>
	<p><input type="checkbox" id="showTweetTimeTF" value="1" name="showTweetTimeTF"<?php if($showTweetTimeTF){ ?> checked="checked"<?php } ?>> <label for="showTweetTimeTF">Show tweeted "time ago"</label></p>
	<input type="hidden" name="latest-twitter-submit" id="latest-twitter-submit" value="1" />
	</div>
<?php

}

function latest_twitter_widget_css( ){
	echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . get_bloginfo('wpurl') ."/wp-content/plugins/latest-twitter-sidebar-widget/latest_twitter_widget.css\" />" . "\n";
}

add_action("plugins_loaded", "init_latest_twitter");
add_action('wp_head', 'latest_twitter_widget_css');

?>