# CardFlow - Digital Card Marketplace Platform

[![Laravel](https://img.shields.io/badge/Laravel-12.0-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A comprehensive, enterprise-grade digital card marketplace platform built with Laravel 12. CardFlow enables businesses to sell digital gift cards, game cards, and prepaid cards with a fully-featured admin dashboard, customer portal, and advanced e-commerce capabilities.

## 🚀 Features

### 🛍️ E-Commerce Core
- **Product Management**: Complete catalog management for digital cards with categories, brands, and variants
- **Shopping Cart**: Advanced cart system with persistent sessions and guest checkout support
- **Checkout System**: Streamlined checkout process with multiple payment options
- **Order Management**: Comprehensive order tracking and management system
- **Payment Integration**: Secure payment processing via AmwalPay gateway

### 👥 User Management
- **Authentication System**: Custom authentication with email/phone login and password reset
- **User Profiles**: Complete profile management with order history and preferences
- **Role-Based Access Control (RBAC)**: Granular permissions system with roles and permissions
- **Session Tracking**: Advanced session management and user activity tracking

### 💎 Loyalty & Rewards
- **Loyalty Points**: Earn and redeem points on purchases
- **Coupon System**: Flexible discount coupons and promotional codes
- **Referral Program**: Built-in referral system with rewards for both referrer and referee
- **Loyalty Settings**: Configurable point earning rates and redemption rules

### 🤖 AI Assistant
- **AI Chat Bot**: Intelligent customer support chat with product recommendations
- **Knowledge Base**: AI-powered knowledge base for instant customer assistance
- **Product Suggestions**: Smart product recommendations based on user queries

### 📊 Admin Dashboard
- **Analytics Dashboard**: Real-time statistics and performance metrics
- **Product Management**: Full CRUD operations for products, categories, and inventory
- **Order Management**: Complete order processing and fulfillment system
- **User Management**: User administration with role and permission assignment
- **Sales Reports**: Detailed sales reports with export capabilities
- **Email Management**: Bulk email sending and campaign management
- **Notification System**: In-app notification management
- **Settings Panel**: Comprehensive system configuration

### 🎨 User Experience
- **Modern UI Design**: Beautiful, responsive design with purple accent theme
- **Responsive Layout**: Mobile-first design optimized for all devices
- **Component-Based**: Reusable Blade components for consistent UI
- **Search & Filters**: Advanced product search and filtering capabilities
- **Product Reviews**: Customer review and rating system with voting
- **Image Galleries**: Multiple product images with gallery view
- **Slider Management**: Homepage slider management for promotions

### 🔒 Security Features
- **CSRF Protection**: Built-in Laravel CSRF protection
- **Rate Limiting**: Protection against brute-force attacks
- **Password Hashing**: Secure bcrypt password hashing
- **Session Security**: Secure session management
- **Input Validation**: Comprehensive form validation
- **SQL Injection Protection**: Eloquent ORM protection
- **XSS Protection**: Blade template escaping

## 📋 Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL 5.7+ / MariaDB 10.3+ / PostgreSQL 10+
- Laravel 12.0

## 🔧 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/cardflow.git
cd cardflow
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Database
Edit `.env` file and set your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cardflow
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run Migrations & Seeders
```bash
# Run migrations
php artisan migrate

# Seed database with initial data
php artisan db:seed
```

### 6. Build Assets
```bash
# Build frontend assets
npm run build

# Or for development with hot reload
npm run dev
```

### 7. Start Development Server
```bash
# Start Laravel development server
php artisan serve

# Start queue worker (optional, for background jobs)
php artisan queue:work
```

### 8. Access the Application
- **Frontend**: http://localhost:8000
- **Admin Dashboard**: http://localhost:8000/dashboard
- **Login**: http://localhost:8000/login

## 👤 Default Accounts

After seeding the database, you can use these default accounts:

### Administrator
- **Email**: admin@example.com
- **Password**: password

### Regular User
- **Email**: user@example.com
- **Password**: password

> ⚠️ **Security Note**: Change default passwords immediately in production!

## 📁 Project Structure

```
cardflow/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/          # API controllers (AI Chat, etc.)
│   │   │   ├── Auth/         # Authentication controllers
│   │   │   └── Dashboard/    # Admin dashboard controllers
│   │   ├── Middleware/       # Custom middleware
│   │   └── Requests/         # Form request validation
│   ├── Mail/                 # Email templates and classes
│   ├── Models/               # Eloquent models
│   └── Services/             # Business logic services
├── config/                   # Configuration files
├── database/
│   ├── factories/            # Model factories
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── public/                   # Public assets
│   └── assets/
│       ├── js/               # JavaScript files
│       └── styles/           # CSS stylesheets
├── resources/
│   ├── views/                # Blade templates
│   │   ├── components/       # Reusable components
│   │   ├── dashboard/        # Admin dashboard views
│   │   └── layouts/          # Layout templates
│   ├── css/                  # CSS source files
│   └── js/                   # JavaScript source files
├── routes/
│   └── web.php               # Web routes
└── tests/                    # Automated tests
```

## 🎯 Key Routes

### Public Routes
- `/` - Homepage
- `/products` - Product catalog
- `/products/{slug}` - Product details
- `/cart` - Shopping cart
- `/checkout` - Checkout process
- `/login` - User login
- `/register` - User registration
- `/contact` - Contact page

### Protected Routes (Authenticated Users)
- `/profile` - User profile
- `/profile/orders` - Order history
- `/profile/loyalty-points` - Loyalty points
- `/profile/referrals` - Referral program

### Admin Dashboard Routes
- `/dashboard` - Main dashboard
- `/dashboard/products` - Product management
- `/dashboard/orders` - Order management
- `/dashboard/users` - User management
- `/dashboard/categories` - Category management
- `/dashboard/coupons` - Coupon management
- `/dashboard/reports` - Sales reports
- `/dashboard/settings` - System settings

## 🧩 Component System

### Web Components
```blade
<x-web.login-form />
<x-web.register-form />
```

### Dashboard Components
```blade
<x-dashboard.sidebar />
<x-dashboard.header />
<x-dashboard.stats-card 
    title="Total Sales"
    value="1,234"
    icon="bi-currency-dollar" />
<x-dashboard.data-table 
    title="Recent Orders"
    :headers="['Order ID', 'Customer', 'Amount']"
    :data="$ordersData" />
<x-dashboard.filters 
    :filters="$filtersArray"
    search-placeholder="Search..." />
```

## 🎨 Customization

### Changing Theme Colors
Edit `public/assets/styles/variables.css`:
```css
:root {
    --primary-purple: #YOUR_COLOR;
    --secondary-purple: #YOUR_COLOR;
    --accent-color: #YOUR_COLOR;
}
```

### Adding New Components
1. Create component file in `resources/views/components/dashboard/`
2. Use the component in views
3. Add corresponding CSS if needed

### Adding New Pages
1. Create route in `routes/web.php`
2. Create controller in `app/Http/Controllers/`
3. Create view in `resources/views/`
4. Add navigation link if needed

## 🗄️ Database Schema

### Core Tables
- `users` - User accounts and authentication
- `products` - Product catalog
- `categories` - Product categories
- `orders` - Customer orders
- `order_items` - Order line items
- `cart` - Shopping cart sessions
- `cart_items` - Cart line items
- `digital_cards` - Digital card inventory
- `payments` - Payment transactions

### Loyalty & Rewards
- `loyalty_points` - User loyalty points balance
- `loyalty_point_transactions` - Points transaction history
- `loyalty_settings` - Loyalty program configuration
- `coupons` - Discount coupons
- `coupon_usages` - Coupon redemption history
- `referrals` - Referral tracking
- `referral_rewards` - Referral rewards

### Admin & Permissions
- `roles` - User roles
- `permissions` - System permissions
- `role_permissions` - Role-permission mapping
- `user_roles` - User-role assignments

### Additional Features
- `product_reviews` - Product reviews and ratings
- `review_votes` - Review voting system
- `notifications` - In-app notifications
- `contacts` - Contact form submissions
- `sliders` - Homepage sliders
- `settings` - System settings

## 🔐 Security Best Practices

- ✅ CSRF protection enabled
- ✅ Password hashing with bcrypt
- ✅ SQL injection prevention via Eloquent
- ✅ XSS protection via Blade escaping
- ✅ Rate limiting on authentication
- ✅ Secure session management
- ✅ Role-based access control
- ✅ Input validation on all forms

### Production Checklist
- [ ] Change default admin credentials
- [ ] Enable HTTPS
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure secure session settings
- [ ] Set up database backups
- [ ] Configure queue workers
- [ ] Set up logging and monitoring
- [ ] Configure payment gateway properly
- [ ] Review file permissions

## 📈 Performance Optimization

### Implemented Optimizations
- Component-based architecture for code reusability
- Optimized database queries with eager loading
- CSS/JS asset minification
- Caching strategies for frequently accessed data
- Efficient session management

### Recommended Optimizations
- Use Redis for session and cache storage
- Implement CDN for static assets
- Enable database query caching
- Optimize images before upload
- Use queue workers for heavy operations
- Implement lazy loading for images
- Add database indexing for frequently queried columns

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

## 📦 Deployment

### Deployment Steps
1. Clone repository on production server
2. Install dependencies: `composer install --optimize-autoloader --no-dev`
3. Build assets: `npm run build`
4. Set environment variables in `.env`
5. Generate app key: `php artisan key:generate`
6. Run migrations: `php artisan migrate --force`
7. Optimize: `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`
8. Set up queue worker: `php artisan queue:work --daemon`
9. Configure web server (Nginx/Apache)
10. Set up SSL certificate

## 🤝 Contributing

Contributions are welcome! Please follow these steps:
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

For support, email support@cardflow.com or open an issue in the GitHub repository.

## 🙏 Acknowledgments

- Laravel Framework
- Bootstrap Icons
- All contributors and supporters

---

**CardFlow** - Empowering Digital Card Commerce

Built with ❤️ using Laravel 12
