<?php get_header(); ?>
		<div id="container">
		    
            <!-- i fight for the users! -->
			<div id="content-wrap">
			<div class="narrowcolumn">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div class="post" id="post-<?php the_ID(); ?>">
					<h2 class="blog"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
						<p class="postmetadata"><span class="alignleft">Written by <?php the_author_posts_link(); ?> under <?php the_category(', ') ?> on <?php the_time('F d, Y') ?><?php edit_post_link(' Edit', ' | ', ''); ?></span>
							<span class="alignright"><?php comments_popup_link('Nobody is talking.', '1 Person is talking.', '% People are talking.'); ?></span></p>
					<div class="entry">
						<?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>
						<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
						<?php the_tags( '<p>Tags: ', ', ', '</p>'); ?>
					</div>
					<?php comments_template(); ?>
				</div>
				
				<?php endwhile; else: ?>
				<p>Sorry, no posts matched your criteria.</p>
			<?php endif; ?>
			</div>
	
<?php get_sidebar(); ?>
<?php get_footer(); ?>
