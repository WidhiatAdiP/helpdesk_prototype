@echo off
title Helpdesk Auto Commit

cd /d C:\xampp\htdocs\helpdesk

echo.
echo ========================================
echo         HELPDESK AUTO COMMIT
echo ========================================
echo.

git add .

git diff --cached --quiet
if %errorlevel%==0 (
    echo Tidak ada perubahan.
    pause
    exit
)

echo Pilih jenis commit:
echo.
echo [1] feat      = Fitur baru
echo [2] fix       = Perbaikan bug
echo [3] refactor  = Perbaikan kode tanpa ubah fungsi
echo [4] style     = UI / CSS
echo [5] docs      = Dokumentasi
echo [6] chore     = Maintenance
echo.

set /p type=Pilih (1-6): 

if "%type%"=="1" set prefix=feat
if "%type%"=="2" set prefix=fix
if "%type%"=="3" set prefix=refactor
if "%type%"=="4" set prefix=style
if "%type%"=="5" set prefix=docs
if "%type%"=="6" set prefix=chore

echo.
set /p msg=Deskripsi perubahan: 

git commit -m "%prefix%: %msg%"

if errorlevel 1 (
    pause
    exit
)

git push origin main

echo.
echo ========================================
echo Commit berhasil dipush!
echo ========================================
pause