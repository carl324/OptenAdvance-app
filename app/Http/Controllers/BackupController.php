<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;

class BackupController extends Controller
{
    /**
     * Copia manual del archivo SQLite a la carpeta Downloads/opten-backups del usuario.
     * Incluye: WAL checkpoint, validación post-copia, prevención de doble ejecución.
     */
    public function store(Request $request)
    {
        $lockFile = null;

        try {
            // 0) Validar confirmación (checkbox backend)
            if (!$request->input('confirm_backup')) {
                return redirect()->back()->with('error', 'Debes confirmar que deseas crear el respaldo.');
            }

            // 1) Prevenir doble ejecución: crear lock temporal
            $lockFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'opten_backup.lock';
            if (file_exists($lockFile)) {
                return redirect()->back()->with('error', 'Un respaldo ya está en proceso. Intenta de nuevo en unos segundos.');
            }
            if (!@file_put_contents($lockFile, time())) {
                return redirect()->back()->with('error', 'No se pudo crear el lock de respaldo. Intenta de nuevo.');
            }

            // 2) Verificar ventas activas (estado distinto de 'completada' o 'anulada')
            $tieneActivas = Venta::whereNotIn('estado', ['completada', 'anulada'])->exists();
            if ($tieneActivas) {
                @unlink($lockFile);
                return redirect()->back()->with('error', 'Hay ventas en curso. Finalízalas antes de crear la copia de seguridad.');
            }

            // 2) Ruta origen del archivo SQLite dentro del proyecto
            $source = base_path('database' . DIRECTORY_SEPARATOR . 'database.sqlite');
            if (!file_exists($source)) {
                @unlink($lockFile);
                return redirect()->back()->with('error', 'No se encontró el archivo de base de datos (database/database.sqlite).');
            }

            // 3) WAL checkpoint: consolidar datos de Write-Ahead Log en archivo principal
            try {
                DB::statement('PRAGMA wal_checkpoint(FULL);');
            } catch (\Exception $walError) {
                // Log pero no fallar: WAL puede no estar activo, es optional
            }

            // 4) Detectar carpeta HOME / USERPROFILE y determinar Downloads
            $home = getenv('HOME') ?: getenv('USERPROFILE') ?: null;
            if (!$home) {
                // intentos alternativos en Windows
                $homeDrive = getenv('HOMEDRIVE');
                $homePath = getenv('HOMEPATH');
                if ($homeDrive && $homePath) {
                    $home = rtrim($homeDrive, '\\') . $homePath;
                }
            }

            if (!$home) {
                @unlink($lockFile);
                return redirect()->back()->with('error', 'No se pudo determinar la carpeta de usuario para guardar el respaldo.');
            }

            $downloadsDir = $home . DIRECTORY_SEPARATOR . 'Downloads' . DIRECTORY_SEPARATOR . 'opten-backups';

            // 5) Crear carpeta si no existe
            if (!is_dir($downloadsDir)) {
                if (!@mkdir($downloadsDir, 0755, true) && !is_dir($downloadsDir)) {
                    @unlink($lockFile);
                    return redirect()->back()->with('error', 'No se pudo crear la carpeta de destino en Descargas. Comprueba permisos.');
                }
            }

            // 6) Verificar permisos de escritura
            if (!is_writable($downloadsDir)) {
                @unlink($lockFile);
                return redirect()->back()->with('error', 'La carpeta de Descargas no permite escritura. Revisa permisos.');
            }

            // 7) Nombre de archivo y evitar sobreescritura
            $timestamp = date('Y-m-d_H-i-s');
            $fileName = "opten_backup_{$timestamp}.sqlite";
            $target = $downloadsDir . DIRECTORY_SEPARATOR . $fileName;
            if (file_exists($target)) {
                $fileName = "opten_backup_{$timestamp}_" . uniqid() . '.sqlite';
                $target = $downloadsDir . DIRECTORY_SEPARATOR . $fileName;
            }

            // 8) Copiar el archivo
            if (!@copy($source, $target)) {
                @unlink($lockFile);
                return redirect()->back()->with('error', 'Error al copiar el archivo de base de datos a Descargas. Verifica espacio y permisos.');
            }

            // 9) VALIDACIÓN POST-COPIA: Verificar integridad del backup
            try {
                $backupPDO = new \PDO('sqlite:' . $target);
                $backupPDO->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                
                // Ejecutar integrity check en el backup
                $result = $backupPDO->query('PRAGMA integrity_check;')->fetch(\PDO::FETCH_COLUMN);
                
                if ($result !== 'ok') {
                    // Backup corrupto: eliminarlo
                    @unlink($target);
                    @unlink($lockFile);
                    return redirect()->back()->with('error', 'El respaldo generado está corrupto. Se eliminó. Intenta de nuevo.');
                }

                // Cerrar conexión al backup
                $backupPDO = null;

            } catch (\Exception $integrityError) {
                // Error al validar (BD corrupta, no accesible, etc.)
                @unlink($target);
                @unlink($lockFile);
                return redirect()->back()->with('error', 'Error validando el respaldo: ' . $integrityError->getMessage());
            }

            // 10) Limpieza y éxito
            @unlink($lockFile);
            return redirect()->back()->with('success', 'Respaldo creado exitosamente en Descargas: ' . $fileName);

        } catch (\Throwable $e) {
            if ($lockFile) {
                @unlink($lockFile);
            }
            return redirect()->back()->with('error', 'Error creando respaldo: ' . $e->getMessage());
        }
    }
}
