<?php
global $soup;
get_header();
the_post();
?>

<div id="content">
	<article id="contentHeadA" <?php post_class(); ?>>
		<header id="contentHead">
			<<?php echo $soup->pageNameTag; ?> id="pageName" class="entry-title">
				<?php the_title()?>
			</<?php echo $soup->pageNameTag; ?>>
			<p>Posted on <time datetime="<?php the_time('c') ?>" pubdate class="entry-date"><?php the_time(get_option('date_format')); ?></time> by <span class="author vcard"><a class="url fn n" href="<?php echo get_author_posts_url( $authordata->ID, $authordata->user_nicename ); ?>" title="View all posts by <?php the_author(); ?>"><?php the_author(); ?></a></span></p>
		</header>			
		
		<div id="contentA">
			<?php 
			$soup->writeSinglePost($post);
			?>

			
			<?php comments_template(); ?>
			
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