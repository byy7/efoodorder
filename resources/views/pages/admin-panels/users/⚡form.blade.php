<?php

use App\Concerns\WithNotifications;
use Livewire\Component;
use App\Livewire\Forms\UserForm;
use Livewire\Attributes\On;

new class extends Component {
    public UserForm $form;

    #[On('open-modal')]
    public function openModal($mode, $id = null): void
    {
        $this->resetValidation();
        $this->form->setData($mode, $id);

        if ($mode === 'create' || $mode === 'edit') {
            $this->dispatch('show-form-modal');
        } elseif ($mode === 'delete') {
            $this->dispatch('show-delete-modal');
        }
    }

    public function delete(): void
    {
        if (auth()->user()->id === decrypt($this->form->id) || decrypt($this->form->id) == 1) {
            $this->dispatch('hide-delete-modal');
            $this->dispatch('user-restriction');
        }else{
            $this->form->delete();
            $this->dispatch('hide-delete-modal');
            $this->dispatch('user-deleted');
        }
    }
};
?>

<div wire:ignore.self>
    {{-- CREATE/EDIT MODAL --}}
    <div class="modal fade" id="formModal" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0 py-2 bg-grd-info">
                    <h5 class="modal-title">Registration Form</h5>
                    <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="modal">
                        <i class="material-icons-outlined">close</i>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="form-body">
                        <form class="row g-3">
                            <div class="col-md-6">
                                <label for="input1" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="input1" placeholder="First Name">
                            </div>
                            <div class="col-md-6">
                                <label for="input2" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="input2" placeholder="Last Name">
                            </div>
                            <div class="col-md-12">
                                <label for="input3" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="input3" placeholder="Phone">
                            </div>
                            <div class="col-md-12">
                                <label for="input4" class="form-label">Email</label>
                                <input type="email" class="form-control" id="input4">
                            </div>
                            <div class="col-md-12">
                                <label for="input5" class="form-label">Password</label>
                                <input type="password" class="form-control" id="input5">
                            </div>
                            <div class="col-md-12">
                                <label for="input6" class="form-label">DOB</label>
                                <input type="date" class="form-control" id="input6">
                            </div>
                            <div class="col-md-12">
                                <label for="input7" class="form-label">Country</label>
                                <select id="input7" class="form-select">
                                    <option selected="">Choose...</option>
                                    <option>One</option>
                                    <option>Two</option>
                                    <option>Three</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="input8" class="form-label">City</label>
                                <input type="text" class="form-control" id="input8" placeholder="City">
                            </div>
                            <div class="col-md-4">
                                <label for="input9" class="form-label">State</label>
                                <select id="input9" class="form-select">
                                    <option selected="">Choose...</option>
                                    <option>One</option>
                                    <option>Two</option>
                                    <option>Three</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="input10" class="form-label">Zip</label>
                                <input type="text" class="form-control" id="input10" placeholder="Zip">
                            </div>
                            <div class="col-md-12">
                                <label for="input11" class="form-label">Address</label>
                                <textarea class="form-control" id="input11" placeholder="Address ..."
                                          rows="3"></textarea>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="input12">
                                    <label class="form-check-label" for="input12">Check me out</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="d-md-flex d-grid align-items-center gap-3">
                                    <button type="button" class="btn btn-grd-danger px-4">Submit</button>
                                    <button type="button" class="btn btn-grd-info px-4">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DELETE MODAL --}}
    <div class="modal fade" id="deleteModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
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
            </div>
        </div>
    </div>
</div>

<script>
    let deleteModal;

    Livewire.on('show-delete-modal', () => {
        deleteModal = new bootstrap.Modal(
            document.getElementById('deleteModal')
        );
        deleteModal.show();
    });

    Livewire.on('hide-delete-modal', () => {
        deleteModal?.hide();
    });

    let formModal;

    Livewire.on('show-form-modal', () => {
        formModal = new bootstrap.Modal(
            document.getElementById('formModal')
        );
        formModal.show();
    });

    Livewire.on('hide-form-modal', () => {
        formModal?.hide();
    });
</script>
