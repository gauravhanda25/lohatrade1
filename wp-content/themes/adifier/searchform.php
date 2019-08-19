<form method="get" class="searchform" action="<?php echo esc_url( home_url('/') ); ?>">
	<div class="adifier-form">
		<input type="text" value="" name="s" placeholder="<?php esc_attr_e( 'Search for...', 'adifier' ); ?>">
		<input type="hidden" value="post" name="post_type">
		<a href="javascript:void(0);" class="submit-form"><i class="aficon-search"></i></a>
	</div>
</form>