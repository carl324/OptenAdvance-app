<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;

class BackupController extends Controller
{
    /**
     * Copia manual del archivo SQLite a la carpeta Downloads/opten-backups del usuario.
     */
    public function store(Request $request)
    {
        try {
            // 1) Verificar ventas activas (estado distinto de 'completada' o 'anulada')
            $tieneActivas = Venta::whereNotIn('estado', ['completada', 'anulada'])->exists();
            if ($tieneActivas) {
                return redirect()->back()->with('error', 'Hay ventas en curso. Finalízalas antes de crear la copia de seguridad.');
            }

            // 2) Ruta origen del archivo SQLite dentro del proyecto
            $source = base_path('database' . DIRECTORY_SEPARATOR . 'database.sqlite');
            if (!file_exists($source)) {
                return redirect()->back()->with('error', 'No se encontró el archivo de base de datos (database/database.sqlite).');
            }

            // 3) Detectar carpeta HOME / USERPROFILE y determinar Downloads
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
                return redirect()->back()->with('error', 'No se pudo determinar la carpeta de usuario para guardar el respaldo.');
            }

            $downloadsDir = $home . DIRECTORY_SEPARATOR . 'Downloads' . DIRECTORY_SEPARATOR . 'opten-backups';

            // 4) Crear carpeta si no existe
            if (!is_dir($downloadsDir)) {
                if (!@mkdir($downloadsDir, 0755, true) && !is_dir($downloadsDir)) {
                    return redirect()->back()->with('error', 'No se pudo crear la carpeta de destino en Descargas. Comprueba permisos.');
                }
            }

            // 5) Verificar permisos de escritura
            if (!is_writable($downloadsDir)) {
                return redirect()->back()->with('error', 'La carpeta de Descargas no permite escritura. Revisa permisos.');
            }

            // 6) Nombre de archivo y evitar sobreescritura
            $timestamp = date('Y-m-d_H-i-s');
            $fileName = "opten_backup_{$timestamp}.sqlite";
            $target = $downloadsDir . DIRECTORY_SEPARATOR . $fileName;
            if (file_exists($target)) {
                $fileName = "opten_backup_{$timestamp}_" . uniqid() . '.sqlite';
                $target = $downloadsDir . DIRECTORY_SEPARATOR . $fileName;
            }

            // 7) Copiar el archivo
            if (!@copy($source, $target)) {
                return redirect()->back()->with('error', 'Error al copiar el archivo de base de datos a Descargas. Verifica espacio y permisos.');
            }

            return redirect()->back()->with('success', 'Respaldo creado en Descargas: ' . $fileName);

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Error creando respaldo: ' . $e->getMessage());
        }
    }
}
