<?php get_header(); ?>

		<div id="container">
		    
            <!-- on occasion one finds that llama's steal sections of ones brain and replaces it with little chocolate covered marshmellows -->
			<div id="content-wrap">
	            <div class="narrowcolumn">
				<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>
					<div class="post" id="post-<?php the_ID(); ?>">
						<h2 class="blog"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
						<p class="postmetadata"><span class="alignleft">Filed in <?php the_category(', ') ?> On <?php the_time('M d y') ?><?php edit_post_link(' Edit', ' | ', ''); ?></span>  <span class="alignright"><?php comments_popup_link('Nobody is talking.', '1 Person is talking.', '% People are talking.'); ?></span></p>
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

<?php get_sidebar(); ?>

<?php get_footer(); ?>