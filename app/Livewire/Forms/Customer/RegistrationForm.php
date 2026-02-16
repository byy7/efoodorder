<?php

namespace App\Livewire\Forms\Customer;

use App\Models\Customer;
use Livewire\Attributes\Validate;
use Livewire\Form;

class RegistrationForm extends Form
{
    public ?Customer $customer;

    #[Validate('required|string')]
    public string $name = '';

    #[Validate('nullable|email')]
    public ?string $email = null;

    #[Validate('nullable|string')]
    public ?string $phone_number = null;

    public function setData($customer): void
    {
        $this->customer = $customer;
    }

    public function save()
    {
        $validated = $this->validate();

        return $this->customer->create($validated);
    }
}
