<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\CustomerOrder;

class NewCustomerOrderAlert extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(CustomerOrder $order)
    {
        $this->order = $order;
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
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $customerName = optional($this->order->customer)->name ?? 'مشتری نامشخص';
        
        return [
            'order_id' => $this->order->co_id,
            'order_name' => $this->order->order_name,
            'customer_name' => $customerName,
            'type' => 'new_order',
            'message' => "فرمایش جدید {$this->order->order_name} برای {$customerName} ثبت شد."
        ];
    }
}
