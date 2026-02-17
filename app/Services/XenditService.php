<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class XenditService
{
    private string $baseUrl = 'https://api.xendit.co';

    private string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.xendit.secret_key');
    }

    /**
     * Create a Xendit invoice for cashless payment.
     */
    public function createInvoice(array $payload): array
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/v2/invoices", $payload);

        if ($response->failed()) {
            throw new \RuntimeException(
                'Xendit invoice creation failed: '.$response->body()
            );
        }

        return $response->json();
    }

    /**
     * Retrieve an invoice by its Xendit invoice ID.
     */
    public function getInvoice(string $invoiceId): array
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->get("{$this->baseUrl}/v2/invoices/{$invoiceId}");

        if ($response->failed()) {
            throw new \RuntimeException(
                'Xendit get invoice failed: '.$response->body()
            );
        }

        return $response->json();
    }

    /**
     * Verify the webhook callback token from Xendit.
     */
    public function verifyCallback(string $callbackToken): bool
    {
        return $callbackToken === config('services.xendit.webhook_token');
    }

    /**
     * Build a unique external ID for an order.
     */
    public function buildExternalId(string $orderNumber): string
    {
        return 'order-'.$orderNumber.'-'.Str::random(8);
    }
}
