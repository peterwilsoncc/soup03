<?php
global $soup;
//div: #content is already open
?>
	<div id="contentB" class="sidebar">
		<?php
		
			if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-a') ) {
				//insert static sidebar
			}
		
		?>
	</div>
	<!-- //#contentB .sidebar -->

</div>
<!-- //#content -->

<div id="contentC" class="sidebar">
	<?php
	
		if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-b') ) {
			//insert static sidebar
		}
	
	?>
</div>
<!-- //#contentC .sidebar -->
