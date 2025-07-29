        <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#f5f6fa">
            <tr>
               <td style="padding: 40px 0;">
                    <table style="width:100%;max-width:620px;margin:0 auto;">
                        <tbody>
                            <tr>
                                <td style="text-align: center; padding-bottom:25px">
                                    <a href="<?=url('void')?>"><img style="height: 40px" src="<?=img()?>images/bookswap.png" alt="logo"></a>
                                    <p style="font-size: 14px; color: #6576ff; padding-top: 12px;"><?=$heading?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="width:100%;max-width:620px;margin:0 auto;background-color:#ffffff;">
                        <tbody>
                            <tr>
                                <td style="padding: 30px 30px 15px 30px;">
                                    <h2 style="font-size: 18px; color: #6576ff; font-weight: 600; margin: 0;"><?=$sub_heading?></h2>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0 30px 20px">
                                    <p style="margin-bottom: 10px;">Hi <?=ucwords($firstname)?>,</p>
                                    <p style="margin-bottom: 10px;">Welcome! <br> You are receiving this email because you have registered on our site.</p>
                                    <p style="margin-bottom: 10px;">Click the link below to verify your <?=config('app_name')?> account.</p>
                                    <p style="margin-bottom: 25px;">This link will expire in some few days and can only be used once.</p>
                                    <a href="<?=$url_slug?>" style="background-color:#6576ff;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0 30px">Confirm Your Account</a>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0 30px">
                                    <h4 style="font-size: 15px; color: #000000; font-weight: 600; margin: 0; text-transform: uppercase; margin-bottom: 10px">or</h4>
                                    <p style="margin-bottom: 10px;">If the button above does not work, paste this link into your web browser:</p>
                                    <a href="<?=url('void')?>" style="color: #6576ff; text-decoration:none;word-break: break-all;"><?=$url_slug?></a>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 20px 30px 40px">
                                    <p>If you did not make this request, please contact us or ignore this message.</p>
                                    <p style="margin: 0; font-size: 13px; line-height: 22px; color:#9ea8bb;">This is an automatically generated email please do not reply to this email. If you face any issues, please contact us at  support@seguah.com</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="width:100%;max-width:620px;margin:0 auto;">
                        <tbody>
                            <tr>
                                <td style="text-align: center; padding:25px 20px 0;">
                                    <p style="font-size: 13px;">Copyright &copy; <?=date('Y')?> <?=config('app_name')?>. All rights reserved. <br> A product of <a style="color: #6576ff; text-decoration:none;" href="https://seguah.com" target="_blank">Seguah Dreams Limited</a>.</p>
                                    
                                    <p style="padding-top: 15px; font-size: 12px;">This email was sent to you as a registered user of <a style="color: #6576ff; text-decoration:none;" href="https://bookswap.seguah.com">Seguah BookSwap</a>.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
               </td>
            </tr>
        </table>