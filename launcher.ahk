#NoTrayIcon
#Persistent
#SingleInstance Force
SetTitleMatchMode, 2
DetectHiddenWindows, On

Menu, Tray, Tip, OptenAdvance Launcher
Menu, Tray, NoStandard
Menu, Tray, Add, Cerrar OptenAdvance, CerrarLauncher
Menu, Tray, Add
Menu, Tray, Add, Salir, CerrarLauncher

base := A_ScriptDir . "\laragon"
mysqlPID := 0
apachePID := 0

OnExit("Cleanup")

; GUI minimalista sin bordes
Gui, -Caption +AlwaysOnTop +ToolWindow
Gui, Color, F5F5F5
Gui, Margin, 50, 50

; Titulo
Gui, Font, s15 c2D2D2D, Segoe UI Light
Gui, Add, Text, x50 y50 w300 h35 Center BackgroundTrans, OptenAdvance

; Barra de progreso bonita
Gui, Add, Progress, x50 y105 w300 h5 BackgroundE0E0E0 c007BFF vBarra, 0

; Mensaje
Gui, Font, s11 c7A7A7A, Segoe UI
Gui, Add, Text, x50 y125 w300 h30 Center BackgroundTrans vMensaje, Preparando todo...

Gui, Show, w400 h205, OptenAdvance

Sleep, 500

; Base de datos
GuiControl,, Mensaje, Cargando base de datos...
GuiControl,, Barra, 10
Run, % base "\bin\mysql\mysql-8.4.3-winx64\bin\mysqld.exe",, Hide, mysqlPID

; Timeout 25 segundos para MySQL
timeout := 0
Loop {
    Process, Exist, mysqld.exe
    if (ErrorLevel > 0) {
        Break
    }
    Sleep, 500
    timeout += 500
    if (timeout >= 25000) {
        Gui, Destroy
        MsgBox, 16, Error Timeout, No se pudo cargar la base de datos.`n`nTiempo de espera agotado.
        ExitApp
    }
}

GuiControl,, Barra, 40
Sleep, 500

; Servidor web
GuiControl,, Mensaje, Iniciando servidor...
GuiControl,, Barra, 50
Run, % base "\bin\apache\httpd-2.4.62-240904-win64-VS17\bin\httpd.exe",, Hide, apachePID

; Timeout 20 segundos para Apache
timeout := 0
Loop {
    Process, Exist, httpd.exe
    if (ErrorLevel > 0) {
        Break
    }
    Sleep, 500
    timeout += 500
    if (timeout >= 20000) {
        Gui, Destroy
        MsgBox, 16, Error Timeout, No se pudo iniciar el servidor.`n`nTiempo de espera agotado.
        Cleanup("Error", 1)
        ExitApp
    }
}

GuiControl,, Barra, 75
Sleep, 500

; Abrir aplicacion
GuiControl,, Mensaje, Abriendo aplicacion...
GuiControl,, Barra, 90
Sleep, 800
Run, https://public.test/ventas/nueva

GuiControl,, Barra, 100

; Listo
Gui, Font, s11 c007BFF Bold, Segoe UI
GuiControl, Font, Mensaje
GuiControl,, Mensaje, Listo 

Sleep, 1500
Gui, Destroy

return

CerrarLauncher:
    MsgBox, 36, Cerrar, ¿Cerrar el sistema?
    IfMsgBox, Yes
        ExitApp
return

Cleanup(ExitReason, ExitCode) {
    global apachePID, mysqlPID
    
    ; Matar Apache por PID
    if (apachePID > 0) {
        Process, Close, %apachePID%
        Sleep, 300
    }
    Run, taskkill /F /IM httpd.exe,, Hide
    Sleep, 500
    
    ; Matar MySQL por PID
    if (mysqlPID > 0) {
        Process, Close, %mysqlPID%
        Sleep, 300
    }
    Run, taskkill /F /IM mysqld.exe,, Hide
    Sleep, 500
}

GuiClose:
GuiEscape:
return