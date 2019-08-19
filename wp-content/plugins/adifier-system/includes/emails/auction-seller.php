<table style="background-color:#f2f2f2" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
    <tbody>
        <tr>
           <td style="padding:40px 20px" align="center" valign="top">
               <table style="width:600px" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td style="padding-bottom:30px" align="center" valign="top">
                                <table style="background-color:#ffffff;border-collapse:separate!important;border-radius:4px" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tbody>
                                        <tr>
                                           <td style="padding-top:40px;padding-right:40px;padding-bottom:0;padding-left:40px" align="center" valign="top">
                                               <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td style="padding-bottom:40px;color:#606060;font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:150%;text-align:center" align="center" valign="middle">
                                                                <h1 style="color:#202020!important;font-family:Helvetica,Arial,sans-serif;font-size:26px;font-weight:bold;letter-spacing:-1px;line-height:115%;margin:0;padding:0;text-align:center"><?php esc_html_e( 'Congratulations', 'adifier'); ?></h1>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align:center;" align="center" valign="top">
                                                                <?php include( get_theme_file_path( 'includes/emails/email-logo.php' ) ); ?>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="color:#606060;font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:150%;padding-top:40px;padding-right:40px;padding-bottom:20px;padding-left:40px;text-align:center" align="center" valign="top">
                                               <?php echo sprintf(esc_html__( 'Buyer %s has offered highest bid on your auction %s, go to your ad by clicking button below and contact buyer in the next 24h', 'adifier' ), '<strong>'.$buyer_name.'</strong>', '<strong>'.$auction->post_title.'</strong>'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-right:40px;padding-bottom:40px;padding-left:40px" align="center" valign="middle">
                                                <table style="background-color:<?php echo adifier_get_option( 'main_color' ) ?>;border-collapse:separate!important;border-radius:3px" border="0" cellpadding="0" cellspacing="0">
                                                    <tbody>
                                                        <tr>
                                                            <td style="color:#ffffff;font-family:Helvetica,Arial,sans-serif;font-size:15px;font-weight:bold;line-height:100%" align="center" valign="middle">
                                                                <a href="<?php echo esc_url( add_query_arg( array( 'screen' => 'edit', 'id' => $auction->ID ), get_author_posts_url( $seller->ID ) ) ) ?>" style="color:<?php echo adifier_get_option( 'main_color_font' ); ?>;text-decoration:none;padding-top:15px;padding-right:20px;padding-bottom:15px;padding-left:20px;display:block;" target="_blank"><?php esc_html_e( 'Go To Auction', 'adifier' ) ?></a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>