<?php

use App\Concerns\WithNotifications;
use App\Models\Order;
use App\Services\OrderService;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-customer')]
class extends Component {
    use WithNotifications;

    public Order $order;
    public float $cashReceived = 0;

    public function mount(string $orderId): void
    {
        $this->order = Order::with(['items.product', 'payment', 'customer'])
            ->findOrFail(decrypt($orderId));

        // Already paid — redirect away
        if ($this->order->isPaid()) {
            $this->redirect(route('customer.payment.success', [
                'order' => encrypt($this->order->id),
            ]));
        }
    }

    public function confirmCash(): void
    {
        $this->validate([
            'cashReceived' => ['required', 'numeric', 'min:' . $this->order->amount],
        ], [
            'cashReceived.min' => 'Uang yang diterima kurang dari total pesanan',
        ]);

        try {
            app(OrderService::class)->payCash($this->order, $this->cashReceived);

            $this->redirectRoute('customer.payment.success', encrypt($this->order->id));
        } catch (\Throwable $e) {
            $this->notifyError($e->getMessage());
        }
    }

    public function change(): float
    {
        return max(0, $this->cashReceived - $this->order->amount);
    }
};
?>

<div>
    <x-header :heading="__('Konfirmasi Pembayaran Tunai')"></x-header>

    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">

            {{-- Order Summary --}}
            <div class="card mb-3">
                <div class="card-header fw-semibold">
                    <i class="material-icons-outlined align-middle">receipt_long</i>
                    Ringkasan Pesanan
                    <span class="float-end badge bg-warning text-dark">{{ $order->order_number }}</span>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($order->items as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-start py-2">
                                <div>
                                    <div class="fw-semibold" style="font-size:0.9rem;">
                                        {{ $item->product?->name ?? '(produk dihapus)' }}
                                    </div>
                                    @if($item->notes)
                                        <div class="text-muted" style="font-size:0.78rem;">
                                            <i class="material-icons-outlined align-middle" style="font-size:12px;">notes</i>
                                            {{ $item->notes }}
                                        </div>
                                    @endif
                                    <small class="text-muted">x{{ $item->quantity }}</small>
                                </div>
                                <span class="fw-semibold">@currency($item->subtotal)</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer d-flex justify-content-between fw-bold">
                    <span>Total</span>
                    <span class="text-primary fs-5">@currency($order->amount)</span>
                </div>
            </div>

            @if($order->notes)
                <div class="alert alert-info py-2 mb-3" style="font-size:0.85rem;">
                    <i class="material-icons-outlined align-middle" style="font-size:16px;">notes</i>
                    {{ $order->notes }}
                </div>
            @endif

            {{-- Cash Input --}}
            <div class="card">
                <div class="card-body">
                    <label class="form-label fw-semibold">
                        <i class="material-icons-outlined align-middle">payments</i>
                        Uang Diterima (Rp)
                    </label>
                    <input
                        type="number"
                        wire:model.live.debounce.300ms="cashReceived"
                        class="form-control form-control-lg mb-2"
                        placeholder="0"
                        min="{{ $order->amount }}"
                        step="1000">

                    @if($cashReceived >= $order->amount && $cashReceived > 0)
                        <div class="alert alert-success py-2 mb-3">
                            <i class="material-icons-outlined align-middle">currency_exchange</i>
                            Kembalian: <strong>@currency($this->change())</strong>
                        </div>
                    @endif

                    {{-- Quick-amount chips --}}
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        @foreach([10000, 20000, 50000, 100000] as $amount)
                            <button
                                wire:click="$set('cashReceived', {{ $amount }})"
                                type="button"
                                class="btn btn-sm btn-outline-secondary">
                                @currency($amount)
                            </button>
                        @endforeach
                        <button
                            wire:click="$set('cashReceived', {{ ceil($order->amount / 1000) * 1000 }})"
                            type="button"
                            class="btn btn-sm btn-outline-primary">
                            Pas
                        </button>
                    </div>

                    <button
                        wire:click="confirmCash"
                        wire:loading.attr="disabled"
                        class="btn btn-success w-100"
                        type="button"
                        @if($cashReceived < $order->amount) disabled @endif>
                        <span wire:loading.remove wire:target="confirmCash">
                            <i class="material-icons-outlined align-middle">check_circle</i>
                            Konfirmasi Pembayaran
                        </span>
                        <span wire:loading wire:target="confirmCash">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
