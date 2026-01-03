# Quick Start: Create Admin Users

## 🚀 Fast Setup (Copy & Paste)

Run these commands to create admin and responsable users:

```bash
cd technova_store

# Create Admin User
php bin/console app:create-user admin@technova.com "admin123" "Admin User" --role=ROLE_ADMIN

# Create Responsable User (Product Manager)
php bin/console app:create-user manager@technova.com "manager123" "Product Manager" --role=ROLE_RESPONSABLE

# Create Regular User (for testing)
php bin/console app:create-user user@technova.com "user123" "Test User" --role=ROLE_USER
```

## 📝 Login Credentials

After running the commands above, you can login with:

### Admin Access (Full Access)
- **Email**: `admin@technova.com`
- **Password**: `admin123`
- **Access**: Everything (KPI Dashboard, Orders, Users, Products, Categories)

### Responsable Access (Product Management)
- **Email**: `manager@technova.com`
- **Password**: `manager123`
- **Access**: Products and Categories only

### Regular User
- **Email**: `user@technova.com`
- **Password**: `user123`
- **Access**: Profile and order history only

## 🎯 What Each Role Can Do

### ROLE_ADMIN
- ✅ Access KPI Dashboard (`/admin/kpi`)
- ✅ Manage Products
- ✅ Manage Categories
- ✅ Manage Orders
- ✅ Manage Users
- ✅ View all analytics and charts

### ROLE_RESPONSABLE
- ✅ Access Admin Panel (`/admin`)
- ✅ Manage Products (CRUD)
- ✅ Manage Categories (CRUD)
- ❌ Cannot access KPI Dashboard
- ❌ Cannot manage Orders
- ❌ Cannot manage Users

### ROLE_USER
- ✅ View Profile
- ✅ View Order History
- ✅ Edit Profile
- ✅ Change Password
- ❌ Cannot access Admin Panel

## 🔧 Customize Your Users

### Create Admin with Custom Email
```bash
php bin/console app:create-user your-email@example.com "your_password" "Your Name" --role=ROLE_ADMIN
```

### Create Admin with Phone Number
```bash
php bin/console app:create-user admin@example.com "password123" "Admin Name" --role=ROLE_ADMIN --phone="+1234567890"
```

## ⚠️ Security Note

**Change default passwords immediately in production!**

The passwords above (`admin123`, `manager123`, `user123`) are for development/testing only.

## 📚 More Information

See `CREATE_ADMIN_USERS.md` for detailed documentation and alternative methods.

