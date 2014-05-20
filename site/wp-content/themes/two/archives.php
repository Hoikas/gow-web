<?php
/*
Template Name: Archives
*/
?>

<?php get_header(); ?>

		<div id="container">
		    
            <!-- on occasion one finds that llama's steal sections of ones brain and replaces it with little chocolate covered marshmellows -->
			<div id="content-wrap">
	<div class="narrowcolumn">
    <div class="post" id="post-<?php the_ID(); ?>">
	    <div class="entry">
            <h2>Archives by Month</h2>
    	    <ul>
    	        <?php wp_get_archives('type=monthly'); ?>
    	    </ul>

            <h2>Archives by Category</h2>
    	    <ul>
    	        <li><a href="http://beneath.dnijazzclub.com/category/dni-raiders" title="View all posts filed under D&#039;ni Raiders">D&#039;ni Raiders</a></li>
    	        <li><a href="http://beneath.dnijazzclub.com/category/developement" title="View all posts filed under Developement">Developement</a></li>
    	        <li><a href="http://beneath.dnijazzclub.com/category/exploration" title="View all posts filed under Exploration">Exploration</a></li>
    	        <li><a href="http://beneath.dnijazzclub.com/category/general" title="View all posts filed under General">General</a></li>
    	        <li><a href="http://beneath.dnijazzclub.com/category/photos" title="View all posts filed under Photos">Photos</a></li>
    	        <li><a href="http://beneath.dnijazzclub.com/category/restoration" title="View all posts filed under Restoration">Restoration</a></li>
    	        <li><a href="http://beneath.dnijazzclub.com/category/the-journey" title="View all posts filed under The Journey">The Journey</a></li>
    	        <li><a href="http://beneath.dnijazzclub.com/category/the-path-of-co-operation" title="View all posts filed under The Path of Co-operation">The Path of Co-operation</a></li>
    	        <li><a href="http://beneath.dnijazzclub.com/category/the-path-of-the-shell" title="View all posts filed under The Path of the Shell">The Path of the Shell</a></li>
    	    </ul>
		</div>
    </div>
			</div>
	
<?php get_sidebar(); ?>
<?php get_footer(); ?>
