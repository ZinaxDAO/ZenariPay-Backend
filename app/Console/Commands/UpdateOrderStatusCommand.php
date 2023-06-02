<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TradeHistory;
use App\Models\Trade;
use Carbon\Carbon;

class UpdateOrderStatusCommand extends Command
{
    protected $signature = 'order:update-status';

    protected $description = 'Update order status to failed after timeout';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $orders = TradeHistory::where('trade_status', 'pending')->where('is_paid', false)->get();
        mail('towojuads@gmail.com', 'cron successful on P2P', "Trade status updated successfully");

        foreach ($orders as $order) {
            $createdAt = Carbon::parse($order->created_at);
            $now = Carbon::now();
            
            $trade = Trade::whereId($order->trade_id)->first();
            if($now->diffInMinutes($createdAt) >= $trade->time_limit){
                $order->trade_status = 'failed';
                $trade->cancellation_rate = ((int)$trade->cancellation_rate + 1);
                $trade->save();
                $order->save();
                
            }
        }

        $this->info('Order status updated successfully.');
    }
}
