<?php

use App\Concerns\WithNotifications;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;
    use WithNotifications;

    public string $search = '';

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
    public function users()
    {
        $query = User::query();

        /* Search Filter */
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%$this->search%")
                    ->orWhere('email', 'like', "%$this->search%");
            });
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
    <x-breadcrumb :heading="__('Pengguna')" :sub-heading="__('Daftar Pengguna')"></x-breadcrumb>
    {{-- END BREADCRUMB --}}

    <div class="row g-3">
        <div class="col-auto">
            <div class="position-relative">
                <input class="form-control px-5" type="search" wire:model.live.debounce.300ms="search"
                       placeholder="Cari...">
                <span
                    class="material-icons-outlined position-absolute ms-3 translate-middle-y start-0 top-50 fs-5">search</span>
            </div>
        </div>
        <div class="col-auto">
            <div class="d-flex align-items-center gap-2 justify-content-lg-between justify-content-xl-between">
                <button class="btn btn-primary px-4" wire:click="create"><i class="bi bi-plus-lg me-2"></i>Tambah Data
                </button>
            </div>
        </div>
    </div>

    @island(defer:true,always:true)
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
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Dibuat Pada</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($this->users as $key => $value)
                        <tr>
                            <td>{{ ($this->users->currentPage() - 1) * $this->users->perPage() + $key + 1}}</td>
                            <td>{{ $value->name }}</td>
                            <td>{{ $value->email }}</td>
                            <td>{{ $value->created_at->format('d/m/Y | H:i') }}</td>
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
                            <td colspan="5"><h6>Data tidak tersedia</h6></td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $this->users->links() }}
        </div>
    </div>
    {{-- Modal Form --}}
    <livewire:pages::admin-panels.users.form/>
    @endisland
</div>
