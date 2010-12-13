<?php
global $soup;
get_header();
the_post();
?>

<div id="content">
	<div id="contentHeadA" class="article5"><article>
		<div id="contentHead"><header>
			<<?php echo $soup->pageNameTag; ?> id="pageName" class="entry-title">
				File Not Found
			</<?php echo $soup->pageNameTag; ?>>
		</header></div>			
		
		<div id="contentA">


			<p>Apologies, but we were unable to find what you were looking for. Perhaps  searching will help.</p>
			
			<form method="get" action="<?php echo home_url(); ?>" class="search-form search-404">
				
				<div>
					<label for="s-404notfound" class="search-label">Search this Site</label>
					<input type="text" name="s" id="s-404notfound" class="search-input" />
					<input type="submit" value="Search" class="search-submit" />
				</div>
			</form>
			
			
		</div>
		<!-- //#contentA -->
	</article></div
	<!-- //#contentHeadA -->
<?php
get_sidebar();
//#content [get_sidebar() closes div #content]
?>			



<?
get_footer();
?>