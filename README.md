# LabourLink 🌾

A modern web application connecting farmers with agricultural labour workers.

## Features

- 👨‍🌾 Dual role system (Farmer & Labour)
- 📱 Mobile-first responsive design
- 🔐 Secure authentication with OTP verification
- 🌤️ Real-time weather integration
- 📍 Location-based labour search
- 💼 Job posting and management
- 👤 User profiles and settings

## Tech Stack

- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5, Font Awesome
- **Backend:** PHP 7.4+
- **Database:** MySQL
- **APIs:** wttr.in (weather)

## Project Structure

```
auth-app/              ← Application source files
├── api/              ← API endpoints
├── assets/           ← CSS stylesheets
├── *.php             ← Application pages
└── *.sql             ← Database schemas

.htaccess             ← Apache configuration
deploy.bat            ← Windows deployment script
deploy.sh             ← Linux/Mac deployment script
DEPLOYMENT.md         ← Detailed deployment guide
QUICK_START.md        ← Quick deployment guide
```

## Local Development

1. Install XAMPP/WAMP/MAMP
2. Copy `auth-app` contents to `htdocs/labourlink`
3. Create database and import `SETUP_QUERIES.sql`
4. Update `config.php` with local database credentials
5. Visit `http://localhost/labourlink/`

## Deployment to Hostinger

### Your Structure
Your app is deployed to: `public_html/labourlink/` (subfolder, not root)

### Quick Deploy

**Windows:**
```bash
deploy.bat
```

**Linux/Mac:**
```bash
chmod +x deploy.sh
./deploy.sh
```

This creates a `labourlink-deploy` folder ready for upload.

### Manual Steps

1. Update `config.php` with Hostinger database credentials
2. Upload files to: `/public_html/labourlink/`
3. Upload `redirect.htaccess` to `/public_html/` and rename to `.htaccess`
4. Import database schema via phpMyAdmin
5. Visit your subdomain (auto-redirects to /labourlink/)

**See [VISUAL_GUIDE.md](VISUAL_GUIDE.md) for detailed step-by-step instructions with diagrams.**

Quick references:
- [SETUP_INSTRUCTIONS.txt](SETUP_INSTRUCTIONS.txt) - Quick reference
- [QUICK_START.md](QUICK_START.md) - Fast deployment
- [DEPLOYMENT.md](DEPLOYMENT.md) - Complete guide
- [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) - Verification checklist

## Database Setup

Import one of these files via phpMyAdmin:
- `SETUP_QUERIES.sql` - Fresh installation
- `db.sql` - With sample data (if available)

## Configuration

Update `config.php` with your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'your_database');
```

## User Roles

### Farmer
- Post job listings
- Search for labour workers
- Manage applications
- View worker profiles

### Labour
- Browse available jobs
- Apply for positions
- Manage profile
- Track applications

## Security Features

- Password hashing (bcrypt)
- OTP verification
- Session management
- SQL injection prevention
- XSS protection
- CSRF protection

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## License

Proprietary - All rights reserved

## Support

For deployment issues, see:
- [QUICK_START.md](QUICK_START.md) - Quick deployment guide
- [DEPLOYMENT.md](DEPLOYMENT.md) - Detailed troubleshooting

## Live Demo

🌐 https://labourlink.labxco.cloud/

---

Built with ❤️ for the agricultural community
