# Khutta Ma Jutta – Shoe Inventory System

A professional PHP + MySQL web application for managing shoe inventory, including branding, variants, and stock management. Designed with a premium aesthetic using a specific color palette: **#E83C91**, **#43334C**, **#F8F4EC**, **#ffffff**.

---

## 🛠️ Setup Instructions (XAMPP)

Follow these steps to set up the project on your local machine using XAMPP:

### 1. Project Placement
Move the `Product_Ineventory_System` folder into your XAMPP's `htdocs` directory:
- **Windows**: `C:\xampp\htdocs\Product_Ineventory_System`
- **macOS**: `/Applications/XAMPP/xamppfiles/htdocs/Product_Ineventory_System`

### 2. Database Setup
1. Open **XAMPP Control Panel** and start **Apache** and **MySQL**.
2. Open your browser and go to `http://localhost/phpmyadmin`.
3. Create a new database named `khutta_ma_jutta`.
4. Select the database, click the **Import** tab, and choose the SQL file:
   - `sql/khutta_ma_jutta_v2.sql`
5. Click **Go** to import the tables and default data.

### 3. Configuration
Edit `config/db.php` if your credentials differ from the defaults:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'khutta_ma_jutta');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 4. Accessing the Application
Open your browser and navigate to:
`http://localhost/Product_Ineventory_System/public/index.php`

---

## ✨ Features

- **📊 Management Dashboard**: Overview of total products, low stock alerts, and total inventory value.
- **🏷️ Brand & Category Management**: Dynamic brand registration via AJAX modals during product creation.
- **👟 Advanced Variant Support**: Manage multiple sizes and colors (with HEX codes) for each shoe model.
- **🔍 AJAX Live Search**: Real-time filtering by product name, brand, category, or type without page reloads.
- **🔐 Secure Authentication**: Multi-level RBAC (Superadmin, Admin, User) with session-based security.
- **🛡️ Enterprise Security**:
  - **CSRF Protection**: All POST requests are validated with unique tokens.
  - **SQL Injection Prevention**: Full use of PDO prepared statements.
  - **XSS Sanitization**: Input cleaning and output escaping for all user-generated content.
- **📱 Premium Responsive UI**: Crafted with modern CSS variables, smooth transitions, and a custom design system.

---

## 🔑 Default Credentials

| Role       | Username | Password   |
|------------|----------|------------|
| Superadmin | `admin`  | `admin123` |
| Admin      | `Shishir`| `Shishir123` |

> [!WARNING]
> Please change these credentials immediately after the first login in a production environment.

---

## ⚠️ Known Issues & Notes

- **Session Expiry**: Sessions are handled by PHP defaults; if left idle, users may be redirected to login without a warning.
- **Database Name Case-Sensitivity**: On some Linux environments, database/table names may be case-sensitive. Ensure `DB_NAME` matches exactly.
- **File Permissions**: Ensure the server has read/write permissions for the project directory if any file logging or uploads are added in the future.

---

## 📑 Requirements

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Web Server**: Apache (XAMPP/WAMP recommended)
- **Browser**: Modern browser (Chrome, Firefox, Safari, Edge)
