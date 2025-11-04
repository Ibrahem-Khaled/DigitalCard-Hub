<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعادة تعيين كلمة المرور</title>
</head>
<body style="margin:0;padding:0;background-color:#f8f9fa;font-family:'Cairo',Arial,sans-serif;color:#333;line-height:1.6;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f8f9fa;padding:20px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;background-color:#ffffff;border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#ffffff;padding:30px 20px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:bold;">{{ $siteName }}</h1>
                            <p style="margin:10px 0 0 0;font-size:14px;opacity:0.9;">إعادة تعيين كلمة المرور</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 20px;">
                            <p style="font-size:18px;margin-bottom:20px;color:#2c3e50;">مرحباً {{ $user->full_name }}،</p>
                            
                            <p style="font-size:16px;line-height:1.8;margin-bottom:20px;color:#34495e;">
                                تم طلب إعادة تعيين كلمة المرور لحسابك في {{ $siteName }}. اضغط على الزر أدناه لإعادة تعيين كلمة المرور الخاصة بك:
                            </p>

                            <div style="text-align:center;margin:30px 0;">
                                <a href="{{ $resetUrl }}" 
                                   style="display:inline-block;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#ffffff;padding:15px 40px;text-decoration:none;border-radius:8px;font-weight:bold;font-size:16px;">
                                    إعادة تعيين كلمة المرور
                                </a>
                            </div>

                            <p style="font-size:14px;line-height:1.8;margin-bottom:20px;color:#7f8c8d;">
                                أو يمكنك نسخ الرابط التالي ولصقه في المتصفح:
                            </p>
                            <p style="font-size:12px;word-break:break-all;color:#95a5a6;background-color:#ecf0f1;padding:10px;border-radius:5px;">
                                {{ $resetUrl }}
                            </p>

                            <div style="background-color:#fff3cd;padding:15px;border-radius:5px;border-right:4px solid #ffc107;margin:20px 0;font-size:14px;color:#856404;">
                                <strong>تنبيه مهم:</strong>
                                <ul style="margin:10px 0 0 20px;padding:0;">
                                    <li>الرابط صالح لمدة ساعة واحدة فقط</li>
                                    <li>إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذه الرسالة</li>
                                    <li>لا تشارك هذا الرابط مع أي شخص آخر</li>
                                </ul>
                            </div>

                            <div style="height:2px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);margin:20px 0;"></div>
                            
                            <p style="font-size:14px;color:#6c757d;margin:20px 0 0 0;text-align:center;">
                                هذه رسالة تلقائية من {{ $siteName }}. يرجى عدم الرد على هذا البريد الإلكتروني.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#f8f9fa;padding:20px;text-align:center;border-top:1px solid #e9ecef;">
                            <p style="font-size:12px;color:#adb5bd;margin:0;">
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


