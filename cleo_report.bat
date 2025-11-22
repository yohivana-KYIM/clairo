@echo off
setlocal enabledelayedexpansion
chcp 65001 >nul

REM ============================================================
REM 🧠 CLEO Symfony Performance Analyzer — Windows BAT v3
REM ============================================================

set "DATE=%date% %time%"
set "REPORT_FILE=cleo_report.html"
set "APP_PATH=/var/www"

echo ============================================================
echo   🔍 Audit CLEO - Symfony (Docker Windows)
echo ============================================================

REM --- Détection conteneur PHP principal ---
for /f "delims=" %%i in ('docker ps --format "{{.Names}}" ^| findstr /I "symfony_php" ^| findstr /V "phpmyadmin"') do (
  set "PHP_CONTAINER=%%i"
  goto FOUND_PHP
)
:FOUND_PHP
if "%PHP_CONTAINER%"=="" (
  echo ❌ Aucun conteneur PHP détecté.
  exit /b 1
)
echo 🐳 Conteneur PHP détecté : %PHP_CONTAINER%

REM --- Détection Redis ---
for /f "delims=" %%i in ('docker ps --format "{{.Names}}" ^| findstr /I "redis"') do set "REDIS_CONTAINER=%%i"
if "%REDIS_CONTAINER%"=="" (
  echo ⚠️ Aucun conteneur Redis détecté.
) else (
  echo 🧱 Conteneur Redis : %REDIS_CONTAINER%
)

REM --- Lecture APP_ENV ---
for /f "tokens=2 delims==" %%a in ('docker exec %PHP_CONTAINER% bash -c "grep APP_ENV %APP_PATH%/.env 2>/dev/null"') do set "APP_ENV=%%a"
if "%APP_ENV%"=="" set "APP_ENV=prod"

if /I "%APP_ENV%"=="prod" (
  set "ENV_STATUS=OK"
  set "ENV_DETAIL=APP_ENV=prod"
) else (
  set "ENV_STATUS=WARN"
  set "ENV_DETAIL=APP_ENV non optimal (%APP_ENV%)"
)

REM --- Vérif OPcache ---
docker exec %PHP_CONTAINER% php -i | findstr /C:"opcache.enable => On" >nul
if %errorlevel%==0 (
  set "OPCACHE_STATUS=OK"
  set "OPCACHE_DETAIL=OPcache activé"
) else (
  set "OPCACHE_STATUS=FAIL"
  set "OPCACHE_DETAIL=OPcache désactivé"
)

REM --- Redis extension ---
docker exec %PHP_CONTAINER% php -m | findstr /C:"redis" >nul
if %errorlevel%==0 (
  set "REDIS_EXT_STATUS=OK"
  set "REDIS_EXT_DETAIL=Extension Redis chargée"
) else (
  set "REDIS_EXT_STATUS=FAIL"
  set "REDIS_EXT_DETAIL=Redis non chargé"
)

REM --- Doctrine 2LC ---
docker exec %PHP_CONTAINER% bash -c "grep -q second_level_cache %APP_PATH%/config/packages/doctrine.yaml" >nul 2>&1
if %errorlevel%==0 (
  set "DOCTRINE_STATUS=OK"
  set "DOCTRINE_DETAIL=Second Level Cache activé"
) else (
  set "DOCTRINE_STATUS=WARN"
  set "DOCTRINE_DETAIL=2LC non configuré"
)

REM --- Twig cache ---
docker exec %PHP_CONTAINER% bash -c "grep -q cache: %APP_PATH%/config/packages/twig.yaml" >nul 2>&1
if %errorlevel%==0 (
  set "TWIG_STATUS=OK"
  set "TWIG_DETAIL=Cache Twig configuré"
) else (
  set "TWIG_STATUS=FAIL"
  set "TWIG_DETAIL=Cache Twig manquant"
)

REM --- Sessions Redis ---
docker exec %PHP_CONTAINER% bash -c "grep -q redis:// %APP_PATH%/config/packages/framework.yaml" >nul 2>&1
if %errorlevel%==0 (
  set "SESSION_STATUS=OK"
  set "SESSION_DETAIL=Sessions Redis actives"
) else (
  set "SESSION_STATUS=WARN"
  set "SESSION_DETAIL=Sessions non Redis"
)

REM --- Lecture Profil Symfony ---
echo 📊 Lecture du dernier profil Symfony...
set "PROFILE_JSON="
for /f "delims=" %%i in ('docker exec %PHP_CONTAINER% bash -c "ls -t %APP_PATH%/var/cache/%APP_ENV%/profiler/*.json 2>/dev/null | head -n 1"') do set "PROFILE_JSON=%%i"

set "TOTAL_MS=0"
set "INIT_MS=0"
set "MEM_MB=0"
set "ROUTE=N/A"

if not "%PROFILE_JSON%"=="" (
  docker exec %PHP_CONTAINER% cat "%PROFILE_JSON%" > "%TEMP%\cleo_profile.json" 2>nul
  for /f "tokens=2 delims=:" %%a in ('findstr /R "\"duration\" *: *[0-9]*" "%TEMP%\cleo_profile.json"') do set "TOTAL_MS=%%a"
  for /f "tokens=2 delims=:" %%a in ('findstr /R "\"initialization\" *: *[0-9]*" "%TEMP%\cleo_profile.json"') do set "INIT_MS=%%a"
  for /f "tokens=2 delims=:" %%a in ('findstr /R "\"memory\" *: *[0-9]*" "%TEMP%\cleo_profile.json"') do set "MEM_MB=%%a"
  for /f "tokens=2 delims=:" %%a in ('findstr /R "\"route\" *: *\".*\"" "%TEMP%\cleo_profile.json"') do set "ROUTE=%%a"
  del "%TEMP%\cleo_profile.json" >nul 2>&1
)

REM Nettoyage caractères dangereux
set "ROUTE_SAFE=%ROUTE:"=%"
set "ROUTE_SAFE=%ROUTE_SAFE:<=%"
set "ROUTE_SAFE=%ROUTE_SAFE:>=%"
set "ROUTE_SAFE=%ROUTE_SAFE:&=%"

echo 🧾 Génération du rapport HTML...

REM --- Écriture HTML ligne par ligne (sécurisé) ---
> "%REPORT_FILE%" echo ^<!DOCTYPE html^>
>> "%REPORT_FILE%" echo ^<html lang="fr"^>^<head^>
>> "%REPORT_FILE%" echo ^<meta charset="UTF-8"^> 
>> "%REPORT_FILE%" echo ^<title^>Rapport CLEO - %DATE%^</title^>
>> "%REPORT_FILE%" echo ^<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"^>
>> "%REPORT_FILE%" echo ^<style^>body{background:#fffbea;font-family:system-ui;color:#1f2937}.ok{color:green}.warn{color:#eab308}.fail{color:#dc2626}^</style^>
>> "%REPORT_FILE%" echo ^</head^>^<body class="p-4"^>
>> "%REPORT_FILE%" echo ^<h1^>⚙️ Rapport d’Audit CLEO - Symfony^</h1^>
>> "%REPORT_FILE%" echo ^<p^><strong^>Conteneur :</strong^> %PHP_CONTAINER%^</p^>
>> "%REPORT_FILE%" echo ^<div class="card p-3 mb-3"^>^<h4^>⏱ Performances (dernier profil)^</h4^>^<ul^>
>> "%REPORT_FILE%" echo ^<li^>Route : %ROUTE_SAFE%^</li^>
>> "%REPORT_FILE%" echo ^<li^>Durée totale : %TOTAL_MS% ms^</li^>
>> "%REPORT_FILE%" echo ^<li^>Initialisation : %INIT_MS% ms^</li^>
>> "%REPORT_FILE%" echo ^<li^>Mémoire max : %MEM_MB% KiB^</li^>
>> "%REPORT_FILE%" echo ^</ul^>^</div^>

call :writeCard "ENV" "%ENV_STATUS%" "%ENV_DETAIL%"
call :writeCard "OPCACHE" "%OPCACHE_STATUS%" "%OPCACHE_DETAIL%"
call :writeCard "REDIS EXT" "%REDIS_EXT_STATUS%" "%REDIS_EXT_DETAIL%"
call :writeCard "DOCTRINE" "%DOCTRINE_STATUS%" "%DOCTRINE_DETAIL%"
call :writeCard "TWIG" "%TWIG_STATUS%" "%TWIG_DETAIL%"
call :writeCard "SESSION" "%SESSION_STATUS%" "%SESSION_DETAIL%"

>> "%REPORT_FILE%" echo ^<footer class="mt-4 text-center text-muted"^>^<hr^>© CLEO Audit Tool — %DATE%^</footer^>^</body^>^</html^>

echo ✅ Rapport généré : %REPORT_FILE%
start "" "%REPORT_FILE%"
exit /b 0

:writeCard
set "TITLE=%~1"
set "STATE=%~2"
set "DETAIL=%~3"
set "COLOR=warn"
set "ICON=⚠️"
if /I "%STATE%"=="OK" set "COLOR=ok" & set "ICON=✅"
if /I "%STATE%"=="FAIL" set "COLOR=fail" & set "ICON=❌"
>> "%REPORT_FILE%" echo ^<div class="card p-3 mb-2"^>^<h5^>%ICON% %TITLE%^</h5^>^<p class="%COLOR%"^>%DETAIL%^</p^>^</div^>
exit /b 0
