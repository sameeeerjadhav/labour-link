# Visual Deployment Guide for labourlink.labxco.cloud

## Your Current Hostinger Structure

```
labourlink.labxco.cloud
│
└── public_html/
    └── labourlink/          ← Your app is HERE
        ├── index.php
        ├── dashboard.php
        └── (all other files)
```

## The Problem

When someone visits `labourlink.labxco.cloud/`, they see an empty page or error because the app is in `/labourlink/` subfolder.

## The Solution

Add a redirect in the root to automatically send visitors to `/labourlink/`

---

## Step-by-Step Visual Guide

### STEP 1: Run Deployment Script

**Windows:**
```
Double-click: deploy.bat
```

**Mac/Linux:**
```bash
chmod +x deploy.sh
./deploy.sh
```

**Result:** Creates `labourlink-deploy/` folder with all files

---

### STEP 2: Update Database Config

Open: `labourlink-deploy/config.php`

```php
// CHANGE THESE:
define('DB_HOST', 'localhost');           // ← Usually stays 'localhost'
define('DB_USER', 'your_username');       // ← Your Hostinger DB username
define('DB_PASS', 'your_password');       // ← Your Hostinger DB password
define('DB_NAME', 'your_database_name');  // ← Your database name
```

---

### STEP 3: Upload Files (TWO LOCATIONS!)

#### 3A. Upload App Files

**From:** `labourlink-deploy/` folder (everything EXCEPT redirect.htaccess)

**To:** `/public_html/labourlink/`

```
/public_html/labourlink/
├── .htaccess          ← From labourlink-deploy
├── index.php          ← From labourlink-deploy
├── config.php         ← From labourlink-deploy (edited)
├── dashboard.php      ← From labourlink-deploy
├── (all other files)  ← From labourlink-deploy
├── api/               ← From labourlink-deploy
└── assets/            ← From labourlink-deploy
```

#### 3B. Upload Redirect File

**From:** `labourlink-deploy/redirect.htaccess`

**To:** `/public_html/.htaccess` (rename it!)

```
/public_html/
├── .htaccess          ← This is redirect.htaccess (renamed!)
└── labourlink/
    └── (your app files)
```

**IMPORTANT:** The file must be named `.htaccess` (with the dot!)

---

### STEP 4: Import Database

1. Login to **Hostinger cPanel**
2. Click **phpMyAdmin**
3. Select your database (or create new one)
4. Click **Import** tab
5. Choose file: `SETUP_QUERIES.sql`
6. Click **Go**

---

### STEP 5: Test Your Site

Visit: **https://labourlink.labxco.cloud/**

**What should happen:**
1. Browser goes to `labourlink.labxco.cloud/`
2. Automatically redirects to `labourlink.labxco.cloud/labourlink/`
3. You see the login page with styles

---

## File Upload Methods

### Method 1: Hostinger File Manager (Easiest)

1. Login to Hostinger
2. Go to **File Manager**
3. Navigate to `/domains/labourlink.labxco.cloud/public_html/`
4. Create folder: `labourlink` (if not exists)
5. Enter `labourlink` folder
6. Click **Upload** button
7. Select all files from `labourlink-deploy` (except redirect.htaccess)
8. Wait for upload to complete
9. Go back to `/public_html/`
10. Upload `redirect.htaccess`
11. Right-click → Rename to `.htaccess`

### Method 2: FTP (FileZilla)

1. Get FTP credentials from Hostinger
2. Open FileZilla
3. Connect to your server
4. Navigate to `/public_html/labourlink/`
5. Drag files from `labourlink-deploy` to right panel
6. Navigate to `/public_html/`
7. Upload `redirect.htaccess`
8. Rename to `.htaccess`

---

## Verification Checklist

After uploading, verify this structure exists:

```
/public_html/
├── .htaccess                    ← Redirects root to /labourlink/
│
└── labourlink/
    ├── .htaccess                ← App security settings
    ├── index.php                ← Login page
    ├── register.php
    ├── verify.php
    ├── dashboard.php
    ├── profile.php
    ├── post-job.php
    ├── find-labour.php
    ├── auth.php
    ├── config.php               ← With YOUR database credentials
    ├── SETUP_QUERIES.sql
    ├── db.sql
    │
    ├── api/
    │   └── get-labour.php
    │
    └── assets/
        ├── style.css
        ├── dashboard.css
        └── profile.css
```

---

## Testing Checklist

- [ ] Visit `labourlink.labxco.cloud/` → redirects to `/labourlink/`
- [ ] Login page displays correctly
- [ ] CSS styles are applied
- [ ] Can click "Create account"
- [ ] Registration form works
- [ ] Can receive OTP (check browser console)
- [ ] Can verify OTP
- [ ] Dashboard loads after login
- [ ] Weather widget displays
- [ ] Bottom navigation works
- [ ] Can access profile page
- [ ] Can logout

---

## Common Issues & Fixes

### Issue: "Not Found" or 404 Error

**Cause:** Files not in correct location

**Fix:** 
- Verify files are in `/public_html/labourlink/`
- Check `.htaccess` exists in both locations

---

### Issue: No redirect from root

**Cause:** Missing or incorrect root .htaccess

**Fix:**
- Ensure `/public_html/.htaccess` exists
- Verify it contains redirect rules
- Check file is named `.htaccess` (with dot)

---

### Issue: "Database connection failed"

**Cause:** Wrong credentials in config.php

**Fix:**
- Open `/public_html/labourlink/config.php`
- Verify DB_USER, DB_PASS, DB_NAME
- Check database exists in phpMyAdmin

---

### Issue: Styles not loading

**Cause:** Assets folder missing or wrong path

**Fix:**
- Verify `/public_html/labourlink/assets/` exists
- Check it contains: style.css, dashboard.css, profile.css
- Clear browser cache (Ctrl+F5)

---

### Issue: Can't see .htaccess files

**Cause:** Hidden files not visible

**Fix:**
- In File Manager: Click Settings → Show hidden files
- In FTP: View → Filename filters → Show hidden files

---

## Quick Reference

| What | Where | From |
|------|-------|------|
| App files | `/public_html/labourlink/` | `labourlink-deploy/` |
| Redirect | `/public_html/.htaccess` | `redirect.htaccess` (renamed) |
| Database | phpMyAdmin | `SETUP_QUERIES.sql` |
| Config | `/labourlink/config.php` | Edit with your DB credentials |

---

## Need More Help?

See these files:
- `SETUP_INSTRUCTIONS.txt` - Quick reference
- `QUICK_START.md` - Fast deployment guide
- `DEPLOYMENT.md` - Detailed instructions
- `DEPLOYMENT_CHECKLIST.md` - Complete checklist

---

**You're all set! Your app will be live at https://labourlink.labxco.cloud/**
