@echo off
echo ============================================
echo King Express Bus - Database Migration Tool
echo ============================================
echo.

REM Check if Python is installed
python --version >nul 2>&1
if %errorlevel% neq 0 (
    echo Error: Python is not installed or not in PATH
    echo Please install Python 3.x and try again
    pause
    exit /b 1
)

REM Navigate to script directory
cd /d "%~dp0"

REM Check if mysql-connector-python is installed
python -c "import mysql.connector" >nul 2>&1
if %errorlevel% neq 0 (
    echo Installing required dependencies...
    pip install mysql-connector-python
    echo.
)

REM Run the migration script
echo Starting migration...
echo.
python migrate_database.py

echo.
echo ============================================
echo Migration process completed
echo ============================================
pause
