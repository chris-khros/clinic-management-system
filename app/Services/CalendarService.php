<?php

namespace App\Services;

use Spatie\GoogleCalendar\Event;
use Carbon\Carbon;
use App\Models\Appointment;
use Illuminate\Support\Facades\Log;

class CalendarService
{
    /**
     * Push an appointment to Google Calendar.
     */
    public function createEventForAppointment(Appointment $appointment, ?string $calendarId = null): ?Event
    {
        try {
            // Build a robust start datetime regardless of how time is stored (TIME or DATETIME)
            $date = Carbon::parse($appointment->appointment_date);
            $rawTime = (string) $appointment->appointment_time;

            // Extract HH:MM:SS from possible "YYYY-MM-DD HH:MM:SS" or just "HH:MM:SS"
            if (preg_match('/(\d{2}:\d{2}:\d{2})/', $rawTime, $m)) {
                $timeStr = $m[1];
            } else {
                $timeStr = '09:00:00';
            }

            [$h,$m,$s] = array_map('intval', explode(':', $timeStr));
            $start = (clone $date)->setTime($h, $m, $s)->setTimezone('Asia/Kuala_Lumpur');
            $end = (clone $start)->addMinutes(30);

            $title = 'Appointment: '.$appointment->patient->full_name;
            $description = 'Doctor: '.optional($appointment->doctor->user)->name."\n".
                'Reason: '.($appointment->reason ?? 'N/A');

            $event = new Event;
            $event->name = $title;
            $event->description = $description;
            $event->startDateTime = $start;
            $event->endDateTime = $end;

            if ($calendarId) {
                return $event->save($calendarId);
            }

            return $event->save();
        } catch (\Exception $e) {
            Log::error('Failed to create Google Calendar event: ' . $e->getMessage(), [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}


