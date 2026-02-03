# Khutta Ma Jutta – Shoe Inventory System

A professional PHP + MySQL web application for managing shoe inventory, including branding, variants, and stock management. Designed with a premium aesthetic using a specific color palette: **#E83C91**, **#43334C**, **#F8F4EC**, **#ffffff**.

## Project Structure

```
/
├── README.md
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── app.js
├── config/
│   └── db.php
├── includes/
│   └── functions.php
├── public/
│   ├── api/
│   │   ├── add_brand.php
│   │   └── get_variants.php
│   ├── add.php
│   ├── delete.php
│   ├── edit.php
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── search.php
│   └── users.php
├── sql/
│   └── khutta_ma_jutta_v2.sql
└── templates/
    ├── auth/
    │   └── login.php
    ├── inventory/
    │   ├── form.php
    │   └── list.php
    ├── users/
    │   ├── form.php
    │   └── list.php
    ├── layout.php
    └── partials/
```

## Setup

1. **Database**  
   Create a MySQL database (e.g., `inventory_system`). Import:  
   `sql/khutta_ma_jutta_v2.sql`  
   You can run this in phpMyAdmin or via command line.

2. **Config**  
   Edit `config/db.php`: Set your `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS`.

3. **Web Server**  
   Run this project on an Apache/Nginx server (e.g., XAMPP/WAMP). Access the application via the `public/` folder.

## Login (default)

| Role      | Username | Password     |
|-----------|----------|------------  |
| Superadmin| admin    | admin123     |
| Admin     | Shishir  | Shishir123   |

Change these in production.

## Features

- **Inventory Management**: Full CRUD for shoe products with brand and variant support.
- **Role-Based Access**: Secure admin and user roles for system management.
- **AJAX Live Search**: Real-time filtering by keyword, brand, and category using Fetch API.
- **Responsive Design**: Premium UI crafted with CSS variables and custom typography.
- **User Management**: Add and manage system users with different privilege levels.

## Security Notes

- **CSRF Protection**: All forms include anti-CSRF tokens to prevent cross-site request forgery.
- **XSS Prevention**: Inputs are sanitized and outputs are escaped using specialized helper functions.
- **SQL Injection**: All database interactions use PDO prepared statements.
- **Production Readiness**: Always update database credentials and use HTTPS for secure traffic.
