<?php get_header(); ?>
		<div id="container">
		    
            <!-- on occasion one finds that llama's steal sections of ones brain and replaces it with little chocolate covered marshmellows -->
			<div id="content-wrap">
<?php get_sidebar(); ?>
	            <div class="narrowcolumn">

		            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		            <div class="post">
		                <h2><?php the_title(); ?></h2>
			            <div class="entry">
				            <?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
            				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
         			    </div>
		            </div>
		            <?php endwhile; endif; ?>
	                <?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
	            </div>

<?php get_footer(); ?>