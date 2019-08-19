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
                                                                <h1 style="color:#202020!important;font-family:Helvetica,Arial,sans-serif;font-size:26px;font-weight:bold;letter-spacing:-1px;line-height:115%;margin:0;padding:0;text-align:center"><?php echo $email_title; ?></h1>
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
                                               <?php echo $email_message ?>
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