<?php

use App\Concerns\WithNotifications;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;
    use WithNotifications;

    public string $search = '';
    public string $status = '';
    public string $payment_method = '';
    public string $type = '';

    #[On('alert-notification')]
    public function alert(string $type, string $message): void
    {
        switch ($type) {
            case 'success':
                $this->notifySuccess($message);
                break;
            case 'warning':
                $this->notifyWarning($message);
                break;
            case 'error':
                $this->notifyError($message);
        }
    }


    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function orders()
    {
        $query = Order::with(['customer', 'payment']);

        /* Search Filter */
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', "%$this->search%");
            });
        }

        if ($this->status !== "") {
            $query->where('status', $this->status);
        }

        if ($this->payment_method !== "") {
            $query->where('payment_method', $this->payment_method);
        }

        if ($this->type !== "") {
            $query->where('type', $this->type);
        }

        return $query->latest()->paginate(10);
    }

    public function show($id): void
    {
        $id = encrypt($id);
        $this->dispatch('open-modal', mode: 'edit', id: $id);
    }

    public function updateTable($id)
    {
        $table = Table::find($id);

        $table->update(['status' => true]);

        return $this->redirectRoute('orders');
    }
};
?>

<div class="main-content">
    {{-- BREADCRUMB --}}
    <x-breadcrumb :heading="__('Pesanan')" :sub-heading="__('Daftar Pesanan')"></x-breadcrumb>
    {{-- END BREADCRUMB --}}

    <div class="row g-3 align-items-end">
        <div class="col-12 col-md-6 col-lg-3">
            <label for="search-input" class="form-label mb-2">Cari Pesanan</label>
            <div class="position-relative">
                <input
                    id="search-input"
                    class="form-control ps-5 pe-3"
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari..."
                    aria-label="Cari produk">
                <span
                    class="material-icons-outlined position-absolute ms-3 translate-middle-y start-0 top-50 fs-5 text-muted"
                    aria-hidden="true">search</span>
            </div>
        </div>

        <div wire:ignore class="col-12 col-md-6 col-lg-3">
            <label for="select-active" class="form-label mb-2">Status</label>
            <select
                id="statusOrder"
                class="form-select"
                aria-label="Filter berdasarkan status" data-placeholder="Pilih Status">
                <option value=""></option>
                <option value="pending">Diproses</option>
                <option value="confirmed">Selesai</option>
                <option value="cancelled">Dibatalkan</option>
            </select>
        </div>

        <div wire:ignore class="col-12 col-md-6 col-lg-3">
            <label for="select-active" class="form-label mb-2">Pembayaran</label>
            <select
                id="paymentMethod"
                class="form-select"
                aria-label="Filter berdasarkan status" data-placeholder="Pilih Pembayaran">
                <option value=""></option>
                <option value="cash">Tunai</option>
                <option value="cashless">Non Tunai</option>
            </select>
        </div>

        <div wire:ignore class="col-12 col-md-6 col-lg-3">
            <label for="select-active" class="form-label mb-2">Tipe</label>
            <select
                id="paymentType"
                class="form-select"
                aria-label="Filter berdasarkan tipe" data-placeholder="Pilih Tipe">
                <option value=""></option>
                <option value="dine_in">Dine In</option>
                <option value="takeaway">Take Away</option>
            </select>
        </div>

        <div class="col-lg-auto flex-grow-1 d-none d-lg-block"></div>

        <div class="col-12 col-lg-auto">
            <a class="btn btn-primary w-100 w-lg-auto px-4" target="_blank" href="{{ route('home') }}">Pesanan Baru</a>
        </div>
    </div>

    @island(defer:true, always:true)
    @placeholder
    <div class="mt-3 mb-3 text-center">
        <button class="btn btn-dark" type="button" disabled><span class="spinner-grow spinner-grow-sm" role="status"
                                                                  aria-hidden="true"></span>
            Loading...
        </button>
    </div>
    @endplaceholder
    <div class="card mt-4" wire:poll.10s>
        <div class="card-body">
            <div class="table-responsive white-space-nowrap">
                <table class="table align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Waktu</th>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($this->orders as $key => $value)
                        <tr wire:key="order-{{ $value->id }}">
                            <td>{{ ($this->orders->currentPage() - 1) * $this->orders->perPage() + $key + 1}}</td>
                            <td>
                                <div>
                                    <h6 class="mb-0">{{ $value->created_at->format('d M Y') }}</h6>
                                    <p class="mb-0 text-muted small">{{ $value->created_at->format('H:i') }}</p>
                                </div>
                            </td>
                            <td>
                                <span class="fw-semibold" wire:click="show({{ $value->id }})"
                                      style="cursor: pointer">{{ $value->order_number }}</span>
                                @if(!is_null($value->table_id))
                                    <br>
                                    <span class="fw-light">{{ $value->table->name }}</span>
                                @endif
                            </td>
                            <td>{{ $value->customer?->name ?? '-' }}</td>
                            <td>
                                            <span
                                                class="badge {{ $value->payment_method === 'cash' ? 'bg-secondary' : 'bg-primary' }} bg-opacity-10 text-secondary text-capitalize">
                                                {{ $value->payment_method === 'cash' ? 'Tunai' : 'Non-Tunai' }}
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
                                    [$bg, $text, $label] = $statusMap[$value->status] ?? ['bg-secondary', 'text-secondary', $value->status];

                                    $typeMap = [
                                        'dine_in' => ['badge bg-primary', 'text-primary', 'Dine In'],
                                        'takeaway' => ['badge bg-secondary', 'text-secondary', 'Take Away'],
                                        ];
                                     [$bgType, $textType, $labelType] = $typeMap[$value->type] ?? ['bg-secondary', 'text-secondary', $value->type];
                                @endphp
                                <div class="card-lable {{ $bg }} {{ $text }} bg-opacity-10">
                                    <p class="{{ $text }} mb-0">{{ $label }}</p>
                                </div>
                                <br>
                                <div class="card-lable {{ $bgType }} {{ $textType }} bg-opacity-10">
                                    <p class="{{ $textType }} mb-0">{{ $labelType }}</p>
                                </div>
                            </td>
                            <td>
                                <h6 class="mb-0">@currency($value->amount)</h6>
                            </td>
                            @if($value->payment_method == 'cash' && $value->payment_status !== "completed")
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-filter dropdown-toggle dropdown-toggle-nocaret"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item text-success" target="_blank"
                                                   href="{{ route('customer.payment.cash', encrypt($value->id)) }}"><i
                                                        class="bi bi-cash me-2"></i>Bayar</a></li>
                                        </ul>
                                    </div>
                                </td>
                            @elseif(!is_null($value->table_id))
                                @if(!$value->table->status)
                                    <td>
                                        <div class="dropdown">
                                            <button
                                                class="btn btn-sm btn-filter dropdown-toggle dropdown-toggle-nocaret"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item text-warning" href="javascript:0;"
                                                       wire:confirm="Meja akan tersedia, update status meja?"
                                                       wire:click="updateTable({{$value->table_id}})"><i
                                                            class="bi bi-pencil me-2"></i>Meja Selesai</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                @else
                                    <td><i class="bi bi-ban text-danger"></i></td>
                                @endif
                            @else
                                <td><i class="bi bi-ban text-danger"></i></td>
                            @endif
                        </tr>
                    @empty
                        <tr class="text-center">
                            <td colspan="8"><h6>Data tidak tersedia</h6></td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $this->orders->links() }}
        </div>
    </div>
    @endisland
    {{-- Modal Form --}}
    <livewire:pages::admin-panels.orders.show/>
</div>

{{-- Select 2 --}}
<script>
    $(document).ready(function () {
        $('#statusOrder').select2({
            theme: "bootstrap-5",
            width: '100%',
            placeholder: $(this).data('placeholder'),
            allowClear: true
        });

        $('#statusOrder').on('change', function () {
            var data = $('#statusOrder').select2("val");
            $wire.$set('status', data);
        });

        $('#paymentMethod').select2({
            theme: "bootstrap-5",
            width: '100%',
            placeholder: $(this).data('placeholder'),
            allowClear: true
        });

        $('#paymentMethod').on('change', function () {
            var data = $('#paymentMethod').select2("val");
            $wire.$set('payment_method', data);
        });

        $('#paymentType').select2({
            theme: "bootstrap-5",
            width: '100%',
            placeholder: $(this).data('placeholder'),
            allowClear: true
        });

        $('#paymentType').on('change', function () {
            var data = $('#paymentType').select2("val");
            $wire.$set('type', data);
        });
    });
</script>
