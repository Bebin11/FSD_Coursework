# Khutta Ma Jutta - Shoe Inventory System (V2)

A production-grade Inventory Management System built with strict PHP MVC architecture.

## Deployment
**Live URL**: [https://student.heraldcollege.edu.np/~np03cs4a240164/Product_Ineventory_System/public/](https://student.heraldcollege.edu.np/~np03cs4a240164/Product_Ineventory_System/public/)

## Features
- **MVC Pattern**: Clear separation of Business Logic (Controllers) and Views.
- **Role-Based Access Control**: Admins have full access; regular users (configurable) have read-only access to critical actions.
- **AJAX Live Search**: Instant filtering by Keyword, Brand, Type, and Category using Fetch API.
- **Security First**: 
    - CSRF Tokens on all forms.
    - XSS Escaping on all outputs.
    - Prepared Statements for all queries.

## Installation
1.  Import `sql/khutta_ma_jutta.sql` into your MySQL server.
2.  Configure `config/db.php` with your database credentials.
3.  Navigate to `public/index.php`.

## Credentials
- **Username**: `admin`
- **Password**: `admin123`

## Folder Structure
- `public/`: Web-accessible controllers.
- `templates/`: HTML Views.
- `includes/`: Helper functions and Template Engine.
- `assets/`: CSS and JS.
- `config/`: Configuration.
# FSD_Coursework
