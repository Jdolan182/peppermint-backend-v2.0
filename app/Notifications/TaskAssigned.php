<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssigned extends Notification
{
    use Queueable;

    public function __construct(public Task $task) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $task     = $this->task;
        $frontend = config('peppermint.frontend_url');
        $priority = ucfirst($task->priority ?? 'normal');
        $appName  = config('app.name');

        $message = (new MailMessage)
            ->subject("Task assigned to you: {$task->title}")
            ->greeting("Hi {$notifiable->name},")
            ->line("You've been assigned a new task:")
            ->line("**{$task->title}**");

        if ($task->description) {
            $message->line($task->description);
        }

        if ($task->due_date) {
            $message->line('Due: ' . $task->due_date->format('j M Y'));
        }

        $message->line("Priority: {$priority}");

        if ($task->consumer) {
            $message->line("Client: {$task->consumer->name}");
        }

        $message
            ->action('View tasks', $frontend)
            ->salutation("Thanks,\n{$appName}");

        return $message;
    }
}
