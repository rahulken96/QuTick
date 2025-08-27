<?php

use Carbon\Carbon;
use App\Models\Ticket;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

if (! function_exists('generateTicketCode')) {
    function generateTicketCode()
    {
        do {
            $number = 'TCKT-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) . now()->format('ymd') . '-' . strtoupper(Str::random(8));
            $exists = Ticket::where('code', $number)->exists();
        } while ($exists);

        return $number;
    }
}

if (! function_exists('formatCreatedForHumans')) {
    function formatCreatedForHumans($timestamps)
    {
        $datetime = $timestamps ? Carbon::parse($timestamps)->format('Y-m-d H:i') : null;
        return $datetime;
    }
}

if (! function_exists('isThisAdmin')) {
    function isThisAdmin()
    {
        return Auth::check() && Auth::user()->role == 'admin';
    }
}
