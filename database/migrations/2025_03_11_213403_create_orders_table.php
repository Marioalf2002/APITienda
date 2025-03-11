<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_state_id')->constrained()->onDelete('restrict');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('restrict');
            $table->decimal('total', 10, 2)->default(0);
            $table->integer('total_items')->default(0);
            $table->timestamps();

            // Índices
            $table->index('user_id');
            $table->index('order_state_id');
            $table->index('payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
