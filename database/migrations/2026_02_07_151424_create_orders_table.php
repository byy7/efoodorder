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
        Schema::createWithManageBy('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('table_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->enum('type', ['dine_in', 'takeaway']);
            $table->decimal('amount');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled']);
            $table->enum('payment_status', ['pending', 'completed', 'cancelled']);
            $table->enum('payment_method', ['cash', 'cashless']);
            $table->timestamps();
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
