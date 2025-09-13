<?php

namespace App\Services;

use App\Models\Announcement;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    /**
     * Broadcast an announcement to a single hardcoded WhatsApp number (demo only).
     *
     * @param \App\Models\Announcement $announcement
     * @return int Number of recipients (1 if successful, 0 if failed)
     */
    public function broadcastAnnouncement(Announcement $announcement): int
    {
        // ✅ Replace with your own WhatsApp number (must include country code, no '+')
        $testNumber = "60123456789"; // Example: Malaysia

        $message = "📢 *New Announcement*\n\n" .
                   "*{$announcement->title}*\n" .
                   "{$announcement->content}\n\n" .
                   "- Clinic Management System";

        try {
            $response = Http::post('http://localhost:3001/send', [
                'number'  => $testNumber,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp message sent to {$testNumber}");
                return 1;
            } else {
                Log::error("Failed to send WhatsApp message: " . $response->body());
                return 0;
            }
        } catch (\Exception $e) {
            Log::error("WhatsApp service error: " . $e->getMessage());
            return 0;
        }
    }
}
