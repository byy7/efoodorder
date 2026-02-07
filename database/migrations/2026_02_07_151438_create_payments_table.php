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
        Schema::createWithManageBy('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->enum('payment_method', ['cash', 'cashless']);
            $table->decimal('amount');
            $table->enum('status', ['pending', 'success', 'failed', 'expired']);
            $table->decimal('cash_received')->default(0);
            $table->string('xendit_invoice_id')->nullable();
            $table->string('xendit_external_id')->nullable();
            $table->string('xendit_payment_channel')->nullable();
            $table->text('xendit_payment_url')->nullable();
            $table->json('xendit_callback_data')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
