<?php

namespace Backstage\Laravel\OK\Notifications;

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
use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

class CheckFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Check $check, protected Result $result)
    {
        //
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

        return $emoji.' '.$this->getMessage().' on **'.config('app.name').'** ('.app()->environment().')';
    }

    public function getMessage(): string
    {
        if ($this->result->getMessage()) {
            return trim($this->result->getMessage(), '.');
        }

        return $this->check->getName().' check failed';
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
        return (new DiscordMessage)
            ->body($this->getTitle())
            ->embed([
                'title' => $this->getMessage(),
                'color' => 0xF44336,
                'fields' => [
                    [
                        'name' => 'App',
                        'value' => config('app.name'),
                        'inline' => false,
                    ],
                    [
                        'name' => 'Environment',
                        'value' => app()->environment(),
                        'inline' => false,
                    ],
                    [
                        'name' => 'Host',
                        'value' => gethostname(),
                        'inline' => false,
                    ],
                ],
                'url' => config('app.url'),
            ]);
    }

    public function toSlack(): SlackMessage
    {
        return (new SlackMessage)
            ->content($this->getTitle())
            ->error();
    }

    public function toTelegram(): TelegramMessage
    {
        return TelegramMessage::create()
            ->content($this->getTitle());
    }
}
