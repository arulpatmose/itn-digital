<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en">

<head>
    <meta name="x-apple-disable-message-reformatting">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Test Email — <?= site_name() ?></title>
</head>

<body style="margin:0; padding:0; background:#fff; font-family: Arial, sans-serif; color: #2F2F3B;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#fff; padding: 20px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="border:1px solid #2f2f3b; border-radius: 8px; overflow:hidden;">

                    <!-- Header -->
                    <tr style="background:#2f2f3b;">
                        <td style="padding: 20px; text-align: center;">
                            <img src="<?= base_url('assets/media/logos/dark_logo.png') ?>" alt="<?= site_name() ?> Logo" width="150" style="display:block; margin:0 auto;">
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px; font-size: 16px; line-height: 1.6;">

                            <p style="margin: 0 0 16px;">Hello,</p>

                            <p style="margin: 0 0 24px;">
                                This is a <strong>test email</strong> sent from the <strong><?= site_name() ?></strong> admin panel to confirm that your email configuration is working correctly.
                            </p>

                            <!-- Success box -->
                            <div style="background:#f0fdf4; border-left: 4px solid #16a34a; padding: 16px 20px; border-radius: 4px; margin: 0 0 24px;">
                                <p style="margin: 0 0 6px; font-weight: bold; color: #15803d;">&#10003; Email delivery is working</p>
                                <p style="margin: 0; font-size: 14px; color: #555;">
                                    Sent to: <strong><?= esc($recipientEmail) ?></strong><br>
                                    Sent at: <?= date('d M Y, H:i:s T') ?>
                                </p>
                            </div>

                            <!-- Config summary -->
                            <p style="margin: 0 0 10px; font-size: 14px; color: #888; text-transform: uppercase; letter-spacing: 1px; font-weight: bold;">Configuration Used</p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fa; border-radius: 6px; padding: 16px; margin: 0 0 24px; font-size: 14px; color: #555;">
                                <?php
                                helper('function');
                                $cfg = get_settings('email', true);
                                ?>
                                <tr>
                                    <td style="padding: 4px 0; width: 160px; color: #888;">Protocol</td>
                                    <td style="padding: 4px 0;"><strong><?= esc($cfg['protocol'] ?? '—') ?></strong></td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; color: #888;">SMTP Host</td>
                                    <td style="padding: 4px 0;"><strong><?= esc($cfg['SMTPHost'] ?? '—') ?></strong></td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; color: #888;">SMTP Port</td>
                                    <td style="padding: 4px 0;"><strong><?= esc($cfg['SMTPPort'] ?? '—') ?></strong></td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; color: #888;">Encryption</td>
                                    <td style="padding: 4px 0;"><strong><?= esc($cfg['SMTPCrypto'] ?? '—') ?></strong></td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; color: #888;">From</td>
                                    <td style="padding: 4px 0;"><strong><?= esc(($cfg['fromName'] ?? '') . ' &lt;' . ($cfg['fromEmail'] ?? '') . '&gt;') ?></strong></td>
                                </tr>
                            </table>

                            <p style="margin: 0; font-size: 14px; color: #999;">
                                If you received this email unexpectedly, no action is required — it was sent by an administrator testing the email configuration.
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr style="background:#2F2F3B; color:#fff; text-align:center; font-size:14px;">
                        <td style="padding: 15px;">
                            &copy; <?= date('Y') ?> <?= site_name() ?>. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>