# OptenAdvance

Sistema de Punto de Venta (POS) completamente funcional y offline para Windows, diseñado para pequeños y medianos negocios.

---

## Descripción

**optenAdvance** es una aplicación moderna de Punto de Venta desarrollada con Laravel que opera 100% offline. Proporciona todas las funcionalidades necesarias para gestionar ventas, inventario, empleados y clientes de forma segura y confiable, sin depender de conexión a internet.

La aplicación es accesible desde cualquier navegador web local, ofrece una interfaz intuitiva y cuenta con características avanzadas de auditoría, reportes y gestión empresarial.

---

##  Características Principales

### Gestión de Caja
- Apertura y cierre de caja diarios
- Control de efectivo y discrepancias
- Reportes de caja por período

### Gestión de Datos
- **Productos**: Crear, leer, actualizar y eliminar (CRUD) con control de inventario
- **Empleados**: Gestión completa de personal con perfiles
- **Clientes**: Base de datos de clientes con historial de compras
- **Roles**: Sistema de permisos (Admin y Empleado)

### Ventas y Transacciones
- Punto de venta interactivo y ágil
- Sistema de **crédito interno** integrado
- **Anulación de ventas** con control de auditoría
- **Devoluciones de productos** con reintegro de inventario
- Comprobantes digitales inmediatos

### Facturación
- Generación automática de facturas con datos de la empresa (NIT, razón social, etc.)
- **Descarga de facturas en PDF** personalizadas
- **Impresión directa** desde la aplicación
- Numeración correlativa automática

### Reportes y Análisis
- **Dashboard** con indicadores clave de desempeño (KPI)
- Reportes detallados de ventas
- **Exportación a Excel** para análisis adicional
- Filtros avanzados por período, empleado, cliente, etc.

### Auditoría y Seguridad
- **Registro completo** de todas las transacciones
- Trazabilidad de cambios (quién, cuándo, qué cambió)
- Historial de anulaciones y devoluciones
- Control de sesiones y roles de usuario

### Administración del Sistema
- **Sistema de licencias** con validación de vigencia (activa/vencida)
- **Notificaciones del sistema** (estado de licencia, fallos de backup, etc.)
- **Backups automáticos** programados
- **Backups manuales** bajo demanda
- **Restauración de base de datos** desde backups anteriores
- Gestor de servicios en segundo plano


---

##  Stack Tecnológico

### Backend
- **PHP 8.x** - Lenguaje de programación
- **Laravel Framework** - Framework MVC robusto
- **MySQL** - Base de datos relacional

### Servidor Local
- **Apache HTTP Server** - Servidor web local
- **Certificado SSL autogenerado** - Conexiones HTTPS seguras

### Servicios
- **NSSM** (Non-Sucking Service Manager) - Gestor de servicios en Windows
- **Gestor de Tareas de Windows** - Para backups programados

### Características Técnicas
- **100% Offline** - No requiere conexión a internet
- **Interfaz Web** - Accesible desde navegador local 
- **Multiplataforma (local)** - Funciona en cualquier navegador de Windows

---

##  Optimización y Rendimiento

### Velocidad y Escalabilidad
- **Base de datos optimizada** - Maneja miles de registros sin pérdida de velocidad
- **Índices inteligentes** - Queries optimizadas para búsquedas instantáneas
- **Caché implementado** - Reduce tiempos de carga significativamente
- **Compresión de datos** - Minimiza el uso de almacenamiento

### Apache Optimizado
- **Configuración de máximo rendimiento** - Ajustes para carga rápida
- **Compresión GZIP** - Reduce tamaño de respuestas HTTP
- **Keep-Alive habilitado** - Reutiliza conexiones para mayor velocidad
- **KeepAliveTimeout optimizado** - Balance entre rendimiento y recursos

### Rendimiento en Cliente
- **Interfaz ligera** - Carga inicial < 2 segundos
- **JavaScript minificado** - Reduce tamaño de archivos
- **Lazy loading** - Carga datos bajo demanda
- **Sincronización asincrónica** - No bloquea la interfaz

---

##  Confiabilidad y Recuperación Automática

### Monitoreo Continuo
- **Vigilancia de servicios 24/7** - Monitoreo constante de Apache y MySQL
- **Detección de fallos** - Identifica automáticamente caídas de servicios
- **Alertas en tiempo real** - Notificaciones inmediatas de problemas

### Recuperación Automática
- **Reinicio automático de Apache** - Se levanta solo si se detiene
- **Reinicio automático de MySQL** - Se reinicia automáticamente en caso de fallo
- **Sin intervención manual** - Sistema se repara a sí mismo
- **Disponibilidad garantizada** - Minimiza tiempos de inactividad
- **Logs de recuperación** - Registro de cada reinicio automático

### Resiliencia del Sistema
- **Validación de integridad** - Verifica estado de servicios al iniciar
- **Recovery automático de base de datos** - Recuperación de bloqueos o inconsistencias
- **Protección contra caídas** - Reintentos automáticos de conexión
- **Heartbeat monitoring** - Pulso continuo de servicios críticos

---

##  Requisitos del Sistema

- **Sistema Operativo**: Windows 7 o superior (recomendado Windows 10/11)
- **Espacio en disco**: Mínimo 500 MB
- **RAM**: Mínimo 2 GB (recomendado 4 GB)
- **Navegador**: Chrome, Edge, Firefox o similar (moderno)
- **Permisos**: Acceso de administrador para la instalación

---

##  Instalación

### Instalación Automática (Recomendado)

1. **Descargar el instalador**
   - Descarga el archivo `AOptenAdvance-setup.exe` desde el repositorio

2. **Ejecutar como Administrador**
   - Haz clic derecho en el archivo `.exe`
   - Selecciona "Ejecutar como administrador"

3. **Seguir el asistente de instalación**
   - El instalador descargará e instalará automáticamente:
     - Apache HTTP Server
     - MySQL Database Server
     - PHP
     - OptenAdvance
     - Certificado SSL

4. **Inicio automático**
   - Los servicios se iniciarán automáticamente al finalizar




##  Directorios Importantes

```
OptenAdvance/
├── app/
│   ├── apache/          # Servidor web Apache
│   ├── mysql/           # Base de datos MySQL
│   ├── php/             # Intérprete PHP
│   ├── www/             # Aplicación Laravel
│   ├── scripts/         # Scripts de instalación y gestión
│   ├── ssl/             # Certificados SSL
│   ├── logs/            # Registros de errores
│   └── Backup/          # Backups de base de datos
└── README.md            # Este archivo
```

---

##  Seguridad

- **Certificado SSL autofirmado** - Previene advertencias del navegador
- **Sistema de roles y permisos** - Acceso controlado por rol
- **Validación de licencia** - Protege el uso no autorizado
- **Auditoría completa** - Registro de todas las acciones
- **Contraseñas encriptadas** - Hash seguro con Laravel

---

##  Uso



### Operación Diaria
- **Apertura de caja**: Inicio de jornada
- **Ventas**: Registra cada transacción
- **Anulaciones**: Gestiona ventas incorrectas
- **Cierre de caja**: Fin de jornada con validación de efectivo

### Mantenimiento
- **Backups**: Se ejecutan automáticamente según programación
- **Reportes**: Genera en cualquier momento desde el dashboard
- **Licencia**: Valida automáticamente al iniciar

---

##  Administración del Sistema

### Backups
```bash
# Ejecutar backup manual
app\scripts\ejecutar-backup.bat
```

### Monitoreo de Servicios
```bash
# Monitorear estado de servicios
app\scripts\monitor-services.bat
```

### Instalación de Servicios
```bash
# Instalar como servicios de Windows
app\scripts\instalar-servicio.bat

# Desinstalar servicios
app\scripts\desinstalar-servicio.bat
```

---

##  Reportes y Exportación

- **Dashboard**: Vista de resumen con KPI principales
- **Reportes de Ventas**: Detalles completos con filtros
- **Exportación Excel**: Para análisis y auditoría externa
- **Facturas PDF**: Descargables en cualquier momento

---

##  Sistema de Licencia

OptenAdvance utiliza un sistema de licencias para proteger su propiedad intelectual:

- **Validación**: Se valida automáticamente al iniciar
- **Notificaciones**: Alertas cuando la licencia está próxima a vencer
- **Funcionalidades Restringidas**: Límites según el tipo de licencia



##  Licencia

OptenAdvance © 2026. Todos los derechos reservados.

Este software está protegido por derechos de autor. Se prohíbe su reproducción, distribución o modificación sin autorización explícita del desarrollador.

---

##  Agradecimientos

Desarrollado con tecnologías de código abierto:
- Laravel Framework
- Apache HTTP Server
- MySQL Community Edition
- PHP

---

##  Hoja de Ruta Futura
- [ ] Integracion con la DIAN para control de impuestos
- [ ] Aplicación móvil para vendedores
- [ ] Integración con pasarelas de pago
- [ ] Sincronización en la nube (modo opcional)
- [ ] API REST para integraciones
- [ ] Módulo de compras y proveedores
- [ ] Control de horarios de empleados
- [ ] Loyalty program para clientes

---


