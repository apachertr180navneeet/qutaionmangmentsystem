<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Services\JaquarProductImageService;
use Exception;

class SyncProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:sync-images 
                            {--chunk=100 : Number of records to process per chunk}
                            {--table= : Database table to read products from}
                            {--force : Force download even if file already exists on disk}
                            {--missing-only : Process only products where image column is empty}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically download product images from Jaquar website based on product SKU and update database.';

    protected JaquarProductImageService $imageService;

    /**
     * Create a new command instance.
     */
    public function __construct(JaquarProductImageService $imageService)
    {
        parent::__construct();
        $this->imageService = $imageService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tableName = $this->determineTableName();
        $chunkSize = (int) $this->option('chunk');
        if ($chunkSize <= 0) {
            $chunkSize = 100;
        }

        $force = (bool) $this->option('force');

        $this->info("Starting product image sync on table [{$tableName}] (chunk size: {$chunkSize}, force mode: " . ($force ? 'ON' : 'OFF') . ")...");

        // Determine query for products (processes all records in the table)
        $baseQuery = DB::table($tableName);
        if ($this->option('missing-only')) {
            $baseQuery->where(function ($query) {
                $query->whereNull('image')
                    ->orWhere('image', '');
            });
        }

        $totalRecords = $baseQuery->count();

        if ($totalRecords === 0) {
            $this->info("No products found to process in table [{$tableName}].");
            $this->renderSummary(0, 0, 0, 0);
            return Command::SUCCESS;
        }

        $this->info("Found {$totalRecords} product(s) to process.");

        $processedCount = 0;
        $downloadedCount = 0;
        $skippedCount = 0;
        $failedCount = 0;

        $progressBar = $this->output->createProgressBar($totalRecords);
        $progressBar->start();

        // Process products in chunks
        $chunkQuery = DB::table($tableName);
        if ($this->option('missing-only')) {
            $chunkQuery->where(function ($query) {
                $query->whereNull('image')
                    ->orWhere('image', '');
            });
        }

        $chunkQuery->orderBy('id')
            ->chunkById($chunkSize, function ($products) use (
                $tableName,
                $force,
                &$processedCount,
                &$downloadedCount,
                &$skippedCount,
                &$failedCount,
                $progressBar
            ) {
                foreach ($products as $product) {
                    $processedCount++;
                    $sku = trim($product->sku ?? '');

                    if (empty($sku)) {
                        $failedCount++;
                        $this->logFailure("N/A (Product ID: {$product->id})", "Missing or empty SKU");
                        $progressBar->advance();
                        continue;
                    }

                    $safeSku = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $sku);
                    $expectedPath = "uploads/items/{$safeSku}.jpg";

                    // Skip if not in force mode, image is set and file exists on disk
                    $fileExists = file_exists(public_path($expectedPath)) || Storage::disk('public')->exists($expectedPath);
                    if (!$force && !empty($product->image) && $fileExists) {
                        $skippedCount++;
                        $progressBar->advance();
                        continue;
                    }

                    try {
                        $relativeImagePath = $this->imageService->fetchAndSaveImageForSku($sku);

                        if ($relativeImagePath) {
                            DB::table($tableName)
                                ->where('id', $product->id)
                                ->update([
                                    'image' => $relativeImagePath,
                                    'updated_at' => now(),
                                ]);

                            $downloadedCount++;
                        } else {
                            $failedCount++;
                            $this->logFailure($sku, "Image service returned null");
                        }
                    } catch (Exception $e) {
                        $failedCount++;
                        $this->logFailure($sku, $e->getMessage());
                    }

                    $progressBar->advance();
                }
            });

        $progressBar->finish();
        $this->newLine(2);

        $this->renderSummary($processedCount, $downloadedCount, $skippedCount, $failedCount);

        return Command::SUCCESS;
    }

    /**
     * Determine which table to use (items vs item_master).
     */
    protected function determineTableName(): string
    {
        $customTable = $this->option('table');
        if (!empty($customTable)) {
            return $customTable;
        }

        // Prefer items table if it exists and has records
        if (Schema::hasTable('items') && DB::table('items')->count() > 0) {
            return 'items';
        }

        if (Schema::hasTable('item_master') && DB::table('item_master')->count() > 0) {
            return 'item_master';
        }

        if (Schema::hasTable('items')) {
            return 'items';
        }

        return 'item_master';
    }

    /**
     * Log failed SKUs to storage/logs/product_image_sync.log.
     */
    protected function logFailure(string $sku, string $reason): void
    {
        $message = sprintf("[%s] Failed SKU: %s | Reason: %s", now()->toDateTimeString(), $sku, $reason);

        try {
            Log::channel('product_image_sync')->error($message);
        } catch (Exception $e) {
            // Fallback writing to storage/logs/product_image_sync.log directly if channel fails
            $logPath = storage_path('logs/product_image_sync.log');
            @file_put_contents($logPath, $message . PHP_EOL, FILE_APPEND);
        }
    }

    /**
     * Render total processed summary stats table in terminal.
     */
    protected function renderSummary(int $processed, int $downloaded, int $skipped, int $failed): void
    {
        $this->info("----------------------------------------");
        $this->info(" Product Image Sync Execution Summary ");
        $this->info("----------------------------------------");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Processed', $processed],
                ['Downloaded & Updated', $downloaded],
                ['Skipped', $skipped],
                ['Failed', $failed],
            ]
        );
    }
}
