<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BillPaidSuccess extends Notification
{
    use Queueable;
    public $trails, $order, $bill;
    /**
     * Create a new notification instance.
     */
    public function __construct($trails, $order, $bill)
    {
        $this->trails = $trails;
        $this->bill   = $bill;
        $this->order  = $order;
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
        $strings = null;
        if ($this->trails->status == "P") {
            $strings = "Paid";
        }
        return (new MailMessage)
            ->subject('Bill Status')
            ->greeting('Hello! ' . $this->bill->vendor->user->first_name)
            ->line('Order id:' . $this->order->id)
            ->line('Bill id:' . $this->bill->id)
            ->line('Owner Name:' . $this->bill->restaurant->users->first()->first_name)
            ->line('Restaurant Name:' . $this->order->restaurant->name)
            ->line('Total Amount:' . $this->order->bill->total_amount)
            ->line('Bill status:' . $strings)
            ->line('Best regards!');
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
