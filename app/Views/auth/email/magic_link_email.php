<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="x-apple-disable-message-reformatting">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?= lang('Auth.magicLinkSubject') ?></title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f9; font-family:Helvetica,Arial,sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f9; padding:40px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px; width:100%;">

                <!-- Logo -->
                <tr>
                    <td align="center" style="padding-bottom:24px;">
                        <img src="https://digital.itn.lk/assets/media/logos/dark_logo.png"
                             alt="ITN Digital" width="160" style="display:block; border:0;">
                    </td>
                </tr>

                <!-- Card -->
                <tr>
                    <td style="background-color:#ffffff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,.08); overflow:hidden;">

                        <!-- Header bar -->
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="background-color:#AB2112; padding:24px 32px;">
                                    <p style="margin:0; font-size:18px; font-weight:700; color:#ffffff;">
                                        Your magic sign-in link
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Body -->
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding:32px;">

                                    <p style="margin:0 0 24px; font-size:15px; color:#374151; line-height:1.6;">
                                        Click the button below to sign in to ITN Digital. This link is valid for <strong>1 hour</strong> and can only be used once.
                                    </p>

                                    <!-- CTA Button -->
                                    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto 24px;">
                                        <tr>
                                            <td align="center" style="background-color:#AB2112; border-radius:6px;">
                                                <a href="<?= url_to('verify-magic-link') ?>?token=<?= $token ?>"
                                                   style="display:inline-block; padding:14px 32px; font-size:15px; font-weight:600; color:#ffffff; text-decoration:none; border-radius:6px; white-space:nowrap;">
                                                    Sign in to ITN Digital
                                                </a>
                                            </td>
                                        </tr>
                                    </table>

                                    <p style="margin:0 0 24px; font-size:13px; color:#6b7280; text-align:center;">
                                        If the button doesn't work, copy and paste this link into your browser:
                                    </p>
                                    <p style="margin:0 0 24px; font-size:12px; color:#AB2112; word-break:break-all; text-align:center;">
                                        <?= url_to('verify-magic-link') ?>?token=<?= $token ?>
                                    </p>

                                    <!-- Divider -->
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="border-top:1px solid #e5e7eb; padding-top:24px;">
                                                <p style="margin:0 0 8px; font-size:13px; font-weight:600; color:#374151;">
                                                    <?= lang('Auth.emailInfo') ?>
                                                </p>
                                                <p style="margin:0 0 4px; font-size:13px; color:#6b7280;">
                                                    <?= lang('Auth.emailIpAddress') ?> <?= esc($ipAddress) ?>
                                                </p>
                                                <p style="margin:0 0 4px; font-size:13px; color:#6b7280;">
                                                    <?= lang('Auth.emailDevice') ?> <?= esc($userAgent) ?>
                                                </p>
                                                <p style="margin:0; font-size:13px; color:#6b7280;">
                                                    <?= lang('Auth.emailDate') ?> <?= esc($date) ?>
                                                </p>
                                            </td>
                                        </tr>
                                    </table>

                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="padding:24px 0 0;">
                        <p style="margin:0; font-size:12px; color:#9ca3af;">
                            &copy; <?= date('Y') ?> ITN Digital. All rights reserved.
                        </p>
                        <p style="margin:4px 0 0; font-size:12px; color:#9ca3af;">
                            If you did not request this sign-in link, you can safely ignore this email.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
