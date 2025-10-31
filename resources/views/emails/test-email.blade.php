<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f8f9fa;font-family:'Cairo',Arial,sans-serif;color:#333;line-height:1.6;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f8f9fa;padding:20px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;background-color:#ffffff;border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#ffffff;padding:30px 20px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:bold;">{{ $siteName }}</h1>
                            <p style="margin:10px 0 0 0;font-size:14px;opacity:0.9;">رسالة من {{ $senderName }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 20px;">
                            <p style="font-size:18px;margin-bottom:20px;color:#2c3e50;">مرحباً {{ $recipientName }}،</p>
                            <div style="font-size:16px;line-height:1.8;margin-bottom:30px;color:#34495e;">
                                {!! $body !!}
                            </div>
                            <div style="height:2px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);margin:20px 0;"></div>
                            <div style="background-color:#fff3cd;padding:15px;border-radius:5px;border-right:4px solid #ffc107;margin:20px 0;font-size:14px;color:#856404;">
                                <strong>ملاحظة:</strong> هذه رسالة تلقائية من {{ $siteName }}. يرجى عدم الرد على هذا البريد الإلكتروني.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#f8f9fa;padding:20px;text-align:center;border-top:1px solid #e9ecef;">
                            <p style="font-size:14px;color:#6c757d;margin:0 0 10px 0;">
                                <strong>مرسل من:</strong> {{ $senderName }}<br>
                                <strong>تاريخ الإرسال:</strong> {{ $sentAt }}
                            </p>
                            <p style="font-size:12px;color:#adb5bd;margin:15px 0 0 0;">
                                © {{ date('Y') }} {{ $siteName }}. جميع الحقوق محفوظة.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>


