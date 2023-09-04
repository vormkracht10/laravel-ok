<?php

namespace Vormkracht10\LaravelOK\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class CheckFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Check $check, protected Result $result)
    {
    }

    public function getDriver(string $alias): string
    {
        return match ($alias) {
            'discord' => DiscordChannel::class,
            'telegram' => TelegramChannel::class,
            default => $alias,
        };
    }

    public function via(): array
    {
        $channels = collect(config('ok.notifications.via'))->keys();

        $drivers = $channels->map(fn (string $channel) => $this->getDriver($channel));

        return $drivers->toArray();
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
        $mail = (new MailMessage)
            ->subject($this->getTitle());

        if (($view = $this->check->view ?? null) !== null) {
            $mail->markdown($view, $this->check->data);
        } else {
            $mail->greeting($this->getMessage());
        }

        return $mail;
    }

    public function toDiscord(): DiscordMessage
    {
        return DiscordMessage::create(embed: [
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
