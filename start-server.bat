@echo off
:: Ensure running from project folder
cd /d "c:\laragon\www\kothak-photo"

:: Kill any old orphan processes on port 8000 or duplicate tunnel if needed
:: Start PHP artisan serve silently
start "" /b php artisan serve --port=8000 --host=0.0.0.0

:: Start Cloudflare Tunnel
start "" /b "C:\laragon\bin\cloudflared.exe" tunnel run kothak-tunnel
