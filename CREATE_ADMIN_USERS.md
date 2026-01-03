# How to Create Admin/Responsable Users

## Method 1: Using Console Command (Recommended)

Use the Symfony console command to create users with specific roles:

### Create an Admin User
```bash
cd technova_store
php bin/console app:create-user admin@example.com "secure_password123" "Admin User" --role=ROLE_ADMIN
```

### Create a Responsable User
```bash
php bin/console app:create-user responsable@example.com "secure_password123" "Product Manager" --role=ROLE_RESPONSABLE
```

### Create a Regular User
```bash
php bin/console app:create-user user@example.com "secure_password123" "Regular User" --role=ROLE_USER
```

### With Phone Number
```bash
php bin/console app:create-user admin@example.com "password123" "Admin User" --role=ROLE_ADMIN --phone="+1234567890"
```

## Method 2: Using SQL (Direct Database)

If you prefer to use SQL directly:

### Create Admin User
```sql
-- Note: You need to hash the password first using PHP
-- Use: php bin/console security:hash-password
-- Then insert the hashed password below

INSERT INTO user (name, email, password, roles, phone) 
VALUES (
    'Admin User',
    'admin@example.com',
    '$2y$13$YOUR_HASHED_PASSWORD_HERE',
    '["ROLE_ADMIN"]',
    NULL
);
```

### Create Responsable User
```sql
INSERT INTO user (name, email, password, roles, phone) 
VALUES (
    'Product Manager',
    'responsable@example.com',
    '$2y$13$YOUR_HASHED_PASSWORD_HERE',
    '["ROLE_RESPONSABLE"]',
    NULL
);
```

### Update Existing User's Role
```sql
-- Make existing user an admin
UPDATE user SET roles = '["ROLE_ADMIN"]' WHERE email = 'existing@example.com';

-- Make existing user a responsable
UPDATE user SET roles = '["ROLE_RESPONSABLE"]' WHERE email = 'existing@example.com';
```

## Method 3: Hash Password First (for SQL method)

If using SQL, you need to hash the password first:

```bash
cd technova_store
php bin/console security:hash-password
```

Enter your password when prompted, and it will output the hashed version to use in SQL.

## Quick Setup Script

Create these users quickly:

```bash
# Admin user
php bin/console app:create-user admin@technova.com "admin123" "Admin User" --role=ROLE_ADMIN

# Responsable user  
php bin/console app:create-user manager@technova.com "manager123" "Product Manager" --role=ROLE_RESPONSABLE

# Regular user (for testing)
php bin/console app:create-user user@technova.com "user123" "Test User" --role=ROLE_USER
```

## Verify Users

Check if users were created:

```bash
php bin/console doctrine:query:sql "SELECT id, name, email, roles FROM user"
```

## Default Credentials (for testing)

After running the quick setup:

- **Admin**: admin@technova.com / admin123
- **Responsable**: manager@technova.com / manager123  
- **User**: user@technova.com / user123

⚠️ **Important**: Change these passwords in production!

