<?php
global $soup;
get_header();
the_post();
?>

<div id="content">
	<div id="contentHeadA" <?php post_class('article5'); ?>><article>
		<div id="contentHead"><header>
			<<?php echo $soup->pageNameTag; ?> id="pageName" class="entry-title">
				<?php the_title()?>
			</<?php echo $soup->pageNameTag; ?>>
		</header></div>			
		
		<div id="contentA">
			<?php 
			$soup->writePagePost($post);
			?>

			<?php
			if ( get_post_custom_values('comments') ) {
				comments_template();
			}
			?>
			
		</div>
		<!-- //#contentA -->
	</article></div>
	<!-- //#contentHeadA -->
<?php
get_sidebar();
//#content [get_sidebar() closes div #content]
?>			



<?
get_footer();
?>