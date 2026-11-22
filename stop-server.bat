@echo off
taskkill /F /IM cloudflared.exe >nul 2>&1
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8000') do taskkill /F /PID %%a >nul 2>&1
echo Server Kothak Photo dan Cloudflare Tunnel telah dimatikan.
pause
