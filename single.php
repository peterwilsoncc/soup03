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
			<p class="entry-meta">Posted on <span class="time pubdate"><time datetime="<?php the_time('c') ?>" pubdate class="entry-date"><?php the_time(get_option('date_format')); ?></time></span> by <span class="author vcard"><a class="url fn n" href="<?php echo get_author_posts_url( $authordata->ID, $authordata->user_nicename ); ?>" title="View all posts by <?php the_author(); ?>"><?php the_author(); ?></a></span></p>
		</header></div>			
		
		<div id="contentA">
			<?php 
			$soup->writeSinglePost($post);
			?>

			
			<?php comments_template(); ?>
			
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