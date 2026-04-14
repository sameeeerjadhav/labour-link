# Deployment Checklist for labourlink.labxco.cloud

## Pre-Deployment

- [ ] Run `deploy.bat` (Windows) or `./deploy.sh` (Linux/Mac)
- [ ] Verify `labourlink-deploy` folder created successfully
- [ ] All files copied from `auth-app` folder

## Database Setup

- [ ] Login to Hostinger cPanel
- [ ] Navigate to phpMyAdmin
- [ ] Create new database (or note existing database name)
- [ ] Create database user with all privileges
- [ ] Import `SETUP_QUERIES.sql` file
- [ ] Verify tables created: `users`, `jobs`, etc.

## Configuration

- [ ] Open `labourlink-deploy/config.php`
- [ ] Update `DB_HOST` (usually 'localhost')
- [ ] Update `DB_USER` with Hostinger database username
- [ ] Update `DB_PASS` with Hostinger database password
- [ ] Update `DB_NAME` with your database name
- [ ] Save changes

## File Upload

### Option 1: Hostinger File Manager
- [ ] Login to Hostinger control panel
- [ ] Navigate to File Manager
- [ ] Go to `/domains/labourlink.labxco.cloud/public_html/`
- [ ] Delete any default files (index.html, etc.)
- [ ] Upload all files from `labourlink-deploy` folder
- [ ] Verify `.htaccess` file uploaded (enable "Show hidden files")

### Option 2: FTP (FileZilla)
- [ ] Install FileZilla
- [ ] Get FTP credentials from Hostinger
- [ ] Connect to FTP server
- [ ] Navigate to `/public_html/` directory
- [ ] Upload all files from `labourlink-deploy` folder
- [ ] Verify all files transferred successfully

## File Permissions

- [ ] Set folder permissions to 755
  - `/public_html/`
  - `/public_html/api/`
  - `/public_html/assets/`

- [ ] Set file permissions to 644
  - All `.php` files
  - All `.css` files
  - `.htaccess` file

## SSL Certificate

- [ ] Verify SSL certificate active in Hostinger
- [ ] Force HTTPS (optional - uncomment in .htaccess)
- [ ] Test HTTPS access

## Testing

### Basic Access
- [ ] Visit https://labourlink.labxco.cloud/
- [ ] Login page loads correctly
- [ ] CSS styles applied
- [ ] No console errors

### Registration Flow
- [ ] Click "Create account"
- [ ] Fill registration form
- [ ] Select role (Farmer/Labour)
- [ ] Submit form
- [ ] Check browser console for OTP (dev mode)
- [ ] Enter OTP on verification page
- [ ] Successfully redirected to dashboard

### Login Flow
- [ ] Enter username/password
- [ ] Select correct role
- [ ] Login successful
- [ ] Dashboard loads with weather widget
- [ ] Stats display correctly

### Navigation
- [ ] Bottom navigation works
- [ ] Side drawer opens/closes
- [ ] All menu links functional
- [ ] Profile page accessible
- [ ] Logout works correctly

### Farmer Features
- [ ] Post Job page loads
- [ ] Find Labour page loads
- [ ] Can view labour list
- [ ] Quick actions work

### Labour Features
- [ ] Find Jobs page loads
- [ ] Can view job listings
- [ ] Application system works
- [ ] Profile updates save

## Performance

- [ ] Page load time < 3 seconds
- [ ] Images optimized
- [ ] CSS/JS cached properly
- [ ] Mobile responsive

## Security

- [ ] config.php not accessible via browser
- [ ] .htaccess protecting sensitive files
- [ ] HTTPS enabled
- [ ] Session security working
- [ ] SQL injection protection active

## Post-Deployment

- [ ] Test on multiple browsers
- [ ] Test on mobile devices
- [ ] Monitor error logs
- [ ] Set up regular backups
- [ ] Document admin credentials securely

## Troubleshooting

### If you see "Database connection failed"
1. Check config.php credentials
2. Verify database exists
3. Check database user permissions
4. Test database connection in phpMyAdmin

### If styles don't load
1. Clear browser cache
2. Check assets folder uploaded
3. Verify file permissions
4. Check browser console for 404 errors

### If you get 500 errors
1. Check .htaccess syntax
2. Review PHP error logs in cPanel
3. Verify PHP version (7.4+ required)
4. Check file permissions

### If sessions don't work
1. Check PHP session settings
2. Verify session.save_path writable
3. Check session cookies in browser

## Support Resources

- Hostinger Knowledge Base: https://support.hostinger.com
- PHP Documentation: https://www.php.net/docs.php
- Project Documentation: See DEPLOYMENT.md

## Completion

- [ ] All tests passed
- [ ] Application fully functional
- [ ] Users can register and login
- [ ] All features working
- [ ] Performance acceptable
- [ ] Security measures in place

---

**Deployment Date:** _______________
**Deployed By:** _______________
**Database Name:** _______________
**Notes:** _______________
