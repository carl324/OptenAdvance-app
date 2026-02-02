<?php

namespace App\Http\Controllers;

use App\Services\LicenseService;

class LicenseController extends Controller
{
    public function showModal()
    {
        $data = app(LicenseService::class)->uiData();
        return view('modals.license', compact('data'));
    }
}
