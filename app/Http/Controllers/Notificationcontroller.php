<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Vista principal de notificaciones
    public function index()
    {
        $notifications = Notification::orderBy('created_at', 'desc')->get();

        // Marcar todas como leídas al abrir la vista
        Notification::where('leida', false)->update(['leida' => true]);

        return view('notifications.index', compact('notifications'));
    }

    // Endpoint para el contador de la campana (sin marcar como leídas)
    public function count()
    {
        return response()->json([
            'count' => Notification::noLeidas()->count()
        ]);
    }

    // Eliminar notificación
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return response()->json(['success' => true]);
    }

    // Eliminar todas las notificaciones
    public function destroyAll()
    {
        Notification::query()->delete();

        return response()->json(['success' => true]);
    }
}