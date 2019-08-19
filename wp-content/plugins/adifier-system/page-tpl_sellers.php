<?php
/*
	Template Name: Sellers
*/
get_header();
the_post();

include( get_theme_file_path( 'includes/headers/breadcrumbs.php' ) );
include( get_theme_file_path( 'includes/headers/header-search.php' ) );
include( get_theme_file_path( 'includes/headers/gads.php' ) );

$number = 21;
$cur_page = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1; //get curent page
$offset = ( $cur_page - 1 ) * $number;  
$args = array(
	'orderby' 				=> 'display_name',
	'order' 				=> 'ASC',	
	'count_total' 			=> true,
	'number' 				=> $number,
	'offset' 				=> $offset,
	'has_published_posts'	=> 'advert'
);
$users = new WP_User_Query( $args );
$total_users = $users->get_total();
$total_pages = ceil( $total_users / $number );


$pagination = paginate_links( 
	array(
		'prev_next' 	=> true,
		'end_size' 		=> 2,
		'mid_size' 		=> 2,
		'total' 		=> $total_pages,
		'current' 		=> $cur_page,	
		'prev_next' 	=> false
	)
);
?>
<main>
	<div class="container">
		<div class="af-items-3">
			<?php   
			if ( !empty( $users->results ) ) {
				foreach( $users->results as $user ){
					?>
					<div class="af-item-wrap">
						<div class="white-block">
							<div class="white-block-content">
								<div class="seller-details flex-wrap flex-start-h">
									<a href="<?php echo get_author_posts_url( $user->ID ) ?>" class="avatar-wrap">
										<?php echo get_avatar( $user->ID, 70 ); ?>
									</a>

									<div class="seller-name">
										<h5>
											<a href="<?php echo get_author_posts_url( $user->ID ) ?>">
												<?php echo adifier_author_name( $user )  ?>
											</a>
										</h5>
										<?php adifier_user_rating( $user->ID ); ?>
										<?php echo adifier_seller_online_status( $user->ID ) ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
			}
			?>
		</div>
		<?php
		if( !empty( $pagination ) ){
			?>
			<div class="pagination">
				<?php echo $pagination ?>
			</div>
			<?php
		}
		?>		
	</div>
</main>

<?php get_footer(); ?>