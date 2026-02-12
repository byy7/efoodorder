<?php

use App\Concerns\WithNotifications;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;
    use WithNotifications;

    public string $search = '';
    public string $is_available = '';

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
    public function products()
    {
        $query = Product::query()
            ->with('category');

        /* Search Filter */
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%$this->search%")
                    ->orWhere('description', 'like', "%$this->search%");
            });
        }

        if ($this->is_available !== "") {
            $query->where('is_available', $this->is_available);
        }

        return $query->latest()->paginate(10);
    }

    public function create(): void
    {
        $this->dispatch('open-modal', mode: 'create');
    }

    public function edit($id): void
    {
        $id = encrypt($id);
        $this->dispatch('open-modal', mode: 'edit', id: $id);
    }

    public function delete($id): void
    {
        $id = encrypt($id);
        $this->dispatch('open-modal', mode: 'delete', id: $id);
    }
};
?>

<div class="main-content">
    {{-- BREADCRUMB --}}
    <x-breadcrumb :heading="__('Produk')" :sub-heading="__('Daftar Produk')"></x-breadcrumb>
    {{-- END BREADCRUMB --}}

    <div class="row g-3 align-items-end">
        <div class="col-12 col-md-6 col-lg-4">
            <label for="search-input" class="form-label mb-2">Cari Produk</label>
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
            <select wire:model="is_available"
                    id="statusProduct"
                    class="form-select"
                    aria-label="Filter berdasarkan status" data-placeholder="Pilih Status">
                <option value=""></option>
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
            </select>
        </div>

        <div class="col-lg-auto flex-grow-1 d-none d-lg-block"></div>

        <div class="col-12 col-lg-auto">
            <button
                class="btn btn-primary w-100 w-lg-auto px-4"
                wire:click="create"
                aria-label="Tambah kategori baru">
                <i class="bi bi-plus-lg me-2" aria-hidden="true"></i>
                Tambah Produk
            </button>
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
    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive white-space-nowrap">
                <table class="table align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>kategori</th>
                        <th>Nama</th>
                        <th>Gambar</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($this->products as $key => $value)
                        <tr>
                            <td>{{ ($this->products->currentPage() - 1) * $this->products->perPage() + $key + 1}}</td>
                            <td>{{ $value->category?->name ?? "-" }}</td>
                            <td>{{ $value->name }}</td>
                            <td>@if(!is_null($value->image))
                                    <img src="{{ Storage::url($value->image) }}" class="img-fluid img-thumbnail"
                                         width="100" alt="{{ $value->name }}">
                                @else
                                    <p>Foto tidak tersedia.</p>
                                @endif
                            </td>
                            <td>@currency($value->price)</td>
                            <td><span
                                    class="badge {{ $value->is_available ? "bg-grd-success" : "bg-grd-danger" }}">{{ $value->is_available ? "Aktif" : "Tidak Aktif" }}</span>
                            </td>
                            <td>{{ $value->stock }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-filter dropdown-toggle dropdown-toggle-nocaret"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item text-warning" href="javascript:"
                                               wire:click="edit({{ $value->id }})"><i
                                                    class="bi bi-pencil me-2"></i>Edit</a></li>
                                        <li><a class="dropdown-item text-danger" href="javascript:"
                                               wire:click="delete({{ $value->id }})"><i
                                                    class="bi bi-trash me-2"></i>Hapus</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="text-center">
                            <td colspan="7"><h6>Data tidak tersedia</h6></td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $this->products->links() }}
        </div>
    </div>
    {{-- Modal Form --}}
    <livewire:pages::admin-panels.products.form/>
    @endisland
</div>

{{-- Select 2 --}}
<script>
    $(document).ready(function () {
        $('#statusProduct').select2({
            theme: "bootstrap-5",
            width: '100%',
            placeholder: $(this).data('placeholder'),
            allowClear: true
        });

        $('#statusProduct').on('change', function () {
            var data = $('#statusProduct').select2("val");
            $wire.$set('is_available', data);
        });
    });
</script>
