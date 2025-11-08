<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تذكير: لديك منتجات في سلة التسوق</title>
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
            background: linear-gradient(135deg, #a855f7 0%, #f97316 100%);
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
            color: rgba(255, 255, 255, 0.9);
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
        .cart-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 40px;
        }
        .cart-box h3 {
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
        .items-section {
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
        .item-card {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .item-header {
            background: #0f172a;
            color: #ffffff;
            padding: 18px 25px;
            font-size: 17px;
            font-weight: 700;
        }
        .item-body {
            padding: 25px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        .item-row:last-child {
            border-bottom: none;
        }
        .item-label {
            color: #64748b;
            font-weight: 600;
        }
        .item-value {
            color: #0f172a;
            font-weight: 700;
        }
        .price-badge {
            background: linear-gradient(135deg, #a855f7 0%, #f97316 100%);
            color: #ffffff;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 700;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #a855f7 0%, #f97316 100%);
            color: #ffffff;
            padding: 15px 40px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            margin: 30px 0;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .cta-button:hover {
            transform: translateY(-2px);
        }
        .cta-container {
            text-align: center;
            margin: 40px 0;
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
            .item-card {
                margin: 15px;
            }
            .item-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="header">
            <h1>🛒 تذكير: لديك منتجات في سلة التسوق</h1>
            <p>لا تفوت فرصة إكمال طلبك!</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Greeting -->
            <div class="greeting">
                مرحباً {{ $user->full_name ?? $user->email }} 👋
            </div>

            <!-- Intro Text -->
            <p class="intro-text">
                لاحظنا أنك تركت بعض المنتجات في سلة التسوق الخاصة بك. نحن هنا لمساعدتك في إكمال طلبك!
                ستجد أدناه جميع المنتجات التي أضفتها إلى السلة.
            </p>

            <!-- Cart Info -->
            <div class="cart-box">
                <h3>📋 معلومات السلة</h3>
                <div class="info-row">
                    <span class="info-label">عدد المنتجات:</span>
                    <span class="info-value">{{ $cart->items_count ?? ($cart->items ? $cart->items->count() : 0) }} منتج</span>
                </div>
                <div class="info-row">
                    <span class="info-label">إجمالي المبلغ:</span>
                    <span class="info-value">{{ number_format($cart->total_amount ?? 0, 2) }} {{ $cart->currency ?? 'USD' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">تاريخ آخر نشاط:</span>
                    <span class="info-value">{{ $cart->last_activity_at ? $cart->last_activity_at->format('Y-m-d H:i') : 'غير محدد' }}</span>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Items Section -->
            <div class="items-section">
                <h2 class="section-title">🛍️ المنتجات في السلة</h2>

                @if($cart->items && $cart->items->count() > 0)
                    @foreach($cart->items as $item)
                    <div class="item-card">
                        <div class="item-header">
                            📦 {{ $item->product ? $item->product->name : 'منتج محذوف' }}
                        </div>
                        <div class="item-body">
                            <div class="item-row">
                                <span class="item-label">الكمية:</span>
                                <span class="item-value">{{ $item->quantity }}</span>
                            </div>
                            <div class="item-row">
                                <span class="item-label">السعر:</span>
                                <span class="item-value">{{ number_format($item->price, 2) }} {{ $cart->currency ?? 'USD' }}</span>
                            </div>
                            <div class="item-row">
                                <span class="item-label">المجموع:</span>
                                <span class="price-badge">{{ number_format($item->quantity * $item->price, 2) }} {{ $cart->currency ?? 'USD' }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="item-card">
                        <div class="item-body">
                            <p style="text-align: center; color: #64748b;">لا توجد منتجات في السلة</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- CTA Button -->
            <div class="cta-container">
                <a href="{{ route('cart.index') }}" class="cta-button">
                    إكمال الطلب الآن →
                </a>
            </div>

            @if($message)
            <div class="cart-box" style="margin-top: 30px;">
                <p style="color: #64748b; line-height: 1.8;">{{ $message }}</p>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-logo">🛒 {{ $siteName }}</div>
            <p>شكراً لثقتك بنا! نتمنى أن نراك قريباً لإكمال طلبك.</p>
            <p>إذا كان لديك أي استفسار، لا تتردد في التواصل معنا.</p>

            <p style="margin-top: 25px; font-size: 12px; color: #94a3b8;">
                &copy; {{ date('Y') }} {{ $siteName }}. جميع الحقوق محفوظة.
            </p>
        </div>
    </div>
</body>
</html>

