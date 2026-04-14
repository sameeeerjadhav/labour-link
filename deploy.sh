#!/bin/bash

# LabourLink Deployment Script
# This script prepares files for deployment to Hostinger subdomain

echo "=========================================="
echo "LabourLink Deployment Preparation"
echo "=========================================="
echo ""

# Create deployment directory
DEPLOY_DIR="labourlink-deploy"
echo "Creating deployment directory: $DEPLOY_DIR"
mkdir -p "$DEPLOY_DIR"

# Copy all files from auth-app to deployment directory
echo "Copying application files..."
cp -r auth-app/* "$DEPLOY_DIR/"

# Copy .htaccess to deployment directory
echo "Copying .htaccess..."
cp .htaccess "$DEPLOY_DIR/"

# Copy redirect.htaccess to deployment directory
echo "Copying redirect.htaccess..."
cp redirect.htaccess "$DEPLOY_DIR/"

# Copy deployment guide
echo "Copying deployment guides..."
cp START_HERE.txt "$DEPLOY_DIR/"
cp DEPLOYMENT_CARD.txt "$DEPLOY_DIR/"
cp VISUAL_GUIDE.md "$DEPLOY_DIR/"
cp QUICK_START.md "$DEPLOY_DIR/"
cp SETUP_INSTRUCTIONS.txt "$DEPLOY_DIR/"
cp DEPLOYMENT.md "$DEPLOY_DIR/"
cp DEPLOYMENT_CHECKLIST.md "$DEPLOY_DIR/"
cp README_DEPLOYMENT.md "$DEPLOY_DIR/"

echo ""
echo "=========================================="
echo "Deployment files ready!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Review and update config.php with your Hostinger database credentials"
echo "2. Upload all files from '$DEPLOY_DIR' to:"
echo "   /home/username/domains/labourlink.labxco.cloud/public_html/labourlink/"
echo "3. Upload redirect.htaccess to /public_html/ and rename to .htaccess"
echo "4. Import the database using SETUP_QUERIES.sql"
echo "5. Visit https://labourlink.labxco.cloud/ to test"
echo ""
echo "See SETUP_INSTRUCTIONS.txt for detailed step-by-step guide."
echo ""
