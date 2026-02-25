<?php

use App\Livewire\Forms\Customer\RegistrationForm;
use App\Models\Customer;
use App\Models\Table;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-customer')]
class extends Component {
    public RegistrationForm $form;
    public string $type = '';
    public ?int $table_id = null;

    public function mount(string $type)
    {
        $this->type = $type;

        if ($this->type == "dine_in") {
            $checkTable = Table::where('status', true)->count();
            if ($checkTable == 0) {
                session()->flash('error', 'Meja tidak tersedia/penuh!');
                $this->redirectRoute('home');
            }
        }

        $this->form->setData(new Customer());
    }

    public function save()
    {
        try {
            $customer = $this->form->save();
            $this->dispatch('alert-notification', type: 'sukses', message: 'Data berhasil disimpan!');

            if ($this->type == "dine_in" && $this->table_id) {
                $this->redirectRoute('customer_orders', [$this->type, encrypt($customer->id), $this->table_id]);
            } else {
                $this->redirectRoute('customer_orders', [$this->type, encrypt($customer->id)]);
            }


        } catch (Exception $e) {
            $this->dispatch('alert-notification', type: 'error', message: $e->getMessage());
        }
    }

    #[Computed]
    public function tables()
    {
        return Table::where('status', true)
            ->get(['id', 'name']);
    }
};
?>

<div>
    <x-header :heading="__('DATA CUSTOMER')"></x-header>

    <div class="card shadow-none bg-transparent bg-none">
        <div class="card-body">
            <div class="form-body">
                <h4 class="mb-1">Registrasi Customer</h4>
                <p>Silahkan isi data dibawah terlebih dahulu</p>
                <form class="mt-4" wire:submit.prevent="save">
                    <div class="row g-4">
                        @if($this->type == "dine_in")
                            <div class="col-12">
                                <div class="position-relative">
                                    <label for="table" class="form-label">Meja <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="table" wire:model="table_id">
                                        <option value="">Pilih Meja</option>
                                        @foreach($this->tables as $value)
                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div class="col-12">
                            <div class="position-relative">
                                <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" wire:model="form.name" class="form-control form-control-lg ps-5"
                                       id="name"
                                       placeholder="Masukkan Nama" required>
                                <span
                                    class="material-icons-outlined position-absolute top-50 start-0 translate-middle-x ms-4">person</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="position-relative">
                                <label for="phone_number" class="form-label">Nomor Handphone (opsional)</label>
                                <input type="tel" wire:model="form.phone_number"
                                       class="form-control form-control-lg ps-5"
                                       id="phone_number"
                                       placeholder="Masukkan Nomor HP">
                                <span
                                    class="material-icons-outlined position-absolute top-50 start-0 translate-middle-x ms-4">phone_iphone</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="position-relative">
                                <label for="email" class="form-label">Email (opsional)</label>
                                <input type="email" wire:model="form.email" class="form-control form-control-lg ps-5"
                                       id="email"
                                       placeholder="Masukkan Email">
                                <span
                                    class="material-icons-outlined position-absolute top-50 start-0 translate-middle-x ms-4">email</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-grid">
                                <button class="btn btn-grd btn-lg btn-grd-primary" type="submit">Simpan</button>
                                <span wire:loading wire:target="save">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    Menyimpan...
                                </span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
