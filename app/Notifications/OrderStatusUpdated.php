<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    use Queueable;
    public $order, $bill;


    /**
     * Create a new notification instance.
     */
    public function __construct($order, $bill)
    {
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
        if ($this->order->status == "D") {
            $strings = "Delivered";
        }
        return (new MailMessage)
            ->subject('Order Status')
            ->greeting('Hello! ' . $this->bill->restaurant->users->first()->first_name)
            ->line('Order Number:' . $this->order->order_number)
            ->line('Bill Number:' . $this->bill->bill_number)
            ->line('Vendor Name:' . $this->bill->vendor->user->first_name)
            ->line('Restaurant Name:' . $this->order->orderItem->first()->restaurant->name)
            ->line('Total Amount:' . $this->order->bill->total_amount)
            ->line('Tax:' . $this->order->bill->tax)
            ->line('Quantity:' . $this->order->quantity)
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
