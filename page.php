<?php
global $soup;
get_header();
the_post();
?>

<div id="content">
	<article id="contentHeadA" <?php post_class(); ?>>
		<header id="contentHead">
			<<?php echo $soup->pageNameTag; ?> id="pageName" class="entry-title">
				<a href="<?php the_permalink();?>"><?php the_title()?></a>
			</<?php echo $soup->pageNameTag; ?>>
		</header>			
		
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
	</article>
	<!-- //#contentHeadA -->
<?php
get_sidebar();
//#content [get_sidebar() closes div #content]
?>			



<?
get_footer();
?>