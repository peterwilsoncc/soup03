<?php
global $soup;
get_header();
?>

<div id="content">
	<div id="contentHeadA"><section>
		
		<div id="contentA" class="hfeed">
			<?php 
			while ( have_posts() ) : 
			the_post();
			$soup->writeIndexPost($post);
			endwhile;
			?>

			<div id="page-nav" class="page-nav nav">
				<div class="page-nav-older"><?php next_posts_link('Older posts') ?></div>
				<div class="page-nav-newer"><?php previous_posts_link('Newer posts') ?></div>
			</div>
			<!-- //#page-nav -->
			
			
		</div>
		<!-- //#contentA -->
	</section></div>
	<!-- //#contentHeadA -->
<?php
get_sidebar();
//#content [get_sidebar() closes div #content]
?>			



<?
get_footer();
?>