<?php

namespace App\Console\Commands;

use App\Notifications\AbandonedCartNotification;
use Binafy\LaravelCart\Models\Cart;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckAbandonedCarts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carts:check-abandoned {--hours=24 : Number of hours to consider a cart as abandoned}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for abandoned carts and send notifications to admins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');
        $threshold = now()->subHours($hours);

        $this->info("Checking for abandoned carts (not updated in the last {$hours} hours)...");

        // Get abandoned carts (carts with items that haven't been updated recently)
        $abandonedCarts = Cart::query()
            ->whereHas('items')
            ->where(function ($query) use ($threshold) {
                $query->where('updated_at', '<', $threshold)
                    ->orWhereNull('updated_at');
            })
            ->withCount('items')
            ->get();

        $count = $abandonedCarts->count();

        if ($count === 0) {
            $this->info('No abandoned carts found.');
            return 0;
        }

        $this->info("Found {$count} abandoned cart(s).");

        // Get details for notification
        $details = $abandonedCarts->map(function ($cart) {
            return [
                'cart_id' => $cart->id,
                'user_id' => $cart->user_id,
                'items_count' => $cart->items_count,
                'last_updated' => $cart->updated_at?->toDateTimeString(),
            ];
        })->toArray();

        // Send notification to all admins
        try {
            notifyAdmins(new AbandonedCartNotification($count, $details));
            $this->info("Notification sent to admins about {$count} abandoned cart(s).");
        } catch (\Exception $e) {
            $this->error("Failed to send notification: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
