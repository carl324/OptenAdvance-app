@echo off
setlocal

REM Ejecutar desde la carpeta www del proyecto
cd /d "%~dp0"

set "PHP_EXE=..\php\php.exe"
if not exist "%PHP_EXE%" (
  echo [ERROR] No se encontro PHP portable en ..\php\php.exe
  pause
  exit /b 1
)

echo Iniciando Laravel en http://127.0.0.1:8000
echo.
"%PHP_EXE%" -d opcache.enable=0 -d opcache.enable_cli=0 -d opcache.validate_timestamps=1 artisan serve --host=127.0.0.1 --port=8000

endlocal
