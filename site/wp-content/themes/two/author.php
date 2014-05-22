<?php
/**
 * The template for displaying Author Archive pages.
 */

get_header();
$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
?>

	<div id="container">
		<div id="content-wrap">
<?php get_sidebar(); ?>
			<div class="narrowcolumn">
				<?php if (have_posts()) : ?>
				
				<?php if ($curauth->description != '') : ?>
					<!--Author Info-->
					<div class="post" id="post-0">
						<h2 class="blog"><?php echo $curauth->display_name; ?>'s Journal</h2>
						<p class="postmetadata">
							<?php
							$useTheNick = ($curauth->nickname == $curauth->display_name);
							if ($curauth->first_name != '' && $useTheNick) :
							?>
							<span class="alignleft">Real Name: <?php printf('%s %s', $curauth->first_name, $curauth->last_name); ?></span>
							<?php elseif ($curauth->nickname != '' && !$useTheNick) : ?>
							<span class="alignleft">Nickname: <?php echo $curauth->nickname; ?></span>
							<?php endif; ?>
							
							<span class="alignright">User Biography</span>
						</p>
						
						<div class="entry">
							<p><?php echo $curauth->description; ?></p>
						</div>
					</div>
				<?php endif; ?>
				
				<!--Post Archive Stuff-->
				<?php while (have_posts()) : the_post(); ?>
					<div class="post" id="post-<?php the_ID(); ?>">
						<h2 class="blog"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
						<p class="postmetadata"><span class="alignleft">Written under <?php the_category(', ') ?> on <?php the_time('F d, Y') ?><?php edit_post_link(' Edit', ' | ', ''); ?></span>
							<span class="alignright"><?php comments_popup_link('Nobody is talking.', '1 Person is talking.', '% People are talking.'); ?></span></p>
						<div class="entry">
							<?php the_content('Read More...'); ?>
						</div>
						
					</div>
				<?php endwhile; ?>
				<p class="page-nav"><?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); } ?></p>
			<?php else : ?>
				<h2 class="center">Not Found</h2>
				<p class="center">Sorry, but you are looking for something that isn't here.</p>
				<?php include (TEMPLATEPATH . "/searchform.php"); ?>
			<?php endif; ?>
			</div>

<?php get_footer(); ?>
