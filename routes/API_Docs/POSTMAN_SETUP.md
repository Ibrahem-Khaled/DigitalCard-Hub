# دليل إعداد Postman Collection

## 📦 الملفات المتوفرة

1. **Digital_Cards_Store_API.postman_collection.json** - ملف Collection الرئيسي
2. **Digital_Cards_Store_API.postman_environment.json** - ملف Environment

## 🚀 خطوات الإعداد

### 1. استيراد Collection

1. افتح Postman
2. اضغط على **Import** في الزاوية اليسرى العلوية
3. اختر ملف `Digital_Cards_Store_API.postman_collection.json`
4. اضغط **Import**

### 2. استيراد Environment

1. في Postman، اضغط على **Environments** في القائمة الجانبية
2. اضغط على **Import**
3. اختر ملف `Digital_Cards_Store_API.postman_environment.json`
4. اضغط **Import**

### 3. تفعيل Environment

1. في الزاوية اليمنى العلوية من Postman
2. اختر **Digital Cards Store API - Environment** من القائمة المنسدلة

### 4. تعديل Base URL

1. اضغط على **Environments** في القائمة الجانبية
2. اختر **Digital Cards Store API - Environment**
3. عدّل قيمة `base_url` حسب بيئتك:
   - **Local**: `http://localhost:8000`
   - **Development**: `https://dev.your-domain.com`
   - **Production**: `https://your-domain.com`

## 🔐 المصادقة التلقائية

Collection يحتوي على **Tests Scripts** تلقائية تقوم بـ:

1. **تسجيل الدخول**: يحفظ `user_id` تلقائياً
2. **التحقق من الكود**: يحفظ `auth_token` تلقائياً
3. **جميع الطلبات المحمية**: تستخدم `auth_token` تلقائياً

### كيفية الحصول على Token:

1. **Register** أو **Login** → ستحصل على `user_id`
2. **Verify Code** → ستحصل على `auth_token` تلقائياً
3. جميع الطلبات المحمية ستستخدم `auth_token` تلقائياً

## 📁 هيكل Collection

### Authentication
- Register
- Login
- Verify Code
- Resend Verification Code
- Get Current User
- Update Profile
- Change Password
- Forgot Password
- Reset Password
- Refresh Token
- Logout

### Products
- Get All Products (مع Query Parameters)
- Get Single Product
- Search Products
- Get Product Reviews
- Add Product Review
- Update Product Review
- Delete Product Review

### Categories
- Get All Categories
- Get Single Category
- Get Category Products

### Cart
- Get Cart
- Add Product to Cart
- Update Cart Item
- Remove Cart Item
- Clear Cart
- Apply Coupon
- Remove Coupon

### Orders
- Get All Orders
- Get Single Order
- Create Order
- Cancel Order

### Profile
- Get Profile
- Update Profile
- Get Profile Orders
- Get Profile Loyalty Points
- Get Profile Referrals

### Coupons
- Get All Coupons
- Validate Coupon

### Loyalty Points
- Get Loyalty Points
- Get Loyalty Points Transactions

### Notifications
- Get All Notifications
- Get Unread Notifications
- Mark Notification as Read
- Mark All Notifications as Read
- Delete Notification

### AI Chat
- Chat with AI
- Get Product Suggestions

## 🔧 المتغيرات (Variables)

### Environment Variables

- `base_url` - Base URL للـ API
- `auth_token` - Token المصادقة (يتم حفظه تلقائياً)
- `user_id` - معرف المستخدم (يتم حفظه تلقائياً)
- `product_id` - معرف المنتج (افتراضي: 1)
- `category_id` - معرف الفئة (افتراضي: 1)
- `order_id` - معرف الطلب (افتراضي: 1)
- `cart_item_id` - معرف عنصر السلة (افتراضي: 1)
- `notification_id` - معرف الإشعار (افتراضي: 1)

## 📝 أمثلة الاستخدام

### 1. تسجيل الدخول والحصول على Token

```
1. Authentication → Login
   - Body: {"login": "ahmed@example.com", "password": "password123"}
   - سيتم حفظ user_id تلقائياً

2. Authentication → Verify Code
   - Body: {"user_id": {{user_id}}, "code": "123456", "type": "login"}
   - سيتم حفظ auth_token تلقائياً
```

### 2. استخدام Token في الطلبات

جميع الطلبات المحمية تستخدم `{{auth_token}}` تلقائياً في Header:
```
Authorization: Bearer {{auth_token}}
```

### 3. تعديل البيانات

يمكنك تعديل أي request:
- اضغط على Request
- عدّل Body أو Headers
- اضغط **Send**

## 🎯 نصائح مفيدة

1. **استخدم Environment Variables**: لتغيير Base URL بسهولة
2. **Tests Scripts**: تحفظ Token تلقائياً - لا حاجة لإدخاله يدوياً
3. **Query Parameters**: في "Get All Products" يمكنك تفعيل/تعطيل أي parameter
4. **Save Responses**: احفظ Responses المهمة كأمثلة

## 🐛 استكشاف الأخطاء

### Token غير صالح
- قم بتسجيل الدخول مرة أخرى
- أو استخدم **Refresh Token**

### 401 Unauthorized
- تأكد من تفعيل Environment
- تأكد من وجود `auth_token` في Environment

### 404 Not Found
- تأكد من صحة Base URL
- تأكد من أن الـ ID موجود في قاعدة البيانات

### 422 Validation Error
- راجع Body في Request
- تأكد من إرسال جميع الحقول المطلوبة

## 📚 المزيد من المعلومات

راجع ملف `API_README.md` للحصول على:
- تفاصيل جميع الـ Endpoints
- أمثلة الاستجابة
- أكواد الحالة
- Rate Limiting

## ✅ Checklist

- [ ] استيراد Collection
- [ ] استيراد Environment
- [ ] تفعيل Environment
- [ ] تعديل Base URL
- [ ] تسجيل الدخول والحصول على Token
- [ ] اختبار بعض الطلبات

---

**تم إنشاء Collection بواسطة Digital Cards Store API Team**

