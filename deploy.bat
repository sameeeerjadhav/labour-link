@echo off
REM LabourLink Deployment Script for Windows
REM This script prepares files for deployment to Hostinger subdomain

echo ==========================================
echo LabourLink Deployment Preparation
echo ==========================================
echo.

REM Create deployment directory
set DEPLOY_DIR=labourlink-deploy
echo Creating deployment directory: %DEPLOY_DIR%
if not exist "%DEPLOY_DIR%" mkdir "%DEPLOY_DIR%"

REM Copy all files from auth-app to deployment directory
echo Copying application files...
xcopy /E /I /Y auth-app\* "%DEPLOY_DIR%\"

REM Copy .htaccess to deployment directory
echo Copying .htaccess...
copy /Y .htaccess "%DEPLOY_DIR%\"

REM Copy redirect.htaccess to deployment directory
echo Copying redirect.htaccess...
copy /Y redirect.htaccess "%DEPLOY_DIR%\"

REM Copy deployment guides
echo Copying deployment guides...
copy /Y START_HERE.txt "%DEPLOY_DIR%\"
copy /Y DEPLOYMENT_CARD.txt "%DEPLOY_DIR%\"
copy /Y VISUAL_GUIDE.md "%DEPLOY_DIR%\"
copy /Y QUICK_START.md "%DEPLOY_DIR%\"
copy /Y SETUP_INSTRUCTIONS.txt "%DEPLOY_DIR%\"
copy /Y DEPLOYMENT.md "%DEPLOY_DIR%\"
copy /Y DEPLOYMENT_CHECKLIST.md "%DEPLOY_DIR%\"
copy /Y README_DEPLOYMENT.md "%DEPLOY_DIR%\"

echo.
echo ==========================================
echo Deployment files ready!
echo ==========================================
echo.
echo Next steps:
echo 1. Review and update config.php with your Hostinger database credentials
echo 2. Upload all files from '%DEPLOY_DIR%' to:
echo    /home/username/domains/labourlink.labxco.cloud/public_html/labourlink/
echo 3. Upload redirect.htaccess to /public_html/ and rename to .htaccess
echo 4. Import the database using SETUP_QUERIES.sql
echo 5. Visit https://labourlink.labxco.cloud/ to test
echo.
echo See SETUP_INSTRUCTIONS.txt for detailed step-by-step guide.
echo.
pause
