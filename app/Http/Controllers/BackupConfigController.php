<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfiguracionBackup;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class BackupConfigController extends Controller
{
    // Obtener configuración actual
    public function obtener()
    {
        try {
            $config = ConfiguracionBackup::first();
            
            // Si no existe configuración, crear una por defecto
            if (!$config) {
                $backupsPath = storage_path('app/backups');
                if (!File::exists($backupsPath)) {
                    File::makeDirectory($backupsPath, 0755, true);
                }
                
                $config = ConfiguracionBackup::create([
                    'carpeta_destino' => $backupsPath,
                    'prefijo_nombre_archivo' => 'backup_opten',
                    'frecuencia' => 'semanal',
                    'hora_backup' => '02:00',
                    'retencion' => 30
                ]);
            }
            
            return response()->json([
                'success' => true,
                'config' => $config
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error obteniendo configuración: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener configuración: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Guardar configuración
    public function guardar(Request $request)
    {
        try {
            // Validación personalizada para evitar errores con barras invertidas
            $rules = [
                'carpeta_destino' => 'required|string|max:500',
                'prefijo_nombre_archivo' => 'nullable|string|max:50',
                'frecuencia' => 'required|in:diario,semanal,mensual',
                'hora_backup' => 'required',
                'retencion' => 'required|integer|min:1|max:365'
            ];
            
            $messages = [
                'carpeta_destino.required' => 'Debe ingresar una ruta de destino',
                'carpeta_destino.max' => 'La ruta es demasiado larga',
                'frecuencia.required' => 'Debe seleccionar una frecuencia',
                'frecuencia.in' => 'Frecuencia inválida',
                'hora_backup.required' => 'Debe seleccionar una hora',
                'retencion.required' => 'Debe especificar la retención',
                'retencion.integer' => 'La retención debe ser un número',
                'retencion.min' => 'La retención mínima es 1 día',
                'retencion.max' => 'La retención máxima es 365 días'
            ];
            
            $validator = Validator::make($request->all(), $rules, $messages);
            
            // Validación adicional manual para evitar problemas con regex
            $validator->after(function ($validator) use ($request) {
                // Validar prefijo_nombre_archivo manualmente
                if ($request->prefijo_nombre_archivo) {
                    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $request->prefijo_nombre_archivo)) {
                        $validator->errors()->add('prefijo_nombre_archivo', 
                            'El prefijo solo puede contener letras, números, guiones y guiones bajos');
                    }
                }
                
                // Validar formato de hora manualmente
                if ($request->hora_backup) {
                    if (!preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $request->hora_backup)) {
                        $validator->errors()->add('hora_backup', 'Formato de hora inválido');
                    }
                }
            });
            
            if ($validator->fails()) {
                Log::error('Validación fallida:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Normalizar la ruta (convertir todas las barras a forward slash para consistencia)
            $carpeta = trim($request->carpeta_destino);
            // Mantener el formato original pero limpiar espacios
            
            Log::info('Ruta recibida: ' . $carpeta);
            
            // Intentar crear la carpeta si no existe
            if (!File::exists($carpeta)) {
                try {
                    File::makeDirectory($carpeta, 0755, true);
                    Log::info('Carpeta creada exitosamente: ' . $carpeta);
                } catch (\Exception $e) {
                    Log::error('Error creando carpeta: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'No se pudo crear la carpeta. Verifica que la ruta sea válida y tengas permisos suficientes'
                    ], 422);
                }
            }
            
            // Verificar permisos de escritura
            if (!File::isWritable($carpeta)) {
                Log::error('Carpeta no escribible: ' . $carpeta);
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos de escritura en esta carpeta'
                ], 422);
            }
            
            // Guardar o actualizar configuración
            $config = ConfiguracionBackup::updateOrCreate(
                ['id' => 1],
                [
                    'carpeta_destino' => $carpeta,
                    'prefijo_nombre_archivo' => $request->prefijo_nombre_archivo ?: 'backup_opten',
                    'frecuencia' => $request->frecuencia,
                    'hora_backup' => $request->hora_backup,
                    'retencion' => $request->retencion
                ]
            );
            
            Log::info('Configuración guardada exitosamente');
            
            return response()->json([
                'success' => true,
                'message' => 'Configuración guardada exitosamente',
                'config' => $config
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error guardando configuración backup: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar configuración: ' . $e->getMessage()
            ], 500);
        }
    }
}