<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class XenditWebhookController extends Controller
{
    public function __construct(private XenditService $xendit) {}

    public function __invoke(Request $request): Response
    {
        // 1. Verify the callback token
        $callbackToken = $request->header('x-callback-token');

        if (! $this->xendit->verifyCallback($callbackToken)) {
            return response('Unauthorized', 401);
        }

        $data = $request->all();

        // 2. Only handle PAID invoices
        if (($data['status'] ?? '') !== 'PAID') {
            return response('ERROR, WRONG STATUS', 500);
        }

        // 3. Find the payment by xendit_external_id
        $payment = Payment::where('xendit_external_id', $data['external_id'])->first();

        if (! $payment) {
            return response('PAYMENT NOT FOUND', 500);
        }

        // 4. Mark as paid
        $payment->markAsPaid(
            callbackData: $data,
            channel: $data['payment_channel'] ?? null,
        );

        return response('PAYMENT SUCCESS', 200);
    }
}
