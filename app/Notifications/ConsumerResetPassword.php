<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ConsumerResetPassword extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = config('peppermint.frontend_url') . '/reset-password'
            . '?token=' . $this->token
            . '&email=' . urlencode($notifiable->getEmailForPasswordReset());

        return $this->buildMailMessage($url);
    }
}
