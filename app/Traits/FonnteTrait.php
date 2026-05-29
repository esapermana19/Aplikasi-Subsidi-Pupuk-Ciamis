<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait FonnteTrait
{
    public function sendWhatsAppMessage($target, $message)
    {
        $token = env('FONNTE_TOKEN');
        
        if (!$token || empty($target)) {
            return false;
        }

        try {
            $response = Http::withoutVerifying()
                ->asForm()
                ->withHeaders([
                    'Authorization' => $token
                ])
                ->timeout(30)
                ->post('https://api.fonnte.com/send', [
                    'target' => $target,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            if (!$response->successful()) {
                \Illuminate\Support\Facades\Log::error('Fonnte Error: ' . $response->body());
            }

            return $response->successful();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Fonnte Error: ' . $e->getMessage());
            return false;
        }
    }
}
