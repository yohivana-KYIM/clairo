@echo off
REM ================================
REM ⚡ Script de test INSEE API Token
REM ================================

set CLIENT_ID=8021fc87-2ac5-4840-9aa0-cac11661ebf4
set CLIENT_SECRET=hKjySphSsNPGWBqd8QGWMm7hShtD7SbE

REM Concatène client_id:client_secret
set CRED=%CLIENT_ID%:%CLIENT_SECRET%

REM Encode en base64 avec PowerShell
for /f "usebackq delims=" %%i in (`powershell -NoProfile -Command ^
    "[Convert]::ToBase64String([Text.Encoding]::ASCII.GetBytes('%CRED%'))"`) do set B64=%%i

echo ---
echo ID     : %CLIENT_ID%
echo Secret : %CLIENT_SECRET%
echo Auth   : %B64%
echo ---

REM Appel curl vers l’API INSEE
curl -X POST "https://api.insee.fr/token" ^
  -H "Authorization: Basic %B64%" ^
  -H "Content-Type: application/x-www-form-urlencoded" ^
  -d "grant_type=client_credentials"

pause
