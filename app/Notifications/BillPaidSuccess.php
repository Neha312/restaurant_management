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
        $status = null;
        if ($this->trails->status == "P") {
            $status  = "Paid";
        }
        return (new MailMessage)
            ->subject('Bill Status')
            ->greeting('Hello! ' . $this->bill->vendor->user->first_name)
            ->line('Order Number:' . $this->order->order_number)
            ->line('Bill Number:' . $this->bill->bill_number)
            ->line('Owner Name:' . $this->bill->restaurant->users->first()->first_name)
            ->line('Restaurant Name:' . $this->order->orderItem->first()->restaurant->name)
            ->line('Total Amount:' . $this->order->bill->total_amount)
            ->line('Bill status:' . $status)
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
