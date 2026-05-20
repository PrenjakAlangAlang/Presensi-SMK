@echo off
set "PHP_BIN=C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe"
set "APP_DIR=C:\laragon\www\Presensi-SMK"

"%PHP_BIN%" "%APP_DIR%\app\cron\sync_presensi_mapel.php" >> "%APP_DIR%\php_errors.log" 2>&1
