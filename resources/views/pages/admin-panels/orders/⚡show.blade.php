<?php

use App\Models\Order;
use App\Models\OrderItem;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public ?Order $order = null;

    #[On('open-modal')]
    public function openModal($mode, $id = null): void
    {
        $this->order = Order::find(decrypt($id));
        $this->dispatch('show-modal', mode: $mode);
    }

    #[Computed]
    public function totalOrders()
    {
        return $this->order->items->sum('subtotal');
    }

    private function closeModal(string $type, string $message): void
    {
        $this->dispatch('hide-modal');
        $this->dispatch('alert-notification', type: $type, message: $message);
    }
};
?>

<div>
    <x-modal :modal-id="__('formModal')">
        {{-- HEADER --}}
        <div class="modal-header border-bottom-0 py-2 bg-info">
            <h5 class="modal-title">Detail Pesanan {{ $this->order?->order_number }}</h5>
            <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="modal">
                <i class="material-icons-outlined">close</i>
            </a>
        </div>

        {{-- BODY --}}
        <div class="modal-body">
            <div class="form-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Menu</th>
                            <th>Jumlah</th>
                            <th>Catatan</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($this->order)
                            @foreach($this->order?->items as $key => $value)
                                <tr wire:key="order-item-{{ $value->id }}">
                                    <td>{{ $key + 1}}</td>
                                    <td>{{ $value->product?->name }}</td>
                                    <td>{{ $value->quantity }}</td>
                                    <td>{{ $value->notes ? \Illuminate\Support\Str::limit($value->notes,20)  : '-'}}</td>
                                    <td>@currency($value->subtotal)</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>

                    @if($this->order)
                        <div class="text-end">
                            <h6 class="fw-bold text-dark">TOTAL PEMBELIAN: @currency($this->totalOrders)</h6>
                        </div>

                    @endif
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="modal-footer border-top-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                Tutup
            </button>
        </div>
    </x-modal>
</div>
