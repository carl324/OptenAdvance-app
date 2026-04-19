<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\DatabaseRestoreService;
use App\Models\Venta;

class DatabaseRestoreController extends Controller
{
    protected $restoreService;

    public function __construct(DatabaseRestoreService $restoreService)
    {
        $this->restoreService = $restoreService;
    }

    public function restore(Request $request)
{
    $lockFile = null;

    try {
        if (!$request->input('confirm_restore')) {
            return response()->json(['error' => 'Debes confirmar que entiendes que esta acción sobrescribirá tus datos actuales.'], 400);
        }

        $request->validate([
            'backup_file' => 'required|file|max:512000',
        ], [
            'backup_file.required' => 'Debes seleccionar un archivo de respaldo',
            'backup_file.file' => 'El archivo no es válido',
            'backup_file.max' => 'El archivo excede el tamaño máximo permitido (500MB)',
        ]);

        $file = $request->file('backup_file');

        $validationErrors = $this->restoreService->validateBackupFile($file);
        if (!empty($validationErrors)) {
            return response()->json(['error' => implode(' ', $validationErrors)], 400);
        }

        $lockFile = storage_path('app' . DIRECTORY_SEPARATOR . 'restore.lock');
        $lockTimeout = 10 * 60;
        
        // FIX: Eliminar lock expirado antes de crear uno nuevo
        if (file_exists($lockFile)) {
            $lockTime = (int)@file_get_contents($lockFile);
            if (time() - $lockTime < $lockTimeout) {
                Log::warning('DatabaseRestoreController: Intento de restauración mientras otra está en proceso');
                return response()->json(['error' => 'Una restauración ya está en proceso. Intenta de nuevo más tarde.'], 429);
            }
            @unlink($lockFile); // ← FIX: Eliminar lock expirado
        }

        if (!@file_put_contents($lockFile, time())) {
            return response()->json(['error' => 'No se pudo crear el lock de restauración.'], 500);
        }

        $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp');
        
        // FIX: Verificar que se puede crear el directorio
        if (!is_dir($tempDir)) {
            if (!mkdir($tempDir, 0755, true)) {
                @unlink($lockFile);
                return response()->json(['error' => 'No se pudo crear directorio temporal'], 500);
            }
        }
        
        // FIX: Verificar permisos de escritura
        if (!is_writable($tempDir)) {
            @unlink($lockFile);
            return response()->json(['error' => 'Directorio temporal no tiene permisos de escritura'], 500);
        }

        $tempFileName = 'restore_' . time() . '.sql';
        $tempFilePath = $tempDir . DIRECTORY_SEPARATOR . $tempFileName;
        $file->move($tempDir, $tempFileName);

        Log::info('DatabaseRestoreController: Creando backup automático antes de restaurar');
        $backupResult = $this->restoreService->createAutoBackup();
        
        if (!$backupResult['success']) {
            @unlink($tempFilePath);
            @unlink($lockFile);
            return response()->json(['error' => 'Error al crear backup automático: ' . $backupResult['error']], 500);
        }

        Log::info('DatabaseRestoreController: Iniciando restauración de base de datos');
        $restoreResult = $this->restoreService->restoreDatabase($tempFilePath);

        @unlink($tempFilePath);
        @unlink($lockFile);

        if (!$restoreResult['success']) {
            return response()->json([
                'error' => 'Error al restaurar la base de datos: ' . $restoreResult['error'],
                'backup_created' => $backupResult['file'],
            ], 500);
        }

        $this->restoreService->cleanupOldBackups(7);

        Log::info('DatabaseRestoreController: Restauración completada exitosamente');

        return response()->json([
            'success' => true,
            'message' => 'Base de datos restaurada exitosamente',
            'backup_created' => $backupResult['file'],
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        if ($lockFile) @unlink($lockFile);
        return response()->json(['error' => $e->validator->errors()->first()], 422);
        
    } catch (\Exception $e) {
        Log::error('DatabaseRestoreController: Excepción no controlada', ['error' => $e->getMessage()]);
        if ($lockFile) @unlink($lockFile);
        return response()->json(['error' => 'Error inesperado: ' . $e->getMessage()], 500);
    }
}
}