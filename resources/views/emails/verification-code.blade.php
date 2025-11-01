<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كود التحقق</title>
</head>
<body style="margin:0;padding:0;background-color:#f8f9fa;font-family:'Cairo',Arial,sans-serif;color:#333;line-height:1.6;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f8f9fa;padding:20px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;background-color:#ffffff;border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#ffffff;padding:30px 20px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:bold;">{{ $siteName }}</h1>
                            <p style="margin:10px 0 0 0;font-size:14px;opacity:0.9;">كود التحقق</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 20px;">
                            <p style="font-size:18px;margin-bottom:20px;color:#2c3e50;">مرحباً {{ $user->full_name }}،</p>
                            
                            @if($type === 'registration')
                                <p style="font-size:16px;line-height:1.8;margin-bottom:20px;color:#34495e;">
                                    شكراً لك على إنشاء حسابك في {{ $siteName }}. لإكمال عملية التسجيل، يرجى إدخال كود التحقق التالي:
                                </p>
                            @else
                                <p style="font-size:16px;line-height:1.8;margin-bottom:20px;color:#34495e;">
                                    تم طلب كود التحقق لتسجيل الدخول إلى حسابك في {{ $siteName }}. يرجى إدخال كود التحقق التالي:
                                </p>
                            @endif

                            <div style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:25px;border-radius:10px;text-align:center;margin:30px 0;">
                                <p style="margin:0 0 10px 0;font-size:14px;color:#ffffff;opacity:0.9;">كود التحقق</p>
                                <p style="margin:0;font-size:36px;font-weight:bold;color:#ffffff;letter-spacing:8px;">{{ $code }}</p>
                            </div>

                            <div style="background-color:#fff3cd;padding:15px;border-radius:5px;border-right:4px solid #ffc107;margin:20px 0;font-size:14px;color:#856404;">
                                <strong>تنبيه مهم:</strong>
                                <ul style="margin:10px 0 0 20px;padding:0;">
                                    <li>كود التحقق صالح لمدة 10 دقائق فقط</li>
                                    <li>لا تشارك هذا الكود مع أي شخص آخر</li>
                                    <li>إذا لم تطلب هذا الكود، يرجى تجاهل هذه الرسالة</li>
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

