# LabourLink Deployment Guide

## Hostinger Subdomain Setup (labourlink.labxco.cloud)

### Step 1: Upload Files
Upload all files from the `auth-app` folder to your subdomain's labourlink directory:
- `/home/username/domains/labourlink.labxco.cloud/public_html/labourlink/`

### Step 2: Setup Root Redirect
Upload `redirect.htaccess` to `/public_html/` and rename it to `.htaccess`
- This redirects visitors from root to /labourlink/ automatically

### Step 3: File Structure
Your subdomain should have this structure:
```
/public_html/
├── .htaccess (from redirect.htaccess)
└── /labourlink/
    ├── .htaccess
    ├── api/
    │   └── get-labour.php
    ├── assets/
    │   ├── dashboard.css
    │   ├── profile.css
    │   └── style.css
    ├── auth.php
    ├── config.php
    ├── dashboard.php
    ├── db.sql
    ├── find-labour.php
    ├── index.php
    ├── post-job.php
    ├── profile.php
    ├── register.php
    ├── SETUP_QUERIES.sql
    └── verify.php
```

### Step 4: Database Configuration

1. Create a MySQL database in Hostinger cPanel
2. Import the database schema:
   - Use `SETUP_QUERIES.sql` for the initial setup
   - Or use `db.sql` if you have existing data

3. Update `config.php` in `/labourlink/` folder with your Hostinger database credentials:
```php
define('DB_HOST', 'localhost');  // Usually 'localhost' on Hostinger
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'your_db_name');
```

### Step 5: File Permissions
Set proper permissions via FTP or File Manager:
- Folders: 755
- PHP files: 644
- Both .htaccess files: 644

### Step 6: Test Your Application
1. Visit: https://labourlink.labxco.cloud/
2. Should auto-redirect to: https://labourlink.labxco.cloud/labourlink/
3. You should see the login page
4. Test registration and login functionality

### Step 7: Security Checklist
- [ ] Update database credentials in config.php
- [ ] Ensure both .htaccess files are in place
- [ ] Root .htaccess redirects to /labourlink/
- [ ] App .htaccess protects config.php
- [ ] Enable HTTPS (SSL certificate via Hostinger)
- [ ] Set proper file permissions
- [ ] Test all functionality

### Troubleshooting

**500 Internal Server Error:**
- Check .htaccess syntax
- Verify PHP version (7.4+ recommended)
- Check error logs in cPanel

**Database Connection Failed:**
- Verify database credentials in config.php
- Ensure database user has proper privileges
- Check if database exists

**Assets Not Loading:**
- Verify file paths are relative (no 'auth-app/' prefix)
- Check file permissions
- Clear browser cache
- Ensure you're accessing via /labourlink/ path

**Session Issues:**
- Ensure session.save_path is writable
- Check PHP session settings in cPanel

### Support
For Hostinger-specific issues, contact their support or check:
- cPanel Error Logs
- PHP Error Logs
- Apache Error Logs
