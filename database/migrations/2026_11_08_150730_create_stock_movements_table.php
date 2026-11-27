<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $col) {
            $col->id();
            $col->foreignId('product_id')->constrained()->onDelete('cascade');
            $col->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $col->enum('type', ['in', 'out']);
            $col->decimal('qty_before', 15, 2);
            $col->decimal('qty_after', 15, 2);
            $col->dateTime('date');
            $col->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
