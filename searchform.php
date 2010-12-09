<?php
//make sure id isn't specified twice on the same page
//no need to seed since php 4.2.0
$soup_safeIds = rand(1, 999999);
?>
<form method="get" action="<?php echo home_url();; ?>" class="search-form">
<div>
	<label for="s-<?php echo $soup_safeIds; ?>" class="search-label">Search this Site</label>
	<input type="text" name="s" id="s-<?php echo $soup_safeIds; ?>" class="searchInput inputText" />
	<input type="submit" value="Search" class="searchSubmit inputSubmit" />
</div>
</form>