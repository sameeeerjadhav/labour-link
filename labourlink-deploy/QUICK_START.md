# Quick Start - Deploy to Hostinger

## For Windows Users

1. Run the deployment script:
   ```
   deploy.bat
   ```

2. This creates a `labourlink-deploy` folder with all necessary files

3. Update database credentials in `labourlink-deploy/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_hostinger_db_user');
   define('DB_PASS', 'your_hostinger_db_password');
   define('DB_NAME', 'your_hostinger_db_name');
   ```

4. Upload the entire contents of `labourlink-deploy` folder to:
   ```
   /home/username/domains/labourlink.labxco.cloud/public_html/labourlink/
   ```
   
   Use Hostinger File Manager or FTP client (FileZilla)

5. **IMPORTANT:** Upload `redirect.htaccess` to `/public_html/` and rename it to `.htaccess`
   - This redirects `labourlink.labxco.cloud/` → `labourlink.labxco.cloud/labourlink/`

6. In Hostinger cPanel:
   - Go to phpMyAdmin
   - Create a new database (or use existing)
   - Import `SETUP_QUERIES.sql`

7. Visit: https://labourlink.labxco.cloud/ (will auto-redirect to /labourlink/)

## For Linux/Mac Users

1. Run the deployment script:
   ```bash
   chmod +x deploy.sh
   ./deploy.sh
   ```

2. Follow steps 2-7 from Windows instructions above

## File Structure on Hostinger

Your subdomain structure should look like this:
```
public_html/
├── .htaccess          ← Redirects root to /labourlink/
└── labourlink/
    ├── .htaccess      ← App security & settings
    ├── index.php      ← Login page (entry point)
    ├── register.php   ← Registration page
    ├── verify.php     ← OTP verification
    ├── dashboard.php  ← Main dashboard
    ├── profile.php    ← User profile
    ├── post-job.php   ← Post job (farmers)
    ├── find-labour.php ← Find labour (farmers)
    ├── auth.php       ← Authentication handler
    ├── config.php     ← Database configuration
    ├── api/
    │   └── get-labour.php ← API endpoint
    └── assets/
        ├── style.css
        ├── dashboard.css
        └── profile.css
```

## Important Notes

✅ All paths are already relative - no code changes needed
✅ Application starts at index.php (login page)
✅ Two .htaccess files needed:
   - `/public_html/.htaccess` - Redirects root to /labourlink/
   - `/public_html/labourlink/.htaccess` - App security
✅ Access via: https://labourlink.labxco.cloud/ (auto-redirects)

## Testing Checklist

- [ ] Can access https://labourlink.labxco.cloud/
- [ ] Login page loads with styles
- [ ] Can register new account
- [ ] Receive OTP (check console for dev OTP)
- [ ] Can verify OTP
- [ ] Dashboard loads correctly
- [ ] Navigation works
- [ ] Can access profile page

## Troubleshooting

**Can't connect to database:**
- Check config.php credentials
- Verify database exists in cPanel
- Check database user permissions

**404 errors:**
- Verify .htaccess is uploaded
- Check file permissions (644 for files, 755 for folders)

**Styles not loading:**
- Clear browser cache
- Check assets folder uploaded correctly
- Verify file paths in browser console

**Need Help?**
See DEPLOYMENT.md for detailed troubleshooting guide.
