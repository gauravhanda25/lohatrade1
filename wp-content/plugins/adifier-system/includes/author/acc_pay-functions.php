<?php
/*
* Get packages
*/
if( !function_exists('adifier_get_packs') ){
function adifier_get_packs( $pack, $include_free = false ){
	$packages = adifier_get_option( $pack );
	$tax = adifier_get_option( 'tax' );
	$packs = array();
	if( $include_free === true ){
		if( $pack == 'packages' ){
			$free = adifier_get_option( 'package_free_ads' );
			if( !empty( $free ) ){
				$packs[] = array(
					'name'		=> esc_html__( 'Free', 'adifier' ),
					'price'		=> '-',
					'adverts'	=> $free
				);
			}
		}
		else if( $pack == 'subscriptions' ){
			$free = adifier_get_option( 'subscription_free_time' );
			if( !empty( $free ) ){
				$packs[] = array(
					'name'		=> esc_html__( 'Free', 'adifier' ),
					'price'		=> '-',
					'days'		=> $free
				);
			}
		}
		else if( $pack == 'hybrids' ){
			$free = adifier_get_option( 'hybrid_free_stuff' );
			if( !empty( $free ) ){
				$temp = explode( '|', $free );
				$packs[] = array(
					'name'		=> esc_html__( 'Free', 'adifier' ),
					'price'		=> '-',
					'adverts'	=> !empty( $temp[0] ) ? $temp[0] : '',
					'days'		=> !empty( $temp[1] ) ? $temp[1] : ''
				);
			}
		}
	}
	if( !empty( $packages ) ){
		$packages = explode( '||', $packages );
		foreach( $packages as $package ){
			$temp = explode( '|', $package );
			$price = $pack == 'hybrids' ? $temp[3] : $temp[2];
			$packs[] = array(
				'name'		=> $temp[0],
				'price'		=> !empty( $tax ) ? $price * ( 1 + $tax/100 ) : $price,
			);
			if( $pack == 'packages' ){
				$packs[sizeof( $packs )-1]['adverts'] = $temp[1];
			}
			else if( $pack == 'subscriptions' ){
				$packs[sizeof( $packs )-1]['days'] = $temp[1];	
			}
			else if( $pack == 'hybrids' ){
				$packs[sizeof( $packs )-1]['adverts'] = $temp[1];
				$packs[sizeof( $packs )-1]['days'] = $temp[2];	
			}
		}
	}
	return $packs;
}
}

/*
* Create text for pricing tables
*/
if( !function_exists('adifier_packs_message') ){
function adifier_packs_message( $account_payment, $pack ){
	if( $account_payment == 'packages' ){
		if( $pack['price'] == '-' ){
			echo sprintf( __( 'Once you create an account you will be able to submit <b>%s</b> ads for free', 'adifier'), $pack['adverts'] );
		}
		else{
			echo sprintf( __( 'You will be able to submit up to <b>%s</b> ads for an unlimited time', 'adifier'), $pack['adverts'] );
		}
	}
	else if( $account_payment == 'subscriptions' ){
		if( $pack['price'] == '-' ){
			$time = strstr( $pack['days'], '+' ) ? '<b>'.str_replce('+', '', $pack['days']).'</b> '.esc_html__( 'hours', 'adifier' ) : '<b>'.$pack['days'].'</b> '.esc_html__( 'days', 'adifier' );
			echo sprintf( __( 'You will be able to post an unlimited number of ads for the period of %s', 'adifier'), $time );
		}
		else{
			echo sprintf( __( 'You will be able to post an unlimited number of ads for the period of <b>%s</b> days', 'adifier'), $pack['days'] );
		}
	}
	else if( $account_payment == 'hybrids' ){
		$period = esc_html__( 'days', 'adifier' );
		if( strpos( $pack['days'], '+') !== false ){
			$period = str_replace( '+', '', $pack['days'] ) == 1 ? esc_html__( 'hour', 'adifier' ) : esc_html__( 'hours', 'adifier' );
		}
		echo sprintf( __( 'You will be able to submit up to <b>%s</b> ads for the period of <b>%s</b> %s', 'adifier'), $pack['adverts'], str_replace( '+', '', $pack['days'] ), $period );
	}
}
}
?>