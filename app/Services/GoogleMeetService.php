<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\ConferenceData;
use Google\Service\Calendar\CreateConferenceRequest;
use Google\Service\Calendar\ConferenceSolutionKey;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GoogleMeetService
{
    protected $client;
    protected $credentialsPath;
    protected $tokenPath;

    public function __construct()
    {
        $this->credentialsPath = storage_path('app/google-calendar/credentials.json');
        $this->tokenPath = storage_path('app/google-calendar/token.json');

        $this->client = new Client();
        $this->client->setApplicationName(config('app.name', 'abcsheba'));
        $this->client->setScopes([Calendar::CALENDAR_EVENTS]);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');

        if (file_exists($this->credentialsPath)) {
            $this->client->setAuthConfig($this->credentialsPath);
        }
    }

    /**
     * Check if credentials.json exists
     */
    public function hasCredentials(): bool
    {
        return file_exists($this->credentialsPath);
    }

    /**
     * Check if token.json exists and is valid
     */
    public function hasValidToken(): bool
    {
        if (!file_exists($this->tokenPath)) {
            return false;
        }

        $token = json_decode(file_get_contents($this->tokenPath), true);
        $this->client->setAccessToken($token);

        // If token is expired, try refreshing
        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                $newToken = $this->client->getAccessToken();
                // Keep refresh token
                if (!isset($newToken['refresh_token']) && isset($token['refresh_token'])) {
                    $newToken['refresh_token'] = $token['refresh_token'];
                }
                file_put_contents($this->tokenPath, json_encode($newToken));
                return true;
            }
            return false;
        }

        return true;
    }

    /**
     * Get the authorization URL for initial OAuth setup
     */
    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Exchange authorization code for access token
     */
    public function authenticate(string $authCode): bool
    {
        try {
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);

            if (isset($accessToken['error'])) {
                Log::error('Google OAuth Error: ' . ($accessToken['error_description'] ?? $accessToken['error']));
                return false;
            }

            $this->client->setAccessToken($accessToken);

            // Save token
            if (!is_dir(dirname($this->tokenPath))) {
                mkdir(dirname($this->tokenPath), 0700, true);
            }
            file_put_contents($this->tokenPath, json_encode($accessToken));

            Log::info('Google Calendar token saved successfully.');
            return true;
        } catch (\Exception $e) {
            Log::error('Google Auth Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if service is fully ready to create meetings
     */
    public function isReady(): bool
    {
        return $this->hasCredentials() && $this->hasValidToken();
    }

    /**
     * Create a Google Meet meeting via Calendar API
     *
     * @param string $summary Meeting title
     * @param string $startTime Start time (parseable by Carbon)
     * @param int $durationMinutes Duration in minutes
     * @return string|null The Google Meet link, or null on failure
     */
    public function createMeeting(string $summary, string $startTime, int $durationMinutes = 30): ?string
    {
        if (!$this->isReady()) {
            Log::warning('GoogleMeetService: Not ready. Credentials or token missing.');
            return null;
        }

        try {
            $service = new Calendar($this->client);

            $startDateTime = Carbon::parse($startTime);
            $endDateTime = $startDateTime->copy()->addMinutes($durationMinutes);

            $event = new Event([
                'summary' => $summary,
                'start' => [
                    'dateTime' => $startDateTime->toRfc3339String(),
                    'timeZone' => config('app.timezone', 'Asia/Dhaka'),
                ],
                'end' => [
                    'dateTime' => $endDateTime->toRfc3339String(),
                    'timeZone' => config('app.timezone', 'Asia/Dhaka'),
                ],
                'conferenceData' => [
                    'createRequest' => [
                        'requestId' => 'meet-' . uniqid(),
                        'conferenceSolutionKey' => [
                            'type' => 'hangoutsMeet',
                        ],
                    ],
                ],
            ]);

            $calendarId = 'primary';
            $createdEvent = $service->events->insert($calendarId, $event, [
                'conferenceDataVersion' => 1,
            ]);

            // Extract the Meet link
            $meetLink = null;
            if ($createdEvent->getConferenceData() && $createdEvent->getConferenceData()->getEntryPoints()) {
                foreach ($createdEvent->getConferenceData()->getEntryPoints() as $entryPoint) {
                    if ($entryPoint->getEntryPointType() === 'video') {
                        $meetLink = $entryPoint->getUri();
                        break;
                    }
                }
            }

            // Fallback to HTML link if Meet link not found
            if (!$meetLink) {
                $meetLink = $createdEvent->getHangoutLink() ?? $createdEvent->getHtmlLink();
            }

            Log::info('Google Meet created: ' . $meetLink);
            return $meetLink;
        } catch (\Google\Service\Exception $e) {
            Log::error('Google Calendar API Error: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Log::error('Google Meet Error: ' . $e->getMessage());
            return null;
        }
    }
}
