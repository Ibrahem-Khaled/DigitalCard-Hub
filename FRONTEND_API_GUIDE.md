# دليل API للمبرمج Frontend - Digital Cards Store

## 📋 نظرة عامة

هذا الدليل الشامل لمبرمجي Frontend لاستخدام API متجر البطاقات الرقمية. API مبني باستخدام Laravel 12 و Laravel Sanctum للمصادقة.

---

## 🔗 Base URL

```
https://your-domain.com/api/v1
```

أو للتطوير المحلي:
```
http://localhost:8000/api/v1
```

---

## 🔐 المصادقة (Authentication)

### كيفية الحصول على Token

1. **تسجيل الدخول:**
```javascript
POST /api/v1/auth/login
Body: {
  "login": "ahmed@example.com",
  "password": "password123"
}
```

2. **التحقق من الكود:**
```javascript
POST /api/v1/auth/verify
Body: {
  "user_id": 1,
  "code": "123456",
  "type": "login"
}
```

3. **استخدام Token:**
```javascript
Headers: {
  "Authorization": "Bearer {your-token}",
  "Accept": "application/json",
  "Content-Type": "application/json"
}
```

---

## 📊 هيكل الاستجابة الموحد

### ✅ نجاح العملية

```json
{
  "success": true,
  "message": "تمت العملية بنجاح",
  "data": {
    // البيانات هنا
  }
}
```

### ❌ خطأ

```json
{
  "success": false,
  "message": "رسالة الخطأ",
  "errors": {
    // أخطاء التحقق (إن وجدت)
  }
}
```

### 📄 Pagination

```json
{
  "success": true,
  "message": "تم جلب البيانات بنجاح",
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 150,
    "from": 1,
    "to": 15
  },
  "links": {
    "first": "https://your-domain.com/api/v1/products?page=1",
    "last": "https://your-domain.com/api/v1/products?page=10",
    "prev": null,
    "next": "https://your-domain.com/api/v1/products?page=2"
  }
}
```

---

## 🎯 Endpoints الرئيسية

### 1. Authentication (المصادقة)

#### تسجيل مستخدم جديد
```http
POST /api/v1/auth/register
```

**Request Body:**
```json
{
  "first_name": "أحمد",
  "last_name": "محمد",
  "email": "ahmed@example.com",
  "phone": "+96812345678",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "تم التسجيل بنجاح. يرجى التحقق من بريدك الإلكتروني لإكمال التسجيل",
  "data": {
    "user": {
      "id": 1,
      "first_name": "أحمد",
      "last_name": "محمد",
      "email": "ahmed@example.com",
      "phone": "+96812345678",
      "full_name": "أحمد محمد",
      "is_active": true,
      "created_at": "2025-11-08T01:00:00+00:00"
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "requires_verification": true
  }
}
```

#### تسجيل الدخول
```http
POST /api/v1/auth/login
```

**Request Body:**
```json
{
  "login": "ahmed@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "تم إرسال كود التحقق إلى بريدك الإلكتروني",
  "data": {
    "user_id": 1,
    "requires_verification": true
  }
}
```

#### التحقق من الكود
```http
POST /api/v1/auth/verify
```

**Request Body:**
```json
{
  "user_id": 1,
  "code": "123456",
  "type": "login"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "تم التحقق بنجاح",
  "data": {
    "user": {
      "id": 1,
      "first_name": "أحمد",
      "last_name": "محمد",
      "email": "ahmed@example.com",
      "full_name": "أحمد محمد",
      "avatar": null,
      "is_active": true
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
  }
}
```

#### الحصول على المستخدم الحالي
```http
GET /api/v1/auth/me
Headers: Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "message": "تمت العملية بنجاح",
  "data": {
    "user": {
      "id": 1,
      "first_name": "أحمد",
      "last_name": "محمد",
      "email": "ahmed@example.com",
      "phone": "+96812345678",
      "full_name": "أحمد محمد",
      "avatar": "https://your-domain.com/storage/avatars/user.jpg",
      "city": "مسقط",
      "country": "عمان",
      "is_active": true,
      "created_at": "2025-11-08T01:00:00+00:00"
    }
  }
}
```

---

### 2. Products (المنتجات)

#### الحصول على جميع المنتجات
```http
GET /api/v1/products?per_page=15&sort=latest&category=electronics&featured=true
```

**Query Parameters:**
- `per_page` - عدد العناصر (افتراضي: 15)
- `sort` - الترتيب: `latest`, `price_low`, `price_high`, `name`, `popular`
- `category` - فلترة حسب الفئة (slug)
- `search` - البحث
- `min_price` - الحد الأدنى للسعر
- `max_price` - الحد الأقصى للسعر
- `featured` - المنتجات المميزة فقط (`true`/`false`)

**Response (200):**
```json
{
  "success": true,
  "message": "تم جلب البيانات بنجاح",
  "data": [
    {
      "id": 1,
      "name": "بطاقة شحن PlayStation 50 ريال",
      "slug": "playstation-card-50",
      "description": "بطاقة شحن لجهاز PlayStation",
      "short_description": "بطاقة شحن 50 ريال",
      "price": 50.00,
      "sale_price": null,
      "current_price": 50.00,
      "sku": "PS-50-001",
      "stock_quantity": 100,
      "is_in_stock": true,
      "image": "https://your-domain.com/storage/products/ps-card.jpg",
      "images": [],
      "brand": "PlayStation",
      "card_provider": "Sony",
      "card_type": "gaming",
      "is_featured": true,
      "is_active": true,
      "tags": ["gaming", "playstation"],
      "category": {
        "id": 1,
        "name": "بطاقات الألعاب",
        "slug": "gaming-cards"
      },
      "average_rating": 4.5,
      "reviews_count": 25,
      "created_at": "2025-11-01T12:00:00+00:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75,
    "from": 1,
    "to": 15
  }
}
```

#### الحصول على منتج واحد
```http
GET /api/v1/products/1
```

**Response (200):**
```json
{
  "success": true,
  "message": "تمت العملية بنجاح",
  "data": {
    "id": 1,
    "name": "بطاقة شحن PlayStation 50 ريال",
    "slug": "playstation-card-50",
    "description": "بطاقة شحن لجهاز PlayStation",
    "price": 50.00,
    "current_price": 50.00,
    "image": "https://your-domain.com/storage/products/ps-card.jpg",
    "category": {
      "id": 1,
      "name": "بطاقات الألعاب",
      "slug": "gaming-cards"
    },
    "reviews": [
      {
        "id": 1,
        "rating": 5,
        "comment": "منتج رائع!",
        "user": {
          "id": 2,
          "name": "محمد علي",
          "avatar": null
        },
        "created_at": "2025-11-05T10:00:00+00:00"
      }
    ],
    "average_rating": 4.5,
    "reviews_count": 25
  }
}
```

---

### 3. Categories (الفئات)

#### الحصول على جميع الفئات
```http
GET /api/v1/categories
```

**Response (200):**
```json
{
  "success": true,
  "message": "تمت العملية بنجاح",
  "data": [
    {
      "id": 1,
      "name": "بطاقات الألعاب",
      "slug": "gaming-cards",
      "description": "بطاقات شحن للألعاب",
      "image": "https://your-domain.com/storage/categories/gaming.jpg",
      "is_active": true,
      "sort_order": 1,
      "products_count": 25,
      "created_at": "2025-11-01T12:00:00+00:00"
    }
  ]
}
```

---

### 4. Sliders (السلايدرات)

#### الحصول على سلايدرات الصفحة الرئيسية
```http
GET /api/v1/sliders/homepage
```

**Response (200):**
```json
{
  "success": true,
  "message": "تمت العملية بنجاح",
  "data": [
    {
      "id": 1,
      "title": "عروض رائعة على البطاقات الرقمية",
      "description": "احصل على أفضل العروض والخصومات",
      "image": "https://your-domain.com/storage/sliders/slider-1.jpg",
      "image_url": "https://your-domain.com/storage/sliders/slider-1.jpg",
      "button_text": "تسوق الآن",
      "button_url": "https://your-domain.com/products",
      "sort_order": 1,
      "is_active": true,
      "position": "homepage",
      "position_label": "الصفحة الرئيسية",
      "settings": {
        "animation_type": "fade",
        "animation_duration": 3
      },
      "starts_at": "2025-11-01T00:00:00+00:00",
      "ends_at": "2026-02-01T00:00:00+00:00",
      "is_currently_active": true,
      "created_at": "2025-11-01T12:00:00+00:00"
    }
  ]
}
```

---

### 5. Cart (السلة) - يتطلب مصادقة

#### الحصول على السلة
```http
GET /api/v1/cart
Headers: Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "message": "تمت العملية بنجاح",
  "data": {
    "id": 1,
    "items": [
      {
        "id": 1,
        "quantity": 2,
        "price": 50.00,
        "subtotal": 100.00,
        "product": {
          "id": 1,
          "name": "بطاقة شحن PlayStation 50 ريال",
          "slug": "playstation-card-50",
          "image": "https://your-domain.com/storage/products/ps-card.jpg",
          "current_price": 50.00
        },
        "created_at": "2025-11-08T01:00:00+00:00"
      }
    ],
    "subtotal": 100.00,
    "discount": 0.00,
    "total": 100.00,
    "coupon": null,
    "items_count": 2,
    "created_at": "2025-11-08T01:00:00+00:00"
  }
}
```

#### إضافة منتج للسلة
```http
POST /api/v1/cart/add
Headers: Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "product_id": 1,
  "quantity": 2
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "تم إضافة المنتج للسلة بنجاح",
  "data": {
    "id": 1,
    "items": [...],
    "subtotal": 100.00,
    "total": 100.00
  }
}
```

---

### 6. Orders (الطلبات) - يتطلب مصادقة

#### إنشاء طلب جديد
```http
POST /api/v1/orders
Headers: Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "shipping_first_name": "أحمد",
  "shipping_last_name": "محمد",
  "shipping_email": "ahmed@example.com",
  "shipping_phone": "+96812345678",
  "shipping_address": "شارع السلطان قابوس",
  "shipping_city": "مسقط",
  "shipping_country": "عمان",
  "shipping_postal_code": "12345",
  "payment_method": "amwalpay"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "تم إنشاء الطلب بنجاح",
  "data": {
    "id": 1,
    "order_number": "ORD-20251108-ABC123",
    "status": "pending",
    "payment_status": "pending",
    "subtotal": 100.00,
    "tax": 14.00,
    "discount": 0.00,
    "total": 114.00,
    "currency": "OMR",
    "items": [
      {
        "id": 1,
        "quantity": 2,
        "price": 50.00,
        "subtotal": 100.00,
        "status": "pending",
        "product": {
          "id": 1,
          "name": "بطاقة شحن PlayStation 50 ريال",
          "slug": "playstation-card-50",
          "image": "https://your-domain.com/storage/products/ps-card.jpg"
        }
      }
    ],
    "payment": {
      "id": 1,
      "method": "amwalpay",
      "status": "pending",
      "amount": 114.00
    },
    "shipping_address": {
      "first_name": "أحمد",
      "last_name": "محمد",
      "phone": "+96812345678",
      "email": "ahmed@example.com",
      "address": "شارع السلطان قابوس",
      "city": "مسقط",
      "country": "عمان",
      "postal_code": "12345"
    },
    "created_at": "2025-11-08T01:00:00+00:00"
  }
}
```

---

## 💻 أمثلة كود JavaScript

### 1. Helper Function للـ API

```javascript
// api.js
const API_BASE_URL = 'https://your-domain.com/api/v1';

class ApiClient {
  constructor() {
    this.token = localStorage.getItem('auth_token');
  }

  setToken(token) {
    this.token = token;
    localStorage.setItem('auth_token', token);
  }

  async request(endpoint, options = {}) {
    const url = `${API_BASE_URL}${endpoint}`;
    const headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      ...options.headers,
    };

    if (this.token) {
      headers['Authorization'] = `Bearer ${this.token}`;
    }

    try {
      const response = await fetch(url, {
        ...options,
        headers,
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'حدث خطأ');
      }

      return data;
    } catch (error) {
      console.error('API Error:', error);
      throw error;
    }
  }

  // Authentication
  async login(login, password) {
    const response = await this.request('/auth/login', {
      method: 'POST',
      body: JSON.stringify({ login, password }),
    });
    return response;
  }

  async verify(userId, code, type = 'login') {
    const response = await this.request('/auth/verify', {
      method: 'POST',
      body: JSON.stringify({ user_id: userId, code, type }),
    });
    if (response.data?.token) {
      this.setToken(response.data.token);
    }
    return response;
  }

  async getMe() {
    return this.request('/auth/me');
  }

  // Products
  async getProducts(params = {}) {
    const queryString = new URLSearchParams(params).toString();
    return this.request(`/products?${queryString}`);
  }

  async getProduct(id) {
    return this.request(`/products/${id}`);
  }

  // Sliders
  async getHomepageSliders() {
    return this.request('/sliders/homepage');
  }

  // Cart
  async getCart() {
    return this.request('/cart');
  }

  async addToCart(productId, quantity) {
    return this.request('/cart/add', {
      method: 'POST',
      body: JSON.stringify({ product_id: productId, quantity }),
    });
  }

  // Orders
  async createOrder(orderData) {
    return this.request('/orders', {
      method: 'POST',
      body: JSON.stringify(orderData),
    });
  }
}

export default new ApiClient();
```

### 2. استخدام في React

```jsx
// App.jsx
import { useState, useEffect } from 'react';
import ApiClient from './api';

function App() {
  const [sliders, setSliders] = useState([]);
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [user, setUser] = useState(null);

  useEffect(() => {
    loadData();
    loadUser();
  }, []);

  const loadData = async () => {
    try {
      // تحميل السلايدرات
      const slidersResponse = await ApiClient.getHomepageSliders();
      if (slidersResponse.success) {
        setSliders(slidersResponse.data);
      }

      // تحميل المنتجات
      const productsResponse = await ApiClient.getProducts({
        per_page: 12,
        sort: 'latest',
        featured: true
      });
      if (productsResponse.success) {
        setProducts(productsResponse.data);
      }
    } catch (error) {
      console.error('Error loading data:', error);
    } finally {
      setLoading(false);
    }
  };

  const loadUser = async () => {
    try {
      const response = await ApiClient.getMe();
      if (response.success) {
        setUser(response.data.user);
      }
    } catch (error) {
      console.error('Error loading user:', error);
    }
  };

  const handleLogin = async (email, password) => {
    try {
      // 1. تسجيل الدخول
      const loginResponse = await ApiClient.login(email, password);
      
      if (loginResponse.success && loginResponse.data.user_id) {
        // 2. طلب كود التحقق من المستخدم
        const code = prompt('أدخل كود التحقق المرسل إلى بريدك:');
        
        // 3. التحقق من الكود
        const verifyResponse = await ApiClient.verify(
          loginResponse.data.user_id,
          code,
          'login'
        );
        
        if (verifyResponse.success) {
          alert('تم تسجيل الدخول بنجاح!');
          loadUser();
        }
      }
    } catch (error) {
      alert('خطأ في تسجيل الدخول: ' + error.message);
    }
  };

  if (loading) {
    return <div>جاري التحميل...</div>;
  }

  return (
    <div>
      {/* السلايدرات */}
      <div className="sliders">
        {sliders.map(slider => (
          <div key={slider.id} className="slider">
            <img src={slider.image} alt={slider.title} />
            <div className="slider-content">
              <h2>{slider.title}</h2>
              {slider.description && <p>{slider.description}</p>}
              {slider.button_text && (
                <a href={slider.button_url} className="btn">
                  {slider.button_text}
                </a>
              )}
            </div>
          </div>
        ))}
      </div>

      {/* المنتجات */}
      <div className="products">
        {products.map(product => (
          <div key={product.id} className="product-card">
            <img src={product.image} alt={product.name} />
            <h3>{product.name}</h3>
            <p className="price">{product.current_price} ريال</p>
            <button onClick={() => handleAddToCart(product.id)}>
              أضف للسلة
            </button>
          </div>
        ))}
      </div>
    </div>
  );

  const handleAddToCart = async (productId) => {
    try {
      const response = await ApiClient.addToCart(productId, 1);
      if (response.success) {
        alert('تم إضافة المنتج للسلة!');
      }
    } catch (error) {
      alert('خطأ: ' + error.message);
    }
  };
}

export default App;
```

### 3. استخدام في Vue.js

```vue
<template>
  <div>
    <!-- السلايدرات -->
    <div class="sliders">
      <div v-for="slider in sliders" :key="slider.id" class="slider">
        <img :src="slider.image" :alt="slider.title" />
        <div class="slider-content">
          <h2>{{ slider.title }}</h2>
          <p v-if="slider.description">{{ slider.description }}</p>
          <a v-if="slider.button_text" :href="slider.button_url" class="btn">
            {{ slider.button_text }}
          </a>
        </div>
      </div>
    </div>

    <!-- المنتجات -->
    <div class="products">
      <div v-for="product in products" :key="product.id" class="product-card">
        <img :src="product.image" :alt="product.name" />
        <h3>{{ product.name }}</h3>
        <p class="price">{{ product.current_price }} ريال</p>
        <button @click="addToCart(product.id)">أضف للسلة</button>
      </div>
    </div>
  </div>
</template>

<script>
import ApiClient from './api';

export default {
  data() {
    return {
      sliders: [],
      products: [],
      loading: true
    };
  },
  async mounted() {
    await this.loadData();
  },
  methods: {
    async loadData() {
      try {
        // السلايدرات
        const slidersRes = await ApiClient.getHomepageSliders();
        if (slidersRes.success) {
          this.sliders = slidersRes.data;
        }

        // المنتجات
        const productsRes = await ApiClient.getProducts({
          per_page: 12,
          featured: true
        });
        if (productsRes.success) {
          this.products = productsRes.data;
        }
      } catch (error) {
        console.error('Error:', error);
      } finally {
        this.loading = false;
      }
    },
    async addToCart(productId) {
      try {
        const response = await ApiClient.addToCart(productId, 1);
        if (response.success) {
          alert('تم إضافة المنتج للسلة!');
        }
      } catch (error) {
        alert('خطأ: ' + error.message);
      }
    }
  }
};
</script>
```

---

## ⚠️ معالجة الأخطاء

### أكواد الحالة (Status Codes)

- `200` - نجاح العملية
- `201` - تم الإنشاء بنجاح
- `400` - خطأ في الطلب
- `401` - غير مصرح (غير مسجل دخول)
- `403` - ممنوع الوصول
- `404` - غير موجود
- `422` - خطأ في التحقق من البيانات
- `429` - تجاوز حد الطلبات
- `500` - خطأ في الخادم

### مثال معالجة الأخطاء

```javascript
async function handleApiCall() {
  try {
    const response = await ApiClient.getProducts();
    
    if (response.success) {
      // نجاح العملية
      console.log(response.data);
    } else {
      // خطأ في الاستجابة
      console.error(response.message);
    }
  } catch (error) {
    // خطأ في الشبكة أو الخادم
    if (error.message.includes('401')) {
      // غير مصرح - إعادة توجيه لتسجيل الدخول
      window.location.href = '/login';
    } else if (error.message.includes('429')) {
      // تجاوز الحد - انتظر قليلاً
      alert('تم تجاوز عدد الطلبات المسموح. يرجى المحاولة لاحقاً');
    } else {
      // خطأ عام
      alert('حدث خطأ: ' + error.message);
    }
  }
}
```

---

## 📝 Checklist للمبرمج Frontend

- [ ] استخدام Base URL الصحيح
- [ ] حفظ Token في localStorage بعد تسجيل الدخول
- [ ] إرسال Token في Header لجميع الطلبات المحمية
- [ ] التحقق من `success` قبل استخدام البيانات
- [ ] معالجة الأخطاء بشكل صحيح
- [ ] استخدام Pagination عند الحاجة
- [ ] التحقق من وجود البيانات قبل عرضها (null checks)
- [ ] استخدام Loading States
- [ ] معالجة حالات Empty State

---

## 🔗 روابط مفيدة

- **Base URL**: `https://your-domain.com/api/v1`
- **Authentication**: Bearer Token في Header
- **Response Format**: JSON موحد مع `success`, `message`, `data`

---

**تم إنشاء هذا الدليل بواسطة Digital Cards Store API Team**

