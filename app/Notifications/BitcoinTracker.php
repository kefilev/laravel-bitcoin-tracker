<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BitcoinTracker extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public array $data)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $hours = $this->data['period'] == 1 ? ' hour.' : ' hours.';

        return (new MailMessage)
            ->line('The Bitcoin price has changed with more than ' 
                . $this->data['userPercent'] . '% in the last ' . $this->data['period'] . $hours)

            ->line('Old price in USD: ' . $this->data['oldPriceUSD'])
            ->line('Current price in USD: ' . $this->data['currentPriceUSD'])
            ->line('% change in USD: ' . $this->data['percentUSD'])

            ->line('----------------')

            ->line('Old price in EUR: ' . $this->data['oldPriceEUR'])
            ->line('Current price in EUR: ' . $this->data['currentPriceEUR'])
            ->line('% change in EUR: ' . $this->data['percentEUR'])

            ->action('Unsubscribe', url(route('unsubscribe') . '?email=' . $this->data['email']))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
