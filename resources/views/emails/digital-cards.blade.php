<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بطاقاتك الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
            background: #f8f9fa;
            padding: 0;
            direction: rtl;
        }
        .email-wrapper {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
        }
        .header {
            background: #0f172a;
            padding: 50px 40px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .header p {
            color: #cbd5e1;
            font-size: 16px;
        }
        .content {
            padding: 40px;
        }
        .greeting {
            font-size: 20px;
            color: #1e293b;
            margin-bottom: 15px;
            font-weight: 700;
        }
        .intro-text {
            font-size: 15px;
            color: #64748b;
            line-height: 1.8;
            margin-bottom: 35px;
        }
        .order-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 40px;
        }
        .order-box h3 {
            color: #0f172a;
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 700;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #64748b;
            font-weight: 600;
        }
        .info-value {
            color: #0f172a;
            font-weight: 700;
        }
        .cards-section {
            margin-top: 40px;
        }
        .section-title {
            font-size: 24px;
            color: #0f172a;
            margin-bottom: 30px;
            font-weight: 700;
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 3px solid #0f172a;
        }
        .product-card {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 30px;
            overflow: hidden;
        }
        .product-header {
            background: #0f172a;
            color: #ffffff;
            padding: 18px 25px;
            font-size: 17px;
            font-weight: 700;
        }
        .card-item {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            margin: 20px;
            background: #f8fafc;
            padding: 25px;
        }
        .card-number {
            background: #0f172a;
            color: #ffffff;
            font-weight: 700;
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 20px;
            font-size: 15px;
        }
        .detail-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .detail-label {
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
            flex: 1;
        }
        .detail-value {
            color: #0f172a;
            font-size: 16px;
            font-weight: 700;
            background: #f8fafc;
            padding: 8px 15px;
            border-radius: 6px;
            direction: ltr;
            text-align: left;
            font-family: 'Courier New', monospace;
            border: 1px solid #e2e8f0;
        }
        .value-badge {
            background: #0f172a;
            color: #ffffff;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 700;
        }
        .warning-box {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 25px;
            margin-top: 40px;
        }
        .warning-box h4 {
            color: #92400e;
            font-size: 18px;
            margin-bottom: 15px;
            font-weight: 700;
        }
        .warning-list {
            list-style: none;
            padding: 0;
        }
        .warning-list li {
            color: #92400e;
            font-size: 14px;
            padding: 8px 0;
            padding-right: 30px;
            position: relative;
            line-height: 1.8;
        }
        .warning-list li::before {
            content: '✓';
            position: absolute;
            right: 0;
            color: #059669;
            font-weight: bold;
            font-size: 18px;
        }
        .footer {
            background: #0f172a;
            padding: 40px;
            text-align: center;
            color: #cbd5e1;
        }
        .footer-logo {
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 20px;
        }
        .footer p {
            font-size: 14px;
            margin: 10px 0;
            line-height: 1.8;
        }
        .divider {
            height: 2px;
            background: #e2e8f0;
            margin: 40px 0;
        }
        @media only screen and (max-width: 600px) {
            .header, .content, .footer {
                padding: 30px 20px;
            }
            .card-item {
                margin: 15px;
                padding: 20px;
            }
            .detail-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .detail-value {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="header">
            <h1>🎉 بطاقاتك الرقمية جاهزة!</h1>
            <p>تم إرسال بطاقاتك بنجاح</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Greeting -->
            <div class="greeting">
                مرحباً {{ $customerName }} 👋
            </div>

            <!-- Intro Text -->
            <p class="intro-text">
                نشكرك على طلبك! نحن سعداء بإبلاغك أن بطاقاتك الرقمية جاهزة للاستخدام الآن.
                ستجد أدناه جميع تفاصيل البطاقات التي طلبتها.
            </p>

            <!-- Order Info -->
            <div class="order-box">
                <h3>📋 معلومات الطلب</h3>
                <div class="info-row">
                    <span class="info-label">رقم الطلب:</span>
                    <span class="info-value">{{ $order->order_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">تاريخ الطلب:</span>
                    <span class="info-value">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">إجمالي المبلغ:</span>
                    <span class="info-value">{{ number_format($order->total_amount, 2) }} $</span>
                </div>
                <div class="info-row">
                    <span class="info-label">حالة الطلب:</span>
                    <span class="info-value">{{ $order->getStatusInArabic() }}</span>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Digital Cards Section -->
            <div class="cards-section">
                <h2 class="section-title">💳 بطاقاتك الرقمية</h2>

                @foreach($orderItems as $item)
                <div class="product-card">
                    <div class="product-header">
                        📦 {{ $item['product_name'] }} ({{ $item['quantity'] }} بطاقة)
                    </div>

                    @foreach($item['cards'] as $index => $card)
                    <div class="card-item">
                        <div class="card-number">
                            البطاقة رقم {{ $index + 1 }}
                        </div>

                        <div class="detail-item">
                            <span class="detail-label">🔐 كود البطاقة:</span>
                            <span class="detail-value">{{ $card->card_code ?? 'غير متوفر' }}</span>
                        </div>

                        @if(!empty($card->card_pin))
                        <div class="detail-item">
                            <span class="detail-label">🔑 رقم PIN:</span>
                            <span class="detail-value">{{ $card->card_pin }}</span>
                        </div>
                        @endif

                        @if(!empty($card->card_number))
                        <div class="detail-item">
                            <span class="detail-label">💳 رقم البطاقة:</span>
                            <span class="detail-value">{{ $card->card_number }}</span>
                        </div>
                        @endif

                        @if(!empty($card->serial_number))
                        <div class="detail-item">
                            <span class="detail-label">📋 الرقم التسلسلي:</span>
                            <span class="detail-value">{{ $card->serial_number }}</span>
                        </div>
                        @endif

                        @if(!empty($card->value))
                        <div class="detail-item">
                            <span class="detail-label">💰 القيمة:</span>
                            <span class="value-badge">{{ number_format($card->value, 2) }} {{ $card->currency ?? 'USD' }}</span>
                        </div>
                        @endif

                        @if(!empty($card->expiry_date))
                        <div class="detail-item">
                            <span class="detail-label">📅 تاريخ الانتهاء:</span>
                            <span class="detail-value">{{ $card->expiry_date->format('Y-m-d') }}</span>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>

            <!-- Important Notes -->
            <div class="warning-box">
                <h4>⚠️ ملاحظات هامة</h4>
                <ul class="warning-list">
                    <li>احتفظ بهذا البريد الإلكتروني في مكان آمن</li>
                    <li>لا تشارك معلومات البطاقة مع أي شخص</li>
                    <li>تحقق من تاريخ انتهاء صلاحية البطاقة قبل الاستخدام</li>
                    <li>في حالة وجود أي مشكلة، تواصل معنا فوراً</li>
                    <li>البطاقات غير قابلة للاسترجاع بعد الإرسال</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-logo">💳 متجر البطاقات الرقمية</div>
            <p>شكراً لثقتك بنا! نتمنى لك تجربة ممتعة مع بطاقاتك الرقمية.</p>
            <p>إذا كان لديك أي استفسار، لا تتردد في التواصل معنا.</p>

            <p style="margin-top: 25px; font-size: 12px; color: #94a3b8;">
                &copy; {{ date('Y') }} متجر البطاقات الرقمية. جميع الحقوق محفوظة.
            </p>
        </div>
    </div>
</body>
</html>




