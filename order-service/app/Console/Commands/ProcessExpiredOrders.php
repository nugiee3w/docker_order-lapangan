<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:process-expired 
                            {--dry-run : Show what would be processed without making changes}
                            {--delete-paid : Delete paid orders instead of completing them}
                            {--complete-paid : Complete paid orders (default behavior)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process expired orders: delete all expired orders OR complete paid orders and delete unpaid orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $deletePaid = $this->option('delete-paid');
        $completePaid = $this->option('complete-paid');
        $now = Carbon::now();
        
        // Default behavior: complete paid orders if no specific option is set
        if (!$deletePaid && !$completePaid) {
            $completePaid = true;
        }
        
        $this->info("Processing expired orders at: " . $now->format('Y-m-d H:i:s'));
        
        if ($isDryRun) {
            $this->warn("DRY RUN MODE - No changes will be made");
        }
        
        if ($deletePaid) {
            $this->warn("DELETE MODE: All expired orders will be deleted (including paid ones)");
        } else {
            $this->info("COMPLETE MODE: Paid orders will be completed, unpaid orders will be deleted");
        }
        
        // Find orders that have passed their end time
                // Get all orders that are not completed and potentially expired
        $orders = Order::whereIn('status', ['pending', 'confirmed'])->get();
        
        $expiredOrders = $orders->filter(function ($order) use ($now) {
            try {
                // Get raw attributes to avoid datetime casting issues
                $rawOrder = $order->getAttributes();
                
                // Parse tanggal_booking (format: YYYY-MM-DD)
                $dateOnly = Carbon::parse($rawOrder['tanggal_booking'])->format('Y-m-d');
                
                // Combine date with end time (jam_selesai format: HH:MM)
                $endDateTime = Carbon::parse($dateOnly . ' ' . $rawOrder['jam_selesai'] . ':00');
                
                $isExpired = $endDateTime->lt($now);
                
                if ($this->option('dry-run')) {
                    $this->info("  Order {$order->order_number}: {$dateOnly} {$rawOrder['jam_selesai']} - " . 
                              ($isExpired ? 'EXPIRED' : 'NOT EXPIRED') . 
                              " (Status: {$order->status}, Payment: {$order->payment_status})");
                }
                
                return $isExpired;
            } catch (\Exception $e) {
                $this->error("Error parsing date for order {$order->order_number}: " . $e->getMessage());
                return false;
            }
        });
        
        if ($expiredOrders->isEmpty()) {
            $this->info("No expired orders found.");
            return 0;
        }
        
        $this->info("Found {$expiredOrders->count()} expired orders to process:");
        
        $completedCount = 0;
        $deletedCount = 0;
        $skippedCount = 0;
        
        foreach ($expiredOrders as $order) {
            try {
                // Get raw attributes to avoid datetime casting issues
                $rawOrder = $order->getAttributes();
                $dateOnly = Carbon::parse($rawOrder['tanggal_booking'])->format('Y-m-d');
                $endDateTime = Carbon::parse($dateOnly . ' ' . $rawOrder['jam_selesai'] . ':00');
                $hoursExpired = $now->diffInHours($endDateTime);
            
                $this->line("Processing Order: {$order->order_number}");
                $this->line("  Customer: {$order->customer_name}");
                $this->line("  End Time: {$endDateTime->format('Y-m-d H:i')}");
                $this->line("  Hours Expired: {$hoursExpired}");
                $this->line("  Status: {$order->status} | Payment: {$order->payment_status}");
            
                if ($deletePaid) {
                    // DELETE MODE: Delete all expired orders regardless of payment status
                    $this->line("  Action: DELETE (expired order - delete mode enabled)");
                    
                    if (!$isDryRun) {
                        Log::info("Order {$order->order_number} auto-deleted (expired - delete mode)", [
                            'order_id' => $order->id,
                            'customer' => $order->customer_name,
                            'customer_email' => $order->customer_email,
                            'end_time' => $endDateTime->format('Y-m-d H:i'),
                            'hours_expired' => $hoursExpired,
                            'total_amount' => $order->total_harga,
                            'payment_status' => $order->payment_status,
                            'reason' => 'expired_order_delete_mode'
                        ]);
                        
                        $order->delete();
                    }
                    
                    $deletedCount++;
                    $this->error("  ✗ Order deleted (expired)");
                    
                } elseif ($order->payment_status === 'paid') {
                    // COMPLETE MODE: Mark paid orders as completed
                    $this->line("  Action: Mark as COMPLETED (paid order)");
                    
                    if (!$isDryRun) {
                        $order->update([
                            'status' => 'completed',
                            'notes' => ($order->notes ? $order->notes . ' | ' : '') . 'Auto-completed on ' . $now->format('Y-m-d H:i:s')
                        ]);
                        
                        Log::info("Order {$order->order_number} auto-completed", [
                            'order_id' => $order->id,
                            'customer' => $order->customer_name,
                            'end_time' => $endDateTime->format('Y-m-d H:i'),
                            'hours_expired' => $hoursExpired
                        ]);
                    }
                    
                    $completedCount++;
                    $this->info("  ✓ Order marked as completed");
                    
                } elseif ($order->payment_status === 'unpaid') {
                    // Delete unpaid orders (both modes)
                    $this->line("  Action: DELETE (unpaid order)");
                    
                    if (!$isDryRun) {
                        Log::info("Order {$order->order_number} auto-deleted (unpaid expired)", [
                            'order_id' => $order->id,
                            'customer' => $order->customer_name,
                            'customer_email' => $order->customer_email,
                            'end_time' => $endDateTime->format('Y-m-d H:i'),
                            'hours_expired' => $hoursExpired,
                            'total_amount' => $order->total_harga
                        ]);
                        
                        $order->delete();
                    }
                    
                    $deletedCount++;
                    $this->error("  ✗ Order deleted (unpaid)");
                    
                } else {
                    // Skip orders with other payment statuses (e.g., refunded)
                    $this->line("  Action: SKIP (payment status: {$order->payment_status})");
                    $skippedCount++;
                }
                
            } catch (\Exception $e) {
                $this->error("  Error processing order {$order->order_number}: " . $e->getMessage());
                Log::error("Error processing expired order {$order->order_number}", [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $skippedCount++;
            }
            
            $this->line("");
        }
        
        // Summary
        $this->line("=== SUMMARY ===");
        $this->info("Orders processed: {$expiredOrders->count()}");
        $this->info("Completed (paid): {$completedCount}");
        $this->error("Deleted (unpaid): {$deletedCount}");
        $this->warn("Skipped: {$skippedCount}");
        
        if ($isDryRun) {
            $this->warn("DRY RUN MODE - No actual changes were made");
            $this->line("Run without --dry-run to apply changes");
        } else {
            Log::info("Expired orders processed", [
                'total_processed' => $expiredOrders->count(),
                'completed' => $completedCount,
                'deleted' => $deletedCount,
                'skipped' => $skippedCount,
                'processed_at' => $now->format('Y-m-d H:i:s'),
                'mode' => $deletePaid ? 'delete_all' : 'complete_paid_delete_unpaid'
            ]);
        }
        
        return 0;
    }
}
