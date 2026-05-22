<?php

namespace App\Jobs;


use App\Models\Branch;
use App\Models\Medicine;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CheckLowStockJob
 *
 * Scans branch_medicine for rows where quantity is between 1 and threshold.
 * Sends LowStockNotification to relevant branch managers and pharmacy owners.
 *
 * ──────────────────────────────────────────────────────────────
 * Schedule — add to routes/console.php (Laravel 11+):
 * ──────────────────────────────────────────────────────────────
 *   Schedule::job(new CheckLowStockJob)->dailyAt('08:00');
 *
 * ──────────────────────────────────────────────────────────────
 * Or app/Console/Kernel.php (Laravel 10):
 * ──────────────────────────────────────────────────────────────
 *   $schedule->job(new CheckLowStockJob)->dailyAt('08:00');
 *
 * ──────────────────────────────────────────────────────────────
 * Manual dispatch:
 * ──────────────────────────────────────────────────────────────
 *   CheckLowStockJob::dispatch();
 *   CheckLowStockJob::dispatch(threshold: 5);
 */

class CheckLowStockJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $threshold = 10
    ) {}

    public function handle(): void
    {
        // Query branch_medicine directly — avoids loading full Eloquent models
        $lowStockRows = DB::table('branch_medicine')
            ->where('quantity', '>', 0)
            ->where('quantity', '<=', $this->threshold)
            ->select('branch_id', 'medicine_id', 'quantity')
            ->get();

        if ($lowStockRows->isEmpty()) {
            return;
        }

        // Group by branch to load each branch once — not per row
        $grouped = $lowStockRows->groupBy('branch_id');

        foreach ($grouped as $branchId => $rows) {
            // Load branch with pharmacy and city in one query
            $branch = Branch::with(['pharmacy', 'city'])->find($branchId);

            if (!$branch) {
                continue;
            }

            // Fetch managers and owners once per branch
            $managers = User::ofBranch($branchId)->branchManagers()->get();
            $owners   = User::ofPharmacy($branch->pharmacy_id)->pharmacyOwners()->get();

            foreach ($rows as $row) {
                $medicine = Medicine::find($row->medicine_id);

                if (!$medicine) {
                    continue;
                }

                $notification = new LowStockNotification(
                    medicine: $medicine,
                    branch: $branch,
                    quantity: $row->quantity,
                    threshold: $this->threshold,
                );

                // Notify all branch managers for this branch
                $managers->each(fn(User $u) => $u->notify($notification));

                // Notify all pharmacy owners for this pharmacy
                $owners->each(fn(User $u) => $u->notify($notification));

                Log::info('LowStockNotification dispatched', [
                    'branch_id'   => $branchId,
                    'medicine_id' => $row->medicine_id,
                    'quantity'    => $row->quantity,
                ]);
            }
        }
    }
}
