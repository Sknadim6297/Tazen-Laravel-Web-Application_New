<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;

class SaveSentEmailToImap
{
    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        try {
            // Only save to IMAP if configuration is set
            if (!config('mail.imap.enabled', false)) {
                return;
            }

            $host = config('mail.imap.host');
            $username = config('mail.imap.username');
            $password = config('mail.imap.password');
            $port = config('mail.imap.port', 993);
            $folder = config('mail.imap.sent_folder', 'Sent');

            // Check if IMAP extension is available
            if (!function_exists('imap_open')) {
                Log::warning('IMAP extension not available. Sent email not saved to Sent folder.');
                return;
            }

            // Connect to IMAP
            $mailbox = "{{$host}:{$port}/imap/ssl}{$folder}";
            $imap = @imap_open($mailbox, $username, $password);

            if (!$imap) {
                Log::error('Failed to connect to IMAP: ' . imap_last_error());
                return;
            }

            // Get the raw email message
            $message = $event->message;
            $rawMessage = $message->toString();

            // Append the message to the Sent folder
            $result = imap_append($imap, $mailbox, $rawMessage);

            if ($result) {
                Log::info('Email successfully saved to Sent folder');
            } else {
                Log::error('Failed to save email to Sent folder: ' . imap_last_error());
            }

            imap_close($imap);

        } catch (\Exception $e) {
            Log::error('Error saving sent email to IMAP: ' . $e->getMessage());
        }
    }
}
