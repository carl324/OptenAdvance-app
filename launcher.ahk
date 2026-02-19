#Persistent
#SingleInstance Force
SetTitleMatchMode, 2
DetectHiddenWindows, On

; ==================== CONFIGURACIÓN ====================
global APP_URL := "https://optenadvance.test"
global MYSQL_SERVICE := "OptenAdvanceMySQL"
global APACHE_SERVICE := "OptenAdvanceApache"
global MYSQL_PORT := 3306
global APACHE_PORT := 443
global TIMEOUT_SERVICES := 15000


; ==================== MENÚ DE BANDEJA ====================
Menu, Tray, NoStandard
Menu, Tray, Add, Abrir OptenAdvance, AbrirApp
Menu, Tray, Add, Salir, Salir
Menu, Tray, Default, Abrir OptenAdvance
Menu, Tray, Click, 1
Menu, Tray, Tip, OptenAdvance

OnExit("Cleanup")

; ==================== GUI ORIGINAL ====================
Gui, -Caption +AlwaysOnTop +ToolWindow
Gui, Color, F5F5F5
Gui, Margin, 50, 50

; Titulo
Gui, Font, s15 c2D2D2D, Segoe UI Light
Gui, Add, Text, x50 y50 w300 h35 Center BackgroundTrans, OptenAdvance

; Barra de progreso
Gui, Add, Progress, x50 y105 w300 h5 BackgroundE0E0E0 c007BFF vBarra, 0

; Mensaje
Gui, Font, s11 c7A7A7A, Segoe UI
Gui, Add, Text, x50 y125 w300 h30 Center BackgroundTrans vMensaje, Preparando servicios...

; Botón X para cerrar
Gui, Font, s12 c999999, Segoe UI
Gui, Add, Text, x365 y10 w20 h20 Center BackgroundTrans gCerrarVentana, x

Gui, Show, w400 h205, OptenAdvance

Sleep, 500

; ==================== FLUJO PRINCIPAL ====================
;log("=== Iniciando OptenAdvance Launcher ===")

; Verificar si la aplicación ya está disponible
GuiControl,, Mensaje, Preparando servicios...
GuiControl,, Barra, 5

if (ValidarURL(APP_URL)) {
    ;log("Aplicación ya disponible, abriendo directamente")
    GuiControl,, Mensaje, Abriendo programa...
    GuiControl,, Barra, 100
    Sleep, 800
    Run, %APP_URL%
    Goto, Finalizado
}

;log("Aplicación no disponible, iniciando servicios")

; Verificar y arrancar MySQL
GuiControl,, Mensaje, Iniciando base de datos...
GuiControl,, Barra, 10

if (!IniciarServicio(MYSQL_SERVICE, MYSQL_PORT)) {
    MostrarError("Error: Base de datos no disponible")
    Sleep, 3000
    ExitApp
}

GuiControl,, Barra, 50
Sleep, 500

; Verificar y arrancar Apache
GuiControl,, Mensaje, Iniciando servidor web...
GuiControl,, Barra, 60

if (!IniciarServicio(APACHE_SERVICE, APACHE_PORT)) {
    MostrarError("Error: Servidor web no disponible")
    Sleep, 3000
    ExitApp
}

GuiControl,, Barra, 90
Sleep, 500

; Abrir aplicación
GuiControl,, Mensaje, Abriendo programa...
Sleep, 800

if (ValidarURL(APP_URL)) {
    Run, %APP_URL%
} else {
    MostrarError("Error: Aplicación no responde")
    Sleep, 3000
    ExitApp
}

GuiControl,, Barra, 100

Finalizado:
; Listo
Gui, Font, s11 c007BFF Bold, Segoe UI
GuiControl, Font, Mensaje
GuiControl,, Mensaje, Inicio exitoso

Sleep, 1500
Gui, Destroy
;log("=== Finalizando launcher ===`n")

return

; ==================== FUNCIONES ====================

IniciarServicio(nombreServicio, puerto) {
    ;;log("Iniciando servicio: " . nombreServicio)
    
    ; Verificar si ya está corriendo
    if (ServicioEstaActivo(nombreServicio) && PuertoEstaEscuchando(puerto)) {
        ;log("Servicio ya activo y puerto escuchando: " . nombreServicio)
        return true
    }
    
    ; Intentar iniciar servicio
    if (!ServicioEstaActivo(nombreServicio)) {
        RunWait, net start %nombreServicio%,, Hide UseErrorLevel
        Sleep, 2000
    }
    
    ; Esperar a que el puerto esté disponible
    timeout := 0
    Loop {
        if (PuertoEstaEscuchando(puerto)) {
            ;log("Puerto " . puerto . " está escuchando correctamente")
            return true
        }
        
        Sleep, 500
        timeout += 500
        
        if (timeout >= TIMEOUT_SERVICES) {
            ;log("ERROR: Timeout esperando puerto " . puerto)
            return false
        }
    }
    
    return false
}

ServicioEstaActivo(nombreServicio) {
    RunWait, %ComSpec% /c sc query "%nombreServicio%" | findstr "RUNNING" > nul,, Hide UseErrorLevel
    return (ErrorLevel = 0)
}

PuertoEstaEscuchando(puerto) {
    RunWait, %ComSpec% /c netstat -an | findstr ":%puerto%.*LISTENING" > nul,, Hide UseErrorLevel
    return (ErrorLevel = 0)
}

ValidarURL(url) {
    try {
        http := ComObjCreate("WinHttp.WinHttpRequest.5.1")
        http.SetTimeouts(2000, 2000, 3000, 3000)
        http.Open("GET", url, false)
        http.Send()
        
        if (http.Status >= 200 && http.Status < 400) {
            ;log("URL válida: " . url . " (Status: " . http.Status . ")")
            return true
        }
        
        ;log("URL retornó status: " . http.Status)
        return false
    } catch e {
        ;log("ERROR validando URL: " . e.Message)
        return false
    }
}

MostrarError(mensaje) {
    ;log("Mostrando error: " . mensaje)
    Gui, Font, s11 cFF0000 Bold, Segoe UI
    GuiControl, Font, Mensaje
    GuiControl,, Mensaje, %mensaje%
    GuiControl,, Barra, 0
}



; ==================== EVENTOS ====================

AbrirApp:
    Run, %APP_URL%
return

Salir:
    ExitApp
return

CerrarVentana:
    Gui, Destroy
    ExitApp
return

Cleanup(ExitReason, ExitCode) {
    Gui, Destroy
}

GuiClose:
GuiEscape:
return