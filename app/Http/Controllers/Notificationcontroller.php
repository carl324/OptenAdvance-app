<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Vista principal de notificaciones
public function index(Request $request)
{
    $notifications = Notification::orderBy('created_at', 'desc')
        ->paginate(20);

    // Marcar como leídas solo en carga inicial
    if (!$request->ajax()) {
        Notification::where('leida', false)->update(['leida' => true]);
    }

    if ($request->ajax()) {
    $items = $notifications->getCollection()->map(function ($n) {
        $n->created_at_human = $n->created_at->diffForHumans();
        return $n;
    });

    return response()->json([
        'data'         => $items,
        'current_page' => $notifications->currentPage(),
        'last_page'    => $notifications->lastPage(),
        'from'         => $notifications->firstItem() ?? 0,
        'to'           => $notifications->lastItem() ?? 0,
        'total'        => $notifications->total(),
    ]);
}

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
public function destroyAll(Request $request)
{
    if ($request->has('ids')) {
        Notification::whereIn('id', $request->ids)->delete();
    } else {
        Notification::query()->delete();
    }
    return response()->json(['success' => true]);
}
}