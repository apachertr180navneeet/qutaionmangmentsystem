<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Services\JaquarProductImageService;
use Tests\TestCase;

class SyncProductImagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        // Create item_master table in test memory if it doesn't exist
        if (!Schema::hasTable('item_master')) {
            Schema::create('item_master', function ($table) {
                $table->id();
                $table->string('sku')->unique();
                $table->string('image')->nullable();
                $table->timestamps();
            });
        }
    }

    public function test_service_downloads_and_saves_image_successfully(): void
    {
        $mockHtml = '<html><body>
            <div class="product"><img class="product-img" src="https://www.jaquar.com/images/products/AQT-101.jpg" /></div>
        </body></html>';

        // 1x1 GIF binary
        $mockImageBinary = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        Http::fake([
            'https://www.jaquar.com/en/search*' => Http::response($mockHtml, 200),
            'https://www.jaquar.com/images/products/AQT-101.jpg' => Http::response($mockImageBinary, 200, ['Content-Type' => 'image/gif']),
        ]);

        $service = new JaquarProductImageService();
        $savedPath = $service->fetchAndSaveImageForSku('AQT-101');

        $this->assertEquals('uploads/items/AQT-101.jpg', $savedPath);
        Storage::disk('public')->assertExists('uploads/items/AQT-101.jpg');
    }

    public function test_artisan_command_syncs_images_and_updates_database(): void
    {
        DB::table('item_master')->truncate();

        DB::table('item_master')->insert([
            ['sku' => 'SKU-001', 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['sku' => 'SKU-002', 'image' => '', 'created_at' => now(), 'updated_at' => now()],
            ['sku' => 'SKU-003', 'image' => 'uploads/items/SKU-003.jpg', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Mock image stored on public disk for existing record
        Storage::disk('public')->put('uploads/items/SKU-003.jpg', 'existing-image');

        $mockHtml = '<html><head><meta property="og:image" content="https://www.jaquar.com/media/sku.jpg" /></head></html>';
        $mockImageBinary = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        Http::fake([
            'https://www.jaquar.com/en/search*' => Http::response($mockHtml, 200),
            'https://www.jaquar.com/media/sku.jpg' => Http::response($mockImageBinary, 200),
        ]);

        $this->artisan('products:sync-images', ['--table' => 'item_master'])
            ->assertExitCode(0);

        $sku1 = DB::table('item_master')->where('sku', 'SKU-001')->first();
        $sku2 = DB::table('item_master')->where('sku', 'SKU-002')->first();

        $this->assertEquals('uploads/items/SKU-001.jpg', $sku1->image);
        $this->assertEquals('uploads/items/SKU-002.jpg', $sku2->image);

        Storage::disk('public')->assertExists('uploads/items/SKU-001.jpg');
        Storage::disk('public')->assertExists('uploads/items/SKU-002.jpg');
    }

    public function test_artisan_command_handles_failures_gracefully(): void
    {
        DB::table('item_master')->truncate();

        DB::table('item_master')->insert([
            ['sku' => 'SKU-FAIL', 'image' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Http::fake([
            'https://www.jaquar.com/en/search*' => Http::response('Server Error', 500),
        ]);

        $this->artisan('products:sync-images', ['--table' => 'item_master'])
            ->assertExitCode(0);

        $skuFail = DB::table('item_master')->where('sku', 'SKU-FAIL')->first();
        $this->assertNull($skuFail->image);
    }
}
