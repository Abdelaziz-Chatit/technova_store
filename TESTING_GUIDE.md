# Complete TechNova Store Setup & Testing Guide

## 🚀 Part 1: Login & Navigation

### Create Test Accounts
1. **Go to**: `http://localhost:8000/register`
2. **Create Normal User**: 
   - Email: `user@test.com`
   - Password: `password123`
   - Name: `Test User`
3. **Create Manager Account**:
   - Email: `manager@test.com`
   - Password: `password123`
   - Name: `Manager User`

### Give Manager Role (via Database)
```sql
UPDATE `user` SET roles = '["ROLE_RESPONSABLE"]' WHERE email = 'manager@test.com';
```

---

## 🛍️ Part 2: Testing the Store

### Add Products with Images
1. **Login as Manager** → Click "📊 Dashboard"
2. **Click "⚙️ EasyAdmin"**
3. **Products Section**:
   - Click "Create" button
   - **Name**: `iPhone 15 Pro`
   - **Description**: `Latest Apple smartphone with advanced features`
   - **Price**: `999.99`
   - **Stock**: `50`
   - **Category**: Select or create "Electronics"
   - **Image**: Upload a product image (PNG/JPG)
   - **Save**

### Upload Product Images
- Click on a product → Click "Edit"
- In the "Image" field, click to browse and select an image
- Images are stored in: `public/uploads/products/`

### Add Categories with Images
1. **In EasyAdmin**: Go to **Categories**
2. **Create**:
   - **Name**: `Electronics`
   - **Slug**: `electronics`
   - **Image**: Upload category image
   - **Save**

---

## 💳 Part 3: Complete Payment (Test Orders)

### Step-by-Step Order Process

#### 1. **Browse & Add to Cart**
   - Login as normal user (or guest)
   - Go to **Products**
   - Add items to cart
   - Click **🛒 Cart**

#### 2. **Proceed to Checkout**
   - Review cart items
   - Click **Checkout**
   - Fill in customer information:
     - **Name**: John Doe
     - **Email**: john@test.com
     - **Phone**: +1-555-0123
     - **Address**: 123 Main St, City, State ZIP

#### 3. **Payment Method (Stripe)**
   - Payment is handled via **Stripe** integration
   - Use **Test Card Numbers**:
     - **Success**: `4242 4242 4242 4242`
     - **Decline**: `4000 0000 0000 0002`
   - **Expiry**: `12/25`
   - **CVV**: `123`

#### 4. **Confirm Payment**
   - Click **Pay**
   - Order status changes to `paid`
   - ✅ Dashboard KPIs will now show the order!

---

## 📊 Part 4: Admin Dashboard Testing

### Check KPI Metrics
1. **Login as Manager**
2. **Click "📊 Dashboard"**
3. **View Metrics**:
   - **💰 Total Revenue**: Sum of all paid+pending orders
   - **📦 Total Orders**: Count of all orders
   - **📊 Avg Order Value**: Revenue ÷ Orders
   - **✅ Status**: System health indicator

### Charts
- **💹 Revenue Trend**: 30-day revenue line chart
- **📈 Daily Orders**: Orders per day bar chart
- **🏆 Top 5 Products**: Products sorted by revenue
- **📋 Status Distribution**: Pie chart of order statuses

### Recent Orders Table
- Shows last 10 orders with status badges
- **Colors**:
  - 🟢 **Green** = Paid
  - 🟡 **Yellow** = Pending
  - 🔴 **Red** = Cancelled

---

## ⚙️ Part 5: Product Management

### From EasyAdmin Dashboard
1. **Click "⚙️ EasyAdmin"** from top navigation
2. **Manage Products**:
   - Create, Edit, Delete products
   - Upload/change images
   - Update stock and pricing
3. **Manage Categories**:
   - Create category hierarchies
   - Set category images
4. **Manage Orders**:
   - View all orders
   - Update order status
5. **Manage Users** (NEW):
   - View all users
   - Assign roles (ROLE_RESPONSABLE for managers)
   - Edit user info

### Return to Dashboard
- **Use breadcrumb navigation** at top
- Or click **"📊 Dashboard"** in sidebar

---

## 🔍 Part 6: Search Functionality

### Homepage Search
1. **Go to Home Page**: `http://localhost:8000/`
2. **Search Bar** (top center):
   - Type product name or keyword
   - Press **Enter** or click search button
   - Redirected to **Products Page** with filtered results

### Advanced Filtering
- **By Category**: Click category buttons
- **By Search**: Use search bar
- **Combination**: Both work together

---

## 🖼️ Part 7: Image Management

### Product Images
```
Location: public/uploads/products/
Formats:  PNG, JPG, JPEG, GIF
Size:     Max 5MB recommended
```

### Category Images
```
Location: public/uploads/categories/
Formats:  PNG, JPG, JPEG, GIF
Size:     Max 5MB recommended
```

### Upload Process
1. **In EasyAdmin**:
   - Go to Products or Categories
   - Click "Create" or "Edit"
   - Find "Image" field
   - Click **"Browse"** button
   - Select image from your computer
   - Click **"Save"**

---

## 👥 Part 8: User Management

### Add New Users
1. **Login as Manager**
2. **Go to Dashboard** → **⚙️ EasyAdmin**
3. **Users Section**:
   - Click **"Create"** button
   - Fill form:
     - **Name**: User's full name
     - **Email**: Unique email
     - **Password**: Secure password
     - **Roles**: Select "ROLE_RESPONSABLE" for managers
   - **Save**

### Edit Users
1. **Users Section** → **Edit** on a user
2. **Promote to Manager**:
   - Check "ROLE_RESPONSABLE"
   - Save
3. **Change Password**:
   - Leave blank to keep current
   - Enter new password to change

### Revoke Manager Access
1. **Edit User**
2. **Uncheck "ROLE_RESPONSABLE"**
3. **Save**

---

## 🔐 Part 9: Role-Based Access

### Normal User (ROLE_USER)
- ✅ Browse products
- ✅ Search products
- ✅ Add to cart
- ✅ Checkout
- ❌ Cannot access admin panel
- Shows **"👤 Profile"** in navbar

### Manager (ROLE_RESPONSABLE)  
- ✅ All normal user features
- ✅ Access **"📊 Dashboard"** (KPI dashboard)
- ✅ Access **"⚙️ EasyAdmin"** (manage everything)
- ✅ View analytics & charts
- ✅ Manage products, categories, users, orders

---

## 🐛 Troubleshooting

### KPIs Showing Zero?
- ✅ **Solution**: Complete a purchase using the checkout process
- Orders must reach `paid` or `pending` status to count

### Images Not Uploading?
- Check `public/uploads/` directory exists
- Verify permissions (chmod 755)
- Max file size is 5MB

### Not Seeing Dashboard?
- ✅ Make sure logged in as manager (ROLE_RESPONSABLE)
- Check email has this role in database

### Search Not Working?
- Clear browser cache
- Restart PHP server
- Verify product data exists

---

## 📋 Quick Command Reference

```bash
# Clear application cache
php bin/console cache:clear

# Create database
php bin/console doctrine:database:create

# Run migrations
php bin/console doctrine:migrations:migrate

# Create test data (if available)
php bin/console doctrine:fixtures:load

# Start development server
php -S localhost:8000 -t public
```

---

## 🎯 Testing Checklist

- [ ] Create normal user account
- [ ] Create manager account with ROLE_RESPONSABLE
- [ ] Add 3-5 products with images
- [ ] Create 2 categories with images
- [ ] Complete a purchase to test payment
- [ ] Verify KPIs update on dashboard
- [ ] Test search functionality
- [ ] Test category filtering
- [ ] Add/edit/delete a product from EasyAdmin
- [ ] Verify navigation works from all pages

---

## 💡 Pro Tips

1. **Bulk Test Data**: Create multiple orders with different products for realistic analytics
2. **Test Different Statuses**: Manually update order statuses in DB to see different chart results
3. **Cache Issues**: Always clear cache after schema changes
4. **Mobile Testing**: Responsive design works on phones/tablets
5. **Browser DevTools**: Use to test network requests and performance

---

**Last Updated**: January 3, 2026
**Version**: 1.0
