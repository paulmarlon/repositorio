<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\DeviceSession;
use Illuminate\Http\Request;

class LogSuccessfulLogin
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(Login $event): void
    {
        $user = $event->user;
        $userAgent = $this->request->header('User-Agent');

        $deviceType = 'Escritorio';
        if (preg_match('/mobile|android|iphone|ipad|phone/i', $userAgent)) {
            $deviceType = 'Móvil / Tablet';
        }

        DeviceSession::create([
            'user_id' => $user->id,
            'ip_address' => $this->request->ip(),
            'user_agent' => $userAgent,
            'device_type' => $deviceType,
            'last_activity' => now(),
        ]);
    }
}
