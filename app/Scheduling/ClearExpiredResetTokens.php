<?php

namespace App\Scheduling;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClearExpiredResetTokens
{
    public function __invoke(Schedule $schedule): void
    {
        $schedule->call(function () {
            $count = DB::table('password_resets')
                ->where('created_at', '<', now()->subHour())
                ->delete();

            if ($count > 0) {
                Log::info("Wyczyszczono {$count} przeterminowanych tokenów resetu hasła.");
            }
        })->daily(); 
    }
}
