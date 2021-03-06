<?php
/*
Note:
Function file load order:
	1) child
	2) parent
*/


function soup_setupParentThemeClass(){
	Class SoupThemeParent {

		public $parent;
		public $child;
		public $siteNameTag;
		public $pageNameTag;	
	 	public $postAlt;
		public $inlineFooterJSarray = array();
	
	
	

		/**
	     * PHP 4 Compatible Constructor
	     */
		function SoupThemeParent(){$this->__construct();}
    
	    /**
	     * PHP 5 Constructor
	     */		
		function __construct(){
			$this->defineParentURLs();
			$this->defineChildURLs();
			$this->defineMinimisedCode();
			$this->defineParentVersions();
			$this->defineChildVersions();
		
			$this->postAlt = 1;
			
			
					
			$this->initTheme();
		
		
		}
	
		function initTheme() {
			//add hooks, filters, etc
		
		
			$this->defineThemeOptions();
			$this->registerSidebars();
			$this->registerMenus();
			$this->setThumbnailSizes();
			$this->setupThemeOptions();
			$this->initChildTheme();
			
		
			add_action('wp_head', array(&$this, 'setHeaderTag'));
			add_action('wp_head', array(&$this, 'favIcon'));
			add_filter('body_class', array(&$this, 'bodyClass'),1, 2);
			add_filter('post_class', array(&$this, 'postClass'),5, 3);
		
			add_action('wp_print_styles', array(&$this,'registerCSS'), 50);
			add_action('wp_print_styles', array(&$this,'registerJS'),  50);
			add_action('wp_print_styles', array(&$this,'registerAdditionalCSSandJS'),  75);
			add_action('wp_print_styles', array(&$this,'enqueueCSS'), 50);
			add_action('wp_print_styles', array(&$this,'enqueueJS'),  100);
		
			add_filter('script_loader_src', array(&$this, 'removeVersionQstring'));
			add_filter('style_loader_src', array(&$this, 'removeVersionQstring'));

			add_filter('wp_nav_menu', array(&$this, 'filterMenus'));
			add_filter('wp_title', array(&$this, 'filterHtmlTitle'), 10, 2);

			add_filter('wp_print_footer_scripts', array(&$this, 'inlineFooterJs'));
		}

		function initChildTheme(){
			//placeholder function for additional initing by the child theme
		}

		function setHeaderTag() {
			if (is_front_page()){
				$this->siteNameTag = 'h1';
				$this->pageNameTag = 'h2';
			}
			else {
				$this->siteNameTag = 'p';
				$this->pageNameTag = 'h1';
			}
		}
    
		function defineParentURLs(){
			$this->parent = array(
				'url'	=> get_template_directory_uri(),
				'assets'=> get_template_directory_uri() . '/assets/parent',
				'css'	=> get_template_directory_uri() . '/assets/parent/c',
				'js'	=> get_template_directory_uri() . '/assets/parent/j',
				'img'	=> get_template_directory_uri() . '/assets/parent/i',
				'php'	=> get_template_directory_uri() . '/assets/parent/p',
				'phpPath' => TEMPLATEPATH . '/assets/parent/p'
			);		
		}
	
		function defineChildUrls() {
			$this->child = array(
				'url'	=> get_stylesheet_directory_uri(),
				'assets'=> get_stylesheet_directory_uri() . '/assets/child',
				'css'	=> get_stylesheet_directory_uri() . '/assets/child/c',
				'js'	=> get_stylesheet_directory_uri() . '/assets/child/j',
				'img'	=> get_stylesheet_directory_uri() . '/assets/child/i',
				'php'	=> get_stylesheet_directory_uri() . '/assets/child/p',
				'phpPath' => STYLESHEETPATH . '/assets/child/p'
			);		
		}
	
		function defineMinimisedCode() {
			$this->parent['mincss'] = false;
			$this->parent['minjs'] = false;

			$this->child['mincss'] = false;
			$this->child['minjs'] = false;		
		}
	
		function defineParentVersions() {
			$this->parent['cssVer'] = 20100706.01;
			$this->parent['jsVer']  = 20100706.01;
		}
	
		function defineChildVersions() {
			$this->child['cssVer'] = 20100706.01;
			$this->child['jsVer']  = 20100706.01;
			
			$this->child['jsDependencies'] = array (
					'soup-base', 
					'prettyPhoto',
					'hashchange',
					'form-validation',
					'jquery'
				);
		}
		
		function defineThemeOptions(){
			$this->options['thumbnails'] = true;
			$this->options['feedLinks'] = false;
			$this->options['headerWidgets'] = true;
			$this->options['footerWidgets'] = true;
			$this->options['contentBWidgets'] = true;
			$this->options['contentCWidgets'] = true;
			$this->options['showWPAdminBar'] = false;
			$this->options['handheldCssMedia'] = ''; //use to customise @media query
			
			
			$this->options['editorCSS'] = false; // if true, url is $this->child['css'] . '/all/editor-styles.css'
			
			/* ********************************
			If classes are to be added to the editor, add them to editorEnglishClasses as an array
			otherwise leave the setting as false
			EG:
			$this->options['editorEnglishClasses'] = array(
				'Leader Text' => 'leadertext',
				'Highlighted' => 'hilight'
			);
			********************************* */
			$this->options['editorEnglishClasses'] = false;

			/* ********************
			 forge the header levels in the editor to keep inline with document layout
			On pages, Header 1 is a h2 and so on
			On pasts, Header 1 is a h4 and so on
			also adds the class bxPage or bxPost as appropriate
			********************* */
			$this->options['editorFakeHeaderLevels'] = false; 
		}
		
		function isSSL() {
			if ((function_exists('getenv') AND ((getenv('HTTPS') != '' AND getenv('HTTPS') != 'off') 
			   OR (getenv('SERVER_PORT') == '433'))) OR (isset($_SERVER) AND ((isset($_SERVER['HTTPS']) 
			   AND $_SERVER['https'] !='' AND $_SERVER['HTTPS'] != 'off') OR (isset($_SERVER['SERVER_PORT']) 
			   AND $_SERVER['SERVER_PORT'] == '443')))) {
				return true;
			}
			else {
				return false;
			}
		}

		function setThumbnailSizes(){
			if ($this->options['thumbnails'] == true) {
				if ( function_exists( 'add_theme_support' ) ) {
					add_theme_support( 'post-thumbnails' );
					set_post_thumbnail_size( 150, 150, true ); // 150x150 size
				}
			}
			// add_image_size( '150x150', 150, 150, true); // 150x150 image size
			// add_image_size( '270x150', 270, 150, true ); // 270x150 image size
			// add_image_size( '310x150', 310, 150, true ); // 310x150 image size
			// add_image_size( '310x310', 310, 310, true ); // 310x310 image size
			// add_image_size( '590x400', 590, 400, true ); // 590x400 image size
			// add_image_size( '590', 590, 9999 ); // 590 image size
			// add_image_size( '950', 950, 9999 ); // 950 image size
			
		}
		
		function setupThemeOptions(){
			if (function_exists('remove_theme_support')) {
				if ($this->options['feedLinks'] == true) {
					add_theme_support('automatic-feed-links');
				}
				elseif  ($this->options['feedLinks'] == false) {
					remove_theme_support('automatic-feed-links');
					remove_action('wp_head','feed_links_extra', 3);
					remove_action('wp_head','feed_links', 2);
				}
			}
			if ((function_exists('add_editor_style')) AND ($this->options['editorCSS'] == true)) {
				add_editor_style("assets/child/c/all/editor-style.css");
			}
			if (is_array($this->options['editorEnglishClasses']) == true) {
				add_filter('mce_buttons_2', array(&$this, 'editorButtons'));
				add_filter('tiny_mce_before_init', array(&$this, 'editorEnglishClasses'));
			}
			if ($this->options['editorFakeHeaderLevels'] == true) {
				add_filter('tiny_mce_before_init', array(&$this, 'editorSettings'));
			}
			
			if ($this->options['showWPAdminBar'] != true) {
				//credit due: http://yoast.com/disable-wp-admin-bar/
				
				/* Disable the Admin Bar. */
				add_filter( 'show_admin_bar', '__return_false' );

				/* Remove the Admin Bar preference in user profile */
				remove_action( 'personal_options', '_admin_bar_preferences' );			}
		}

		//register styles
		function registerCSS(){
			global $wp_styles;
			if ($this->parent['mincss'] === false) {
				$psuffix = '';
			} else {
				$psuffix = '-min';
			}
		
			if ($this->child['mincss'] === true) {
				$csuffix = '-min';
			}
			else {
				$csuffix = '';
			}
			
			if ($this->options['handheldCssMedia'] == '') {
				'handheld, only screen and (min-width: 1px), only screen and (min-device-width: 1px)';
			}
		
			//register pretty photo css
			wp_register_style(
				'prettyPhoto-css',
				$this->parent['css'] . "/prettyphoto$psuffix.css",
				null,
				'2.5.6',
				'all'
			);
	

	
			//register all child styles
			wp_register_style(
				'soup-all',
				$this->child['css'] . "/all/all$csuffix.css",
				null,
				$this->child['cssVer'],
				'all'
			);
		
			wp_register_style(
				'soup-all-ie6',
				$this->child['css'] . "/all/all-ie6$csuffix.css",
				array('soup-all'),
				$this->child['cssVer'],
				'all'
			);
			$wp_styles->registered['soup-all-ie6']->extra['conditional'] = 'IE 6';
	
			wp_register_style(
				'soup-all-ie7',
				$this->child['css'] . "/all/all-ie7$csuffix.css",
				array('soup-all'),
				$this->child['cssVer'],
				'all'
			);
			$wp_styles->registered['soup-all-ie7']->extra['conditional'] = 'IE 7';
	
			wp_register_style(
				'soup-all-ie8',
				$this->child['css'] . "/all/all-ie8$csuffix.css",
				array('soup-all'),
				$this->child['cssVer'],
				'all'
			);
			$wp_styles->registered['soup-all-ie8']->extra['conditional'] = 'IE 8';
	
			wp_register_style(
				'soup-all-ie9',
				$this->child['css'] . "/all/all-ie9$csuffix.css",
				array('soup-all'),
				$this->child['cssVer'],
				'all'
			);
			$wp_styles->registered['soup-all-ie9']->extra['conditional'] = 'IE 9';
		
		
			//register mobile child styles
			wp_register_style(
				'soup-mobile',
				$this->child['css'] . "/mobile/mobile$csuffix.css",
				array('soup-all'),
				$this->child['cssVer'],
				$this->options['handheldCssMedia']
			);	
	
			//register print child styles
			wp_register_style(
				'soup-print',
				$this->child['css'] . "/print/print$csuffix.css",
				array('soup-all'),
				$this->child['cssVer'],
				'print'
			);

			wp_register_style(
				'soup-print-ie6',
				$this->child['css'] . "/print/print-ie6$csuffix.css",
				array('soup-print'),
				$this->child['cssVer'],
				'print'
			);
			$wp_styles->registered['soup-print-ie6']->extra['conditional'] = 'IE 6';

			wp_register_style(
				'soup-print-ie7',
				$this->child['css'] . "/print/print-ie7$csuffix.css",
				array('soup-print'),
				$this->child['cssVer'],
				'print'
			);
			$wp_styles->registered['soup-print-ie7']->extra['conditional'] = 'IE 7';

			wp_register_style(
				'soup-print-ie8',
				$this->child['css'] . "/print/print-ie8$csuffix.css",
				array('soup-print'),
				$this->child['cssVer'],
				'print'
			);
			$wp_styles->registered['soup-print-ie8']->extra['conditional'] = 'IE 8';

			wp_register_style(
				'soup-print-ie9',
				$this->child['css'] . "/print/print-ie9$csuffix.css",
				array('soup-print'),
				$this->child['cssVer'],
				'print'
			);
			$wp_styles->registered['soup-print-ie9']->extra['conditional'] = 'IE 9';
			
			
			//register all-media child styles
			wp_register_style(
				'soup-all-media',
				$this->child['css'] . "/all-media/all-media$csuffix.css",
				null,
				$this->child['cssVer'],
				'all'
			);

			wp_register_style(
				'soup-all-media-ie6',
				$this->child['css'] . "/all-media/all-media-ie6$csuffix.css",
				array('soup-all-media'),
				$this->child['cssVer'],
				'all'
			);
			$wp_styles->registered['soup-all-media-ie6']->extra['conditional'] = 'IE 6';

			wp_register_style(
				'soup-all-media-ie7',
				$this->child['css'] . "/all-media/all-media-ie7$csuffix.css",
				array('soup-all-media'),
				$this->child['cssVer'],
				'all'
			);
			$wp_styles->registered['soup-all-media-ie7']->extra['conditional'] = 'IE 7';

			wp_register_style(
				'soup-all-media-ie8',
				$this->child['css'] . "/all-media/all-media-ie8$csuffix.css",
				array('soup-all-media'),
				$this->child['cssVer'],
				'all'
			);
			$wp_styles->registered['soup-all-media-ie8']->extra['conditional'] = 'IE 8';

			wp_register_style(
				'soup-all-media-ie9',
				$this->child['css'] . "/all-media/all-media-ie9$csuffix.css",
				array('soup-all-media'),
				$this->child['cssVer'],
				'all'
			);
			$wp_styles->registered['soup-all-media-ie9']->extra['conditional'] = 'IE 9';
			
		}
	
		function enqueueCSS() {  
			//usually overwritten by child
			if (!is_admin()) :
			
				/* 
					never enqueue seperate media styles and 
					all-media styles at the same time.
				*/
				wp_enqueue_style('soup-all');
				wp_enqueue_style('soup-all-ie6');
				wp_enqueue_style('soup-all-ie7');
				wp_enqueue_style('soup-all-ie8');
				wp_enqueue_style('soup-all-ie9');
						
				wp_enqueue_style('soup-mobile');
			
				wp_enqueue_style('soup-print');
				wp_enqueue_style('soup-print-ie6');
				wp_enqueue_style('soup-print-ie7');
				wp_enqueue_style('soup-print-ie8');
				wp_enqueue_style('soup-print-ie9');
				/* */
				
				/* 
					never enqueue seperate media styles and 
					all-media styles at the same time.
				
				wp_enqueue_style('soup-all-media');
				wp_enqueue_style('soup-all-media-ie6');
				wp_enqueue_style('soup-all-media-ie7');
				wp_enqueue_style('soup-all-media-ie8');
				wp_enqueue_style('soup-all-media-ie9');
				/* */
						
			endif; //if (!is_admin()):
		
		}

		function editorButtons($buttons){
			array_unshift($buttons, 'styleselect');
			return $buttons;
		}

		function editorEnglishClasses($settings) {
			//Use english names for editor classes
			// this should be overridden in the child theme
			
			$classes = $this->options['editorEnglishClasses'];
			
			
			
			
			if ( !empty($settings['theme_advanced_styles']) ) {
				$settings['theme_advanced_styles'] .= ';';
			}
			else {
				$settings['theme_advanced_styles'] = '';
			}
				
			$class_settings = '';
			foreach ( $classes as $name => $value ) {
				$class_settings .= "{$name}={$value};";
			}

			$settings['theme_advanced_styles'] .= trim($class_settings, '; ');
			
			return $settings;
		}
		
		function editorSettings($settings) {			

			if ( !empty($settings['theme_advanced_blockformats']) )
				$settings['theme_advanced_blockformats'] .= ';';
			else
				$settings['theme_advanced_blockformats'] = '';
			
			if (get_post_type() == 'page') {
				$formats = array(
					'Paragraph' => 'p',
					'Address' => 'address',
					'Preformatted' => 'pre',
					'Heading 1' => 'h2',
					'Heading 2' => 'h3',
					'Heading 3' => 'h4',
					'Heading 4' => 'h5',
					'Heading 5' => 'h6'
				);
				
				$settings['body_class'] .= 'bxPage';
			}
			else {			
				$formats = array(
					'Paragraph' => 'p',
					'Address' => 'address',
					'Preformatted' => 'pre',
					'Heading 1' => 'h4',
					'Heading 2' => 'h5',
					'Heading 3' => 'h6'
				);
				$settings['body_class'] .= 'bxPost';
			}
				
			$format_settings = '';
			foreach ( $formats as $name => $value ) {
				$format_settings .= "{$name}={$value};";
			}

			$settings['theme_advanced_blockformats'] .= trim($format_settings, '; ');
			
			return $settings;
		}
	
		function registerJS() {
			global $wp_scripts;
			if ($this->parent['minjs'] === false) {
				$psuffix = '';
				if ($this->isSSL() == true) {
					$validatorURL = 'https://ajax.aspnetcdn.com/ajax/jquery.validate/1.7/jquery.validate.js';
				}
				else {
					$validatorURL = 'http://ajax.aspnetcdn.com/ajax/jquery.validate/1.7/jquery.validate.js';			
				}
			}
			else {
				$psuffix = '-min';
				if ($this->isSSL() == true) {
					$validatorURL = 'https://ajax.aspnetcdn.com/ajax/jquery.validate/1.7/jquery.validate.min.js';
				}
				else {
					$validatorURL = 'http://ajax.aspnetcdn.com/ajax/jquery.validate/1.7/jquery.validate.min.js';			
				}
			}				

			if ($this->child['minjs'] === true) {
				$csuffix = '-min';
			}
			else {
				$csuffix = '';
			}
		
			wp_register_script(
				'soup-base',
				$this->parent['js'] . "/base$psuffix.js",
				array('jquery'),
				$this->parent['jsVer'],
				true
			);
			wp_localize_script('soup-base', 'SOUPGIANT_wpURLS', array(
				'register' => site_url('wp-login.php?action=register', 'login'),
				'regoEnabled' => get_option('users_can_register') ? "y" : "n",
				'lostpassword' => wp_lostpassword_url( site_url( $_SERVER['REQUEST_URI'] ) ),
				'loginsubmit' => site_url( 'wp-login.php', 'login' ),
				'currentURL' => site_url( $_SERVER['REQUEST_URI'] )
			));
				
			/* jQuery plugins */
			wp_register_script(
				'form-validation',
				$validatorURL,
				array('jquery'),
				'1.7',
				true
			);
		
			wp_register_script(
				'prettyPhoto',
				$this->parent['js'] . "/jqplugins/prettyphoto$psuffix.js",
				array('jquery'),
				'2.5.6',
				true
			);
	
			wp_register_script(
				'hashchange',
				$this->parent['js'] . "/jqplugins/ba-hashchange$psuffix.js",
				array('jquery'),
				'1.2',
				true
			);

			wp_register_script(
				'modernizr',
				$this->parent['js'] . "/modernizr$psuffix.js",
				null,
				'1.6',
				true
			);

			wp_register_script(
				'custom',
				$this->child['js'] . "/custom$csuffix.js",
				$this->child['jsDependencies'],
				$this->child['jsVer'],
				true
			);
		
		
		}

		function registerAdditionalCSSandJS() {
			// this is designed for child to register additional files without 
			// having to regregister them all
			return true;
		}
		
		function enqueueChildJs(){
			//this function is usually overwitten in child
			wp_enqueue_script('custom');
		}

		function enqueueJS(){
			//this function can be overwritten in child but usually isn't
			$this->enqueueChildJs();
			
			//threaded comments
			
			
			//for the wp_script_is checks below, we need to manually enque 'custom' dependancies too
			
			if (wp_script_is('custom') == true) {
				foreach ($this->child['jsDependencies'] as $handle) {
					wp_enqueue_script($handle);
				}
			}
			
		
			if (wp_script_is('prettyPhoto') == true) {
				wp_enqueue_style('prettyPhoto-css');
			}
			
			if (wp_script_is('prettyPhoto') && wp_script_is('hashchange')) {
				global $wp_scripts;
				if ($this->child['minjs'] === true) {
					$psuffix = '-min';
				}
				else {
					$psuffix = '';
				}
				$wp_scripts->query('prettyPhoto')->src = '';
				$wp_scripts->query('hashchange')->src = $this->parent['js'] . "/jqplugins/prettyphoto-hashchange$psuffix.js";
				$wp_scripts->query('hashchange')->ver = $wp_scripts->query('prettyPhoto')->ver . ',' . $wp_scripts->query('hashchange')->ver;
			}
			$this->commentReply();
			
		
		}

		function removeVersionQstring($src){
			if ( preg_match( '/ajax\.googleapis\.com\/|ajax\.aspnetcdn\.com\/|ajax\.microsoft\.com\//', $src ) )
				$src = remove_query_arg('ver',$src);
			return $src;
		}
	
		function commentReply() {
			if ((!is_admin()) AND is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
				wp_enqueue_script( 'comment-reply' );
			}
		}

		function bodyClass($classes, $class = null) {
			//set classes on <body> tag
			// based on same function in Sandbox
			global $wp_query, $current_user, $wpdb;
			//much of this function is sourced from the sandbox_body_class from the sandbox theme
			
			$c = array();
			$r = array(); //values that will be removed from the array
			
			$c = array_merge($classes);
			
			$c[] = 'nojs';
			$c[] = 'nojswin';
			
			is_front_page()  ? $c[] = 'bxHome'       : null; // For the front page, if set
			is_home()        ? $c[] = 'bxBlog bxList bxAllBlog'       : null; // For the blog posts page, if set
			is_archive()     ? $c[] = 'bxArch bxList bxAllBlog'    : null;
			is_date()        ? $c[] = 'bxDate'       : null;
			is_search()      ? $c[] = 'bxSearch'     : null;
			is_singular()    ? $c[] = 'bxSngl'      : null;
			is_paged()       ? $c[] = 'bxPaged'      : null;
			is_attachment()  ? $c[] = 'bxAtt' : null;
			is_404()         ? $c[] = 'bx404'     : null; // CSS does not allow a digit as first character
			
			
			if (is_front_page()) {
				$c[] = 'bxHome';
				
				$r[] = 'home';
			}
			if (is_home()) {
				$c[] = 'bxBlog';
				$c[] = 'bxList';
				$c[] = 'bxAllBlog';
				
				$r[] = 'blog';
			}
			if (is_archive()) {
				$c[] = 'bxArch';
				$c[] = 'bxList';
				$c[] = 'bxAllBlog';
				
				$r[] = 'archive';
			}
			if (is_date()) {
				$c[] = 'bxDate';
				
				$r[] = 'date';
			}
			if ( is_search() ) {
				$c[] = 'bxSearch';
				
				$r[] = 'search';
			}
			if ( is_paged() ) {
				$c[] = 'bxPaged';
				
				$r[] = 'paged';
			}
			
			if (is_singular()) {
				$c[] = 'bxSngl';
			}
			
			if ( is_attachment() ) {
				$c = 'bxAtt';
				
				$r[] = 'attachment';
			}
			
			if ( is_404() ) {
				$c[] = 'bx404';
				
				$r[] = 'error404';
			}
			



			if ( is_single() ) {
				$post_id = $wp_query->get_queried_object_id();
				$post = $wp_query->get_queried_object();
				$postSlug = $wp_query->post->post_name;
				
				
				$c[] = 'bxAllBlog';
				
				$c[] = 'bxPost';
				$r[] = 'single';
				
				$c[] = 'bxPTy-' . sanitize_html_class($post->post_type, $post_id);
				$r[] = 'single-' . sanitize_html_class($post->post_type, $post_id);
				
				$c[] = 'bxP-' . $postSlug;
				$c[] = 'bxP-id' . $post_id;
				$r[] = 'postid-' . $post_id;

				// Adds category classes for each category on single posts
				if ( $cats = get_the_category() )
					foreach ( $cats as $cat )
						$c[] = 'bxPC-' . $cat->slug;

				// Adds tag classes for each tags on single posts
				if ( $tags = get_the_tags() )
					foreach ( $tags as $tag )
						$c[] = 'bxPTag-' . $tag->slug;


				if (function_exists('get_post_format')) {
					$post_format = get_post_format( $post->ID );

					if ( $post_format && !is_wp_error($post_format) ) {
						$c[] = 'bxPF-' . sanitize_html_class( $post_format );
						$r[] = 'single-format-' . sanitize_html_class( $post_format );
					}
					else {
						$c[] = 'bxPF-standard';
						$r[] = 'single-format-standard';
					}
				}

				if ( is_attachment() ) {
					
					
					$mime_type = get_post_mime_type($post_id);
					$mime_prefix = array( 'application/', 'image/', 'text/', 'audio/', 'video/', 'music/' );
					$c[] = 'bxAtt';
					$c[] = 'bxAtt-' . $postSlug;
					$c[] = 'bxAtt-' . str_replace( $mime_prefix, "", "$mime_type" );
					
					$r[] = 'attachmentid-' . $post_id;
					$r[] = 'attachment-' . str_replace( $mime_prefix, '', $mime_type );
				}
			}	
			// Author name classes for BODY on author archives
			elseif ( is_author() ) {
				$author = $wp_query->get_queried_object();
				$c[] = 'bxAuthor';
				$c[] = 'bxA-' . $author->user_nicename;
				
				$r[] = 'author';
				$r[] = 'author-' . sanitize_html_class( $author->user_nicename , $author->ID );
			}
			// Category name classes for BODY on category archvies
			elseif ( is_category() ) {
				$cat = $wp_query->get_queried_object();
				$c[] = 'bxCat';
				$c[] = 'bxC-' . $cat->slug;

				$r[] = 'category';
				$r[] = 'category-' . sanitize_html_class( $cat->slug, $cat->cat_ID );
			}
			// Tag name classes for BODY on tag archives
			elseif ( is_tag() ) {
				$tags = $wp_query->get_queried_object();
				$c[] = 'bxTag';
				$c[] = 'bxTag-' . $tags->slug;


				$r[] = 'tag';
				$r[] = 'tag-' . sanitize_html_class( $tags->slug, $tags->term_id );

			}

			// Tag name classes for BODY on taxonomy archives
			elseif (function_exists('is_tax') AND is_tax() ) {			
				
				$term = $wp_query->get_queried_object();
				
				$c[] = 'bxTax-' . sanitize_html_class( $term->taxonomy );
				$c[] = 'bxTerm-' . sanitize_html_class( $term->slug, $term->term_id );
				$c[] = 'bxTerm-' . $term->term_id;
				
				
				$r[] = 'tax-' . sanitize_html_class( $term->taxonomy );
				$r[] = 'term-' . sanitize_html_class( $term->slug, $term->term_id );
				$r[] = 'term-' . $term->term_id;
			}

			// Page author for BODY on 'pages'
			elseif ( is_page() ) {
				$pageID = $wp_query->post->ID;
				$pageSlug = $wp_query->post->post_name;
				$page_children = wp_list_pages("child_of=$pageID&echo=0");
				$post = get_page($pageID);
				$c[] = 'bxPage';
				$r[] = 'page';
				$r[] = 'page-id-' . $pageID;

				$c[] = 'bxPg-' . $pageSlug;
				$c[] = 'bxPgA-' . sanitize_title_with_dashes(strtolower(get_the_author('login')));



				if ( $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'page' AND post_status = 'publish' LIMIT 1", $page_id) ) ) {
					$c[] = 'bxPgTree-' . $pageID;
					$r[] = 'page-parent';
				}

				if ( $post->post_parent ) {
					$r[] = 'page-child';
					$r[] = 'parent-pageid-' . $post->post_parent;
					
					$c[] = 'bxPgChild';
					$c[] = 'bxPgTree-' . $post->post_parent;
					
				}
				if ( is_page_template() ) {
					$c[] = 'bxPgTemplate';
					$c[] = 'bxPgT-' . str_replace( '.php', '', get_post_meta( $pageID, '_wp_page_template', true ) );
					
					$r[] = 'page-template';
					$r[] = 'page-template-' . sanitize_html_class( str_replace( '.', '-', get_post_meta( $page_id, '_wp_page_template', true ) ), '' );
				}
				
				
			}
			elseif ( is_search() ) {
				if ( !empty( $wp_query->posts ) ) {
					$c[] = 'bxSearchResults';
					$r[] = 'search-results';
				}
				else {
					$c[] = 'bxSearchNil';
					$r[] = 'search-no-results';
				}
			}			

			// For when a visitor is logged in while browsing
			if ( $current_user->ID ) {
				$r[] = 'logged-in';
				$c[] = 'bxLoggedIn';
			}
			
			//remove the default classes that have been replaced with the soupgiant equivs.
			$soupClasses = array_diff($c, $r);


			// Separates classes with a single space, collates classes for BODY
			//$c = join( ' ', apply_filters( 'body_class',  $c ) ); // Available filter: body_class

			// And tada!
			return $soupClasses;	
		}

		function postClass($classes, $class = null, $post_ID = null) {
			//set classes on post's <div> tag
			// sourced from the sandbox theme
			
			if (isset($post_ID)) {
				$post = get_post($post_ID);
			}
			else {
				global $post;
			}
			
			$r = array();
			$c = array_merge($classes);

			// hentry for hAtom compliace, gets 'alt' for every other post DIV, describes the post type and p[n]
			$c[] = "p$this->postAlt";
			$c[] = $post->post_type;
			$c[] = $post->post_status;

			// Author for the post queried
			$c[] = 'author-' . sanitize_title_with_dashes(strtolower(get_the_author('login')));

			// Category for the post queried
			foreach ( (array) get_the_category() as $cat ) {
				if ( empty($cat->slug ) ) {
					continue;
				}
				$c[] = 'cat-' . sanitize_html_class($cat->slug, $cat->cat_ID);
				$r[] = 'category-' . sanitize_html_class($cat->slug, $cat->cat_ID);
				
			}

			// Tags for the post queried; if not tagged, use .untagged
			if ( get_the_tags() == null ) {
				$c[] = 'untagged';
			} else {
				$c[] = 'tagged';
			}

			// For password-protected posts
			if ( $post->post_password )
				$c[] = 'protected';

			// Applies the time- and date-based classes (below) to post DIV
			//sandbox_date_classes( mysql2date( 'U', $post->post_date ), $c );

			// If it's the other to the every, then add 'alt' class
			if ( ++$this->postAlt % 2 )
				$c[] = 'alt';

			$soupClasses = array_diff($c, $r);
			return $soupClasses;	
		}
	
		function favIcon(){
			$result = "";
			$result .= '<link rel="shortcut icon" type="image/x-icon" href="' . $this->child['img'] . '/favicon.ico" />' . "\n";
			$result .= '<link rel="icon" type="image/x-icon" href="' . $this->child['img'] . '/favicon.ico" />' . "\n";		
		
			echo $result;
			return;		
		}
	
		function getTree($post_id){
			global $wp_query, $post;
			if ($post_id === null) {
				$post_id = $post->ID;
			}
		
			if (is_page($post_id)) {
				//only works on pages.

				$tree[] = $post_id;


				$parent_id = get_post($post_id)->post_parent;
				if($parent_id != 0){
					$tree = array_merge($tree, soup_getTree($parent_id));
				}
			
				return $tree;
			}
			else {
				return false;
			}
		}

		function tagQuery() {
			//source: Thematic theme
			//if multiple tags are searched, display them correctly on the tag archive page
			static $nice_tag_query_result;

			if ($nice_tag_query_result) {
				echo $nice_tag_query_result;
				return;
			}
			else {
				$nice_tag_query = get_query_var('tag'); // tags in current query
				$nice_tag_query = str_replace(' ', '+', $nice_tag_query); // get_query_var returns ' ' for AND, replace by +
				$tag_slugs = preg_split('%[,+]%', $nice_tag_query, -1, PREG_SPLIT_NO_EMPTY); // create array of tag slugs
				$tag_ops = preg_split('%[^,+]*%', $nice_tag_query, -1, PREG_SPLIT_NO_EMPTY); // create array of operators

				$tag_ops_counter = 0;
				$nice_tag_query = '';

				if (count($tag_slugs) > 1) {
					foreach ($tag_slugs as $tag_slug) { 
						$tag = get_term_by('slug', $tag_slug ,'post_tag');
						// prettify tag operator, if any
						if ($tag_ops[$tag_ops_counter] == ',') {
							$tag_ops[$tag_ops_counter] = ', ';
						} elseif ($tag_ops[$tag_ops_counter] == '+') {
							$tag_ops[$tag_ops_counter] = ' + ';
						}
						// concatenate display name and prettified operators
						$nice_tag_query = $nice_tag_query.$tag->name.$tag_ops[$tag_ops_counter];
						$tag_ops_counter += 1;
					}
					$nice_tag_query_result = $nice_tag_query;
				}
				else {
					$nice_tag_query_result = single_tag_title('',false);
				}

				echo $nice_tag_query_result;
			}
		}
	
		//register sidebars
		function registerSidebars() {
			if ( function_exists('register_sidebar') ) {
			
				if ($this->options['headerWidgets'] == true) {
					register_sidebar(array(
						'name' => 'Header',
						'id' => 'header',
						'before_widget' => '<div id="%1$s" class="head-widget widget %2$s">', 
						'after_widget' => '</div>', 
						'before_title' => '<h5 class="widget-title">', 
						'after_title' => '</h5>', 
					));
				}

				if ($this->options['contentBWidgets'] == true) {
					register_sidebar(array(
						'name' => 'Sidebar A',
						'id' => 'sidebar-a',
						'before_widget' => '<div id="%1$s" class="widget %2$s">', 
						'after_widget' => '</div>', 
						'before_title' => '<h5 class="widget-title">', 
						'after_title' => '</h5>', 
					));
				}
			
				if ($this->options['contentCWidgets'] == true) {
					register_sidebar(array(
						'name' => 'Sidebar B',
						'id' => 'sidebar-b',
						'before_widget' => '<div id="%1$s" class="widget %2$s">', 
						'after_widget' => '</div>', 
						'before_title' => '<h5 class="widget-title">', 
						'after_title' => '</h5>', 
					));
				}

				if ($this->options['footerWidgets'] == true) {
					register_sidebar(array(
						'name' => 'Footer',
						'id' => 'footer',
						'before_widget' => '<div id="%1$s" class="foot-widget widget %2$s">', 
						'after_widget' => '</div>', 
						'before_title' => '<h5 class="widget-title">', 
						'after_title' => '</h5>', 
					));
				}

			}
			return;
		}
	
		//register menus
		function registerMenus(){
			if ( function_exists('register_nav_menus') ) {
				register_nav_menus( array(
					'header' => 'Header Navigation',
					'footer' => 'Footer Navigation'
				) );
			}
		}
	
		//filter the menu classes, get rid of surrounding ul
		function filterMenus($menu) {
			$menu = str_replace('current_page_item', 'on active current_page_item', $menu);
			$menu = str_replace('current-page-ancestor', 'on active current-page-ancestor', $menu);
			$menu = str_replace('current_page_parent', 'on active current_page_parent', $menu);
			$menu = str_replace('</ul>', '', $menu);
			$menu = preg_replace('(\<ul(/?[^\>]+)\>)', '',$menu);
			return $menu;
		}

		function filterHtmlTitle($title, $separator){
			//write out the html title, format similar to All In One SEO
			// SOURCE: twentyten theme
			if (is_feed()){
				return $title;
			}
			
			// The $paged global variable contains the page number of a listing of posts.
			// The $page global variable contains the page number of a single post that is paged.
			// We'll display whichever one applies, if we're not looking at the first page.
			global $paged, $page;

			if ( is_search() ) {
				// If we're a search, let's start over:
				$title = 'Search results for ' . get_search_query();
				// Add a page number if we're on page 2 or more:
				if ( $paged >= 2 )
					$title .= " $separator Page $paged";
				// Add the site name to the end:
				$title .= " $separator " . get_bloginfo( 'name', 'display' );
				// We're done. Let's send the new title back to wp_title():
				return $title;
			}
			
			if (is_404()) {
				//rewite request as words, as w/ all in one seo
				$request = htmlspecialchars($_SERVER['REQUEST_URI']);
				$request = str_replace('.html', ' ', $request);
				$request = str_replace('.htm', ' ', $request);
				$request = str_replace('.', ' ', $request);
				$request = str_replace('/', ' ', $request);
				$request_a = explode(' ', $request);
				$request_new = array();
				foreach ($request_a as $token) {
					$request_new[] = ucwords(trim($token));
				}
				$request = implode(' ', $request_new);
				
				$title = "Nothing found for $request $separator ";
			}
			
			if ( is_date() ) {
				if (is_day()) {
					$title = get_the_time(get_option('date_format')) . " $separator ";
				}
				elseif (is_month()) {
					$title = get_the_time('F Y') . " $separator ";
				}
				elseif (is_year()) {
					$title = get_the_time('Y') . " $separator ";
				}
				
			}

			// Otherwise, let's start by adding the site name to the end:
			$title .= get_bloginfo( 'name', 'display' );

			// If we have a site description and we're on the home/front page, add the description:
			$site_description = get_bloginfo( 'description', 'display' );
			if ( $site_description && ( is_home() || is_front_page() ) ) {
				$title .= " $separator " . $site_description;
			}

			// Add a page number if necessary:
			if ( $paged >= 2 || $page >= 2 )
				$title .= " $separator Page " . max( $paged, $page ) ;

			// Return the new title to wp_title():
			return $title;
		}

		function listPages($args = ''){
			/* similar to wp_list_pages with a number of changes
				- 'on active' classes added to current tree, eg class="on active current_page_ancestor etc"
				- able to show home page
			*/
			$defaults = array(
				'date_format' => get_option('date_format'),
				'image_replacement' => 0,
				'echo' => 1,
				'depth' => 2, 
				'show_date' => '',
				'child_of' => 0, 
				'exclude' => '',
				'title_li' => '', 
				'show_home' => 1,
				'authors' => '', 
				'sort_column' => 'menu_order, post_title',
				'link_before' => '', 
				'link_after' => ''
			);

			$r = wp_parse_args($args, $defaults);

			$menu = '';

			// Show Home in the menu
			if ( isset($r['show_home']) && ! empty($r['show_home']) ) {
				if ( true === $r['show_home'] || '1' === $r['show_home'] || 1 === $r['show_home'] )
					$text = 'Home';
				else
					$text = $r['show_home'];
				$class = 'class="page_item page-item-home"';
				if ( is_front_page() && !is_paged() )
					$class = 'class="page_item page-item-home current_page_item"';
				$menu .= '<li ' . $class . '><a href="' . home_url() . '">' . $r['link_before'] . $text . $r['link_after'] . '</a></li>';
				// If the front page is a page, add it to the exclude list
				if (get_option('show_on_front') == 'page') {
					if ( !empty( $list_args['exclude'] ) ) {
						$list_args['exclude'] .= ',';
					} else {
						$list_args['exclude'] = '';
					}
					$list_args['exclude'] .= get_option('page_on_front');
				}
			}



			$list_args = $r;
			$list_args['echo'] = 0;
			$menu .= wp_list_pages($list_args);
			$menu = str_replace('current_page_item', 'on active current_page_item', $menu);
			$menu = str_replace('current_page_ancestor', 'on active current_page_ancestor', $menu);

			if ( $r['echo'] )
				echo $menu;
			else
				return $menu;	
		}

		//cats and tags meow -- for cat/tag archives, list other cats/tags only
		
		function jsString($string){
			//takes a string and converts it for output to Javascript (escaped chars, etc)
			$string = preg_replace('!\s+!', ' ', $string);
			$string = trim($string);
		    return strtr($string, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/', ';'=>'\\;'));
		}
		
		function inlineFooterJs(){
			$showJs = false;
			$outputJs = '<script>var SOUPGIANT=SOUPGIANT||{};';
			foreach ($this->inlineFooterJSarray as $js) {
				$showJs = true;
				$outputJs .= $js;
			}
			$outputJs .= '</script>';
			if ($showJs) {
				echo $outputJs;
			}
		}
		
		function get_category_link($id){
			if (is_numeric($id)) {
				$cat_id = $id;
			}
			else {
				$cat_id = get_cat_ID( $id );
			}
			
			return get_category_link( $cat_id );
		}
		/* *************************
		**     CONTENT WRITERS    **
		************************* */
		
		
		function writePostHeader($post, $hx = 'h3') {
			global $authordata;
			?>
			<div><header>
				<<?php echo $hx;?> class="entry-title"><a href="<?php the_permalink();?>"><?php the_title()?></a></<?php echo $hx;?>>
				<p class="entry-meta">Posted on <span class="time pubdate"><time datetime="<?php the_time('c') ?>" pubdate class="entry-date"><?php the_time(get_option('date_format')); ?></time></span> by <span class="author vcard"><a class="url fn n" href="<?php echo get_author_posts_url( $authordata->ID, $authordata->user_nicename ); ?>" title="View all posts by <?php the_author(); ?>"><?php the_author(); ?></a></span></p>
			</header></div>
			<?php
		}
		
		function writePostContent($post) {
			?>
				<div class="entry-content"><section>
					<?php the_content('Continue reading "'.the_title('', '', false).'" &raquo;'); ?>
				</section></div>
			<?php
		}
		
		function writePostExcerpt($post) {
			?>
				<div class="entry-summary"><section>
					<?php the_excerpt(); ?>
				</section></div>
			<?php
		}
		
		function writePostFooter($post) {
			?>
				<div class="footer"><footer>
					<p class="entry-meta">Posted in <span class="cat-links"><?php the_category(', '); ?></span> &bull; 
					<?php edit_post_link('Edit', '', ' &bull; '); ?> 
					<?php the_tags('<span class="tag-links">Tagged: ', ', ', '</span> &bull; '); ?>
					<span class="comments-link">
						<?php
							if (('open' == $post->comment_status) || (get_comments_number() > 0)) {
								echo '<a href="';
								comments_link();
								echo '">';
								comments_number('Leave a comment','One comment','% comments');
								echo '</a>';
							}
							else {
								echo 'Comments are closed';
							}
						?>
					</span>
					</p>
				</footer></div>
			<?php
		}

		
		
		function writeArchivePost($post, $hx = 'h2') {
			?>
			<div id="post-<?php the_ID() ?>" <?php post_class('article5'); ?>><article>
				<?php
				$this->writePostHeader($post, $hx);
				$this->writePostContent($post);
				$this->writePostFooter($post);
				?>
			</article></div>
			<!-- //#post-<?php the_ID() ?> -->
			<?php			
		}		
		
		function writeIndexPost($post, $hx = 'h2') {
			$this->writeArchivePost($post, $hx);
		}
		
		function writeSearchPost($post, $hx = 'h2') {
			$this->writeArchivePost($post, $hx);			
		}
		
		function writeSinglePost($post, $hx = 'h1') {
			// This doesn't write out the header, it's done in single.php
			
			// $this->writePostHeader($post, $hx);
			$this->writePostContent($post);
			wp_link_pages('before=<div id="post-nav" class="page-nav post-nav nav">Pages:&after=</div>'); 
			$this->writePostFooter($post);
			?>
			<div id="page-nav" class="page-nav nav">
				<div class="page-nav-older"><?php previous_post_link('%link','<span class="direction">Previous post </span> <span class="title">%title</span>') ?></div>
				<div class="page-nav-newer"><?php next_post_link('%link', '<span class="direction">Next post </span> <span class="title">%title</span>') ?></div>
			</div>
			<!-- //#page-nav -->
			<?php
		}
		
		function writePagePost($post, $hx = 'h1') {
			// This doesn't write out the header, it's done in single.php
			
			// $this->writePostHeader($post, $hx);
			$this->writePostContent($post);
			wp_link_pages('before=<div id="post-nav" class="page-nav post-nav nav">Pages:&after=</div>'); 
		}

		function loginForm() {
			// Default login form, to override just redefine the variable in child functions
			// Any changes should be reflected in the custom.js output of the login form
			
			
			$loginForm = wp_login_form(array(
				'echo' => false,
				'value_remember' => 1,
				'label_remember' => 'Remember Me'
				));
				
			$loginForm = '<div id="wp-login-form">' . $loginForm;
			$loginForm .= '<div id="wp-login-form-utils"><a href="' . wp_lostpassword_url( get_permalink() ) .
					'" title="Lost your password?" id="wp-login-form-lost">Lost your password?</a>';
					
			$loginForm .= wp_register( ' ', ' ', false);
			$loginForm .= '</div>';
					
			
					
			//$this->inlineFooterJSarray[] = 'SOUPGIANT.wp_login_form = "' . $this->jsString($loginForm) . '";';
			
			
		}
		
		function commentTemplate($comment, $args, $depth) {
			$GLOBALS['comment'] = $comment;
			$GLOBALS['comment_depth'] = $depth;
			?>
			<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
				<div class="comment-head">
					<p class="comment-author vcard">
						<?php 
							if ($args['avatar_size'] != 0) {
								echo get_avatar($comment, $args['avatar_size']); 
							}
						?>
						<cite class="fn"><?php comment_author_link(); ?></cite> <span class="says">says:</span>
					</p>
					<p class="comment-meta">
						<a href="#comment-<?php comment_ID(); ?>" class="time"><time datetime="<?php comment_date('c') ?>" pubdate class="entry-date"><?php comment_date(); ?> at <?php comment_time(); ?></time></a>

						<?php edit_comment_link('(Edit)', ' <span class="comment-edit-link">', '</span>' ); ?>
					</p>
				</div>

				<div class="comment-body" id="c-body-<?php comment_ID(); ?>">
					<?php 

					if ($comment->comment_approved == '0') {
						echo '<p class="comment-moderation">Your comment is awaiting moderation.</p>';
					}
					comment_text(); 
					?>

				<?php // echo the comment reply link with help from Justin Tadlock http://justintadlock.com/ and Will Norris http://willnorris.com/
					if($args['type'] == 'all' || get_comment_type() == 'comment') :
						comment_reply_link(array_merge($args, array(
							'reply_text' => 'Reply to this comment', 
							'login_text' => 'Log in to reply.',
							'depth' => $depth,
							'before' => '<div class="reply">', 
							'after' => '</div>',
							'add_below' => 'c-body'
						)));
					endif;
				?>
				</div>
				<?php
		}
		
		function pingTemplate($comment, $args, $depth) {
			$GLOBALS['comment'] = $comment;
			$GLOBALS['comment_depth'] = $depth;
			?>
			<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
				<div class="comment-head">
					<p class="comment-author">
						<?php comment_author_link(); ?>
					</p>
					<p class="comment-meta">
						<a href="#comment-<?php comment_ID(); ?>"><?php comment_date(); ?> at <?php comment_time(); ?></a>

						<?php edit_comment_link('(Edit)', ' <span class="comment-edit-link">', '</span>' ); ?>
					</p>
				</div>

				<?php
		}
	}


} // function soup_setupParentThemeClass()


$soup = null;
function soup_initialiseSoupObject(){
	global $soup;
	// now need to initiate the soup object
	if (class_exists('SoupTheme')) {
		$soup = new SoupTheme();
	}
	else if (class_exists('SoupThemeParent')){
		$soup = new SoupThemeParent();
	}
	// else let it break
	
} //	function soup_initialiseSoupObject()


/* 
	need to reverse the order the function.php files usually run in
	parent's function.php needs to run before child's
*/
add_action('after_setup_theme', 'soup_setupParentThemeClass', 1);
// add_action('after_setup_theme', 'soup_setupChildThemeClass', 2); //reference: runs in child's function.php
add_action('after_setup_theme', 'soup_initialiseSoupObject', 3);


?>