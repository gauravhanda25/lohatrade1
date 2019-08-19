<?php if( adifier_get_option( 'show_breadcrumbs' ) == 'yes' ): ?>
	<div class="page-title <?php echo esc_attr( adifier_get_option( 'breadcrumbs_style' ) ) ?>">
		<div class="container">
			<div class="flex-wrap">
				<?php if( adifier_get_option( 'breadcrumbs_style' ) == 'quick-search' ): ?>
					<div class="flex-left">
						<h1 class="h4-size"><?php adifier_breadcrumbs_title(); ?></h1>
						<?php echo adifier_breadcrumbs() ?>
					</div>
					<div class="flex-right">
						<a href="#" title="<?php esc_attr_e( 'Quick Search', 'adifier' ) ?>" data-toggle="modal" data-target="#quick-search">
							<i class="aficon-binoculars"></i>
						</a>
					</div>				
				<?php else: ?>					
					<div class="flex-left">
						<h1 class="h4-size"><?php adifier_breadcrumbs_title(); ?></h1>
					</div>
					<div class="flex-right">
						<?php echo adifier_breadcrumbs() ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php endif; ?>