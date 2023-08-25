<?php

namespace Vormkracht10\LaravelOK\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use NotificationChannels\Discord\DiscordMessage;
use NotificationChannels\Telegram\TelegramMessage;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class CheckFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Check $check, protected Result $result)
    {
    }

    public function via(): array
    {
        return array_keys(config('ok.notifications.via'));
    }

    public function shouldSend(Notifiable $notifiable, string $channel): bool
    {
        return config('ok.notifications.enabled', false);
    }

    public function getTitle(): string
    {
        $emoji = Arr::random([
            '🔥', '🧯', '‼️', '⁉️', '🔴', '📣', '😅', '🥵',
        ]);

        return $emoji.' '.$this->result->getMessage();
    }

    public function getMessage(): string
    {
        return $this->result->getMessage();
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getTitle())
            ->greeting($this->getMessage())
            ->line($this->getMessage());
    }

    public function toDiscord(): DiscordMessage
    {
        return DiscordMessage::create($this->getMessage(), [
            'title' => $this->getTitle(),
            'color' => 0xF44336,
        ]);
    }

    public function toSlack(): SlackMessage
    {
        return (new SlackMessage)
            ->content($this->getMessage());
    }

    public function toTelegram(): TelegramMessage
    {
        return TelegramMessage::create()
            ->content($this->getMessage());
    }
}
