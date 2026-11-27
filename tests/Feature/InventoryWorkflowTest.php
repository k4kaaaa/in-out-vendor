<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Transaction;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class InventoryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password')
        ]);
    }

    public function test_admin_can_login()
    {
        Http::fake([
            'www.google.com/recaptcha/*' => Http::response([
                'success' => true,
                'challenge_ts' => now()->toIso8601String(),
                'hostname' => 'in-out-vendor.test',
                'score' => 0.9,
            ], 200),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
            'g-recaptcha-response' => 'recaptcha-token',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_barang_masuk_increases_stock_and_records_movement()
    {
        Storage::fake('public');
        $product = Product::factory()->create(['current_stock' => 10]);
        $photo = UploadedFile::fake()->create('item.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->admin)->post('/transactions', [
            'date' => now()->toDateString(),
            'type' => 'in',
            'product_id' => $product->id,
            'qty' => 5,
            'customer_name' => 'John Doe',
            'image' => $photo
        ]);

        $response->assertRedirect('/transactions');
        
        $product->refresh();
        $this->assertEquals(15, $product->current_stock);
        
        $this->assertDatabaseHas('transactions', [
            'type' => 'in',
            'qty' => 5,
            'customer_name' => 'John Doe'
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'in',
            'qty_before' => 10,
            'qty_after' => 15
        ]);
    }

    public function test_barang_keluar_decreases_stock_and_records_movement()
    {
        $product = Product::factory()->create(['current_stock' => 10]);

        $response = $this->actingAs($this->admin)->post('/transactions', [
            'date' => now()->toDateString(),
            'type' => 'out',
            'product_id' => $product->id,
            'qty' => 3,
            'receiver_name' => 'Project A'
        ]);

        $response->assertRedirect('/transactions');
        
        $product->refresh();
        $this->assertEquals(7, $product->current_stock);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'out',
            'qty_before' => 10,
            'qty_after' => 7
        ]);
    }

    public function test_barang_keluar_fails_if_stock_insufficient()
    {
        $product = Product::factory()->create(['current_stock' => 2]);

        $response = $this->actingAs($this->admin)->from('/transactions/create')->post('/transactions', [
            'date' => now()->toDateString(),
            'type' => 'out',
            'product_id' => $product->id,
            'qty' => 5
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['qty']);
        
        $product->refresh();
        $this->assertEquals(2, $product->current_stock);
        
        $this->assertDatabaseMissing('transactions', [
            'type' => 'out',
            'qty' => 5
        ]);

        $this->assertDatabaseMissing('stock_movements', [
            'product_id' => $product->id,
            'type' => 'out'
        ]);
    }
}
