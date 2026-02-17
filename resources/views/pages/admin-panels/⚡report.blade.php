<?php

use App\Exports\OrderReportExport;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

new class extends Component {

    public string $dateStart = '';
    public string $dateEnd = '';
    public string $status = '';
    public string $paymentMethod = '';
    public string $paymentStatus = '';

    public function mount(): void
    {
        $this->dateStart = today()->startOfMonth()->format('Y-m-d');
        $this->dateEnd = today()->endOfMonth()->format('Y-m-d');
    }

    /* ── Query helper ─────────────────────────────── */
    private function baseQuery()
    {
        return Order::with(['customer', 'payment'])
            ->whereBetween('created_at', [
                Carbon::parse($this->dateStart)->startOfDay(),
                Carbon::parse($this->dateEnd)->endOfDay(),
            ])
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->paymentMethod, fn($q) => $q->where('payment_method', $this->paymentMethod))
            ->when($this->paymentStatus, fn($q) => $q->where('payment_status', $this->paymentStatus))
            ->latest();
    }

    /* ── Computed ─────────────────────────────────── */
    #[Computed]
    public function orders()
    {
        return $this->baseQuery()->get();
    }

    #[Computed]
    public function summary(): array
    {
        $q = $this->baseQuery();

        return [
            'total' => (clone $q)->count(),
            'pending' => (clone $q)->where('status', 'pending')->count(),
            'confirmed' => (clone $q)->whereIn('status', ['confirmed', 'completed'])->count(),
            'cancelled' => (clone $q)->where('status', 'cancelled')->count(),
            'revenue' => (clone $q)->where('payment_status', 'completed')->sum('amount'),
        ];
    }

    /* ── Export: Excel ────────────────────────────── */
    public function exportExcel()
    {
        $filename = 'laporan-pesanan-' . $this->dateStart . '-sd-' . $this->dateEnd . '.xlsx';

        return Excel::download(
            new OrderReportExport(
                dateStart: $this->dateStart,
                dateEnd: $this->dateEnd,
                status: $this->status ?: null,
                paymentMethod: $this->paymentMethod ?: null,
                paymentStatus: $this->paymentStatus ?: null,
            ),
            $filename
        );
    }

    /* ── Export: PDF ──────────────────────────────── */
    public function exportPdf()
    {
        $statusLabels = [
            '' => 'Semua',
            'pending' => 'Diproses',
            'confirmed' => 'Dikonfirmasi',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
        $methodLabels = [
            '' => 'Semua',
            'cash' => 'Tunai',
            'cashless' => 'Non-Tunai',
        ];
        $payStatusLabels = [
            '' => 'Semua',
            'pending' => 'Belum Dibayar',
            'completed' => 'Lunas',
            'cancelled' => 'Dibatalkan',
        ];

        $pdf = Pdf::loadView('pages.admin-panels.report-pdf', [
            'orders' => $this->baseQuery()->get(),
            'dateStart' => $this->dateStart,
            'dateEnd' => $this->dateEnd,
            'statusLabel' => $statusLabels[$this->status] ?? 'Semua',
            'paymentMethodLabel' => $methodLabels[$this->paymentMethod] ?? 'Semua',
            'summary' => $this->summary,
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan-pesanan-' . $this->dateStart . '-sd-' . $this->dateEnd . '.pdf';

        return response()->streamDownload(
            fn() => print($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function resetFilters(): void
    {
        $this->status = '';
        $this->paymentMethod = '';
        $this->paymentStatus = '';
    }
};
?>

<div class="main-content">
    <x-breadcrumb :heading="__('Laporan')" :sub-heading="__('Laporan Pesanan')"></x-breadcrumb>

    {{-- ── Filters ──────────────────────────────────────────── --}}
    <div class="card rounded-4 mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">
                <span class="material-icons-outlined align-middle">filter_list</span>
                Filter Laporan
            </h6>
            <div class="row g-3 align-items-end">
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label small">Tanggal Awal</label>
                    <input wire:model.live="dateStart"
                           type="date"
                           class="form-control">
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label small">Tanggal Akhir</label>
                    <input wire:model.live="dateEnd"
                           type="date"
                           class="form-control">
                </div>
                <div class="col-12 col-sm-4 col-lg-2">
                    <label class="form-label small">Status Pesanan</label>
                    <select wire:model.live="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="pending">Diproses</option>
                        <option value="confirmed">Dikonfirmasi</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
                <div class="col-12 col-sm-4 col-lg-2">
                    <label class="form-label small">Metode Bayar</label>
                    <select wire:model.live="paymentMethod" class="form-select">
                        <option value="">Semua</option>
                        <option value="cash">Tunai</option>
                        <option value="cashless">Non-Tunai</option>
                    </select>
                </div>
                <div class="col-12 col-sm-4 col-lg-2">
                    <label class="form-label small">Status Bayar</label>
                    <select wire:model.live="paymentStatus" class="form-select">
                        <option value="">Semua</option>
                        <option value="pending">Belum Dibayar</option>
                        <option value="completed">Lunas</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
                <div class="col-12 col-lg-auto">
                    <button wire:click="resetFilters"
                            class="btn btn-outline-secondary w-100"
                            type="button">
                        <span class="material-icons-outlined align-middle" style="font-size:16px;">restart_alt</span>
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Summary Cards ────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        @php
            $cards = [
                ['label'=>'Total Pesanan',   'value'=>$this->summary['total'],     'icon'=>'receipt_long', 'color'=>'primary',  'currency'=>false],
                ['label'=>'Diproses',        'value'=>$this->summary['pending'],   'icon'=>'pending',      'color'=>'warning',  'currency'=>false],
                ['label'=>'Selesai',         'value'=>$this->summary['confirmed'], 'icon'=>'check_circle', 'color'=>'success',  'currency'=>false],
                ['label'=>'Dibatalkan',      'value'=>$this->summary['cancelled'], 'icon'=>'cancel',       'color'=>'danger',   'currency'=>false],
                ['label'=>'Total Pendapatan','value'=>$this->summary['revenue'],   'icon'=>'payments',     'color'=>'info',     'currency'=>true],
            ];
        @endphp
        @foreach($cards as $card)
            <div class="col-6 col-lg">
                <div class="card rounded-4 h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small">{{ $card['label'] }}</span>
                            <div class="wh-36 d-flex align-items-center justify-content-center rounded-circle
                                        bg-{{ $card['color'] }} bg-opacity-10 text-{{ $card['color'] }}">
                                <span class="material-icons-outlined" style="font-size:18px;">{{ $card['icon'] }}</span>
                            </div>
                        </div>
                        <h4 class="mb-0 fw-bold">
                            @if($card['currency'])
                                @currency($card['value'])
                            @else
                                {{ $card['value'] }}
                            @endif
                        </h4>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── Table + Export Buttons ──────────────────────────── --}}
    <div class="card rounded-4">
        <div class="card-body">

            {{-- Toolbar --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <h6 class="fw-bold mb-0">
                    <span class="material-icons-outlined align-middle">table_view</span>
                    Detail Pesanan
                    <span class="badge bg-primary ms-2">{{ $this->summary['total'] }}</span>
                </h6>
                <div class="d-flex gap-2">
                    <button wire:click="exportExcel"
                            wire:loading.attr="disabled"
                            wire:target="exportExcel"
                            class="btn btn-success btn-sm"
                            type="button">
                        <span wire:loading.remove wire:target="exportExcel">
                            <span class="material-icons-outlined align-middle"
                                  style="font-size:16px;">table_chart</span>
                            Export Excel
                        </span>
                        <span wire:loading wire:target="exportExcel">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Mengunduh...
                        </span>
                    </button>
                    <button wire:click="exportPdf"
                            wire:loading.attr="disabled"
                            wire:target="exportPdf"
                            class="btn btn-danger btn-sm"
                            type="button">
                        <span wire:loading.remove wire:target="exportPdf">
                            <span class="material-icons-outlined align-middle"
                                  style="font-size:16px;">picture_as_pdf</span>
                            Export PDF
                        </span>
                        <span wire:loading wire:target="exportPdf">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Mengunduh...
                        </span>
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width:40px">No</th>
                        <th>No. Pesanan</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Tipe</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th class="text-end">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($this->orders as $i => $order)
                        <tr wire:key="report-{{ $order->id }}">
                            <td class="text-center text-muted small">{{ $i + 1 }}</td>
                            <td>
                                <span class="fw-semibold">{{ $order->order_number }}</span>
                            </td>
                            <td>
                                <div style="font-size:0.85rem;">{{ $order->created_at->format('d M Y') }}</div>
                                <div class="text-muted"
                                     style="font-size:0.78rem;">{{ $order->created_at->format('H:i') }}</div>
                            </td>
                            <td>{{ $order->customer?->name ?? '-' }}</td>
                            <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                        {{ $order->type === 'dine_in' ? 'Dine In' : 'Takeaway' }}
                                    </span>
                            </td>
                            <td>
                                    <span
                                        class="badge {{ $order->payment_method === 'cash' ? 'bg-secondary bg-opacity-10 text-secondary' : 'bg-info bg-opacity-10 text-info' }} me-1">
                                        {{ $order->payment_method === 'cash' ? 'Tunai' : 'Non-Tunai' }}
                                    </span>
                                <span
                                    class="badge {{ $order->payment_status === 'completed' ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning' }}">
                                        {{ $order->payment_status === 'completed' ? 'Lunas' : 'Belum' }}
                                    </span>
                            </td>
                            <td>
                                @php
                                    $map = [
                                        'pending'   => ['bg-warning',  'text-warning',  'Diproses'],
                                        'confirmed' => ['bg-success',  'text-success',  'Selesai'],
                                        'completed' => ['bg-success',  'text-success',  'Selesai'],
                                        'cancelled' => ['bg-danger',   'text-danger',   'Dibatalkan'],
                                    ];
                                    [$bg, $txt, $lbl] = $map[$order->status] ?? ['bg-secondary', 'text-secondary', $order->status];
                                @endphp
                                <span class="badge {{ $bg }} {{ $txt }} bg-opacity-10">{{ $lbl }}</span>
                            </td>
                            <td class="text-end fw-semibold">@currency($order->amount)</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <span class="material-icons-outlined d-block mb-2" style="font-size:48px;color:#ccc;">search_off</span>
                                Tidak ada data untuk filter yang dipilih
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                    @if($this->orders->isNotEmpty())
                        <tfoot class="table-light">
                        <tr>
                            <td colspan="7" class="text-end fw-bold">Total Pendapatan (Lunas)</td>
                            <td class="text-end fw-bold text-success">
                                @currency($this->summary['revenue'])
                            </td>
                        </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

        </div>
    </div>
</div>
