<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExpireQuotations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quotations:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically expire quotations whose valid_until date has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->format('Y-m-d');
        
        // Find quotations that are 'draft' or 'sent' and have passed their valid_until date
        $expiredCount = \App\Models\Quotation::whereIn('status', ['draft', 'sent'])
            ->whereNotNull('valid_until')
            ->where('valid_until', '<', $today)
            ->update(['status' => 'expired']);

        $this->info("Successfully expired {$expiredCount} quotations.");
    }
}
