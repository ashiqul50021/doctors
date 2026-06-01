<?php

namespace App\Services;

use Illuminate\Support\Str;

class VideoCallService
{
    /**
     * Jitsi Meet base URL
     * Free public server: meet.jit.si
     * Self-hosted হলে নিজের domain দিতে পারবে
     */
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.jitsi.url', 'https://meet.jit.si');
    }

    /**
     * Create a Jitsi Meet room link
     *
     * @param string $doctorName Doctor's name
     * @param string $date Appointment date (Y-m-d)
     * @param string $time Appointment time (H:i)
     * @param int|null $appointmentId Optional appointment ID for extra uniqueness
     * @return string The full Jitsi Meet URL
     */
    public function createMeeting(string $doctorName, string $date, string $time, ?int $appointmentId = null): string
    {
        // Build a unique, readable room name
        $doctorSlug = Str::slug($doctorName);
        $dateSlug = str_replace('-', '', $date);
        $timeSlug = str_replace(':', '', $time);
        $uniqueId = $appointmentId ?? Str::random(6);

        $roomName = "abcsheba-{$doctorSlug}-{$dateSlug}-{$timeSlug}-{$uniqueId}";

        return $this->baseUrl . '/' . $roomName;
    }

    /**
     * Generate embed config for Jitsi iframe (optional, for in-app video)
     */
    public function getEmbedConfig(string $roomName, string $displayName): array
    {
        return [
            'roomName' => $roomName,
            'baseUrl' => $this->baseUrl,
            'displayName' => $displayName,
            'configOverwrite' => [
                'startWithAudioMuted' => true,
                'startWithVideoMuted' => false,
                'disableDeepLinking' => true,
                'prejoinPageEnabled' => false,
            ],
            'interfaceConfigOverwrite' => [
                'SHOW_JITSI_WATERMARK' => false,
                'SHOW_WATERMARK_FOR_GUESTS' => false,
                'DEFAULT_BACKGROUND' => '#1a1a2e',
                'TOOLBAR_BUTTONS' => [
                    'microphone', 'camera', 'desktop', 'chat',
                    'recording', 'fullscreen', 'hangup',
                ],
            ],
        ];
    }

    /**
     * Extract room name from a Jitsi URL
     */
    public function getRoomName(string $meetingLink): string
    {
        return basename(parse_url($meetingLink, PHP_URL_PATH));
    }

    /**
     * Always ready — no credentials needed!
     */
    public function isReady(): bool
    {
        return true;
    }
}
