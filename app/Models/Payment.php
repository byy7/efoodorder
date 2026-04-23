<?php

namespace App\Models;

use App\Services\OrderService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function markAsPaid(array $callbackData = [], ?string $channel = null): void
    {
        $this->update([
            'status' => 'success',
            'xendit_payment_channel' => $channel ?? $this->xendit_payment_channel,
            'xendit_callback_data' => $callbackData,
            'paid_at' => now(),
        ]);

        $this->order->update([
            'payment_status' => 'completed',
            'status' => 'confirmed',
        ]);

        app(OrderService::class)->handleXenditSuccess($this->order);
    }
}
