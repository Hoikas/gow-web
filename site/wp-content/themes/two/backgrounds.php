<?php

$taglines = array();
$taglines[] = "body { background: #DCC8A3 url('/wp-content/themes/two/images/backgrounds/bkgrd-body-01.jpg') no-repeat top center; }";
$taglines[] = "body { background: #DCC8A3 url('/wp-content/themes/two/images/backgrounds/bkgrd-body-02.jpg') no-repeat top center; }";
$taglines[] = "body { background: #DCC8A3 url('/wp-content/themes/two/images/backgrounds/bkgrd-body-03.jpg') no-repeat top center; }";
$taglines[] = "body { background: #DCC8A3 url('/wp-content/themes/two/images/backgrounds/bkgrd-body-04.jpg') no-repeat top center; }";
$taglines[] = "body { background: #DCC8A3 url('/wp-content/themes/two/images/backgrounds/bkgrd-body-05.jpg') no-repeat top center; }";
$taglines[] = "body { background: #DCC8A3 url('/wp-content/themes/two/images/backgrounds/bkgrd-body-06.jpg') no-repeat top center; }";
$taglines[] = "body { background: #DCC8A3 url('/wp-content/themes/two/images/backgrounds/bkgrd-body-07.jpg') no-repeat top center; }";

$longname = "Keith Lord";
$shortname = "Keith Lord";
$email = "tweek@grey-skies.net";

if ( $_GET["html"] ) {
	header("Content-Type: text/html; charset=UTF-8");
	echo "<html>
	<head>
	<style type=\"text/css\">
html {
	color: white;
	background-color: #73a0c5;
	}

.tagline {
	font-family: 'Trebuchet MS', 'Lucida Grande', Verdana, Arial, Sans-Serif;
	text-align: center;
	}

.tagline {
	font-style: italic;
	}

.tagline address:before {
	content: '~ ';
	}

.tagline address {
	font-style: normal;
	display: inline;
	}
	</style>
	<title>Quotes</title>
</head>
<body>
<h1>Quotes</h1>
<p><a href=\"taglines.php?rss=1\">Subscribe</a> to Quotes as <code>RSS 2.0</code> feed</p>";
	echo "<div class=\"tagline\">";
	foreach($taglines as $tagline) {
		echo "<p>$tagline</p>\n";
		}
	echo "</div>";
	echo "</body>\n</html>\n";
	}
elseif ( $_GET["rss"] ) {
	header("Content-Type: text/xml; charset=UTF-8");
	echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<rss version=\"2.0\">
	<channel>
		<title>Grey Skies Quotes</title>
		<link>http://grey-skies.net/</link>
		<description>Quotes for, ".$longname."'s weblog</description>
		<language>en</language>
		<copyright>Copyright 2000-2005 ".$longname.", unless otherwise specified.</copyright>

		<managingEditor>".$email." (".$shortname.")</managingEditor>
		<webMaster>".$email." (".$shortname.")</webMaster>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		<ttl>60</ttl>

";
	foreach($taglines as $tagline) {
		echo "		<item>
			<description>$tagline</description>
		</item>\n";
		}
	echo "	</channel>
</rss>";
}
else {
	$taglineid = array_rand($taglines);
	$tagline = $taglines[$taglineid];
	}
?>
