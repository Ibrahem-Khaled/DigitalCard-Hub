# دليل API المصادقة (Authentication) - للمبرمج Frontend

## 📋 نظرة عامة

هذا الدليل الشامل لجميع endpoints المصادقة في API متجر البطاقات الرقمية. جميع الردود (Responses) موضحة بالتفصيل.

---

## 🔗 Base URL

```
https://your-domain.com/api/v1/auth
```

---

## 🔐 جميع Endpoints المصادقة

### 1. تسجيل مستخدم جديد (Register)

**Endpoint:** `POST /api/v1/auth/register`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "first_name": "أحمد",
  "last_name": "محمد",
  "email": "ahmed@example.com",
  "phone": "+96812345678",
  "password": "password123",
  "password_confirmation": "password123",
  "birth_date": "1990-01-01",
  "gender": "male",
  "address": "شارع السلطان قابوس",
  "city": "مسقط",
  "country": "عمان",
  "postal_code": "12345"
}
```

**ملاحظات:**
- `first_name`, `last_name`, `email`, `password`, `password_confirmation` - **مطلوبة**
- باقي الحقول **اختيارية**

**Response (201 Created):**
```json
{
  "success": true,
  "message": "تم التسجيل بنجاح. يرجى التحقق من بريدك الإلكتروني لإكمال التسجيل",
  "data": {
    "user": {
      "id": 12,
      "first_name": "أحمد",
      "last_name": "محمد",
      "full_name": "أحمد محمد",
      "email": "ahmed@example.com",
      "phone": "+96812345678",
      "avatar": null,
      "birth_date": "1990-01-01",
      "gender": "male",
      "address": "شارع السلطان قابوس",
      "city": "مسقط",
      "country": "عمان",
      "postal_code": "12345",
      "is_active": true,
      "email_verified_at": null,
      "phone_verified_at": null,
      "last_login_at": null,
      "roles": [],
      "created_at": "2025-11-08T01:00:00+00:00",
      "updated_at": "2025-11-08T01:00:00+00:00"
    },
    "token": "1|644b566c196c45c1c28a394eb5e0ed08880d853440da75a85837a83061a3d5c7",
    "requires_verification": true
  }
}
```

**Response عند خطأ (422 Unprocessable Entity):**
```json
{
  "success": false,
  "message": "بيانات غير صحيحة",
  "errors": {
    "email": [
      "البريد الإلكتروني مستخدم بالفعل"
    ],
    "password": [
      "كلمة المرور يجب أن تكون 8 أحرف على الأقل"
    ]
  }
}
```

**مثال كود JavaScript:**
```javascript
async function register(userData) {
  try {
    const response = await fetch('https://your-domain.com/api/v1/auth/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(userData)
    });

    const data = await response.json();

    if (data.success) {
      // حفظ Token إذا كان موجوداً
      if (data.data.token) {
        localStorage.setItem('auth_token', data.data.token);
      }
      // حفظ user_id للتحقق من الكود
      if (data.data.user.id) {
        localStorage.setItem('user_id', data.data.user.id);
      }
      return data;
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Registration error:', error);
    throw error;
  }
}

// الاستخدام
const userData = {
  first_name: "أحمد",
  last_name: "محمد",
  email: "ahmed@example.com",
  phone: "+96812345678",
  password: "password123",
  password_confirmation: "password123"
};

register(userData)
  .then(data => {
    console.log('تم التسجيل بنجاح:', data);
    if (data.data.requires_verification) {
      // توجيه المستخدم لصفحة التحقق من الكود
      window.location.href = '/verify-code';
    }
  })
  .catch(error => {
    console.error('خطأ في التسجيل:', error);
  });
```

---

### 2. تسجيل الدخول (Login)

**Endpoint:** `POST /api/v1/auth/login`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "login": "ahmed@example.com",
  "password": "password123"
}
```

**ملاحظات:**
- `login` يمكن أن يكون **البريد الإلكتروني** أو **رقم الهاتف**
- `password` - **مطلوب**

**Response (200 OK):**
```json
{
  "success": true,
  "message": "تم إرسال كود التحقق إلى بريدك الإلكتروني",
  "data": {
    "user_id": 12,
    "requires_verification": true
  }
}
```

**Response عند خطأ (401 Unauthorized):**
```json
{
  "success": false,
  "message": "بيانات الدخول غير صحيحة"
}
```

**Response عند تجاوز الحد (429 Too Many Requests):**
```json
{
  "success": false,
  "message": "تم تجاوز عدد المحاولات المسموح. حاول مرة أخرى خلال 300 ثانية."
}
```

**مثال كود JavaScript:**
```javascript
async function login(login, password) {
  try {
    const response = await fetch('https://your-domain.com/api/v1/auth/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ login, password })
    });

    const data = await response.json();

    if (data.success) {
      // حفظ user_id للتحقق من الكود
      if (data.data.user_id) {
        localStorage.setItem('user_id', data.data.user_id);
      }
      return data;
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Login error:', error);
    throw error;
  }
}

// الاستخدام
login('ahmed@example.com', 'password123')
  .then(data => {
    console.log('تم إرسال كود التحقق:', data);
    if (data.data.requires_verification) {
      // توجيه المستخدم لصفحة إدخال كود التحقق
      window.location.href = '/verify-code';
    }
  })
  .catch(error => {
    alert('خطأ في تسجيل الدخول: ' + error.message);
  });
```

---

### 3. التحقق من الكود (Verify Code)

**Endpoint:** `POST /api/v1/auth/verify`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "user_id": 12,
  "code": "123456",
  "type": "login"
}
```

**ملاحظات:**
- `user_id` - **مطلوب** (من response تسجيل الدخول أو التسجيل)
- `code` - **مطلوب** (6 أرقام)
- `type` - **اختياري** (`login` أو `registration` - افتراضي: `login`)

**Response (200 OK):**
```json
{
  "success": true,
  "message": "تم التحقق بنجاح",
  "data": {
    "user": {
      "id": 12,
      "first_name": "أحمد",
      "last_name": "محمد",
      "full_name": "أحمد محمد",
      "email": "ahmed@example.com",
      "phone": "+96812345678",
      "avatar": null,
      "birth_date": "1990-01-01",
      "gender": "male",
      "address": "شارع السلطان قابوس",
      "city": "مسقط",
      "country": "عمان",
      "postal_code": "12345",
      "is_active": true,
      "email_verified_at": "2025-11-08T01:05:00+00:00",
      "phone_verified_at": null,
      "last_login_at": "2025-11-08T01:05:00+00:00",
      "roles": [
        {
          "id": 2,
          "name": "عميل",
          "slug": "customer"
        }
      ],
      "created_at": "2025-11-08T01:00:00+00:00",
      "updated_at": "2025-11-08T01:05:00+00:00"
    },
    "token": "1|644b566c196c45c1c28a394eb5e0ed08880d853440da75a85837a83061a3d5c7"
  }
}
```

**Response عند خطأ (400 Bad Request):**
```json
{
  "success": false,
  "message": "كود التحقق غير صحيح أو منتهي الصلاحية"
}
```

**مثال كود JavaScript:**
```javascript
async function verifyCode(userId, code, type = 'login') {
  try {
    const response = await fetch('https://your-domain.com/api/v1/auth/verify', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        user_id: userId,
        code: code,
        type: type
      })
    });

    const data = await response.json();

    if (data.success) {
      // حفظ Token
      if (data.data.token) {
        localStorage.setItem('auth_token', data.data.token);
        localStorage.setItem('user', JSON.stringify(data.data.user));
      }
      return data;
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Verification error:', error);
    throw error;
  }
}

// الاستخدام
const userId = localStorage.getItem('user_id');
const code = '123456'; // من المستخدم

verifyCode(userId, code, 'login')
  .then(data => {
    console.log('تم التحقق بنجاح:', data);
    // توجيه المستخدم للصفحة الرئيسية
    window.location.href = '/dashboard';
  })
  .catch(error => {
    alert('خطأ: ' + error.message);
  });
```

---

### 4. إعادة إرسال كود التحقق (Resend Verification Code)

**Endpoint:** `POST /api/v1/auth/resend-verification`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "user_id": 12,
  "type": "login"
}
```

**ملاحظات:**
- `user_id` - **مطلوب**
- `type` - **اختياري** (`login` أو `registration` - افتراضي: `login`)

**Response (200 OK):**
```json
{
  "success": true,
  "message": "تم إرسال كود التحقق مرة أخرى"
}
```

**مثال كود JavaScript:**
```javascript
async function resendVerificationCode(userId, type = 'login') {
  try {
    const response = await fetch('https://your-domain.com/api/v1/auth/resend-verification', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        user_id: userId,
        type: type
      })
    });

    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Resend verification error:', error);
    throw error;
  }
}
```

---

### 5. الحصول على المستخدم الحالي (Get Current User)

**Endpoint:** `GET /api/v1/auth/me`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**ملاحظات:**
- **يتطلب مصادقة** (Token)

**Response (200 OK):**
```json
{
  "success": true,
  "message": "تمت العملية بنجاح",
  "data": {
    "user": {
      "id": 12,
      "first_name": "أحمد",
      "last_name": "محمد",
      "full_name": "أحمد محمد",
      "email": "ahmed@example.com",
      "phone": "+96812345678",
      "avatar": "https://your-domain.com/storage/avatars/user.jpg",
      "birth_date": "1990-01-01",
      "gender": "male",
      "address": "شارع السلطان قابوس",
      "city": "مسقط",
      "country": "عمان",
      "postal_code": "12345",
      "is_active": true,
      "email_verified_at": "2025-11-08T01:05:00+00:00",
      "phone_verified_at": null,
      "last_login_at": "2025-11-08T01:05:00+00:00",
      "roles": [
        {
          "id": 2,
          "name": "عميل",
          "slug": "customer"
        }
      ],
      "created_at": "2025-11-08T01:00:00+00:00",
      "updated_at": "2025-11-08T01:05:00+00:00"
    }
  }
}
```

**Response عند خطأ (401 Unauthorized):**
```json
{
  "success": false,
  "message": "غير مصرح لك بالوصول"
}
```

**مثال كود JavaScript:**
```javascript
async function getCurrentUser() {
  const token = localStorage.getItem('auth_token');
  
  if (!token) {
    throw new Error('No token found');
  }

  try {
    const response = await fetch('https://your-domain.com/api/v1/auth/me', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    const data = await response.json();

    if (data.success) {
      return data.data.user;
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Get user error:', error);
    // إذا كان Token غير صالح، احذفه
    if (error.message.includes('401')) {
      localStorage.removeItem('auth_token');
      window.location.href = '/login';
    }
    throw error;
  }
}
```

---

### 6. تحديث الملف الشخصي (Update Profile)

**Endpoint:** `PUT /api/v1/auth/profile`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "first_name": "أحمد",
  "last_name": "محمد",
  "email": "ahmed@example.com",
  "phone": "+96812345678",
  "birth_date": "1990-01-01",
  "gender": "male",
  "address": "شارع السلطان قابوس",
  "city": "مسقط",
  "country": "عمان",
  "postal_code": "12345"
}
```

**ملاحظات:**
- جميع الحقول **اختيارية**
- يمكن تحديث أي حقل أو أكثر
- **يتطلب مصادقة** (Token)

**Response (200 OK):**
```json
{
  "success": true,
  "message": "تم تحديث الملف الشخصي بنجاح",
  "data": {
    "user": {
      "id": 12,
      "first_name": "أحمد",
      "last_name": "محمد",
      "full_name": "أحمد محمد",
      "email": "ahmed@example.com",
      "phone": "+96812345678",
      "avatar": null,
      "birth_date": "1990-01-01",
      "gender": "male",
      "address": "شارع السلطان قابوس",
      "city": "مسقط",
      "country": "عمان",
      "postal_code": "12345",
      "is_active": true,
      "created_at": "2025-11-08T01:00:00+00:00",
      "updated_at": "2025-11-08T01:10:00+00:00"
    }
  }
}
```

**مثال كود JavaScript:**
```javascript
async function updateProfile(profileData) {
  const token = localStorage.getItem('auth_token');
  
  try {
    const response = await fetch('https://your-domain.com/api/v1/auth/profile', {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(profileData)
    });

    const data = await response.json();

    if (data.success) {
      // تحديث بيانات المستخدم في localStorage
      localStorage.setItem('user', JSON.stringify(data.data.user));
      return data;
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Update profile error:', error);
    throw error;
  }
}

// الاستخدام
const profileData = {
  first_name: "أحمد",
  last_name: "محمد",
  city: "مسقط",
  country: "عمان"
};

updateProfile(profileData)
  .then(data => {
    alert('تم تحديث الملف الشخصي بنجاح!');
  })
  .catch(error => {
    alert('خطأ: ' + error.message);
  });
```

---

### 7. تغيير كلمة المرور (Change Password)

**Endpoint:** `POST /api/v1/auth/change-password`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "current_password": "password123",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

**ملاحظات:**
- جميع الحقول **مطلوبة**
- **يتطلب مصادقة** (Token)

**Response (200 OK):**
```json
{
  "success": true,
  "message": "تم تغيير كلمة المرور بنجاح"
}
```

**Response عند خطأ (400 Bad Request):**
```json
{
  "success": false,
  "message": "كلمة المرور الحالية غير صحيحة"
}
```

**مثال كود JavaScript:**
```javascript
async function changePassword(currentPassword, newPassword, confirmPassword) {
  const token = localStorage.getItem('auth_token');
  
  try {
    const response = await fetch('https://your-domain.com/api/v1/auth/change-password', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        current_password: currentPassword,
        password: newPassword,
        password_confirmation: confirmPassword
      })
    });

    const data = await response.json();

    if (data.success) {
      return data;
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Change password error:', error);
    throw error;
  }
}
```

---

### 8. نسيت كلمة المرور (Forgot Password)

**Endpoint:** `POST /api/v1/auth/forgot-password`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "email": "ahmed@example.com"
}
```

**ملاحظات:**
- `email` - **مطلوب**
- **لا يتطلب مصادقة**

**Response (200 OK):**
```json
{
  "success": true,
  "message": "تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني",
  "data": {
    "reset_token": "reset_token_here_xxxxxxxxxxxx"
  }
}
```

**ملاحظة:** في الإنتاج، `reset_token` لا يُعاد في الرد، بل يُرسل عبر البريد الإلكتروني.

**مثال كود JavaScript:**
```javascript
async function forgotPassword(email) {
  try {
    const response = await fetch('https://your-domain.com/api/v1/auth/forgot-password', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ email })
    });

    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Forgot password error:', error);
    throw error;
  }
}

// الاستخدام
forgotPassword('ahmed@example.com')
  .then(data => {
    alert('تم إرسال رابط إعادة التعيين إلى بريدك الإلكتروني');
  })
  .catch(error => {
    alert('خطأ: ' + error.message);
  });
```

---

### 9. إعادة تعيين كلمة المرور (Reset Password)

**Endpoint:** `POST /api/v1/auth/reset-password`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "token": "reset_token_from_email",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

**ملاحظات:**
- `token` - **مطلوب** (من رابط البريد الإلكتروني)
- `password` و `password_confirmation` - **مطلوبة**
- **لا يتطلب مصادقة**

**Response (200 OK):**
```json
{
  "success": true,
  "message": "تم إعادة تعيين كلمة المرور بنجاح"
}
```

**Response عند خطأ (400 Bad Request):**
```json
{
  "success": false,
  "message": "رمز إعادة التعيين غير صحيح أو منتهي الصلاحية"
}
```

**مثال كود JavaScript:**
```javascript
async function resetPassword(token, password, passwordConfirmation) {
  try {
    const response = await fetch('https://your-domain.com/api/v1/auth/reset-password', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        token: token,
        password: password,
        password_confirmation: passwordConfirmation
      })
    });

    const data = await response.json();

    if (data.success) {
      return data;
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Reset password error:', error);
    throw error;
  }
}
```

---

### 10. تحديث Token (Refresh Token)

**Endpoint:** `POST /api/v1/auth/refresh-token`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**ملاحظات:**
- **يتطلب مصادقة** (Token القديم)
- يحذف Token القديم وينشئ token جديد

**Response (200 OK):**
```json
{
  "success": true,
  "message": "تم تحديث الرمز المميز بنجاح",
  "data": {
    "token": "2|new_token_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
  }
}
```

**مثال كود JavaScript:**
```javascript
async function refreshToken() {
  const token = localStorage.getItem('auth_token');
  
  try {
    const response = await fetch('https://your-domain.com/api/v1/auth/refresh-token', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    const data = await response.json();

    if (data.success) {
      // حفظ Token الجديد
      localStorage.setItem('auth_token', data.data.token);
      return data;
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Refresh token error:', error);
    throw error;
  }
}
```

---

### 11. تسجيل الخروج (Logout)

**Endpoint:** `POST /api/v1/auth/logout`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**ملاحظات:**
- **يتطلب مصادقة** (Token)
- يحذف Token من قاعدة البيانات

**Request Body:** لا يوجد

**Response (200 OK):**
```json
{
  "success": true,
  "message": "تم تسجيل الخروج بنجاح"
}
```

**مثال كود JavaScript:**
```javascript
async function logout() {
  const token = localStorage.getItem('auth_token');
  
  try {
    const response = await fetch('https://your-domain.com/api/v1/auth/logout', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    const data = await response.json();

    if (data.success) {
      // حذف Token من localStorage
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      localStorage.removeItem('user_id');
      
      // توجيه المستخدم لصفحة تسجيل الدخول
      window.location.href = '/login';
      return data;
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Logout error:', error);
    // حتى لو فشل الطلب، احذف البيانات المحلية
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    localStorage.removeItem('user_id');
    throw error;
  }
}
```

---

## 📝 مثال كامل: Helper Class للمصادقة

```javascript
// AuthService.js
class AuthService {
  constructor() {
    this.baseURL = 'https://your-domain.com/api/v1/auth';
    this.token = localStorage.getItem('auth_token');
    this.user = JSON.parse(localStorage.getItem('user') || 'null');
  }

  setToken(token) {
    this.token = token;
    localStorage.setItem('auth_token', token);
  }

  setUser(user) {
    this.user = user;
    localStorage.setItem('user', JSON.stringify(user));
  }

  clearAuth() {
    this.token = null;
    this.user = null;
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    localStorage.removeItem('user_id');
  }

  getHeaders(includeAuth = false) {
    const headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    };

    if (includeAuth && this.token) {
      headers['Authorization'] = `Bearer ${this.token}`;
    }

    return headers;
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`;
    const headers = this.getHeaders(options.requireAuth || false);

    try {
      const response = await fetch(url, {
        ...options,
        headers: {
          ...headers,
          ...options.headers
        }
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

  // Register
  async register(userData) {
    const response = await this.request('/register', {
      method: 'POST',
      body: JSON.stringify(userData)
    });

    if (response.data?.token) {
      this.setToken(response.data.token);
    }
    if (response.data?.user) {
      this.setUser(response.data.user);
    }
    if (response.data?.user?.id) {
      localStorage.setItem('user_id', response.data.user.id);
    }

    return response;
  }

  // Login
  async login(login, password) {
    const response = await this.request('/login', {
      method: 'POST',
      body: JSON.stringify({ login, password })
    });

    if (response.data?.user_id) {
      localStorage.setItem('user_id', response.data.user_id);
    }

    return response;
  }

  // Verify
  async verify(userId, code, type = 'login') {
    const response = await this.request('/verify', {
      method: 'POST',
      body: JSON.stringify({ user_id: userId, code, type })
    });

    if (response.data?.token) {
      this.setToken(response.data.token);
    }
    if (response.data?.user) {
      this.setUser(response.data.user);
    }

    return response;
  }

  // Resend Verification
  async resendVerification(userId, type = 'login') {
    return this.request('/resend-verification', {
      method: 'POST',
      body: JSON.stringify({ user_id: userId, type })
    });
  }

  // Get Current User
  async getMe() {
    const response = await this.request('/me', {
      method: 'GET',
      requireAuth: true
    });

    if (response.data?.user) {
      this.setUser(response.data.user);
    }

    return response;
  }

  // Update Profile
  async updateProfile(profileData) {
    const response = await this.request('/profile', {
      method: 'PUT',
      requireAuth: true,
      body: JSON.stringify(profileData)
    });

    if (response.data?.user) {
      this.setUser(response.data.user);
    }

    return response;
  }

  // Change Password
  async changePassword(currentPassword, newPassword, confirmPassword) {
    return this.request('/change-password', {
      method: 'POST',
      requireAuth: true,
      body: JSON.stringify({
        current_password: currentPassword,
        password: newPassword,
        password_confirmation: confirmPassword
      })
    });
  }

  // Forgot Password
  async forgotPassword(email) {
    return this.request('/forgot-password', {
      method: 'POST',
      body: JSON.stringify({ email })
    });
  }

  // Reset Password
  async resetPassword(token, password, passwordConfirmation) {
    return this.request('/reset-password', {
      method: 'POST',
      body: JSON.stringify({
        token,
        password,
        password_confirmation: passwordConfirmation
      })
    });
  }

  // Refresh Token
  async refreshToken() {
    const response = await this.request('/refresh-token', {
      method: 'POST',
      requireAuth: true
    });

    if (response.data?.token) {
      this.setToken(response.data.token);
    }

    return response;
  }

  // Logout
  async logout() {
    try {
      await this.request('/logout', {
        method: 'POST',
        requireAuth: true
      });
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      this.clearAuth();
      window.location.href = '/login';
    }
  }

  // Check if user is authenticated
  isAuthenticated() {
    return !!this.token;
  }

  // Get current user
  getCurrentUser() {
    return this.user;
  }
}

export default new AuthService();
```

---

## ✅ Checklist للمبرمج Frontend

- [ ] حفظ Token في localStorage بعد التحقق من الكود
- [ ] حفظ user_id بعد تسجيل الدخول/التسجيل
- [ ] إرسال Token في Header لجميع الطلبات المحمية
- [ ] التحقق من `success` قبل استخدام البيانات
- [ ] معالجة الأخطاء بشكل صحيح
- [ ] حذف Token عند تسجيل الخروج
- [ ] إعادة توجيه المستخدم عند انتهاء صلاحية Token
- [ ] عرض رسائل خطأ واضحة للمستخدم
- [ ] التحقق من صحة البيانات قبل الإرسال

---

**تم إنشاء هذا الدليل بواسطة Digital Cards Store API Team**

