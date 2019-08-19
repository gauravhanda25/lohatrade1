<div class="navigation-wrap">
	<ul class="navigation list-inline list-unstyled">
		<?php
		if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ 'main-navigation' ] ) ) {
			wp_nav_menu( array(
				'theme_location'  	=> 'main-navigation',
				'container'			=> false,
				'echo'          	=> true,
				'items_wrap'        => '%3$s',
				'walker' 			=> new adifier_walker
			) );
		}
		?>
	</ul>
</div>