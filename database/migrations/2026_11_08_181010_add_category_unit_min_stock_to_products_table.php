<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('category')->nullable()->after('name');
            $table->string('unit')->default('pcs')->after('current_stock'); // box, pcs, roll, rim, kg
            $table->decimal('min_stock', 15, 2)->default(5)->after('current_stock');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['category', 'unit', 'min_stock']);
        });
    }
};