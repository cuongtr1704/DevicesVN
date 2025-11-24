# DevicesVN - E-Commerce Platform

## 🎯 Project Overview
DevicesVN is a complete e-commerce website for selling electronic devices (laptops, phones, gaming devices, accessories) built with PHP, MySQL, and Bootstrap following MVC architecture.

## 📁 Folder Structure

```
devicesvn/
├── public/              # Web root (entry point)
│   ├── index.php       # Main entry file
│   ├── .htaccess       # URL rewriting
│   ├── css/            # Stylesheets
│   ├── js/             # JavaScript files
│   ├── images/         # Images
│   └── assets/         # Other public assets
│
├── app/                # Application logic
│   ├── controllers/    # Controllers
│   ├── models/         # Models
│   ├── core/           # Core classes (App, Controller, Database)
│   └── helpers/        # Helper functions
│
├── config/             # Configuration files
│   ├── app.php         # App settings
│   └── database.php    # Database config
│
├── resources/          # Views and templates
│   └── views/
│       ├── layouts/    # Layout templates
│       ├── home/       # Home views
│       ├── products/   # Product views
│       ├── auth/       # Auth views
│       └── errors/     # Error pages
│
├── storage/            # Storage files
│   ├── database/       # SQL files
│   ├── logs/           # Log files
│   └── uploads/        # Uploaded files
│
├── vendor/             # External libraries
└── README.md           # This file
```

## 🚀 Installation

### Step 1: Start XAMPP
- Start Apache
- Start MySQL

### Step 2: Create Database
1. Go to: http://localhost/phpmyadmin
2. Create database: `devicesvn`
3. Import: `storage/database/schema.sql`

### Step 3: Configure
Edit `config/database.php` if needed:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'devicesvn');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Step 4: Access Website
Open: **http://localhost/devicesvn/public/**

## 🔑 Default Login
- Email: `admin@devicesvn.com`
- Password: `admin123`

## ✨ Features

### Implemented ✅
- MVC Architecture
- User Authentication (Register, Login, Logout, Forgot Password)
- Product Catalog with Pagination
- AJAX Search with Suggestions
- Category System with Breadcrumbs
- Store Locations
- Responsive Design (Bootstrap 5)
- SEO-Friendly URLs
- Security (Password Hashing, SQL Injection Prevention)

### To Implement 🚧
- Shopping Cart
- Checkout Process
- Google/Facebook OAuth
- Email Functionality
- Product Reviews
- Admin Panel

## 🌐 URL Routing

| URL | Controller | Method |
|-----|------------|--------|
| `/` | HomeController | index() |
| `/products` | ProductsController | index() |
| `/products/detail/{slug}` | ProductsController | detail() |
| `/products/category/{slug}` | ProductsController | category() |
| `/search` | SearchController | index() |
| `/search/suggestions` | SearchController | suggestions() |
| `/auth/login` | AuthController | login() |
| `/auth/register` | AuthController | register() |

## 🛠️ Technologies

- **Backend**: PHP 7.4+ (MVC Pattern)
- **Database**: MySQL with PDO
- **Frontend**: HTML5, CSS3, JavaScript (jQuery)
- **Framework**: Bootstrap 5
- **Icons**: Font Awesome 6
- **Server**: Apache (XAMPP)

## 📝 Requirements Met

1. ✅ Layout & Navigation (Header, Nav, Body, Footer)
2. ✅ Responsive Design (Desktop, Tablet, Mobile)
3. ✅ Products Page (Sorting, Pagination)
4. ✅ AJAX Search (Dropdown Suggestions)
5. ✅ Categories & Breadcrumbs
6. ✅ Store Locations
7. ✅ User Authentication
8. ✅ SEO (Meta tags, Semantic HTML, Friendly URLs, Sitemap)
9. ✅ Database Design (12 tables)

## 🔐 Security Features

- Password hashing (bcrypt)
- Prepared statements (SQL injection prevention)
- XSS prevention (output escaping)
- Session management
- Input validation

## 📖 Documentation

- See inline code comments
- Configuration in `config/` folder
- Database schema in `storage/database/schema.sql`

## 👨‍💻 Development

### Adding New Controller
1. Create file in `app/controllers/`
2. Extend `Controller` class
3. Access via URL: `/controllername/method`

### Adding New Model
1. Create file in `app/models/`
2. Extend `Model` class
3. Set `$table` property

### Adding New View
1. Create file in `resources/views/`
2. Load in controller: `$this->view('viewname', $data)`

## ⚡ Quick Commands

### Access PHPMyAdmin
http://localhost/phpmyadmin

### Access Website
http://localhost/devicesvn/public/

### Check Error Logs
`c:\xampp\apache\logs\error.log`

## 📞 Support

For issues:
1. Check Apache/MySQL is running
2. Verify database is created and imported
3. Check `config/database.php` settings
4. Review error logs

---

**Web Programming Semester Project**
**Student**: [Your Name]
**Date**: November 2025
