<?php

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-customer')]
class extends Component {
    public Order $order;

    public function mount(string $orderId): void
    {
        $this->order = Order::findOrFail(decrypt($orderId));
    }
};
?>

<div>
    <x-header :heading="__('Pembayaran Gagal')"></x-header>

    <div class="row justify-content-center">
        <div class="col-12 col-md-6 text-center py-5">
            <i class="material-icons-outlined text-danger" style="font-size: 80px;">cancel</i>
            <h4 class="mt-2">Pembayaran Gagal atau Dibatalkan</h4>
            <p class="text-muted">Pesanan <strong>{{ $order->order_number }}</strong> belum dibayar.</p>
            <a href="{{ url()->previous() }}" class="btn btn-primary mt-2">
                <i class="material-icons-outlined align-middle">arrow_back</i>
                Coba Lagi
            </a>
        </div>
    </div>
</div>
