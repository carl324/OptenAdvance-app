<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\LicenseService;
use Illuminate\Support\Carbon;

class LicenseNotificationController extends Controller
{
    public function check(LicenseService $license)
    {
        $data = $license->uiData();

        // Si no hay fecha de vencimiento, no hacemos nada
        if (!$data['end_at'] || $data['days_remaining'] === null) {
            return null;
        }

        $days = (int) $data['days_remaining'];
        $today = Carbon::today();

        // Días que nos interesan
        $checkpoints = [30, 15, 7, 3, 0];

        if (!in_array($days, $checkpoints)) {
            return null;
        }

        $alreadyShown = Notification::where('type', 'license_expiry')
            ->where('days_before', $days)
            ->where('date_key', $today)
            ->exists();

        if ($alreadyShown) {
            return null;
        }

        // Guardamos que ya se mostró HOY
        Notification::create([
            'type'        => 'license_expiry',
            'days_before' => $days,
            'shown_at'    => now(),
            'date_key'    => $today,
        ]);

        // Retornamos señal para la vista
        return [
            'show' => true,
            'days_remaining' => $days,
        ];
    }
}
