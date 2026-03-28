<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(private XenditService $xendit)
    {
    }

    /**
     * Create an order + payment record in one transaction.
     * Returns the Order with its payment relationship loaded.
     */
    public function createOrder(
        Customer $customer,
        array    $cart,
        string   $type,           // 'dine_in' | 'takeaway'
        string   $paymentMethod,  // 'cash' | 'cashless'
        string   $notes = '',
        ?int     $tableId = null,
    ): Order
    {
        return DB::transaction(function () use (
            $customer, $cart, $type, $paymentMethod, $notes, $tableId
        ) {
            $amount = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);

            // 1. Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_id' => $customer->id,
                'table_id' => $tableId,
                'type' => $type,
                'amount' => $amount,
                'notes' => $notes,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $paymentMethod,
            ]);

            // 2. Create order items
            foreach ($cart as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            // 3. Create payment record
            $payment = $order->payment()->create([
                'payment_method' => $paymentMethod,
                'amount' => $amount,
                'status' => 'pending',
                'cash_received' => 0,
            ]);

            // 4. If cashless → create Xendit invoice
            if ($paymentMethod === 'cashless') {
                $externalId = $this->xendit->buildExternalId($order->order_number);

                $invoice = $this->xendit->createInvoice([
                    'external_id' => $externalId,
                    'amount' => (int)$amount,
                    'description' => "Order {$order->order_number} - {$customer->name}",
                    'payer_email' => $customer->email ?? 'customer@example.com',
                    'customer' => [
                        'given_names' => $customer->name,
                        'email' => $customer->email ?? 'user@mail.com',
                        'mobile_number' => $customer->phone_number ?? '+628',
                    ],
                    'success_redirect_url' => route('customer.payment.success', encrypt($order->id)),
                    'failure_redirect_url' => route('customer.payment.failed', encrypt($order->id)),
                    'currency' => 'IDR',
                ]);

                $payment->update([
                    'xendit_invoice_id' => $invoice['id'],
                    'xendit_external_id' => $externalId,
                    'xendit_payment_url' => $invoice['invoice_url'],
                ]);
            }

            return $order->load('payment');
        });
    }

    /**
     * Mark a cash order as paid immediately.
     */
    public function payCash(Order $order, float $cashReceived): void
    {
        abort_if($order->payment_method !== 'cash', 422, 'Not a cash order');
        abort_if($cashReceived < $order->amount, 422, 'Cash received is less than order amount');

        DB::transaction(function () use ($order, $cashReceived) {
            $order->payment->update([
                'cash_received' => $cashReceived,
                'status' => 'success',
                'paid_at' => now(),
            ]);

            $order->update([
                'payment_status' => 'completed',
                'status' => 'confirmed',
            ]);
        });
    }
}
