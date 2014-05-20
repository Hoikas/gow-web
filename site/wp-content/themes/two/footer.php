			
			    <div id="content-bottom">
			    </div>
			</div> <!-- End Content Wrapper -->
			<div id="footer">
				    <p>
					<?php
					// This code grabs the navigation stuff from a
					// WP Link list called "Navigation"... Fun!
					wp_list_bookmarks(array(
						'after'						=>			' | ',
						'before'					=>			null,
						'categorize'				=>			false,
						'category_name'				=>			'Navigation',
						'class'						=>			null,
						'orderby'					=>			'notes, id',
						'show_images'				=>			false,
						'title_li'					=>			null,
					));
					?>
					</p>
            </div>
		</div> <!-- End Container -->
        <?php wp_footer(); ?>
    </body>
</html>
