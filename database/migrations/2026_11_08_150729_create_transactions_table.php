<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $col) {
            $col->id();
            $col->dateTime('date');
            $col->enum('type', ['in', 'out']);
            $col->foreignId('product_id')->constrained()->onDelete('cascade');
            $col->foreignId('vendor_id')->nullable()->constrained()->onDelete('set null');
            $col->decimal('qty', 15, 2);
            $col->string('customer_name')->nullable();
            $col->string('receiver_name')->nullable();
            $col->string('photo_product')->nullable();
            $col->string('photo_delivery')->nullable();
            $col->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
