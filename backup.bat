@echo off
REM ========================================
REM GameTopup Backup Script
REM Backup database + source code
REM ========================================
set BACKUP_DIR=C:\backups\gametopup
set DATE=%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set DATE=%DATE: =0%

if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

echo [%DATE%] Starting backup...

REM Database backup
C:\mariadb\bin\mysqldump.exe -u root -proot123 --single-transaction --routines --triggers gamewinn_topup > "%BACKUP_DIR%\db_%DATE%.sql"
if %errorlevel%==0 (echo   DB backup OK) else (echo   DB backup FAILED)

REM Source backup (zip)
powershell -Command "Compress-Archive -Path 'C:\Users\Admin\projects\game4win-clone\source\*' -DestinationPath '%BACKUP_DIR%\source_%DATE%.zip' -Force"
if %errorlevel%==0 (echo   Source backup OK) else (echo   Source backup FAILED)

REM Clean old backups (> 7 days)
forfiles /p "%BACKUP_DIR%" /s /m *.sql /d -7 /c "cmd /c del @file" 2>NUL
forfiles /p "%BACKUP_DIR%" /s /m *.zip /d -7 /c "cmd /c del @file" 2>NUL

echo Done. Backups in %BACKUP_DIR%
