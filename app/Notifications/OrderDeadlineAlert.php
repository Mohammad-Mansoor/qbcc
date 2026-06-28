<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderDeadlineAlert extends Notification
{
    use Queueable;

    public $order;
    public $daysRemaining;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order, $daysRemaining)
    {
        $this->order = $order;
        $this->daysRemaining = $daysRemaining;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->co_id,
            'order_name' => $this->order->order_name,
            'customer_id' => $this->order->main_customer_id,
            'customer_name' => optional($this->order->customer)->name,
            'days_remaining' => $this->daysRemaining,
            'end_date' => $this->order->end_date,
            'message' => 'تنها ' . $this->daysRemaining . ' روز تا مهلت تحویل فرمایش ' . $this->order->order_name . ' باقی مانده است.'
        ];
    }
}
