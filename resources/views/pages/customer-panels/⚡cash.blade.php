<?php

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-customer')]
class extends Component {
    public Order $order;

    public function mount(string $orderId): void
    {
        $this->order = Order::with(['items.product', 'payment'])
            ->findOrFail(decrypt($orderId));
    }
};
?>

<div>
    <x-header :heading="__('Konfirmasi Pemesanan')"></x-header>

    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 text-center">

            <div class="mb-4">
                <i class="material-icons-outlined text-success" style="font-size: 80px;">check_circle</i>
                <h4 class="mt-2">Pesanan Dikonfirmasi!</h4>
                <p class="text-white">Nomor pesanan: <strong>{{ $order->order_number }}</strong></p>
            </div>

            <div class="card text-start mb-3">
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($order->items as $item)
                            <li class="list-group-item d-flex justify-content-between py-2">
                                <span>
                                    {{ $item->product?->name ?? '(produk dihapus)' }}
                                    <small class="text-white">x{{ $item->quantity }}</small>
                                </span>
                                <span>@currency($item->subtotal)</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer d-flex justify-content-between fw-bold">
                    <span>Total Bayar</span>
                    <span class="text-success">@currency($order->amount)</span>
                </div>
            </div>

            <h6 class="text-white fw-bold">Silahkan ke kasir untuk melakukan pembayaran tunai.</h6>
            <p class="text-white small">
                Anda dapat menutup halaman ini jika sudah selesai melakukan pembayaran.
            </p>
        </div>
    </div>
</div>
