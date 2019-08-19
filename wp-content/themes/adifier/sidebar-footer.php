<?php if( is_active_sidebar( 'bottom-1' ) || is_active_sidebar( 'bottom-2' ) || is_active_sidebar( 'bottom-3' ) ): ?>
	<div class="bottom-sidebar-wrap">
		<div class="container">
			<div class="row">
				<div class="col-sm-4">
					<?php dynamic_sidebar( 'bottom-1' ) ?>
				</div>
				<div class="col-sm-4">
					<?php dynamic_sidebar( 'bottom-2' ) ?>
				</div>
				<div class="col-sm-4">
					<?php dynamic_sidebar( 'bottom-3' ) ?>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>