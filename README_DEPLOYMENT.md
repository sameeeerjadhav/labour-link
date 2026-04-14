# LabourLink - Deployment Package

## 🎯 Quick Start (3 Minutes)

Your app is in: `public_html/labourlink/` (not root)

### Windows
```bash
deploy.bat
```

### Mac/Linux
```bash
chmod +x deploy.sh
./deploy.sh
```

This creates `labourlink-deploy/` folder ready to upload.

---

## 📦 What's Included

| File | Purpose |
|------|---------|
| `deploy.bat` / `deploy.sh` | Automated deployment preparation |
| `.htaccess` | App security (goes in /labourlink/) |
| `redirect.htaccess` | Root redirect (goes in /public_html/) |
| `SETUP_INSTRUCTIONS.txt` | Quick reference guide |
| `VISUAL_GUIDE.md` | Step-by-step with diagrams |
| `QUICK_START.md` | Fast deployment guide |
| `DEPLOYMENT.md` | Detailed instructions |
| `DEPLOYMENT_CHECKLIST.md` | Complete verification checklist |

---

## 🚀 Deployment Overview

### Your Structure
```
labourlink.labxco.cloud/
└── public_html/
    ├── .htaccess          ← Redirects to /labourlink/
    └── labourlink/        ← Your app lives here
        ├── .htaccess      ← App security
        ├── index.php      ← Entry point
        └── (all files)
```

### Why Two .htaccess Files?

1. **Root .htaccess** (`/public_html/.htaccess`)
   - Redirects `labourlink.labxco.cloud/` → `/labourlink/`
   - Visitors don't need to type `/labourlink/` in URL

2. **App .htaccess** (`/public_html/labourlink/.htaccess`)
   - Protects sensitive files (config.php)
   - Handles URL rewriting
   - Sets security headers

---

## 📋 5-Step Deployment

### 1️⃣ Prepare Files
Run `deploy.bat` or `./deploy.sh`

### 2️⃣ Update Config
Edit `labourlink-deploy/config.php`:
```php
define('DB_USER', 'your_hostinger_username');
define('DB_PASS', 'your_hostinger_password');
define('DB_NAME', 'your_database_name');
```

### 3️⃣ Upload Files
- Upload `labourlink-deploy/*` → `/public_html/labourlink/`
- Upload `redirect.htaccess` → `/public_html/.htaccess` (rename it!)

### 4️⃣ Import Database
- cPanel → phpMyAdmin
- Import `SETUP_QUERIES.sql`

### 5️⃣ Test
Visit: https://labourlink.labxco.cloud/

---

## 📚 Documentation Guide

**New to deployment?** Start here:
1. `SETUP_INSTRUCTIONS.txt` - Quick reference
2. `VISUAL_GUIDE.md` - Step-by-step with diagrams

**Want detailed guide?**
1. `QUICK_START.md` - Fast track
2. `DEPLOYMENT.md` - Complete guide

**Need checklist?**
1. `DEPLOYMENT_CHECKLIST.md` - Verify everything

---

## ✅ Verification

After deployment, check:

- [ ] `labourlink.labxco.cloud/` redirects to `/labourlink/`
- [ ] Login page displays with styles
- [ ] Can register new account
- [ ] Can login successfully
- [ ] Dashboard loads correctly
- [ ] All navigation works

---

## 🔧 Troubleshooting

### Database Error
→ Check `config.php` credentials

### 404 Not Found
→ Verify files in `/public_html/labourlink/`

### No Redirect
→ Check `/public_html/.htaccess` exists

### Styles Missing
→ Verify `/labourlink/assets/` folder uploaded

**See VISUAL_GUIDE.md for detailed troubleshooting**

---

## 📞 Support Files

All guides included in `labourlink-deploy/` folder:
- SETUP_INSTRUCTIONS.txt
- VISUAL_GUIDE.md
- QUICK_START.md
- DEPLOYMENT.md
- DEPLOYMENT_CHECKLIST.md

---

## 🎉 You're Ready!

1. Run deployment script
2. Update config.php
3. Upload files (two locations!)
4. Import database
5. Test your site

**Your app will be live at: https://labourlink.labxco.cloud/**

---

*For detailed instructions, see VISUAL_GUIDE.md*
