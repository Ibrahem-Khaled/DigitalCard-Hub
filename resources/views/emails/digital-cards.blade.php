<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>بطاقاتك الرقمية - طلب رقم {{ $order->order_number }}</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td, a { font-family: Arial, sans-serif !important; }
    </style>
    <![endif]-->
    <style>
        /* Reset Styles */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            outline: none;
            text-decoration: none;
        }
        
        /* Main Styles */
        body {
            margin: 0 !important;
            padding: 0 !important;
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            direction: rtl;
            text-align: right;
        }
        
        .email-wrapper {
            max-width: 650px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        
        /* Header */
        .email-header {
            background: linear-gradient(135deg, #8B5CF6 0%, #F97316 100%);
            padding: 50px 40px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            font-size: 32px;
            font-weight: bold;
            margin: 0 0 10px 0;
            line-height: 1.4;
        }
        .email-header p {
            color: #fef3c7;
            font-size: 18px;
            margin: 0;
            line-height: 1.6;
        }
        
        /* Content */
        .email-content {
            padding: 40px;
            background-color: #ffffff;
        }
        
        /* Greeting */
        .greeting-box {
            background: linear-gradient(135deg, #f3e8ff 0%, #fed7aa 100%);
            border-right: 5px solid #8B5CF6;
            padding: 30px;
            margin-bottom: 40px;
            border-radius: 10px;
        }
        .greeting-text {
            color: #7C3AED;
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 10px 0;
            line-height: 1.5;
        }
        .greeting-subtext {
            color: #6D28D9;
            font-size: 16px;
            margin: 0;
            line-height: 1.8;
        }
        
        /* Order Info */
        .order-info-box {
            background-color: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 40px;
        }
        .order-info-title {
            color: #0f172a;
            font-size: 22px;
            font-weight: bold;
            margin: 0 0 25px 0;
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-row {
            border-bottom: 1px solid #e2e8f0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-cell {
            padding: 15px 0;
            vertical-align: top;
        }
        .info-label {
            color: #64748b;
            font-size: 15px;
            font-weight: 600;
            width: 40%;
        }
        .info-value {
            color: #0f172a;
            font-size: 16px;
            font-weight: bold;
            text-align: left;
        }
        
        /* Cards Section */
        .cards-section {
            margin-top: 40px;
        }
        .section-title {
            color: #0f172a;
            font-size: 26px;
            font-weight: bold;
            text-align: center;
            margin: 0 0 15px 0;
            padding-bottom: 15px;
            border-bottom: 3px solid #8B5CF6;
        }
        .section-subtitle {
            color: #64748b;
            font-size: 16px;
            text-align: center;
            margin: 0 0 35px 0;
        }
        
        /* Product Card */
        .product-card {
            background-color: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 15px;
            margin-bottom: 30px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.15);
        }
        .product-header {
            background: linear-gradient(135deg, #8B5CF6 0%, #F97316 100%);
            color: #ffffff;
            padding: 25px 30px;
        }
        .product-name {
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 8px 0;
        }
        .product-badge {
            display: inline-block;
            background-color: rgba(255, 255, 255, 0.2);
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 8px;
        }
        
        /* Card Item */
        .card-container {
            padding: 30px;
        }
        .card-box {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 2px solid #cbd5e1;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
        }
        .card-box:last-child {
            margin-bottom: 0;
        }
        .card-number {
            background: linear-gradient(135deg, #8B5CF6 0%, #F97316 100%);
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
            display: inline-block;
        }
        .card-details {
            margin-top: 20px;
        }
        .detail-row {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 18px;
            margin-bottom: 12px;
        }
        .detail-row:last-child {
            margin-bottom: 0;
        }
        .detail-label {
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }
        .detail-value {
            color: #0f172a;
            font-size: 18px;
            font-weight: bold;
            background-color: #f8fafc;
            padding: 12px 18px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            display: inline-block;
            direction: ltr;
            text-align: left;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
            word-break: break-all;
        }
        .value-badge {
            background: linear-gradient(135deg, #8B5CF6 0%, #F97316 100%);
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            display: inline-block;
        }
        
        /* Warning Box */
        .warning-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 3px solid #f59e0b;
            border-radius: 12px;
            padding: 30px;
            margin-top: 40px;
        }
        .warning-title {
            color: #92400e;
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 20px 0;
        }
        .warning-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .warning-list li {
            color: #92400e;
            font-size: 15px;
            padding: 10px 0;
            padding-right: 30px;
            position: relative;
            line-height: 1.8;
        }
        .warning-list li::before {
            content: '✓';
            position: absolute;
            right: 0;
            top: 10px;
            color: #059669;
            font-weight: bold;
            font-size: 20px;
            background-color: #ffffff;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        /* Footer */
        .email-footer {
            background: linear-gradient(135deg, #8B5CF6 0%, #F97316 100%);
            padding: 40px;
            text-align: center;
        }
        .footer-logo {
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 20px 0;
        }
        .footer-text {
            color: #fef3c7;
            font-size: 16px;
            margin: 15px 0;
            line-height: 1.8;
        }
        .footer-copyright {
            color: #fde68a;
            font-size: 13px;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                width: 100% !important;
            }
            .email-header, .email-content, .email-footer {
                padding: 30px 20px !important;
            }
            .email-header h1 {
                font-size: 26px !important;
            }
            .greeting-text {
                font-size: 20px !important;
            }
            .section-title {
                font-size: 22px !important;
            }
            .product-header {
                padding: 20px !important;
            }
            .card-container {
                padding: 20px !important;
            }
            .card-box {
                padding: 20px !important;
            }
            .detail-value {
                font-size: 16px !important;
                padding: 10px 15px !important;
            }
        }
    </style>
</head>
<body>
    <div style="background-color: #f5f7fa; padding: 20px 0;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td align="center">
                    <table role="presentation" class="email-wrapper" cellspacing="0" cellpadding="0" border="0" width="650" style="background-color: #ffffff;">
                        <!-- Header -->
                        <tr>
                            <td class="email-header">
                                <h1>🎉 بطاقاتك الرقمية جاهزة!</h1>
                                <p>تم إرسال بطاقاتك بنجاح - طلب رقم {{ $order->order_number }}</p>
                            </td>
                        </tr>
                        
                        <!-- Content -->
                        <tr>
                            <td class="email-content">
                                <!-- Greeting -->
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                    <tr>
                                        <td class="greeting-box">
                                            <div class="greeting-text">مرحباً {{ $customerName }} 👋</div>
                                            <div class="greeting-subtext">
                                                نشكرك على طلبك! نحن سعداء بإبلاغك أن بطاقاتك الرقمية جاهزة للاستخدام الآن.
                                                ستجد أدناه جميع تفاصيل البطاقات التي طلبتها.
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                
                                <!-- Order Info -->
                                <table role="presentation" class="order-info-box" cellspacing="0" cellpadding="0" border="0" width="100%">
                                    <tr>
                                        <td>
                                            <div class="order-info-title">📋 معلومات الطلب</div>
                                            <table role="presentation" class="info-table" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                <tr class="info-row">
                                                    <td class="info-cell info-label">رقم الطلب:</td>
                                                    <td class="info-cell info-value">{{ $order->order_number }}</td>
                                                </tr>
                                                <tr class="info-row">
                                                    <td class="info-cell info-label">تاريخ الطلب:</td>
                                                    <td class="info-cell info-value">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                                </tr>
                                                <tr class="info-row">
                                                    <td class="info-cell info-label">إجمالي المبلغ:</td>
                                                    <td class="info-cell info-value">{{ formatPrice($order->total_amount, $order->currency ?? 'USD') }}</td>
                                                </tr>
                                                <tr class="info-row">
                                                    <td class="info-cell info-label">حالة الطلب:</td>
                                                    <td class="info-cell info-value">{{ $order->getStatusInArabic() }}</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                
                                <!-- Cards Section -->
                                <table role="presentation" class="cards-section" cellspacing="0" cellpadding="0" border="0" width="100%">
                                    <tr>
                                        <td>
                                            <div class="section-title">💳 بطاقاتك الرقمية</div>
                                            <div class="section-subtitle">جميع البطاقات التي طلبتها جاهزة للاستخدام</div>
                                            
                                            @foreach($orderItems as $item)
                                            <!-- Product Card -->
                                            <table role="presentation" class="product-card" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                <tr>
                                                    <td class="product-header">
                                                        <div class="product-name">📦 {{ $item['product_name'] }}</div>
                                                        <div class="product-badge">{{ $item['quantity'] }} بطاقة</div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="card-container">
                                                        @foreach($item['cards'] as $index => $card)
                                                        <!-- Card Box -->
                                                        <table role="presentation" class="card-box" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                            <tr>
                                                                <td>
                                                                    <div class="card-number">البطاقة رقم {{ $index + 1 }}</div>
                                                                    <div class="card-details">
                                                                        <!-- Card Code -->
                                                                        <table role="presentation" class="detail-row" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                                            <tr>
                                                                                <td>
                                                                                    <div class="detail-label">🔐 كود البطاقة</div>
                                                                                    <div class="detail-value">{{ $card->card_code ?? 'غير متوفر' }}</div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                        
                                                                        @if(!empty($card->card_pin))
                                                                        <!-- Card PIN -->
                                                                        <table role="presentation" class="detail-row" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                                            <tr>
                                                                                <td>
                                                                                    <div class="detail-label">🔑 رقم PIN</div>
                                                                                    <div class="detail-value">{{ $card->card_pin }}</div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                        @endif
                                                                        
                                                                        @if(!empty($card->card_number))
                                                                        <!-- Card Number -->
                                                                        <table role="presentation" class="detail-row" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                                            <tr>
                                                                                <td>
                                                                                    <div class="detail-label">💳 رقم البطاقة</div>
                                                                                    <div class="detail-value">{{ $card->card_number }}</div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                        @endif
                                                                        
                                                                        @if(!empty($card->serial_number))
                                                                        <!-- Serial Number -->
                                                                        <table role="presentation" class="detail-row" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                                            <tr>
                                                                                <td>
                                                                                    <div class="detail-label">📋 الرقم التسلسلي</div>
                                                                                    <div class="detail-value">{{ $card->serial_number }}</div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                        @endif
                                                                        
                                                                        @if(!empty($card->value))
                                                                        <!-- Card Value -->
                                                                        <table role="presentation" class="detail-row" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                                            <tr>
                                                                                <td>
                                                                                    <div class="detail-label">💰 القيمة</div>
                                                                                    <div class="value-badge">{{ number_format($card->value, 2) }} {{ $card->currency ?? 'USD' }}</div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                        @endif
                                                                        
                                                                        @if(!empty($card->expiry_date))
                                                                        <!-- Expiry Date -->
                                                                        <table role="presentation" class="detail-row" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                                            <tr>
                                                                                <td>
                                                                                    <div class="detail-label">📅 تاريخ الانتهاء</div>
                                                                                    <div class="detail-value">{{ $card->expiry_date->format('Y-m-d') }}</div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            </table>
                                            @endforeach
                                        </td>
                                    </tr>
                                </table>
                                
                                <!-- Warning Box -->
                                <table role="presentation" class="warning-box" cellspacing="0" cellpadding="0" border="0" width="100%">
                                    <tr>
                                        <td>
                                            <div class="warning-title">⚠️ ملاحظات هامة</div>
                                            <ul class="warning-list">
                                                <li>احتفظ بهذا البريد الإلكتروني في مكان آمن</li>
                                                <li>لا تشارك معلومات البطاقة مع أي شخص</li>
                                                <li>تحقق من تاريخ انتهاء صلاحية البطاقة قبل الاستخدام</li>
                                                <li>في حالة وجود أي مشكلة، تواصل معنا فوراً</li>
                                                <li>البطاقات غير قابلة للاسترجاع بعد الإرسال</li>
                                            </ul>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        
                        <!-- Footer -->
                        <tr>
                            <td class="email-footer">
                                <div class="footer-logo">💳 متجر البطاقات الرقمية</div>
                                <div class="footer-text">شكراً لثقتك بنا! نتمنى لك تجربة ممتعة مع بطاقاتك الرقمية.</div>
                                <div class="footer-text">إذا كان لديك أي استفسار، لا تتردد في التواصل معنا.</div>
                                <div class="footer-copyright">
                                    &copy; {{ date('Y') }} متجر البطاقات الرقمية. جميع الحقوق محفوظة.
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
