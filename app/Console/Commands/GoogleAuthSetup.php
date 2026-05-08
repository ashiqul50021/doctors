<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleMeetService;

class GoogleAuthSetup extends Command
{
    protected $signature = 'google:auth';
    protected $description = 'Set up Google Calendar OAuth2 authorization for Meet link generation';

    public function handle()
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║     Google Calendar / Meet Setup Wizard      ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->info('');

        $meetService = new GoogleMeetService();

        // Step 1: Check credentials.json
        if (!$meetService->hasCredentials()) {
            $this->error('❌ credentials.json not found!');
            $this->info('');
            $this->info('Please follow these steps:');
            $this->info('');
            $this->info('1. Go to https://console.cloud.google.com/');
            $this->info('2. Create a new project (or select existing)');
            $this->info('3. Enable "Google Calendar API"');
            $this->info('4. Go to "Credentials" → Create "OAuth 2.0 Client ID"');
            $this->info('   - Application type: "Web application"');
            $this->info('   - Authorized redirect URI: http://localhost:8000/google/callback');
            $this->info('5. Download the JSON file');
            $this->info('6. Save it as: storage/app/google-calendar/credentials.json');
            $this->info('');
            $this->info('Then run this command again: php artisan google:auth');
            return 1;
        }

        $this->info('✅ credentials.json found.');

        // Step 2: Check if already authorized
        if ($meetService->hasValidToken()) {
            $this->info('✅ Already authorized! Google Meet is ready to use.');

            if ($this->confirm('Do you want to re-authorize?', false)) {
                // Continue to auth flow
            } else {
                return 0;
            }
        }

        // Step 3: Generate auth URL
        $this->info('');
        $this->info('📋 Open this URL in your browser:');
        $this->info('');
        $this->line($meetService->getAuthUrl());
        $this->info('');
        $this->info('After authorizing, you will be redirected. Copy the "code" parameter from the URL.');
        $this->info('');

        $code = $this->ask('Paste the authorization code here');

        if (empty($code)) {
            $this->error('No code provided. Aborting.');
            return 1;
        }

        // Step 4: Exchange code for token
        $this->info('');
        $this->info('🔄 Exchanging code for access token...');

        if ($meetService->authenticate($code)) {
            $this->info('');
            $this->info('✅ Authorization successful! Google Meet is now ready.');
            $this->info('   Token saved to: storage/app/google-calendar/token.json');
            $this->info('');

            // Test by checking readiness
            if ($meetService->isReady()) {
                $this->info('🎉 Everything is working! Video consultations will now generate Google Meet links.');
            }
        } else {
            $this->error('❌ Authorization failed. Please check the code and try again.');
            return 1;
        }

        return 0;
    }
}
