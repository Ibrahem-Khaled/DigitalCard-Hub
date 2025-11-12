<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بطاقاتك الرقمية - طلب رقم {{ $order->order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Cairo', Arial, sans-serif;
        }

        .email-wrapper {
            max-width: 650px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
        }

        .header-icon {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }

        .header h1 {
            color: #fff;
            font-size: 28px;
            font-weight: 800;
            margin: 0 0 8px 0;
        }

        .header p {
            color: rgba(255,255,255,0.9);
            font-size: 15px;
            margin: 0;
        }

        /* Content */
        .content {
            background: #fff;
            padding: 35px 30px;
        }

        /* Greeting */
        .greeting {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            padding: 25px;
            border-radius: 16px;
            border-right: 4px solid #667eea;
            margin-bottom: 30px;
        }

        .greeting h2 {
            color: #1f2937;
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 10px 0;
        }

        .greeting p {
            color: #4b5563;
            font-size: 14px;
            margin: 0;
            line-height: 1.6;
        }

        /* Order Info */
        .order-info {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .order-info h3 {
            color: #111827;
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 15px 0;
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .info-item {
            background: #fff;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .info-label {
            color: #6b7280;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .info-value {
            color: #111827;
            font-size: 14px;
            font-weight: 700;
        }

        /* Products Section */
        .section-title {
            color: #111827;
            font-size: 22px;
            font-weight: 800;
            text-align: center;
            margin: 0 0 8px 0;
        }

        .section-subtitle {
            color: #6b7280;
            font-size: 14px;
            text-align: center;
            margin: 0 0 25px 0;
        }

        /* Product Card */
        .product {
            background: #fff;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .product-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .product-name {
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            margin: 0;
        }

        .product-badge {
            background: rgba(255,255,255,0.25);
            color: #fff;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Cards Grid */
        .cards-grid {
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .card-item {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 15px;
            position: relative;
        }

        .card-number-badge {
            position: absolute;
            top: -8px;
            right: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }

        .card-code-wrapper {
            margin-top: 8px;
        }

        .code-label {
            color: #6b7280;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
        }

        .code-value {
            background: #fff;
            border: 2px solid #667eea;
            border-radius: 8px;
            padding: 10px 12px;
            font-family: 'Courier New', monospace;
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            direction: ltr;
            text-align: center;
            letter-spacing: 1px;
            word-break: break-all;
        }

        .card-meta {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .meta-item {
            background: #fff;
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            font-size: 11px;
            flex: 1;
            min-width: calc(50% - 5px);
        }

        .meta-label {
            color: #9ca3af;
            font-size: 10px;
            display: block;
            margin-bottom: 2px;
        }

        .meta-value {
            color: #111827;
            font-weight: 700;
            font-size: 12px;
        }

        /* Warning */
        .warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
        }

        .warning h4 {
            color: #92400e;
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 12px 0;
        }

        .warning ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .warning li {
            color: #78350f;
            font-size: 13px;
            padding: 6px 0 6px 22px;
            position: relative;
            line-height: 1.5;
        }

        .warning li::before {
            content: '✓';
            position: absolute;
            right: 0;
            color: #059669;
            font-weight: 700;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
        }

        .footer h3 {
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 15px 0;
        }

        .footer p {
            color: rgba(255,255,255,0.9);
            font-size: 14px;
            margin: 10px 0;
            line-height: 1.6;
        }

        .footer-copy {
            color: rgba(255,255,255,0.7);
            font-size: 12px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.2);
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .header, .content, .footer {
                padding: 25px 20px !important;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .product-header {
                flex-direction: column;
                gap: 8px;
                align-items: flex-start;
            }

            .cards-grid {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px 15px;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td align="center">
                    <div class="email-wrapper" style="background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">

                        <!-- Header -->
                        <div class="header">
                            <div class="header-icon">
                                @if(!empty($logoPath))
                                    <img src="{{ $message->embed($logoPath) }}" alt="{{ $siteName }}" style="width:100%;height:100%;object-fit:contain;">
                                @else
                                    <span style="font-size: 36px;">🎁</span>
                                @endif
                            </div>
                            <h1>{{ $siteName }}</h1>
                            <p>بطاقاتك جاهزة! - طلب رقم {{ $order->order_number }}</p>
                        </div>

                        <!-- Content -->
                        <div class="content">

                            <!-- Greeting -->
                            <div class="greeting">
                                <h2>مرحباً {{ $customerName }} 👋</h2>
                                <p>تم تجهيز بطاقاتك بنجاح! جميع الأكواد جاهزة للاستخدام الآن.</p>
                            </div>

                            <!-- Order Info -->
                            <div class="order-info">
                                <h3>📋 معلومات الطلب</h3>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="info-label">رقم الطلب</div>
                                        <div class="info-value">{{ $order->order_number }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">التاريخ</div>
                                        <div class="info-value">{{ $order->created_at->format('Y-m-d') }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">إجمالي المبلغ</div>
                                        <div class="info-value">{{ formatPrice($order->total_amount, $order->currency ?? 'USD') }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">حالة الطلب</div>
                                        <div class="info-value">{{ $order->getStatusInArabic() }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Products Section -->
                            <div class="section-title">💳 البطاقات</div>
                            <div class="section-subtitle">جميع الأكواد الخاصة بك</div>

                            @foreach($orderItems as $item)
                            <!-- Product -->
                            <div class="product">
                                <div class="product-header">
                                    <div class="product-name">{{ $item['product_name'] }}</div>
                                    <div class="product-badge">{{ $item['quantity'] }} كود</div>
                                </div>

                                <!-- Cards Grid -->
                                <div class="cards-grid">
                                    @foreach($item['cards'] as $index => $card)
                                    <div class="card-item">
                                        <div class="card-number-badge">#{{ $index + 1 }}</div>

                                        <div class="card-code-wrapper">
                                            <span class="code-label">🔐 الكود</span>
                                            <div class="code-value">{{ $card->card_code ?? 'غير متوفر' }}</div>
                                        </div>

                                        <div class="card-meta">
                                            @if(!empty($card->value))
                                            <div class="meta-item">
                                                <span class="meta-label">القيمة</span>
                                                <div class="meta-value">{{ number_format($card->value, 2) }} {{ $card->currency ?? 'USD' }}</div>
                                            </div>
                                            @endif

                                            @if(!empty($card->expiry_date))
                                            <div class="meta-item">
                                                <span class="meta-label">تاريخ الانتهاء</span>
                                                <div class="meta-value">{{ $card->expiry_date->format('Y-m-d') }}</div>
                                            </div>
                                            @endif

                                            @if(!empty($card->serial_number))
                                            <div class="meta-item" style="flex: 1 1 100%;">
                                                <span class="meta-label">الرقم التسلسلي</span>
                                                <div class="meta-value" style="font-size: 11px; direction: ltr; text-align: left;">{{ $card->serial_number }}</div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach

                            <!-- Warning -->
                            <div class="warning">
                                <h4>⚠️ تنبيهات مهمة</h4>
                                <ul>
                                    <li>احفظ الأكواد في مكان آمن</li>
                                    <li>لا تشارك الأكواد مع أحد</li>
                                    <li>تحقق من تاريخ الانتهاء</li>
                                    <li>الأكواد غير قابلة للاسترجاع</li>
                                </ul>
                            </div>

                            <!-- Invoice Download -->
                            @if($order->payment_status === 'paid' || $order->payment_status === 'free')
                            <div style="text-align: center; margin-top: 30px; padding: 20px; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); border-radius: 12px; border: 2px solid #667eea;">
                                <a href="{{ route('order.invoice', $order->id) }}?token={{ md5($order->order_number . $order->created_at) }}" 
                                   style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 10px; font-size: 15px; font-weight: 700; transition: all 0.3s; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                                    📄 تحميل الفاتورة الضريبية
                                </a>
                                <p style="margin: 12px 0 0 0; color: #6b7280; font-size: 12px;">يمكنك تحميل الفاتورة الضريبية للطلب</p>
                            </div>
                            @endif

                        </div>

                        <!-- Footer -->
                        <div class="footer">
                            <h3>💳 {{ $siteName }}</h3>
                            <p>شكراً لثقتك بنا! نتمنى لك تجربة ممتعة.</p>
                            <p>للاستفسارات، تواصل معنا في أي وقت.</p>
                            <div class="footer-copy">
                                &copy; {{ date('Y') }} {{ $siteName }} - جميع الحقوق محفوظة
                            </div>
                        </div>

                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
