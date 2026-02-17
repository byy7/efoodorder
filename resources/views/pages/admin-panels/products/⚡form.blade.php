<?php

use App\Livewire\Forms\Admin\ProductForm;
use App\Models\Category;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public ProductForm $form;

    #[On('open-modal')]
    public function openModal($mode, $id = null): void
    {
        $this->resetValidation();
        $this->form->setData($mode, $id);
        $this->dispatch('show-modal', mode: $mode);
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('name')->where('is_active', true)->get(['id', 'name']);
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
                <h5 class="modal-title">{{ $this->form->mode === 'create' ? "Tambah" : "Ubah" }} Produk</h5>
                <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="modal">
                    <i class="material-icons-outlined">close</i>
                </a>
            </div>

            {{-- BODY --}}
            <div class="modal-body">
                <div class="form-body">
                    <div class="col-md-12" wire:ignore>
                        <label for="select-active" class="form-label mb-2">Kategori</label>
                        <select wire:model="form.category_id"
                                id="categorySelect"
                                class="form-select"
                                aria-label="Filter berdasarkan status" data-placeholder="Pilih kategori">
                            <option value=""></option>
                            @foreach($this->categories as $value)
                                <option
                                    value="{{ encrypt($value->id) }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($form->mode == "edit")
                        @if(!is_null($form->category_id))
                            <small class="text-primary d-block mt-1">
                                Kategori saat ini: {{ Category::find(decrypt($form->category_id))->name }}
                            </small>
                        @endif
                        <small class="text-danger d-block mt-1">
                            Kosongkan jika tidak ingin mengubah kategori
                        </small>
                    @endif
                    <div class="col-md-12 mb-2">
                        <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                        <input wire:model="form.name" type="text" class="form-control" id="name"
                               placeholder="Masukkan Nama" required>
                        @error('form.name')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="price" class="form-label">Harga <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input wire:model="form.price" type="text" class="form-control number-separator" id="price"
                                   placeholder="Masukkan Harga" required>
                        </div>
                        @error('form.price')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="stock" class="form-label">Stok <span class="text-danger">*</span></label>
                        <input wire:model="form.stock" type="number" min="1" step="1"
                               oninput="validity.valid||(value='')" class="form-control" id="stock"
                               placeholder="Masukkan Stok" required>
                        @error('form.stock')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="description" class="form-label">Keterangan</label>
                        <textarea wire:model="form.description" class="form-control"
                                  placeholder="Masukkan Keterangan"></textarea>
                        @error('form.description')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    {{-- Image Upload --}}
                    <div class="col-md-12 mb-3">
                        <div x-data="{ uploading: false, progress: 0 }"
                             x-on:livewire-upload-start="uploading = true"
                             x-on:livewire-upload-finish="uploading = false"
                             x-on:livewire-upload-cancel="uploading = false"
                             x-on:livewire-upload-error="uploading = false"
                             x-on:livewire-upload-progress="progress = $event.detail.progress">

                            <label class="form-label">Gambar Produk</label>
                            <input type="file"
                                   class="form-control"
                                   wire:model="form.image"
                                   accept="image/*"
                                   x-bind:disabled="uploading">

                            <div class="mt-1">
                                <small class="text-muted">Format: JPG, JPEG, PNG (Max: 2 MB)</small>
                            </div>

                            @if($form->mode == "edit")
                                <small class="text-danger d-block mt-1">
                                    Kosongkan jika tidak ingin mengubah gambar
                                </small>
                            @endif

                            {{-- Upload Progress --}}
                            <div x-show="uploading"
                                 x-transition
                                 class="mt-2">
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                         role="progressbar"
                                         :style="`width: ${progress}%`"
                                         :aria-valuenow="progress"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        <span x-text="`${Math.round(progress)}%`"></span>
                                    </div>
                                </div>
                                <small class="text-muted">Mengupload gambar...</small>
                            </div>

                            @error('form.image')
                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12 mb-2">
                        <div class="form-check form-switch form-check-success">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   id="flexSwitchCheckSuccess"
                                   wire:model="form.is_available">
                            <label class="form-check-label" for="flexSwitchCheckSuccess">Status Tersedia
                                (Aktif/Tidak
                                Aktif)</label>
                        </div>
                        @error('form.is_available')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="submit"
                        class="btn btn-primary"
                        wire:loading.attr="disabled"

                        wire:target="save, form.image">
                    <span wire:loading.remove wire:target="save, form.image">
                        {{ $form->mode === 'create' ? "Simpan" : "Perbarui" }}
                    </span>
                    <span wire:loading wire:target="save">
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                        Menyimpan...
                    </span>
                    <span wire:loading wire:target="form.image">
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                        Mengupload...
                    </span>
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

{{-- Select 2 --}}
<script>
    $(document).ready(function () {
        $('#categorySelect').select2({
            theme: "bootstrap-5",
            width: '100%',
            placeholder: $(this).data('placeholder'),
            allowClear: true,
            dropdownParent: "#formModal"
        });

        $('#categorySelect').on('select2:select', function () {
            var data = $('#categorySelect').select2("val");
            $wire.$set('form.category_id', data);
        });

        Livewire.on('hide-modal', () => {
            $('#categorySelect').val('').trigger('change');
        });
    });
</script>
