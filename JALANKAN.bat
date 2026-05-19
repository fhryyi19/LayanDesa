@echo off
title LayanDesa - Menjalankan Project...
color 0A

echo.
echo  =============================================
echo    LayanDesa - Sistem Layanan Desa Digital
echo  =============================================
echo.
echo  [*] Memeriksa Laragon...

:: Cek apakah Laragon sudah berjalan
tasklist /FI "IMAGENAME eq laragon.exe" 2>NUL | find /I /N "laragon.exe" >NUL
if "%ERRORLEVEL%"=="0" (
    echo  [OK] Laragon sudah berjalan.
) else (
    echo  [*] Membuka Laragon...
    :: Coba berbagai lokasi instalasi Laragon yang umum
    if exist "C:\laragon\laragon.exe" (
        start "" "C:\laragon\laragon.exe"
    ) else if exist "D:\laragon\laragon.exe" (
        start "" "D:\laragon\laragon.exe"
    ) else if exist "%USERPROFILE%\laragon\laragon.exe" (
        start "" "%USERPROFILE%\laragon\laragon.exe"
    ) else (
        echo  [!] Laragon tidak ditemukan di lokasi default.
        echo      Silakan buka Laragon secara manual lalu jalankan file ini lagi.
        pause
        exit /b
    )
    echo  [*] Menunggu Laragon siap (10 detik)...
    timeout /t 10 /nobreak >NUL
)

echo  [*] Memeriksa Apache (Web Server)...

:: Cek apakah Apache sudah jalan
tasklist /FI "IMAGENAME eq httpd.exe" 2>NUL | find /I /N "httpd.exe" >NUL
if "%ERRORLEVEL%"=="0" (
    echo  [OK] Apache sudah berjalan.
) else (
    echo  [!] Apache belum berjalan. Mencoba memulai via Laragon...
    :: Jalankan Apache via Laragon CLI jika tersedia
    if exist "C:\laragon\bin\apache\httpd.exe" (
        start "" "C:\laragon\bin\apache\httpd.exe" -k start
    ) else if exist "D:\laragon\bin\apache\httpd.exe" (
        start "" "D:\laragon\bin\apache\httpd.exe" -k start
    )
    timeout /t 5 /nobreak >NUL
)

echo  [*] Memeriksa MySQL (Database)...

:: Cek apakah MySQL sudah jalan
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe" >NUL
if "%ERRORLEVEL%"=="0" (
    echo  [OK] MySQL sudah berjalan.
) else (
    echo  [!] MySQL belum berjalan. Mencoba memulai...
    if exist "C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqld.exe" (
        start "" "C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqld.exe"
    )
    timeout /t 5 /nobreak >NUL
)

echo.
echo  [*] Membuka LayanDesa di browser...
start "" "http://localhost/layandesa/"

echo.
echo  =============================================
echo   LayanDesa berhasil dijalankan!
echo   URL: http://localhost/layandesa/
echo   Admin: http://localhost/layandesa/admin/
echo  =============================================
echo.
echo  Tekan tombol apa saja untuk menutup jendela ini...
pause >NUL
