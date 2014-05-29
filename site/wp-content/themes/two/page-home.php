<?php get_header(); ?>
        <div id="container">
            
            <!-- Ugly as sin, but hey, so are you. -->
            <div id="content-wrap">
<?php get_sidebar(); ?>
                <div class="narrowcolumn">

                    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <div class="post">
                        <h2><?php the_title(); ?></h2>
                        <div class="entry">
                            <?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
                        </div>
                        <?php edit_post_link('Edit this entry.', '<p class="postmetadata"><span class="alignright">', '</span></p>'); ?>
                    </div>

                    <div
                    <?php 
                        endwhile; endif; 
                        wp_reset_postdata();

                        // Grab the three most recent dev journals posts and show them.
                        $query = new WP_Query('showposts=3');
                        if ($query->have_posts()) :
                        while ($query->have_posts()) : $query->the_post();
                    ?>
                        <div class="post" id="post-<?php the_ID(); ?>">
                            <h2 class="blog"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
                            <p class="postmetadata"><span class="alignleft">Written by <?php the_author_posts_link(); ?> under <?php the_category(', ') ?> on <?php the_time('F d, Y') ?><?php edit_post_link(' Edit', ' | ', ''); ?></span>
                                <span class="alignright"><?php comments_popup_link('Nobody is talking.', '1 Person is talking.', '% People are talking.'); ?></span></p>
                            <div class="entry">
                                <?php the_content('Read More...'); ?>
                            </div>
                            
                        </div>
                    <?php endwhile; endif; ?>
                    </div>

<?php get_footer(); ?>