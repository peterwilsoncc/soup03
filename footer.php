<?php 
global $soup;
?>
<footer>
	<nav id="footNavWrap">
		<ul id="footNav" class="nav siteNav">
		<?php
			if (function_exists('wp_nav_menu')) {
				wp_nav_menu(array(
						'menu' => 'footer',
						'container' => 'nav',
						'container_id' => 'footNavWrap',
						'menu_class' => '',
						'menu_id' => 'footNav',
						'depth' => 1,
						'show_home' => 1,
						'fallback_cb' => array(&$soup,'listPages')
						
					));
			}
			else {
				$soup->listPages();
			}
			
		?>
		</ul>
	</nav>
	<div id="footWidgets"><?php
		if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('footer') ) {
			//insert static sidebar
			echo '<!' . '-- no footer widgets --' . '>';
		}
	?></div>
	<!-- //#footWidgets -->

	<ul id="footLegal">
		<li class="copyright">Copyright &copy; 2009 <?php bloginfo('name'); ?></li>
		<li class="credits">Site design by ???, developed by <a href="http://soupgiant.com">Soupgiant</a></li>
		<li class="powered">Powered by <a href="http://wordpress.org">WordPress</a></li>
	</ul>
	<!-- //#footLegal -->

</footer>

</div> <!-- //#pageWrap -->
<?php
wp_footer(); 
// add pngfix for <ie6, have to do it the ugly way as wp_enqueue_script doesnt have conditionals
?>
<!--[if IE 6]>
<script type='text/javascript' src='<?php echo $soup->parent['js']; ?>/ddbelatedpng-min.js'></script>
<script type="text/javascript">
/* <![CDATA[ */
if(typeof jQuery=="function"){jQuery(window).ready(function(){DD_belatedPNG.fix('img');});}else{DD_belatedPNG.fix('img');}
/* ]]> */
</script>
<![endif]-->

<?php

if (current_user_can('update_themes')) :
	?>
	<script type="text/javascript">
	/* <![CDATA[ */
		if (typeof console == "object") {
			console.log('queries: <?php echo get_num_queries(); ?>');
			console.log('<?php timer_stop(1); ?> seconds');
		}
	/* ]]> */
	</script>
	<?php
endif;
?>
</body>
</html>