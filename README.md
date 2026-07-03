# 🌿 EcoBulk — Eco-Friendly Bulk Buying Marketplace

A database-driven bulk buying marketplace where customers team up, hit a target quantity together, and unlock a discount. Better for the wallet, better for the planet — fewer deliveries, less packaging waste.

Built as a semester project for **DB2001 Database Systems**.

---

## ✨ Features

| Feature | Description |
|---------|-------------|
| 🛒 **Browse Products** | Explore certified eco-friendly products with eco ratings and stock status |
| 👥 **Bulk Buying Groups** | Create or join group orders to unlock discounts through collective purchasing |
| 📦 **Place Orders** | Order individually or through a bulk group with automatic discount applied |
| 📊 **Order Tracking** | Track every order from placement to delivery in real time |
| 🔐 **Authentication** | Separate login flows for Admin and Customer roles |
| ⚙️ **Admin Panel** | Manage products, categories, customers, orders and inventory |
| 👤 **Customer Profile** | Update personal information and view order history |

---

## 🖥️ Tech Stack

- **Frontend:** HTML, CSS, Bootstrap 5, JavaScript
- **Backend:** PHP (Procedural) with MySQLi Prepared Statements
- **Database:** MySQL via phpMyAdmin
- **Server:** Apache (XAMPP)
- **UI:** Custom dark-themed design system built from scratch

---

## 🗄️ Database Highlights

| Concept | Implementation |
|---------|---------------|
| ⚙️ **Stored Procedures** | `place_order()` and `join_bulk_group()` handle the entire order and membership flow |
| 🔁 **Triggers** | Auto-reduce stock on order, restore on cancellation, update group progress automatically |
| 💳 **Transactions** | Every critical operation is atomic — committed or rolled back on failure |
| 👁️ **Views** | Order summaries, inventory status, bulk group participation |
| 📐 **Normalization** | Fully normalized schema up to BCNF |

---

## 📁 Project Structure

```
eco-bulk-marketplace/
│
├── index.php
├── logout.php
│
├── database/
│   └── schema.sql
│
├── includes/
│   ├── config.php
│   ├── functions.php
│   ├── header.php
│   └── footer.php
│
├── auth/
│   ├── login.php
│   └── register.php
│
├── admin/
│   ├── dashboard.php
│   ├── products.php
│   ├── categories.php
│   ├── customers.php
│   └── orders.php
│
├── customer/
│   ├── dashboard.php
│   ├── products.php
│   ├── bulk_groups.php
│   ├── my_orders.php
│   └── profile.php
│
├── css/
│   └── style.css
│
└── js/
    └── app.js
```

---

## 🚀 Getting Started

### 1. Clone the repo
```bash
git clone https://github.com/your-username/eco-bulk-marketplace.git
cd eco-bulk-marketplace
```

### 2. Set up XAMPP
- Install [XAMPP](https://www.apachefriends.org/)
- Place the project folder inside `htdocs/`
- Start **Apache** and **MySQL** from the XAMPP Control Panel

### 3. Import the database
- Open `http://localhost/phpmyadmin`
- Create a new database called `eco_bulk`
- Import `database/schema.sql`

### 4. Configure the connection
Open `includes/config.php` and update if needed:
```php
$conn = mysqli_connect("localhost", "root", "", "eco_bulk");
```

### 5. Run the app
Open your browser and go to:
```
http://localhost/eco-bulk-marketplace/
```

### 6. Admin Demo Credentials
```
Email:    admin@ecobulk.com
Password: admin123
```

---

## 🧭 Bulk Buying Workflow

```
Browse Products → Create / Join Bulk Group → Place Order → Track Status
```

Once the group's target quantity is reached, the status automatically updates to **Completed** and all members receive the discount.

---

## 📸 Screenshots

![Landing Page](screenshots/landing.png)
![Customer Dashboard](screenshots/dashboard.png)
![Bulk Groups](screenshots/bulk_groups.png)
![Admin Panel](screenshots/admin.png)

---

## 🔮 Possible Future Improvements

- Email notifications when a bulk group is completed
- Mobile app version
- Product ratings and reviews
- Chat between bulk group members
- Export order history as PDF

---

## 👥 Group Members

🎓 Burair Hyder – 24K-0804

🎓 Mutahir Ahmed Khan – 24K-0030

🎓 Sameed Imran – 24K-1036

🎓 Ammar Kamran Ali – 24K-0732
