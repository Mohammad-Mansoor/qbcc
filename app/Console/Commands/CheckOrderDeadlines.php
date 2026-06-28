<?php

namespace App\Console\Commands;

use App\CustomerOrder;
use App\User;
use App\Notifications\OrderDeadlineAlert;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckOrderDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:check-deadlines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check customer orders deadlines and send milestone alerts (30, 15, 7 days)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orders = CustomerOrder::where('status', '!=', 'completed')
            ->whereNotNull('end_date')
            ->get();

        $receivers = User::permission('receive_customer_order_alerts')->get();
        if ($receivers->isEmpty()) {
            return;
        }

        foreach ($orders as $order) {
            $daysRemaining = Carbon::now()->startOfDay()->diffInDays(Carbon::parse($order->end_date)->startOfDay(), false);

            if ($daysRemaining <= 0) {
                continue;
            }

            $milestone = null;

            if ($daysRemaining <= 7) {
                $milestone = 7;
            } elseif ($daysRemaining <= 15) {
                $milestone = 15;
            } elseif ($daysRemaining <= 30) {
                $milestone = 30;
            }

            if ($milestone !== null && ($order->last_alert_milestone === null || $order->last_alert_milestone > $milestone)) {
                // Send notification
                Notification::send($receivers, new OrderDeadlineAlert($order, $daysRemaining));

                // Update the order flag
                $order->last_alert_milestone = $milestone;
                $order->save();

                $this->info("Alert sent for order {$order->order_name} (Milestone: {$milestone} days)");
            }
        }
        
        $this->info('Order deadlines checked successfully.');
    }
}
