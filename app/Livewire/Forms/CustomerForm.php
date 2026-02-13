<?php

namespace App\Livewire\Forms;

use App\Models\Customer;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CustomerForm extends Form
{
    public ?Customer $customer;

    public string $mode = '';

    public ?string $id = null;

    #[Validate('required|string')]
    public string $name = '';

    #[Validate('nullable|email')]
    public string $email = '';

    #[Validate('nullable|string')]
    public string $phone_number = '';

    public function setData($mode, $id): void
    {
        $this->mode = $mode;

        if (! is_null($id)) {
            $this->id = $id;
        }

        if ($mode === 'edit' && $id) {
            $this->customer = Customer::find(decrypt($id));
            $this->name = $this->customer->name;
            $this->email = $this->customer->email;
            $this->phone_number = $this->customer->phone_number;
        } elseif ($mode === 'delete' && $id) {
            $this->customer = Customer::find(decrypt($id));
        } else {
            $this->customer = new Customer;
            $this->reset(['name', 'email', 'phone_number']);
        }
    }

    public function save(): void
    {
        if ($this->mode === 'create') {
            $this->store();
        } elseif ($this->mode === 'edit') {
            $this->update();
        }
    }

    public function store(): void
    {
        $validated = $this->validate();
        $this->customer->create($validated);
    }

    public function update(): void
    {
        $validated = $this->validate();
        $this->customer->update($validated);
    }

    public function delete(): void
    {
        $this->customer->delete();
        $this->reset();
    }
}
