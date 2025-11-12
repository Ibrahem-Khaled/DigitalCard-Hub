<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كود التحقق</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body style="margin:0;padding:0;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);font-family:'Cairo',Arial,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:40px 20px;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;background:#ffffff;border-radius:24px;box-shadow:0 20px 60px rgba(0,0,0,0.3);overflow:hidden;">

                    <!-- Header with Logo -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:50px 40px;text-align:center;position:relative;">
                            <div style="background:rgba(255,255,255,0.1);width:80px;height:80px;margin:0 auto 20px;border-radius:20px;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(10px);border:2px solid rgba(255,255,255,0.2);">
                                @if(!empty($logoPath))
                                    <img src="{{ $message->embed($logoPath) }}" alt="Logo" style="width:100%;height:100%;object-fit:contain;">
                                @endif
                            </div>
                            <h1 style="margin:0;font-size:32px;font-weight:800;color:#ffffff;letter-spacing:-0.5px;">{{ $siteName }}</h1>
                            <p style="margin:12px 0 0 0;font-size:16px;color:rgba(255,255,255,0.85);font-weight:500;">تأكيد الهوية</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:50px 40px;">
                            <!-- Greeting -->
                            <div style="margin-bottom:32px;">
                                <h2 style="font-size:24px;font-weight:700;color:#1a1a2e;margin:0 0 12px 0;">مرحباً {{ $user->full_name }} 👋</h2>
                                @if($type === 'registration')
                                    <p style="font-size:16px;line-height:1.8;margin:0;color:#4a5568;">
                                        نحن سعداء بانضمامك إلى <strong style="color:#667eea;">{{ $siteName }}</strong>! لإكمال رحلتك معنا، نحتاج فقط للتحقق من هويتك.
                                    </p>
                                @else
                                    <p style="font-size:16px;line-height:1.8;margin:0;color:#4a5568;">
                                        تم طلب تسجيل دخول جديد إلى حسابك في <strong style="color:#667eea;">{{ $siteName }}</strong>. استخدم الكود أدناه للمتابعة.
                                    </p>
                                @endif
                            </div>

                            <!-- Verification Code Card -->
                            <div style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:40px;border-radius:20px;text-align:center;margin:32px 0;box-shadow:0 10px 30px rgba(102,126,234,0.3);position:relative;overflow:hidden;">
                                <div style="position:absolute;top:-50px;right:-50px;width:150px;height:150px;background:rgba(255,255,255,0.1);border-radius:50%;"></div>
                                <div style="position:absolute;bottom:-30px;left:-30px;width:100px;height:100px;background:rgba(255,255,255,0.1);border-radius:50%;"></div>
                                <p style="margin:0 0 16px 0;font-size:14px;color:rgba(255,255,255,0.8);font-weight:600;text-transform:uppercase;letter-spacing:2px;">كود التحقق الخاص بك</p>
                                <div style="background:rgba(255,255,255,0.95);padding:24px;border-radius:16px;margin:0 auto;max-width:280px;position:relative;">
                                    <p style="margin:0;font-size:48px;font-weight:800;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:12px;font-family:'Courier New',monospace;">{{ $code }}</p>
                                </div>
                                <p style="margin:20px 0 0 0;font-size:14px;color:rgba(255,255,255,0.9);font-weight:500;">⏱️ صالح لمدة 10 دقائق</p>
                            </div>

                            <!-- Security Tips -->
                            <div style="background:linear-gradient(135deg,#fff5e6 0%,#ffe8cc 100%);padding:24px;border-radius:16px;border-right:5px solid #ff9800;margin:32px 0;">
                                <div style="display:flex;align-items:center;margin-bottom:16px;">
                                    <div style="background:#ff9800;width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;margin-left:12px;">
                                        <span style="font-size:18px;">🔒</span>
                                    </div>
                                    <h3 style="margin:0;font-size:16px;font-weight:700;color:#e65100;">نصائح أمان مهمة</h3>
                                </div>
                                <ul style="margin:0;padding:0;list-style:none;color:#5d4037;">
                                    <li style="padding:8px 0;font-size:14px;line-height:1.6;">
                                        <span style="color:#ff9800;font-weight:700;margin-left:8px;">✓</span>
                                        الكود صالح لمدة <strong>10 دقائق</strong> فقط من وقت الإرسال
                                    </li>
                                    <li style="padding:8px 0;font-size:14px;line-height:1.6;">
                                        <span style="color:#ff9800;font-weight:700;margin-left:8px;">✓</span>
                                        <strong>لا تشارك</strong> هذا الكود مع أي شخص نهائياً
                                    </li>
                                    <li style="padding:8px 0;font-size:14px;line-height:1.6;">
                                        <span style="color:#ff9800;font-weight:700;margin-left:8px;">✓</span>
                                        إذا لم تطلب هذا الكود، يُرجى <strong>تجاهل الرسالة</strong>
                                    </li>
                                </ul>
                            </div>

                            <!-- Divider -->
                            <div style="height:1px;background:linear-gradient(90deg,transparent,#e0e0e0,transparent);margin:40px 0;"></div>

                            <!-- Help Section -->
                            <div style="text-align:center;padding:20px;background:#f8f9fa;border-radius:12px;">
                                <p style="margin:0 0 12px 0;font-size:14px;color:#6c757d;line-height:1.6;">
                                    هل تواجه مشكلة؟ نحن هنا لمساعدتك!
                                </p>
                                <a href="#" style="display:inline-block;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#ffffff;text-decoration:none;padding:12px 32px;border-radius:8px;font-size:14px;font-weight:600;transition:all 0.3s;">
                                    اتصل بالدعم الفني
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f8f9fa;padding:32px 40px;text-align:center;border-top:1px solid #e9ecef;">
                            <p style="margin:0 0 16px 0;font-size:13px;color:#6c757d;line-height:1.6;">
                                هذه رسالة تلقائية من <strong>{{ $siteName }}</strong><br>
                                يُرجى عدم الرد على هذا البريد الإلكتروني
                            </p>
                            <div style="margin:20px 0;">
                                <a href="#" style="display:inline-block;margin:0 8px;color:#667eea;text-decoration:none;font-size:12px;">الشروط والأحكام</a>
                                <span style="color:#dee2e6;">|</span>
                                <a href="#" style="display:inline-block;margin:0 8px;color:#667eea;text-decoration:none;font-size:12px;">سياسة الخصوصية</a>
                            </div>
                            <p style="margin:16px 0 0 0;font-size:12px;color:#adb5bd;">
                                © {{ date('Y') }} <strong>{{ $siteName }}</strong> - جميع الحقوق محفوظة
                            </p>
                        </td>
                    </tr>
                </table>

                <!-- Bottom Spacing -->
                <div style="height:40px;"></div>
            </td>
        </tr>
    </table>
</body>
</html>
