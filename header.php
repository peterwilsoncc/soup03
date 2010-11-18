<?php global $soup; ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> <?php body_class(); ?>>
<head>
	<meta charset=<?php bloginfo('charset'); ?>>
	<title><?php wp_title("|", true, 'right'); ?></title>
	<!--[if IE]><![endif]-->
	<!--[if lt IE 9]>
	<script src="<?php echo $soup->parent['js']; ?>/html5shiv.js"></script>
	<![endif]-->
			
	<?php 
		wp_head();
	?>
	
	<?php
	// only use this if CSS and the document will be served from the same domain.
	/*
	<!--[if lt IE 9]>
		<script type="text/javascript" src="<?php echo $soup->parent['js']; ?>/selectivizr-min.js"></script>
	<![endif]-->
	*/
	?>
	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
<nav id="skipLinks">
	<a href="#content">Skip to Content</a>
</nav>
<div id="pageWrap">

<header>
	<a href="<?php echo get_option('home'); ?>" id="siteDetails">
	<<?php echo $soup->siteNameTag; ?> id="siteName"><span></span><?php bloginfo('name'); ?></<?php echo $soup->siteNameTag; ?>>
	<p id="siteDesc"><span></span><?php bloginfo('description'); ?></p>
	</a>
	
	<div id="headerWidgets"><?php
		if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('header') ) {
			//insert static sidebar
			echo '<!' . '-- no header widgets --' . '>';
		}
	?></div>
	<!-- //#headerWidgets -->
	
	<nav id="navWrap">
		<ul id="nav" class="nav siteNav">
			<?php
				if (function_exists('wp_nav_menu')) {
					wp_nav_menu(array(
							'menu' => 'header',
							'container' => 'ul',
							'container_id' => 'navWrap',
							'menu_class' => '',
							'menu_id' => 'nav',
							'depth' => 2,
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
</header>