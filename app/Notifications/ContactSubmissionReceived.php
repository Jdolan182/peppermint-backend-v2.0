<?php

namespace App\Notifications;

use App\Models\ContactSubmission;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactSubmissionReceived extends Notification
{
    public function __construct(public ContactSubmission $submission) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $sub     = $this->submission;
        $appName = config('app.name');
        $frontend = config('peppermint.frontend_url');

        $message = (new MailMessage)
            ->subject("New contact submission from {$sub->name}")
            ->greeting("Hi {$notifiable->name},")
            ->line("You received a new message via the contact form.")
            ->line("**From:** {$sub->name} ({$sub->email})")
            ->line("**Message:**")
            ->line($sub->message);

        if ($sub->page_slug) {
            $message->line("**Page:** /{$sub->page_slug}");
        }

        $message
            ->action('View submissions', $frontend . '/admin/contact')
            ->salutation("Thanks,\n{$appName}");

        return $message;
    }
}
