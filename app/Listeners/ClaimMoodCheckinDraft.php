<?php

namespace App\Listeners;

use App\Services\MoodCheckinDraft;
use Illuminate\Auth\Events\Login;

class ClaimMoodCheckinDraft
{
    public function handle(Login $event): void
    {
        if ($event->guard !== 'user') {
            return;
        }

        app(MoodCheckinDraft::class)->claimForCurrentUser((int) $event->user->getAuthIdentifier());
    }
}
