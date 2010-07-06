<?php
/*
Note:
Function file load order:
	1) child
	2) parent
*/


function soup_setupParentThemeClass(){
	Class SoupThemeParent {

		public $yuiBase;
		public $parent;
		public $child;
	
	 	public $postAlt;
	
	
	

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
			$this->defineYuiBase();
			$this->defineMinimisedCode();
			$this->defineParentVersions();
			$this->defineChildVersions();
		
			$this->postAlt = 1;		
			$this->initTheme();
		
		
		}
	
		function initTheme() {
			//add hooks, filters, etc
		
			$this->registerSidebars();
			$this->registerMenus();
		
			add_action('wp_head', array($this, 'favIcon'));
		
			add_action('wp_print_styles', array($this,'registerCSS'), 50);
			add_action('wp_print_styles', array($this,'registerJS'),  50);
			add_action('wp_print_styles', array($this,'registerAdditionalCSSandJS'),  75);
			add_action('wp_print_styles', array($this,'enqueueCSS'), 100);		
			add_action('wp_print_styles', array($this,'enqueueJS'),  100);
		
			add_filter('script_loader_src', array($this, 'removeVersionQstring'));
			add_filter('style_loader_src', array($this, 'removeVersionQstring'));

			add_filter('wp_nav_menu', array($this, 'filterMenus'));

		
		}
    
		function defineParentURLs(){
			$this->parent = array(
				'url'	=> get_bloginfo('template_directory'),
				'assets'=> get_bloginfo('template_directory') . '/assets/parent',
				'css'	=> get_bloginfo('template_directory') . '/assets/parent/c',
				'js'	=> get_bloginfo('template_directory') . '/assets/parent/j',
				'img'	=> get_bloginfo('template_directory') . '/assets/parent/i',
				'php'	=> get_bloginfo('template_directory') . '/assets/parent/p',
				'phpPath' => TEMPLATEPATH . '/assets/parent/p'
			);		
		}
	
		function defineChildUrls() {
			$this->child = array(
				'url'	=> get_bloginfo('stylesheet_directory'),
				'assets'=> get_bloginfo('stylesheet_directory') . '/assets/child',
				'css'	=> get_bloginfo('stylesheet_directory') . '/assets/child/c',
				'js'	=> get_bloginfo('stylesheet_directory') . '/assets/child/j',
				'img'	=> get_bloginfo('stylesheet_directory') . '/assets/child/i',
				'php'	=> get_bloginfo('stylesheet_directory') . '/assets/child/p',
				'phpPath' => STYLESHEETPATH . '/assets/parent/p'
			);		
		}
	
		function defineMinimisedCode() {
			$this->parent['mincss'] = true;
			$this->parent['minjs'] = true;

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
		}
	
		function defineYuiBase() {
			if ($this->isSSL() == true) {
				$this->yuiBase .= 'https://';
			}
			else {
				$this->yuiBase .= 'http://';			
			}
			$this->yuiBase .= 'ajax.googleapis.com/ajax/libs/yui/2.7.0/build';
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


		//register styles
		function registerCSS(){
			global $wp_styles;
			if ($this->parent['mincss'] === false) {
				$psuffix = '';
			
			
				wp_register_style(
					'yui-reset',
					$this->yuiBase . '/reset/reset.css',
					null,
					'2.7.0',
					'all'
				);
			
				wp_register_style(
					'yui-reset-fonts',
					$this->yuiBase . '/fonts/fonts.css',
					array('yui-reset'),
					'2.7.0'
				);
			
			
			} else {
				$psuffix = '-min';
			
				wp_register_style(
					'yui-reset-fonts',
					$this->yuiBase . '/reset-fonts/reset-fonts.css',
					null,
					'2.7.0',
					'all'
				);
			
			
			}
		
			if ($this->child['mincss'] === true) {
				$csuffix = '-min';
			}
			else {
				$csuffix = '';
			}
		
			wp_register_style(
				'yui-base',
				$this->yuiBase . "/base/base$psuffix.css",
				array('yui-reset-fonts'),
				'2.7.0',
				'all'
			);
			
			//register pretty photo css
			wp_register_style(
				'prettyPhoto-css',
				$this->parent['css'] . "/prettyphoto$psuffix.css",
				null,
				'2.5.6',
				'all'
			);
	

	
			//register all-media child styles
			wp_register_style(
				'soup-all',
				$this->child['css'] . "/all/all$csuffix.css",
				array('yui-base'),
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
				'handheld, only screen and (max-device-width: 480px)'
			);	
	
			//register print-media child styles
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
			
		}
	
		function enqueueCSS() {  
			//usually overwritten by child
			if (!is_admin()) :
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
						
			endif; //if (!is_admin()):
		
		}
	
		function registerJS() {
			global $wp_scripts;
			if ($this->parent['minjs'] === false) {
				$psuffix = '';
				if ($this->isSSL() == true) {
					$validatorURL = 'https://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.js';
				}
				else {
					$validatorURL = 'http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.js';			
				}
			}
			else {
				$psuffix = '-min';
				if ($this->isSSL() == true) {
					$validatorURL = 'https://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js';
				}
				else {
					$validatorURL = 'http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js';			
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
				'custom',
				$this->child['js'] . "/custom$csuffix.js",
				array('jquery', 'soup-base'),
				$this->child['jsVer'],
				true
			);
		
		
			$this->commentReply();
		
		}

		function registerAdditionalCSSandJS() {
			// this is designed for child to register additional files without 
			// having to regregister them all
			return true;
		}

		function enqueueJS(){
			//this function is usually overwritten in child
			wp_enqueue_script('custom');
			wp_enqueue_script('prettyPhoto');
			wp_enqueue_script('form-validation');
			wp_enqueue_script('hashchange');
		
			if (wp_script_is('prettyPhoto') == true) {
				wp_enqueue_style('prettyPhoto-css');
			}
		
		}

		function removeVersionQstring($src){
			if ( preg_match( '/ajax\.googleapis\.com\/|ajax\.microsoft\.com\//', $src ) )
				$src = remove_query_arg('ver',$src);
			return $src;
		}
	
		function commentReply() {
			if ((!is_admin()) AND is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
				wp_enqueue_script( 'comment-reply' );
			}
		}

		function bodyClass($print = true) {
			//set classes on <body> tag
			// based on same function in Sandbox
			global $wp_query, $current_user;
			//much of this function is sourced from the sandbox_body_class from the sandbox theme

			$c = array();
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
			// Special classes for BODY element when a single post
			if ( is_single() ) {
				$postSlug = $wp_query->post->post_name;
				$postID = $wp_query->post->ID;
				the_post();

				// Adds 'post' class and class with the post ID
				$c[] = 'bxAllBlog bxPost bxP-' . $postSlug . ' bxP-id' . $postID;
				// Adds category classes for each category on single posts
				if ( $cats = get_the_category() )
					foreach ( $cats as $cat )
						$c[] = 'bxPC-' . $cat->slug;

				// Adds tag classes for each tags on single posts
				if ( $tags = get_the_tags() )
					foreach ( $tags as $tag )
						$c[] = 'bxPTag-' . $tag->slug;

				// Adds MIME-specific classes for attachments
				if ( is_attachment() ) {
					$mime_type = get_post_mime_type();
					$mime_prefix = array( 'application/', 'image/', 'text/', 'audio/', 'video/', 'music/' );
						$c[] = 'bxAtt-' . $postSlug . ' bxAtt-' . str_replace( $mime_prefix, "", "$mime_type" );
				}

				// Adds author class for the post author
				$c[] = 'bxPA-' . sanitize_title_with_dashes(strtolower(get_the_author_login()));
				rewind_posts();
			}
			// Author name classes for BODY on author archives
			elseif ( is_author() ) {
				$author = $wp_query->get_queried_object();
				$c[] = 'bxAuthor';
				$c[] = 'bxA-' . $author->user_nicename;
			}

			// Category name classes for BODY on category archvies
			elseif ( is_category() ) {
				$cat = $wp_query->get_queried_object();
				$c[] = 'bxCat';
				$c[] = 'bxC-' . $cat->slug;
			}

			// Tag name classes for BODY on tag archives
			elseif ( is_tag() ) {
				$tags = $wp_query->get_queried_object();
				$c[] = 'bxTag';
				$c[] = 'bxTag-' . $tags->slug;
			}

			// Page author for BODY on 'pages'
			elseif ( is_page() ) {
				$pageID = $wp_query->post->ID;
				$pageSlug = $wp_query->post->post_name;
				$page_children = wp_list_pages("child_of=$pageID&echo=0");
				the_post();
				$c[] = 'bxPage bxPg-' . $pageSlug;
				$c[] = 'bxPgA-' . sanitize_title_with_dashes(strtolower(get_the_author('login')));
				// Checks to see if the page has children and/or is a child page; props to Adam
				if ( $page_children )
					$c[] = 'bxPgTree-' . $pageID;
				if ( $wp_query->post->post_parent )
					$c[] = 'bxPgChild bxPgTree-' . $wp_query->post->post_parent;
				if ( is_page_template() ) // Hat tip to Ian, themeshaper.com
					$c[] = 'bxPgTemplate bxPgT-' . str_replace( '.php', '', get_post_meta( $pageID, '_wp_page_template', true ) );
				rewind_posts();
			}

			// Search classes for results or no results
			elseif ( is_search() ) {
				the_post();
				if ( have_posts() ) {
					$c[] = 'bxSearchResults';
				} else {
					$c[] = 'bxSearchNil';
				}
				rewind_posts();
			}

			// For when a visitor is logged in while browsing
			if ( $current_user->ID )
				$c[] = 'bxLoggedIn';


			// Separates classes with a single space, collates classes for BODY
			$c = join( ' ', apply_filters( 'body_class',  $c ) ); // Available filter: body_class

			// And tada!
			return $print ? print($c) : $c;		
		}

		function postClass($print = true, $additionalClasses = null) {
			//set classes on post's <div> tag
			// sourced from the sandbox theme
			global $post;

			// hentry for hAtom compliace, gets 'alt' for every other post DIV, describes the post type and p[n]
			$c = array( 'hentry', "p$this->postAlt", $post->post_type, $post->post_status );

			if ($additionalClasses) {
				$c[] = $additionalClasses;
			}

			// Post ID
			$c[] = 'post-' . $post->post_name;

			// Author for the post queried
			$c[] = 'author-' . sanitize_title_with_dashes(strtolower(get_the_author('login')));

			// Category for the post queried
			foreach ( (array) get_the_category() as $cat )
				$c[] = 'cat-' . $cat->slug;

			// Tags for the post queried; if not tagged, use .untagged
			if ( get_the_tags() == null ) {
				$c[] = 'untagged';
			} else {
				$c[] = 'tagged';
				foreach ( (array) get_the_tags() as $tag )
					$c[] = 'tag-' . $tag->slug;
			}

			// For password-protected posts
			if ( $post->post_password )
				$c[] = 'protected';

			// Applies the time- and date-based classes (below) to post DIV
			//sandbox_date_classes( mysql2date( 'U', $post->post_date ), $c );

			// If it's the other to the every, then add 'alt' class
			if ( ++$this->postAlt % 2 )
				$c[] = 'alt';

			// Separates classes with a single space, collates classes for post DIV
			$c = join( ' ', apply_filters( 'post_class', $c ) ); // Available filter: post_class

			// And tada!
			return $print ? print($c) : $c;
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
			
				register_sidebar(array(
					'name' => 'Sidebar A',
					'id' => 'sidebar-a',
					'before_widget' => '<div id="%1$s" class="widget %2$s">', 
					'after_widget' => '</div>', 
					'before_title' => '<h5 class="widget-title">', 
					'after_title' => '</h5>', 
				));
			
				register_sidebar(array(
					'name' => 'Sidebar B',
					'id' => 'sidebar-b',
					'before_widget' => '<div id="%1$s" class="widget %2$s">', 
					'after_widget' => '</div>', 
					'before_title' => '<h5 class="widget-title">', 
					'after_title' => '</h5>', 
				));

				register_sidebar(array(
					'name' => 'Header',
					'id' => 'header',
					'before_widget' => '<div id="%1$s" class="head-widget widget %2$s">', 
					'after_widget' => '</div>', 
					'before_title' => '<h5 class="widget-title">', 
					'after_title' => '</h5>', 
				));

				register_sidebar(array(
					'name' => 'Footer',
					'id' => 'footer',
					'before_widget' => '<div id="%1$s" class="foot-widget widget %2$s">', 
					'after_widget' => '</div>', 
					'before_title' => '<h5 class="widget-title">', 
					'after_title' => '</h5>', 
				));

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
			$menu = str_replace('current_page_ancestor', 'on active current_page_ancestor', $menu);
			$menu = str_replace('</ul>', '', $menu);
			$menu = preg_replace('(\<ul(/?[^\>]+)\>)', '',$menu);
			return $menu;
		}
	
		//list pages

		//cats and tags meow -- for cat/tag archives, list other cats/tags only
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
add_action('init', 'soup_setupParentThemeClass', 1);
// add_action('init', 'soup_setupChildThemeClass', 2); //reference: runs in child's function.php
add_action('init', 'soup_initialiseSoupObject', 3);


?>