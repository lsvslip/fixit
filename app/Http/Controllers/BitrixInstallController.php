<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BitrixInstallation;

class BitrixInstallController extends Controller
{
    public function install(Request $req)
    {
        if ($req->isMethod('post')) {
            BitrixInstallation::updateOrCreate(
                ['domain' => $req->input('DOMAIN')],
                [
                    'app_sid'      => $req->input('APP_SID'),
                    'auth_id'      => $req->input('AUTH_ID'),
                    'refresh_id'   => $req->input('REFRESH_ID'),
                    'auth_expires' => (int)$req->input('AUTH_EXPIRES'),
                ]
            );
        }

        // Выдаём view, которое завершает установку
        return view('bitrix.install-finish');
    }
}
