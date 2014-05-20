<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
		<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?><?php } ?> <?php wp_title(); ?></title>
		<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
		<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="http://guildofwriters.com/feed" />
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
		<link rel="shortcut icon" type="image/ico" href="http://guildofwriters.com/favicon.ico" />
		<style type="text/css" media="screen">
            <?php include_once('./wp-content/themes/two/backgrounds.php'); echo($tagline); ?>
        </style>
		<?php wp_head(); ?>
	</head>
	<!--[if lt IE 7.]>
           <script defer type="text/javascript" src="/wp-content/themes/two/js/png-fix.js"></script>
           <![endif]-->
	<body>
		<div id="nav-wrap">
		    <div id="nav">
				<ul id="navigation">
					<?php
					wp_list_bookmarks(array(
						'categorize'				=>			false,
						'category_name'				=>			'Navigation',
						'orderby'					=>			'notes, id',
						'show_images'				=>			false,
						'title_li'					=>			null,
					));
					?>
				</ul>
			</div>
			
		</div>
		<div id="header">
		<h1>Guild of Writers</h1>
		<a href="/" title="Guild of Writers"><img class="title" src="/wp-content/themes/two/images/title.png" alt="Guild of Writers" /></a>
		</div>
        