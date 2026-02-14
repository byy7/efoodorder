<?php

use App\Livewire\Forms\Admin\TableForm;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public TableForm $form;

    #[On('open-modal')]
    public function openModal($mode, $id = null): void
    {
        $this->resetValidation();
        $this->form->setData($mode, $id);
        $this->dispatch('show-modal', mode: $mode);
    }

    public function save(): void
    {
        try {
            $this->form->save();
            $this->closeModal('success', 'Data berhasil disimpan!');
        } catch (Exception $e) {
            $this->closeModal('error', $e->getMessage());
        }

    }

    public function delete(): void
    {
        $this->form->delete();
        $this->closeModal('success', 'Data berhasil dihapus!');
    }

    private function closeModal(string $type, string $message): void
    {
        $this->dispatch('hide-modal');
        $this->dispatch('alert-notification', type: $type, message: $message);
    }
};
?>

<div>
    {{-- CREATE/EDIT MODAL --}}
    <x-modal :modal-id="__('formModal')">
        <form wire:submit.prevent="save">
            {{-- HEADER --}}
            <div class="modal-header border-bottom-0 py-2 bg-info">
                <h5 class="modal-title">{{ $this->form->mode === 'create' ? "Tambah" : "Ubah" }} Meja</h5>
                <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="modal">
                    <i class="material-icons-outlined">close</i>
                </a>
            </div>

            {{-- BODY --}}
            <div class="modal-body">
                <div class="form-body">
                    <div class="col-md-12 mb-2">
                        <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                        <input wire:model="form.name" type="text" class="form-control" id="name"
                               placeholder="Masukkan Nama" required>
                        @error('form.name')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-12 mb-2">
                        <div class="form-check form-switch form-check-success">
                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckSuccess"
                                   wire:model="form.status">
                            <label class="form-check-label" for="flexSwitchCheckSuccess">Status Meja (Tersedia/Tidak
                                Tersedia)</label>
                        </div>
                        @error('form.status')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="submit"
                        class="btn btn-primary">{{ $this->form->mode === 'create' ? "Simpan" : "Perbarui" }}
                </button>
            </div>
        </form>
    </x-modal>

    {{-- DELETE MODAL --}}
    <x-modal :modal-id="__('deleteModal')">
        <form wire:submit.prevent="delete">
            {{-- HEADER --}}
            <div class="modal-header border-bottom-0 py-2">
                <h5 class="modal-title">
                    Konfirmasi Hapus
                </h5>
                <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="modal">
                    <i class="material-icons-outlined">close</i>
                </a>
            </div>

            {{-- BODY --}}
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data ini? Data yang dihapus tidak dapat dikembalikan.
            </div>

            {{-- FOOTER --}}
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="submit" class="btn btn-danger">Hapus
                </button>
            </div>
        </form>
    </x-modal>
</div>
