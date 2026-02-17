<?php

use App\Models\Order;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public string $date_start = '';
    public string $date_end = '';

    public function mount(): void
    {
        $this->date_start = today()->startOfMonth()->format('Y-m-d');
        $this->date_end = today()->endOfMonth()->format('Y-m-d');
    }

    #[Computed]
    public function orderData(): array
    {
        $query = Order::query();

        if ($this->date_start && $this->date_end) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->date_start)->startOfDay(),
                Carbon::parse($this->date_end)->endOfDay(),
            ]);
        }

        return [
            [
                'title' => 'Pesanan Diproses',
                'count' => (clone $query)->where('status', 'pending')->count(),
                'icon' => 'edit',
                'color' => 'warning',
            ],
            [
                'title' => 'Pesanan Selesai',
                'count' => (clone $query)->where('status', 'confirmed')->count(),
                'icon' => 'check_circle',
                'color' => 'success',
            ],
            [
                'title' => 'Pesanan Dibatalkan',
                'count' => (clone $query)->where('status', 'cancelled')->count(),
                'icon' => 'block',
                'color' => 'danger',
            ],
            [
                'title' => 'Total Pendapatan',
                'count' => (clone $query)->where('payment_status', 'completed')->sum('amount'),
                'icon' => 'attach_money',
                'color' => 'primary',
                'currency' => true,
            ],
        ];
    }

    #[Computed]
    public function latestOrders()
    {
        return Order::with(['customer', 'payment'])
            ->latest()
            ->take(10)
            ->get();
    }
};
?>

<div class="main-content">
    {{-- BREADCRUMB --}}
    <x-breadcrumb :heading="__('Dashboard')" :sub-heading="__('Dashboard')"></x-breadcrumb>

    {{-- DATE FILTER --}}
    <div class="row mb-3 align-items-end">
        <div class="col-auto">
            <label class="form-label">Tanggal Awal</label>
            <input wire:model.live="date_start"
                   type="date"
                   class="form-control">
        </div>
        <div class="col-auto">
            <label class="form-label">Tanggal Akhir</label>
            <input wire:model.live="date_end"
                   type="date"
                   class="form-control">
        </div>
    </div>

    {{-- SUMMARY --}}
    <div class="row">
        <div class="col-12">
            <h5 class="mb-3 fw-bold">RINGKASAN PESANAN</h5>
        </div>

        @foreach($this->orderData as $item)
            <div class="col-12 col-lg-3 col-xxl-3 d-flex">
                <div class="card rounded-4 w-100">
                    <div class="card-body">
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <div class="wh-42 d-flex align-items-center justify-content-center rounded-circle
                                        bg-{{ $item['color'] }} bg-opacity-10 text-{{ $item['color'] }}">
                                <span class="material-icons-outlined fs-5">{{ $item['icon'] }}</span>
                            </div>
                        </div>
                        <div>
                            <p class="mb-1 text-muted">{{ $item['title'] }}</p>
                            <h4 class="mb-0">
                                @if(!empty($item['currency']))
                                    @currency($item['count'])
                                @else
                                    {{ $item['count'] }}
                                @endif
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    {{-- END SUMMARY --}}

    {{-- LATEST ORDER --}}
    <div class="row mt-4">
        <div class="col-12 d-flex align-items-start gap-3">
            <h5 class="mb-3 fw-bold">PESANAN TERBARU</h5>
        </div>
        <div class="col-12 d-flex">
            <div class="card rounded-4 w-100">
                <div class="card-body">
                    <a class="mb-3 btn btn-outline-info" href="{{ route('orders') }}" wire:navigate>Lihat Semua Pesanan</a>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-striped">
                            <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>No. Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($this->latestOrders as $order)
                                <tr wire:key="order-{{ $order->id }}">
                                    <td>
                                        <div>
                                            <h6 class="mb-0">{{ $order->created_at->format('d M Y') }}</h6>
                                            <p class="mb-0 text-muted small">{{ $order->created_at->format('H:i') }}</p>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $order->order_number }}</span>
                                    </td>
                                    <td>{{ $order->customer?->name ?? '-' }}</td>
                                    <td>
                                            <span
                                                class="badge {{ $order->payment_method === 'cash' ? 'bg-secondary' : 'bg-primary' }} bg-opacity-10 text-secondary text-capitalize">
                                                {{ $order->payment_method === 'cash' ? 'Tunai' : 'Non-Tunai' }}
                                            </span>
                                    </td>
                                    <td>
                                        @php
                                            $statusMap = [
                                                'pending'   => ['badge bg-warning',  'text-warning',  'Diproses'],
                                                'confirmed' => ['badge bg-success',  'text-success',  'Selesai'],
                                                'completed' => ['badge bg-success',  'text-success',  'Selesai'],
                                                'cancelled' => ['badge bg-danger',   'text-danger',   'Dibatalkan'],
                                            ];
                                            [$bg, $text, $label] = $statusMap[$order->status] ?? ['bg-secondary', 'text-secondary', $order->status];
                                        @endphp
                                        <div class="card-lable {{ $bg }} {{ $text }} bg-opacity-10">
                                            <p class="{{ $text }} mb-0">{{ $label }}</p>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="mb-0">@currency($order->amount)</h6>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Belum ada pesanan
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- END LATEST ORDER --}}
</div>
