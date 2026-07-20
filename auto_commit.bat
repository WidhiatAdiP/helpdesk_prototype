@echo off
cd /d C:\xampp\htdocs\helpdesk

git add .

git diff --cached --quiet
if %errorlevel%==0 exit

git commit -m "Auto backup %date% %time%"
git push origin main