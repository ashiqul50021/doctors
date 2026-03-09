<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Carbon\Carbon;

class GoogleMeetService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('Doccure');
        $this->client->setScopes(Calendar::CALENDAR_EVENTS);
        $this->client->setAuthConfig(storage_path('app/google-calendar/credentials.json'));
        $this->client->setAccessType('offline');
    }

    public function createMeeting($summary, $startTime, $durationMinutes = 60)
    {
        // Check if credentials exist
        if (!file_exists(storage_path('app/google-calendar/credentials.json'))) {
            return null; // Graceful fallback
        }

        $service = new Calendar($this->client);

        $startDateTime = Carbon::parse($startTime);
        $endDateTime = $startDateTime->copy()->addMinutes($durationMinutes);

        $event = new Event([
            'summary' => $summary,
            'start' => ['dateTime' => $startDateTime->toRfc3339()],
            'end' => ['dateTime' => $endDateTime->toRfc3339()],
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => uniqid(),
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet']
                ]
            ]
        ]);

        $calendarId = 'primary';
        $event = $service->events->insert($calendarId, $event, ['conferenceDataVersion' => 1]);

        return $event->getHtmlLink();
    }
}
